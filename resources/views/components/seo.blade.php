{{-- SEO Meta Tags --}}
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">

@if($canonical)
<link rel="canonical" href="{{ $canonical }}">
@endif

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:locale" content="ko_KR">
<meta property="og:site_name" content="{{ \App\Models\Setting::get('site_name', 'LaraShop') }}">
@if($canonical)
<meta property="og:url" content="{{ $canonical }}">
@endif
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif

{{-- Naver / Google 인증 메타태그 (설정에서 관리) --}}
@if(\App\Models\Setting::get('naver_site_verification'))
<meta name="naver-site-verification" content="{{ \App\Models\Setting::get('naver_site_verification') }}">
@endif
@if(\App\Models\Setting::get('google_site_verification'))
<meta name="google-site-verification" content="{{ \App\Models\Setting::get('google_site_verification') }}">
@endif

{{-- JSON-LD Structured Data --}}
@foreach($jsonLd as $schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endforeach
