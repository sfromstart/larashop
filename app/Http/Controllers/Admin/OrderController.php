<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = Order::with('user');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Payment method filter
        if ($paymentMethod = $request->input('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        // Date range filter
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['order_number', 'total', 'status', 'payment_method', 'created_at'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $orders = $query->paginate(20)->withQueryString();
        $statuses = OrderStatus::cases();

        // Summary statistics
        $summaryBase = $this->buildFilteredQuery($request);
        $summary = [
            'total_count' => (clone $summaryBase)->count(),
            'total_revenue' => (clone $summaryBase)->where('status', '!=', 'cancelled')->sum('total'),
            'pending' => (clone $summaryBase)->where('status', 'pending')->count(),
            'paid' => (clone $summaryBase)->where('status', 'paid')->count(),
            'preparing' => (clone $summaryBase)->where('status', 'preparing')->count(),
            'shipped' => (clone $summaryBase)->where('status', 'shipped')->count(),
            'delivered' => (clone $summaryBase)->where('status', 'delivered')->count(),
            'cancelled' => (clone $summaryBase)->where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statuses', 'summary'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        $statuses = OrderStatus::cases();
        $optionValueMap = ProductOptionValue::resolveForItems($order->items);

        return view('admin.orders.show', compact('order', 'statuses', 'optionValueMap'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'string'],
            'admin_memo' => ['nullable', 'string', 'max:1000'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'tracking_url' => ['nullable', 'url', 'max:500'],
        ]);

        $newStatus = OrderStatus::tryFrom($request->input('status'));

        if (! $newStatus) {
            return back()->with('error', '유효하지 않은 주문 상태입니다.');
        }

        $updateData = [
            'status' => $newStatus->value,
        ];

        if ($request->filled('admin_memo')) {
            $updateData['admin_memo'] = $request->input('admin_memo');
        }

        if ($request->filled('tracking_number')) {
            $updateData['tracking_number'] = $request->input('tracking_number');
        }

        if ($request->filled('tracking_url')) {
            $updateData['tracking_url'] = $request->input('tracking_url');
        }

        // Set timestamp based on status change
        match ($newStatus) {
            OrderStatus::Paid => $updateData['paid_at'] = $order->paid_at ?? now(),
            OrderStatus::Shipped => $updateData['shipped_at'] = $order->shipped_at ?? now(),
            OrderStatus::Delivered => $updateData['delivered_at'] = $order->delivered_at ?? now(),
            OrderStatus::Cancelled => $updateData['cancelled_at'] = $order->cancelled_at ?? now(),
            default => null,
        };

        $order->update($updateData);

        return back()->with('success', '주문 상태가 변경되었습니다: ' . $newStatus->label());
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'status' => ['required', 'string'],
        ]);

        $newStatus = OrderStatus::tryFrom($request->input('status'));

        if (! $newStatus) {
            return back()->with('error', '유효하지 않은 주문 상태입니다.');
        }

        $orders = Order::whereIn('id', $request->input('order_ids'))->get();
        $count = 0;

        foreach ($orders as $order) {
            $updateData = ['status' => $newStatus->value];

            match ($newStatus) {
                OrderStatus::Paid => $updateData['paid_at'] = $order->paid_at ?? now(),
                OrderStatus::Shipped => $updateData['shipped_at'] = $order->shipped_at ?? now(),
                OrderStatus::Delivered => $updateData['delivered_at'] = $order->delivered_at ?? now(),
                OrderStatus::Cancelled => $updateData['cancelled_at'] = $order->cancelled_at ?? now(),
                default => null,
            };

            $order->update($updateData);
            $count++;
        }

        return back()->with('success', "{$count}건의 주문 상태가 '{$newStatus->label()}'(으)로 변경되었습니다.");
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request);

        // Same sort as index
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['order_number', 'total', 'status', 'payment_method', 'created_at'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $fileName = '주문내역_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            // UTF-8 BOM
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                '주문번호', '주문일시', '고객명', '고객이메일', '수취인', '연락처',
                '주소', '상세주소', '우편번호', '결제방법', '상품금액', '배송비',
                '할인', '총결제금액', '주문상태', '결제일시', '발송일시',
                '배송완료일시', '운송장번호', '관리자메모',
            ]);

            $paymentLabels = [
                'bank_transfer' => '무통장입금',
                'card' => '카드결제',
            ];

            $query->chunk(500, function ($orders) use ($handle, $paymentLabels) {
                foreach ($orders as $order) {
                    $statusLabel = OrderStatus::tryFrom($order->status)?->label() ?? $order->status;
                    $paymentLabel = $paymentLabels[$order->payment_method] ?? ($order->payment_method ?? '-');

                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at?->format('Y-m-d H:i:s'),
                        $order->user?->name ?? '-',
                        $order->user?->email ?? '-',
                        $order->shipping_name,
                        $order->shipping_phone,
                        $order->shipping_address,
                        $order->shipping_address_detail ?? '',
                        $order->shipping_postal_code,
                        $paymentLabel,
                        $order->subtotal,
                        $order->shipping_fee,
                        $order->discount_amount,
                        $order->total,
                        $statusLabel,
                        $order->paid_at?->format('Y-m-d H:i:s') ?? '',
                        $order->shipped_at?->format('Y-m-d H:i:s') ?? '',
                        $order->delivered_at?->format('Y-m-d H:i:s') ?? '',
                        $order->tracking_number ?? '',
                        $order->admin_memo ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
