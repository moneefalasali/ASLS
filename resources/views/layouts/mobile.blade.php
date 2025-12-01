<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4F46E5" />
    <link rel="manifest" href="/manifest.json" />
    <link rel="preload" href="/frontend/fonts/cairo/cairo-regular.woff2" as="font" type="font/woff2" crossorigin="anonymous" />
    <link rel="stylesheet" href="/frontend/fonts/cairo/cairo.css" />
    <link rel="stylesheet" href="/frontend/mobile-styles.css?v={{ file_exists(public_path('frontend/mobile-styles.css')) ? filemtime(public_path('frontend/mobile-styles.css')) : time() }}" />
    <title>@yield('title', 'AISL')</title>
</head>
    <body class="{{ (request()->query('mobile') == '1' ? 'force-mobile' : '') . (request()->is('signs*') ? ' page-signs-theme' : '') }}">
    <div class="mobile-container">
        <!-- Mobile Header -->
        <header class="mobile-header">
            <!-- Left side icons -->
            <div class="header-left">
                @hasSection('header-left')
                    @yield('header-left')
                @endif
            </div>
            
            <!-- Center title -->

            
            <!-- Right side icons -->
            <div class="header-right">
                @hasSection('header-right')
                    @yield('header-right')
                @else
                                    <a href="/settings" class="header-btn" title="Settings" aria-label="Settings">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 2.27 17.9l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09c.7 0 1.3-.4 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 6.7 2.27l.06.06c.45.45 1.07.66 1.68.55.6-.11 1.06-.53 1.26-1.11L10.5 1a2 2 0 1 1 4 0l.3 1.77c.2.58.66 1 1.26 1.11.61.11 1.23-.1 1.68-.55l.06-.06A2 2 0 1 1 21.73 6.7l-.06.06c-.45.45-.66 1.07-.55 1.68.11.6.53 1.06 1.11 1.26L23 10.5a2 2 0 1 1 0 4l-1.77.3c-.58.2-1 .66-1.11 1.26-.11.61.1 1.23.55 1.68z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                @endif

                    <a href="/profile" class="header-btn" title="الملف الشخصي" aria-label="الملف الشخصي">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
            </div>

            <!-- Center title (absolutely centered to guarantee visual center) -->
            <div class="header-center" style="flex:1; text-align:center; align-self:center;">
                <!-- App name: fixed to English 'AISL' and rendered LTR to ensure it's shown in English only -->
                <h1 class="app-title"><span class="app-title-en">AISL</span></h1>
                @hasSection('page-subtitle')
                    <div class="subtitle">@yield('page-subtitle')</div>
                @endif
            </div>

            <!-- Right column: logo (placed visually on the right side) -->
            <div class="header-right" style="flex:0 0 72px; display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                <div class="header-icon">
                        <img src="/frontend/app-icon-192.png" alt="AISL logo" class="brand-logo" onerror="this.style.display='none'" />
                </div>
                @yield('header-right')
            </div>
    </header>

        <!-- Main Content -->
        <main class="mobile-content">
            @if(session('success'))
                <div class="mobile-card" style="background: #D1FAE5; border-color: #10B981; color: #065F46; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mobile-card" style="background: #FEE2E2; border-color: #EF4444; color: #991B1B; margin-bottom: 20px;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        
    </div>

    <!-- Bottom Navigation (moved outside the main container so position:fixed won't be scoped by transformed ancestors) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-container">
            <a href="/" class="bottom-nav-item {{ request()->is('/') ? 'active' : '' }}">
                <div class="bottom-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V11.5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="bottom-nav-label">الرئيسية</span>
            </a>

            <a href="/conversations" class="bottom-nav-item {{ request()->is('conversations*') ? 'active' : '' }}">
                <div class="bottom-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="bottom-nav-label">المحادثات</span>
            </a>

            <a href="/signs" class="bottom-nav-item {{ request()->is('signs*') ? 'active' : '' }}">
                <div class="bottom-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                    </svg>
                </div>
                <span class="bottom-nav-label">الإشارات</span>
            </a>

            <a href="/profile" class="bottom-nav-item {{ request()->is('profile*') ? 'active' : '' }}">
                <div class="bottom-nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 21v-1c0-2.761 4-4 8-4s8 1.239 8 4v1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="bottom-nav-label">الحساب</span>
            </a>
        </div>
    </nav>

    <!-- PWA install banner (hidden by default) -->
    <div id="installBanner" class="install-banner" style="display:none;">
        <div class="mobile-card" style="position: fixed; bottom: 90px; left: 20px; right: 20px; z-index: 1001;">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <div style="font-weight:700; font-size:15px;">أضف التطبيق</div>
                <div style="display:flex; gap:8px;">
                    <button id="installBtn" class="btn btn-primary" style="padding:8px 14px; font-size:14px;">أضف التطبيق</button>
                    <button id="installDismiss" class="btn btn-secondary" style="padding:6px 10px; font-size:14px;">×</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Register service worker and handle PWA install prompt
        (function(){
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').catch(()=>{});
            }

            let deferredPrompt = null;
            const installBanner = document.getElementById('installBanner');
            const installBtn = document.getElementById('installBtn');
            const installDismiss = document.getElementById('installDismiss');

            window.addEventListener('beforeinstallprompt', (e)=>{
                e.preventDefault();
                deferredPrompt = e;
                if (installBanner) installBanner.style.display = 'block';
            });

            if (installBtn) installBtn.addEventListener('click', async ()=>{
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                try { await deferredPrompt.userChoice; } catch(e){}
                deferredPrompt = null;
                if (installBanner) installBanner.style.display = 'none';
            });

            if (installDismiss) installDismiss.addEventListener('click', ()=>{ 
                if (installBanner) installBanner.style.display = 'none'; 
            });

            window.addEventListener('appinstalled', ()=>{ 
                if (installBanner) installBanner.style.display = 'none'; 
            });
        })();
    </script>

    <!-- Frontend client script -->
    <script src="/frontend/app.js?v={{ file_exists(public_path('frontend/app.js')) ? filemtime(public_path('frontend/app.js')) : time() }}" defer></script>
    
    @yield('scripts')
</body>
</html>
