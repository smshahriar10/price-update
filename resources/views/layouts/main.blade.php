<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.head')
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="main-content">
        <section class="section">
          <div class="section-body">
            <div class="row">
                @yield('content')
            </div>
          </div>
        </section>
        @stack('modals')
      </div>
        @include('partials.footer')
    </div>
  </div>
  @if(\Osiset\ShopifyApp\Util::getShopifyConfig('appbridge_enabled') && \Osiset\ShopifyApp\Util::useNativeAppBridge())
      <script src="{{config('shopify-app.appbridge_cdn_url') ?? 'https://unpkg.com'}}/@shopify/app-bridge{{ \Osiset\ShopifyApp\Util::getShopifyConfig('appbridge_version') ? '@'.config('shopify-app.appbridge_version') : '' }}"></script>
      <script
          @if(\Osiset\ShopifyApp\Util::getShopifyConfig('turbo_enabled'))
              data-turbolinks-eval="false"
          @endif
      >
          var AppBridge = window['app-bridge'];
          var actions = AppBridge.actions;
          var utils = AppBridge.utilities;
          var createApp = AppBridge.default;
          var app = createApp({
              apiKey: "{{ \Osiset\ShopifyApp\Util::getShopifyConfig('api_key', $shopDomain ?? Auth::user()->name ) }}",
              host: "{{ \Request::get('host') }}",
              forceRedirect: true,
          });
      </script>

      @include('shopify-app::partials.token_handler')
      @include('shopify-app::partials.flash_messages')
  @endif
  @include('partials.scripts')
</body>
</html>