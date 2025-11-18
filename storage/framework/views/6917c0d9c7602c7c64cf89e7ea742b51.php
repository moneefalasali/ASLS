<?php $__env->startSection('title', 'الملف الشخصي - AISL'); ?>
<?php $__env->startSection('page-title', 'الملف الشخصي'); ?>
<?php $__env->startSection('page-subtitle', 'إعداداتك وإنجازاتك'); ?>

<?php $__env->startSection('header-left'); ?>
    <button class="header-btn" onclick="editProfile()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="1.5"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    </button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header-right'); ?>
    <button class="header-btn" onclick="showSettings()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    </button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
        <style>
        /* Force stacked/mobile layout for profile page and show bottom nav */
        @media (min-width: 900px) {
            .mobile-content { display: block !important; padding: 20px !important; }
            .mobile-card { max-width: 720px; margin: 12px auto; }
            .bottom-nav { display: block !important; }
        }
        @media (max-width: 899px) {
            .bottom-nav { display: block !important; }
        }
        </style>
    <!-- Profile Header -->
    <div class="mobile-card large" style="text-align: center;">
        <div style="width: 120px; height: 120px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 48px;">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                <path d="M4 21v-1c0-2.761 4-4 8-4s8 1.239 8 4v1" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
        
        <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 4px; color: var(--text-primary);">
            <?php if(auth()->guard()->check()): ?>
                <?php echo e(auth()->user()->name ?? 'أحمد محمد'); ?>

            <?php else: ?>
                أحمد محمد
            <?php endif; ?>
        </h2>
        <p style="font-size: 16px; color: var(--text-secondary); margin-bottom: 8px;">متعلم لغة الإشارة</p>
        <p style="font-size: 14px; color: var(--text-light); margin-bottom: 20px;">المستوى المتوسط</p>
        
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button class="btn btn-secondary" onclick="shareProfile()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                مشاركة
            </button>
            <button class="btn btn-primary" onclick="editProfile()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                تعديل الملف
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="mobile-card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">إحصائياتي</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: var(--secondary-green); margin-bottom: 4px;">156</div>
                <div style="font-size: 14px; color: var(--text-secondary);">النصوص المترجمة</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: var(--primary-blue); margin-bottom: 4px;">42</div>
                <div style="font-size: 14px; color: var(--text-secondary);">الإشارات المتعلمة</div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: var(--secondary-orange); margin-bottom: 4px;">1,240</div>
                <div style="font-size: 14px; color: var(--text-secondary);">نقاط الإنجاز</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: var(--secondary-purple); margin-bottom: 4px;">28</div>
                <div style="font-size: 14px; color: var(--text-secondary);">أيام التعلم</div>
            </div>
        </div>
    </div>

    <!-- Achievements -->
    <div class="mobile-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary);">الإنجازات</h3>
            <div style="color: var(--secondary-orange); font-size: 14px; display: flex; align-items: center; gap: 4px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
                شارة
            </div>
        </div>

        <!-- Achievement 1 -->
        <div class="achievement-card">
            <div class="achievement-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
            </div>
            <div class="achievement-content">
                <div class="achievement-title">متعلم نشط</div>
                <div class="achievement-desc">تعلمت 50 إشارة</div>
            </div>
        </div>

        <!-- Achievement 2 -->
        <div class="achievement-card">
            <div class="achievement-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
            </div>
            <div class="achievement-content">
                <div class="achievement-title">مترجم محترف</div>
                <div class="achievement-desc">ترجمت 100 نص</div>
            </div>
        </div>

        <!-- Progress Goals -->
        <div style="margin-top: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 14px; color: var(--text-secondary);">هدف يومي</span>
                <span style="font-size: 14px; color: var(--text-secondary);">7 أيام متتالية</span>
            </div>
            <div style="width: 100%; height: 8px; background: var(--bg-secondary); border-radius: 4px; overflow: hidden;">
                <div style="width: 70%; height: 100%; background: var(--secondary-green);"></div>
            </div>
        </div>

        <div style="margin-top: 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 14px; color: var(--text-secondary);">تقدم سريع</span>
                <span style="font-size: 14px; color: var(--text-secondary);">تحسن بنسبة 80%</span>
            </div>
            <div style="width: 100%; height: 8px; background: var(--bg-secondary); border-radius: 4px; overflow: hidden;">
                <div style="width: 80%; height: 100%; background: var(--primary-blue);"></div>
            </div>
        </div>
    </div>

    <!-- Settings -->
    <div class="mobile-card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">الإعدادات</h3>
        
        <!-- Dark Mode -->
        <div class="setting-item">
            <div>
                <div class="setting-label">الوضع الليلي</div>
                <div class="setting-desc">تفعيل المظهر الداكن</div>
            </div>
            <div class="toggle-switch" onclick="toggleDarkMode(this)">
            </div>
        </div>

        <!-- Notifications -->
        <div class="setting-item">
            <div>
                <div class="setting-label">الإشعارات</div>
                <div class="setting-desc">تلقي تنبيهات التطبيق</div>
            </div>
            <div class="toggle-switch active" onclick="toggleNotifications(this)">
            </div>
        </div>

        <!-- Sounds -->
        <div class="setting-item">
            <div>
                <div class="setting-label">الأصوات</div>
                <div class="setting-desc">تشغيل أصوات التطبيق</div>
            </div>
            <div class="toggle-switch active" onclick="toggleSounds(this)">
            </div>
        </div>
    </div>

    <!-- Additional Settings -->
    <div class="mobile-card">
        <!-- Language -->
        <div class="setting-item" onclick="changeLanguage()">
            <div>
                <div class="setting-label">اللغة</div>
                <div class="setting-desc">العربية</div>
            </div>
            <div style="color: var(--text-light);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polyline points="9,18 15,12 9,6" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
        </div>

        <!-- Sign Preferences -->
        <div class="setting-item" onclick="signPreferences()">
            <div>
                <div class="setting-label">تفضيلات الإشارات</div>
                <div class="setting-desc">تخصيص عرض الإشارات</div>
            </div>
            <div style="color: var(--text-light);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <polyline points="9,18 15,12 9,6" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
        </div>

        <!-- Data Export -->
        <div class="setting-item" onclick="exportData()">
            <div>
                <div class="setting-label">تحميل البيانات</div>
                <div class="setting-desc">نسخ احتياطي من بياناتك</div>
            </div>
            <div style="color: var(--text-light);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="1.5"/>
                    <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
        </div>

        <!-- Privacy -->
        <div class="setting-item" onclick="privacySettings()">
            <div>
                <div class="setting-label">الخصوصية والأمان</div>
                <div class="setting-desc">إدارة بياناتك الشخصية</div>
            </div>
            <div style="color: var(--text-light);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
        </div>

        <!-- Help & Support -->
        <div class="setting-item" onclick="helpSupport()">
            <div>
                <div class="setting-label">المساعدة والدعم</div>
                <div class="setting-desc">الأسئلة الشائعة والدعم</div>
            </div>
            <div style="color: var(--text-light);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M12 17h.01" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="mobile-card" style="text-align: center;">
        <?php if(auth()->guard()->check()): ?>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="width:100%;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn" style="background: var(--secondary-red); color: white; width: 100%; padding: 16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="1.5"/>
                        <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    تسجيل الخروج
                </button>
            </form>
        <?php else: ?>
            <button class="btn btn-primary" style="width: 100%; padding: 16px;" onclick="login()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 3h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-4" stroke="currentColor" stroke-width="1.5"/>
                    <polyline points="10,17 15,12 10,7" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="15" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                تسجيل الدخول
            </button>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Edit profile - open a simple prompt to edit display name or navigate to edit page if available
    function editProfile() {
        const current = document.querySelector('.mobile-card.large h2')?.textContent?.trim() || '';
        const name = prompt('اسم العرض:', current);
        if (name) {
            // Update UI immediately; real app should POST to server
            const h2 = document.querySelector('.mobile-card.large h2');
            if (h2) h2.textContent = name;
            localStorage.setItem('profile_name', name);
            showToast('تم تحديث الاسم محلياً (تجريبي)');
        }
    }

    // Share profile
    function shareProfile() {
        if (navigator.share) {
            navigator.share({
                title: 'ملفي الشخصي في AISL',
                text: 'تعلم لغة الإشارة معي في تطبيق AISL',
                url: window.location.href
            });
        } else {
            alert('مشاركة الملف الشخصي - قريباً');
        }
    }

    // Show settings
    function showSettings() {
        // Show a small modal-like info
        showToast('قائمة الإعدادات المفتوحة (تجريبي)');
    }

    // Toggle functions
    function toggleDarkMode(element) {
        element.classList.toggle('active');
        const enabled = element.classList.contains('active');
        if (enabled) document.documentElement.classList.add('theme-dark'); else document.documentElement.classList.remove('theme-dark');
        localStorage.setItem('ui_dark_mode', enabled ? '1' : '0');
        showToast(enabled ? 'تم تفعيل الوضع الداكن' : 'تم إيقاف الوضع الداكن');
    }

    function toggleNotifications(element) {
        element.classList.toggle('active');
        const isActive = element.classList.contains('active');
        localStorage.setItem('pref_notifications', isActive ? '1' : '0');
        showToast(isActive ? 'تم تفعيل الإشعارات' : 'تم إيقاف الإشعارات');
    }

    function toggleSounds(element) {
        element.classList.toggle('active');
        const isActive = element.classList.contains('active');
        localStorage.setItem('pref_sounds', isActive ? '1' : '0');
        showToast(isActive ? 'تم تفعيل الأصوات' : 'تم إيقاف الأصوات');
    }

    // Settings functions
    function changeLanguage() {
        const lang = prompt('اختر اللغة (ar/en):', 'ar');
        if (lang) { localStorage.setItem('ui_language', lang); showToast('تم تعيين اللغة: ' + lang); }
    }

    function signPreferences() {
        showToast('تفضيلات الإشارات (تجريبي)');
    }

    function exportData() {
        showToast('تصدير البيانات جارٍ... (تجريبي)');
        setTimeout(()=>{ showToast('تم إنشاء نسخة احتياطية (محلي)'); }, 1200);
    }

    function privacySettings() {
        showToast('الخصوصية - إعدادات تجريبية');
    }

    function helpSupport() {
        window.location.href = '/help';
    }

    // Auth functions
    // حذف دالة تسجيل الخروج القديمة - لم تعد مطلوبة

    function login() {
        window.location.href = '/login';
    }

    // small helpers used on this page
    function showToast(msg, timeout=1600){ let t=document.getElementById('__toast'); if(!t){ t=document.createElement('div'); t.id='__toast'; t.style='position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.8);color:#fff;padding:8px 14px;border-radius:8px;z-index:2000;'; document.body.appendChild(t);} t.textContent=msg; t.style.opacity='1'; clearTimeout(t.__to); t.__to=setTimeout(()=>t.style.opacity='0',timeout); }

    // init UI from localStorage
    (function(){
        try{
            const name = localStorage.getItem('profile_name'); if(name){ const h2 = document.querySelector('.mobile-card.large h2'); if(h2) h2.textContent = name; }
            const dark = localStorage.getItem('ui_dark_mode') === '1';
            if(dark){ document.documentElement.classList.add('theme-dark'); }
            const switches = document.querySelectorAll('.toggle-switch');
            switches.forEach(s=>{
                const label = s.parentElement.querySelector('.setting-label')?.textContent?.trim()?.toLowerCase()||'';
                if(label.includes('الوضع')){ if(dark) s.classList.add('active'); }
                if(label.includes('الاشعارات') || label.includes('الإشعارات')){ if(localStorage.getItem('pref_notifications')==='1') s.classList.add('active'); }
                if(label.includes('الأصوات') || label.includes('الاصوات')){ if(localStorage.getItem('pref_sounds')==='1') s.classList.add('active'); }
            });
        }catch(e){}
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\a_i_s_l_project\si\resources\views/profile.blade.php ENDPATH**/ ?>