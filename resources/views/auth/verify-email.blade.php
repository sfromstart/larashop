<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('회원가입해 주셔서 감사합니다! 이메일로 발송된 인증 링크를 클릭하여 이메일 주소를 인증해 주세요.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('새로운 인증 링크가 회원가입 시 입력하신 이메일 주소로 발송되었습니다.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('인증 이메일 재발송') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('로그아웃') }}
            </button>
        </form>
    </div>
</x-guest-layout>
