<?php $__env->startSection('title', 'AISL - تحويل النص والصوت إلى لغة الإشارة'); ?>
<?php $__env->startSection('page-title', 'AISL'); ?>
<?php $__env->startSection('page-subtitle', 'تواصل بلا حدود'); ?>

<?php $__env->startSection('header-left'); ?>
    <button class="header-btn" onclick="toggleSettings()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    </button>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header-right'); ?>
    <?php if(auth()->guard()->check()): ?>
        <span class="header-user">مرحباً <?php echo e(auth()->user()->name); ?></span>
        <button class="header-btn" onclick="location.href='/profile'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M4 21v-1c0-2.761 4-4 8-4s8 1.239 8 4v1" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </button>
    <?php else: ?>
        <button class="header-btn" onclick="location.href='/login'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 3h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-4" stroke="currentColor" stroke-width="1.5"/>
                <path d="M10 17L15 12 10 7" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </button>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Text/Audio Conversion Card -->
    <div class="mobile-card large">
        <div class="tab-navigation">
            <button class="tab-btn active" onclick="switchTab('text')">نص إلى إشارة</button>
            <button class="tab-btn" onclick="switchTab('audio')">إشارة إلى نص</button>
        </div>
        <div id="textTab" class="tab-content">
            <div class="form-group">
                <label class="form-label">إدخال النص أو الصوت</label>
                <textarea id="textInput" class="form-input form-textarea" placeholder="اكتب النص المراد تحويله إلى لغة الإشارة..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">عرض لغة الإشارة</label>
                <div class="sign-display">
                    <div class="sign-container" id="signContainerText">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: var(--text-light);">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="sign-subtitle">سيتم عرض حركات لغة الإشارة هنا</div>
                </div>
            </div>
            <div class="form-group" style="display: flex; gap: 10px;">
                <button id="recordBtn" class="btn btn-secondary" style="flex: 1;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="12" y1="19" x2="12" y2="23" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="8" y1="23" x2="16" y2="23" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    تسجيل صوتي
                </button>
                <button id="playBtn" class="btn btn-secondary" disabled>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="5,3 19,12 5,21" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <button id="convertTextBtn" class="btn btn-primary btn-full btn-large">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                </svg>
                تحويل إلى لغة الإشارة
            </button>
        </div>

        <!-- Audio to Text Tab -->
        <div id="audioTab" class="tab-content" style="display: none;">
            <div class="form-group">
                <label class="form-label">عرض لغة الإشارة</label>
                <div class="sign-display" style="min-height: 150px;">
                    <div class="sign-container" id="signContainer">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: var(--text-light);">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="sign-subtitle">سيتم عرض حركات لغة الإشارة هنا</div>
                </div>
            </div>

            <div class="control-buttons">
                <button id="playSignBtn" class="control-btn primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="5,3 19,12 5,21" fill="currentColor"/>
                    </svg>
                </button>
                <button id="pauseSignBtn" class="control-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="4" width="4" height="16" fill="currentColor"/>
                        <rect x="14" y="4" width="4" height="16" fill="currentColor"/>
                    </svg>
                </button>
                <button id="replayBtn" class="control-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 4v6h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div style="margin-top: 16px;">
                <button id="downloadBtn" class="btn btn-secondary btn-full">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="1.5"/>
                        <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    تحميل الحركات
                </button>
                <button id="shareBtn" class="btn btn-secondary btn-full" style="margin-top: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" stroke="currentColor" stroke-width="1.5"/>
                        <polyline points="16,6 12,2 8,6" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="12" y1="2" x2="12" y2="15" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    مشاركة
                </button>
            </div>
        </div>
    </div>

    <!-- AI Assistant Card -->
    <div class="mobile-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">الذكاء الاصطناعي متصل</div>
                <div style="font-size: 14px; opacity: 0.9;">جاهز للترجمة الفورية</div>
            </div>
            <div style="width: 12px; height: 12px; background: #10B981; border-radius: 50%; animation: pulse 2s infinite;"></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="/signs" class="quick-action">
            <div class="quick-action-icon blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                </svg>
            </div>
            <div class="quick-action-title">ترجمة سريعة</div>
        </a>

        <a href="/conversations" class="quick-action">
            <div class="quick-action-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
            </div>
            <div class="quick-action-title">رسالة صوتية</div>
        </a>

        <a href="/assistant" class="quick-action">
            <div class="quick-action-icon orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
            </div>
            <div class="quick-action-title">مساعد ذكي</div>
        </a>

        <a href="/groups" class="quick-action">
            <div class="quick-action-icon purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="quick-action-title">مجموعة جديدة</div>
        </a>
    </div>

    <!-- Training Section -->
    <div class="mobile-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="text-align: center; padding: 20px 0;">
            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                </svg>
            </div>
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">تدرب الآن</h3>
            <p style="font-size: 14px; opacity: 0.9; margin-bottom: 20px;">مارس الإشارات التي تعلمتها</p>
            <button class="btn" style="background: white; color: var(--primary-blue); font-weight: 600; padding: 12px 24px;">
                بدء التدريب
            </button>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Tab switching functionality
    function switchTab(tab) {
        const textTab = document.getElementById('textTab');
        const audioTab = document.getElementById('audioTab');
        const tabBtns = document.querySelectorAll('.tab-btn');
        
        tabBtns.forEach(btn => btn.classList.remove('active'));
        
        if (tab === 'text') {
            textTab.style.display = 'block';
            audioTab.style.display = 'none';
            tabBtns[0].classList.add('active');
        } else {
            textTab.style.display = 'none';
            audioTab.style.display = 'block';
            tabBtns[1].classList.add('active');
        }
    }

    // Text to Sign conversion
    document.getElementById('convertTextBtn').addEventListener('click', async () => {
        const text = document.getElementById('textInput').value.trim();
        if (!text) {
            alert('رجاءً أدخل نصًا للتحويل');
            return;
        }

        try {
            const response = await fetch('/api/convert-text', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ text, language: 'ar' })
            });

            const data = await response.json();
            displaySignSequence(data.sequence || []);
            
            // Switch to audio tab to show results
            switchTab('audio');
        } catch (error) {
            console.error('Error converting text:', error);
            alert('حدث خطأ أثناء التحويل');
        }
    });

    // Display sign sequence
    function displaySignSequence(sequence) {
        const container = document.getElementById('signContainer');
        container.innerHTML = '';

        if (sequence.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; color: var(--text-secondary);">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
                    </svg>
                    <div style="margin-top: 8px;">لا توجد إشارات للعرض</div>
                </div>
            `;
            return;
        }

        sequence.forEach((sign, index) => {
            const img = document.createElement('img');
            img.src = sign.src || '/storage/signs/placeholder.png';
            img.alt = sign.text || '';
            img.className = 'sign-image';
            img.style.opacity = index === 0 ? '1' : '0.5';
            container.appendChild(img);
        });
    }

    // Recording functionality (placeholder)
    let isRecording = false;
    document.getElementById('recordBtn').addEventListener('click', () => {
        const btn = document.getElementById('recordBtn');
        const playBtn = document.getElementById('playBtn');
        
        if (!isRecording) {
            isRecording = true;
            btn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="6" width="12" height="12" fill="currentColor"/>
                </svg>
                إيقاف التسجيل
            `;
            btn.style.background = 'var(--secondary-red)';
            btn.style.color = 'white';
            
            // Simulate recording
            setTimeout(() => {
                isRecording = false;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="12" y1="19" x2="12" y2="23" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="8" y1="23" x2="16" y2="23" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    تسجيل صوتي
                `;
                btn.style.background = '';
                btn.style.color = '';
                playBtn.disabled = false;
                
                // Add recorded text to textarea
                document.getElementById('textInput').value = 'مرحبا، كيف حالك؟';
            }, 3000);
        }
    });

    // Settings toggle (placeholder)
    function toggleSettings() {
        alert('إعدادات التطبيق - قريباً');
    }

    // Add pulse animation for AI status
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\a_i_s_l_project\si\resources\views/home.blade.php ENDPATH**/ ?>