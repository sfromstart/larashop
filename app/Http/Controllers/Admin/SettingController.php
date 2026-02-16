<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            // General
            'site_name' => Setting::get('site_name', 'LaraShop'),
            'site_description' => Setting::get('site_description', ''),
            'site_phone' => Setting::get('site_phone', ''),
            'site_email' => Setting::get('site_email', ''),
            'site_address' => Setting::get('site_address', ''),
            'site_business_number' => Setting::get('site_business_number', ''),

            // Shopping
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', '50000'),
            'default_shipping_fee' => Setting::get('default_shipping_fee', '3000'),
            'point_rate' => Setting::get('point_rate', '1'),
            'min_order_amount' => Setting::get('min_order_amount', '10000'),
            'max_order_quantity' => Setting::get('max_order_quantity', '10'),

            // SEO
            'meta_title' => Setting::get('meta_title', ''),
            'meta_description' => Setting::get('meta_description', ''),
            'meta_keywords' => Setting::get('meta_keywords', ''),
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'naver_analytics_id' => Setting::get('naver_analytics_id', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:1000'],
            'site_phone' => ['nullable', 'string', 'max:20'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'site_business_number' => ['nullable', 'string', 'max:50'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
            'default_shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'point_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_order_quantity' => ['nullable', 'integer', 'min:1'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'naver_analytics_id' => ['nullable', 'string', 'max:50'],
        ]);

        $generalKeys = ['site_name', 'site_description', 'site_phone', 'site_email', 'site_address', 'site_business_number'];
        $shoppingKeys = ['free_shipping_threshold', 'default_shipping_fee', 'point_rate', 'min_order_amount', 'max_order_quantity'];
        $seoKeys = ['meta_title', 'meta_description', 'meta_keywords', 'google_analytics_id', 'naver_analytics_id'];

        foreach ($generalKeys as $key) {
            Setting::set($key, $request->input($key, ''), 'general');
        }

        foreach ($shoppingKeys as $key) {
            Setting::set($key, $request->input($key, ''), 'shopping');
        }

        foreach ($seoKeys as $key) {
            Setting::set($key, $request->input($key, ''), 'seo');
        }

        return back()->with('success', '설정이 저장되었습니다.');
    }
}
