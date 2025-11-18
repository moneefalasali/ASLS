<!doctype html>
<html lang="ar" dir="rtl">
  <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#ff2d20" />
    <link rel="manifest" href="/manifest.json" />
    <link rel="preload" href="/frontend/fonts/cairo/cairo-regular.woff2" as="font" type="font/woff2" crossorigin="anonymous" />
    <link rel="stylesheet" href="/frontend/fonts/cairo/cairo.css" />
    <link rel="stylesheet" href="/frontend/mobile-styles.css?v=<?php echo e(file_exists(public_path('frontend/mobile-styles.css')) ? filemtime(public_path('frontend/mobile-styles.css')) : time()); ?>" />
  <title><?php echo $__env->yieldContent('title','AISL'); ?></title>
  </head>
  <body class="<?php echo e(request()->query('mobile') == '1' ? 'force-mobile' : ''); ?>">
    <div class="mobile-container">
    <header class="mobile-header">
      <!-- Left: icons (settings/profile) -->
      <div class="header-left">
        <?php echo $__env->yieldContent('header-left'); ?>


        <a href="/profile" class="header-btn" title="الملف الشخصي" aria-label="الملف الشخصي">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>

      <!-- Center title fixed to English AISL -->


      <!-- Right: logo -->
      <div class="header-right">
        <div class="header-icon">
          <img src="/frontend/logo.png" alt="AISL logo" class="brand-logo" onerror="this.style.display='none'" />
        </div>
        <?php echo $__env->yieldContent('header-right'); ?>
        <!-- Compact mode toggle -->
        <button id="compactToggle" class="header-btn" title="وضع مضغوط" aria-label="وضع مضغوط" style="margin-left:8px; padding:6px 10px; font-weight:600;" type="button">
          <span id="compactLabel">وضع مضغوط</span>
        </button>
      </div>
    </header>

    <main class="mobile-content">
      <?php if(session('success')): ?>
        <div class="alert success"><?php echo e(session('success')); ?></div>
      <?php endif; ?>
      <?php echo $__env->yieldContent('content'); ?>
    </main>

    </div>

    <!-- Bottom Navigation (mobile) -->
    <nav class="bottom-nav">
      <!-- Make bottom nav background match page body color (uses page-scoped --bg-primary variable) -->
      <div class="bottom-nav-container" style="background: var(--bg-primary);">
        <a href="/" class="bottom-nav-item <?php echo e(request()->is('/') ? 'active' : ''); ?>">
          <div class="bottom-nav-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V11.5z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </div>
          <span class="bottom-nav-label">الرئيسية</span>
        </a>

        <a href="/conversations" class="bottom-nav-item <?php echo e(request()->is('conversations*') ? 'active' : ''); ?>">
          <div class="bottom-nav-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </div>
          <span class="bottom-nav-label">المحادثات</span>
        </a>

        <a href="/signs" class="bottom-nav-item <?php echo e(request()->is('signs*') ? 'active' : ''); ?>">
          <div class="bottom-nav-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </div>
          <span class="bottom-nav-label">الإشارات</span>
        </a>

        <a href="/profile" class="bottom-nav-item <?php echo e(request()->is('profile*') ? 'active' : ''); ?>">
          <div class="bottom-nav-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="currentColor" stroke-width="1.5"/>
              <path d="M4 21v-1c0-2.761 4-4 8-4s8 1.239 8 4v1" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </div>
          <span class="bottom-nav-label">الحساب</span>
        </a>
      </div>
    </nav>
    <!-- PWA install banner (hidden by default) -->
    <div id="installBanner" class="install-banner" style="display:none;">
      <div class="install-inner">
        <div style="font-weight:700; font-size:15px;">أضف التطبيق</div>
        <div class="install-actions">
          <button id="installBtn" class="btn btn-primary">أضف التطبيق</button>
          <button id="installDismiss" class="btn">×</button>
        </div>
      </div>
    </div>

    <script>
      // small helper: set HTML lang and direction
      document.documentElement.lang = 'ar';

      // register service worker and handle PWA install prompt centrally
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
          if (installBanner) installBanner.style.display = 'flex';
        });

        if (installBtn) installBtn.addEventListener('click', async ()=>{
          if (!deferredPrompt) return;
          deferredPrompt.prompt();
          try { await deferredPrompt.userChoice; } catch(e){}
          deferredPrompt = null;
          if (installBanner) installBanner.style.display = 'none';
        });

        if (installDismiss) installDismiss.addEventListener('click', ()=>{ if (installBanner) installBanner.style.display = 'none'; });

        window.addEventListener('appinstalled', ()=>{ if (installBanner) installBanner.style.display = 'none'; });
      })();
      
      // Compact UI toggle: enable via ?compact=1 or use header button to persist
      (function(){
        try {
          const params = new URLSearchParams(window.location.search);
          const shouldCompact = params.get('compact') === '1' || localStorage.getItem('compact-ui') === '1';
          if (shouldCompact) document.body.classList.add('compact-ui');

          const btn = document.getElementById('compactToggle');
          if (btn) {
            const label = document.getElementById('compactLabel');
            const updateButton = ()=>{
              if (document.body.classList.contains('compact-ui')) {
                btn.style.background = 'rgba(255,255,255,0.12)';
                if (label) label.textContent = 'مضغوط (مفعل)';
              } else {
                btn.style.background = 'rgba(255,255,255,0.06)';
                if (label) label.textContent = 'وضع مضغوط';
              }
            };
            btn.addEventListener('click', ()=>{
              const enabled = document.body.classList.toggle('compact-ui');
              localStorage.setItem('compact-ui', enabled ? '1' : '0');
              updateButton();
            });
            updateButton();
          }
        } catch(e){}
      })();
    </script>

      <script>
        // mobile nav toggle
        (function(){
          const btn = document.getElementById('navToggle');
          const links = document.getElementById('navLinks');
          if(btn && links){
            btn.addEventListener('click', ()=>{
              links.classList.toggle('show');
            });
          }
        })();
      </script>

    <!-- Frontend client script -->
  <script src="/frontend/app.js?v=<?php echo e(file_exists(public_path('frontend/app.js')) ? filemtime(public_path('frontend/app.js')) : time()); ?>" defer></script>
  </body>
</html>
<?php /**PATH C:\xampp\htdocs\a_i_s_l_project\si\resources\views/layouts/main.blade.php ENDPATH**/ ?>