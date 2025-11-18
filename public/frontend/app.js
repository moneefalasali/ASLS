// Prevent double-loading when script is included more than once
// Add a small fetch wrapper so all same-origin fetch/POST requests include CSRF token and credentials.
;(function(){
    try {
        const _origFetch = window.fetch.bind(window);
        window.fetch = function(input, init){
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const url = (typeof input === 'string') ? input : input.url;
                const sameOrigin = url && (new URL(url, location.href)).origin === location.origin;
                if (sameOrigin) {
                    init = init || {};
                    init.headers = init.headers || {};
                    if (token && !init.headers['X-CSRF-TOKEN'] && !init.headers['x-csrf-token']) {
                        init.headers['X-CSRF-TOKEN'] = token;
                    }
                    // Ensure cookies/session are sent with fetch
                    if (!init.credentials) init.credentials = 'same-origin';
                }
            } catch(e) { /* ignore silently */ }
            return _origFetch(input, init);
        };
    } catch(e) {}
})();

if (window.__AISL_APP_LOADED) {
    console.debug('AISL: script already loaded — skipping second execution');
} else {
    window.__AISL_APP_LOADED = true;

    // AISL Frontend Application - Enhanced Version
    class AISLApp {
    constructor() {
        this.isOnline = navigator.onLine;
        this.pendingTranslations = [];
        this.currentUser = null;
        this.settings = this.loadSettings();
        // Force direction preference: 'auto' | 'ltr' | 'rtl'
        this.forceDir = this.loadForceDirPreference();
        // PWA install prompt deferred event (for showing install UI)
        this.deferredPrompt = null;
        this.installBtn = null;
        
        // Legacy state for compatibility
        this.state = {
            currentLang: 'ar',
            signSequence: [],
            currentIndex: 0,
            isPlaying: false,
            speed: 1,
            playInterval: null
        };
        
        this.init();
    }

    async init() {
        console.log('🚀 AISL App initializing...');
        
        // Initialize PWA features
        this.initPWA();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Ensure logout links/forms always submit correctly with CSRF
        this.setupLogoutHijack();
        
        // Initialize offline support
        this.initOfflineSupport();
        
        // Load user preferences
        this.loadUserPreferences();
        
        // Initialize speech recognition if available
        this.initSpeechRecognition();
        
        // Setup performance monitoring
        this.initPerformanceMonitoring();
        
        // Initialize legacy functionality
        this.initLegacyFeatures();
        
        console.log('✅ AISL App initialized successfully');
    }

    // Ensure any anchor or link that points to /logout performs a proper POST with CSRF
    setupLogoutHijack() {
        document.addEventListener('click', (e) => {
            const el = e.target.closest && e.target.closest('a[href]');
            if (!el) return;
            const href = el.getAttribute('href');
            if (!href) return;
            // handle absolute or relative logout path
            if (href === '/logout' || href.endsWith('/logout')) {
                e.preventDefault();
                // Try to find an existing logout form nearby
                const form = document.querySelector('form[action="' + href + '"]');
                if (form) {
                    // submit the existing form (it should include @csrf)
                    form.submit();
                    return;
                }

                // Otherwise create a temporary form with CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const tempForm = document.createElement('form');
                tempForm.method = 'POST';
                tempForm.action = href;
                tempForm.style.display = 'none';
                // CSRF input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = token;
                tempForm.appendChild(input);
                document.body.appendChild(tempForm);
                tempForm.submit();
            }
        }, true);
    }

    // Legacy compatibility initialization
    initLegacyFeatures() {
        // Setup legacy event listeners if elements exist
        this.setupLegacyEventListeners();
        this.updateLanguage();
    }

    // PWA Initialization
    initPWA() {
        // Register service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('✅ Service Worker registered:', registration);
                    
                    // Check for updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                this.showUpdateNotification();
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('❌ Service Worker registration failed:', error);
                });
        }

        // Handle install prompt
        this.setupInstallPrompt();
        
        // Setup push notifications
        this.initPushNotifications();
    }

    setupInstallPrompt() {
        // Listen for the browser install prompt and keep the event
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            // ensure persistent install button exists and update UI
            this.renderInstallButton();
            this.updateInstallButtonState();

            // Show custom install banner if present
            const installBanner = document.getElementById('installBanner');
            if (installBanner) {
                installBanner.style.display = 'block';
            }
        });

        // Render install button always (it will reflect installed/available state)
        this.renderInstallButton();

        // Handle app installed
        window.addEventListener('appinstalled', () => {
            console.log('✅ AISL App installed successfully');
            this.deferredPrompt = null;
            try { localStorage.setItem('aisl-pwa-installed', '1'); } catch(e){}
            this.updateInstallButtonState();

            const installBanner = document.getElementById('installBanner');
            if (installBanner) {
                installBanner.style.display = 'none';
            }
        });
    }

    async initPushNotifications() {
        if (!('Notification' in window) || !('serviceWorker' in navigator)) {
            console.log('Push notifications not supported');
            return;
        }

        try {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                console.log('✅ Notification permission granted');
            }
        } catch (error) {
            console.error('Push notification setup failed:', error);
        }
    }

    // Event Listeners
    setupEventListeners() {
        // Online/Offline status
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.showConnectionStatus('متصل', 'success');
            this.syncPendingData();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.showConnectionStatus('غير متصل', 'warning');
        });

        // Page visibility for performance optimization
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseNonEssentialTasks();
            } else {
                this.resumeNonEssentialTasks();
            }
        });

        // Handle back button for SPA navigation
        window.addEventListener('popstate', (event) => {
            this.handleNavigation(event.state);
        });

        // Touch gestures for mobile
        this.setupTouchGestures();
    }

    setupLegacyEventListeners() {
        // Legacy DOM elements
        const elements = {
            textInput: document.getElementById('textInput'),
            audioInput: document.getElementById('audioInput'),
            audioFileName: document.getElementById('audioFileName'),
            convertTextBtn: document.getElementById('convertTextBtn'),
            convertAudioBtn: document.getElementById('convertAudioBtn'),
            signContainer: document.getElementById('signContainer'),
            subtitles: document.getElementById('subtitles'),
            playBtn: document.getElementById('playBtn'),
            pauseBtn: document.getElementById('pauseBtn'),
            speedSlider: document.getElementById('speedSlider'),
            speedValue: document.getElementById('speedValue'),
            progress: document.getElementById('progress'),
            loadingOverlay: document.getElementById('loadingOverlay'),
            langBtn: document.getElementById('langBtn'),
            tabBtns: document.querySelectorAll('.tab-btn'),
            textTab: document.getElementById('textTab'),
            audioTab: document.getElementById('audioTab'),
            recordBtn: document.getElementById('recordBtn'),
            playSignBtn: document.getElementById('playSignBtn'),
            pauseSignBtn: document.getElementById('pauseSignBtn'),
            replayBtn: document.getElementById('replayBtn')
        };

    // allow either 'signContainer' or legacy 'signList' element ids
    elements.signContainer = document.getElementById('signContainer') || document.getElementById('signList');
    this.elements = elements;

        // Tab switching
        if (elements.tabBtns) {
            elements.tabBtns.forEach(btn => {
                btn.addEventListener('click', () => this.switchTab(btn.dataset.tab));
            });
        }

        // Convert buttons
        if (elements.convertTextBtn) {
            elements.convertTextBtn.addEventListener('click', () => this.convertText());
        }
        
        if (elements.convertAudioBtn) {
            elements.convertAudioBtn.addEventListener('click', () => this.convertAudio());
        }

        // Audio file input
        if (elements.audioInput) {
            elements.audioInput.addEventListener('change', (e) => this.handleAudioFileSelect(e));
        }

        // Playback controls
        if (elements.playBtn) {
            elements.playBtn.addEventListener('click', () => this.playSequence());
        }
        
        if (elements.pauseBtn) {
            elements.pauseBtn.addEventListener('click', () => this.pauseSequence());
        }

        if (elements.playSignBtn) {
            elements.playSignBtn.addEventListener('click', () => this.playSequence());
        }
        
        if (elements.pauseSignBtn) {
            elements.pauseSignBtn.addEventListener('click', () => this.pauseSequence());
        }

        if (elements.replayBtn) {
            elements.replayBtn.addEventListener('click', () => this.replaySequence());
        }

        // Speed control
        if (elements.speedSlider) {
            elements.speedSlider.addEventListener('input', (e) => {
                this.state.speed = parseFloat(e.target.value);
                if (elements.speedValue) {
                    elements.speedValue.textContent = `${this.state.speed}x`;
                }
                if (this.state.isPlaying) {
                    this.pauseSequence();
                    this.playSequence();
                }
            });
        }

        // Language switcher
        if (elements.langBtn) {
            elements.langBtn.addEventListener('click', () => this.toggleLanguage());
        }

        // Direction mode toggle button: Auto / LTR / RTL
        if (elements.langBtn) {
            // Create a small toggle next to langBtn
            const dirToggle = document.createElement('button');
            dirToggle.id = 'dirToggleBtn';
            dirToggle.type = 'button';
            dirToggle.className = 'dir-toggle';
            dirToggle.title = 'وضع اتجاه النص (Auto / LTR / RTL)';
            dirToggle.addEventListener('click', () => this.toggleForceDirectionMode());
            elements.langBtn.parentNode && elements.langBtn.parentNode.insertBefore(dirToggle, elements.langBtn.nextSibling);
            // keep reference
            this.dirToggleBtn = dirToggle;
            this.updateForceToggleUI();
        }

        // Recording button
        if (elements.recordBtn) {
            elements.recordBtn.addEventListener('click', () => this.toggleRecording());
        }
    }

    setupTouchGestures() {
        let startX, startY, startTime;
        
        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            startTime = Date.now();
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (!startX || !startY) return;
            
            const endX = e.changedTouches[0].clientX;
            const endY = e.changedTouches[0].clientY;
            const endTime = Date.now();
            
            const deltaX = endX - startX;
            const deltaY = endY - startY;
            const deltaTime = endTime - startTime;
            
            // Swipe detection
            if (Math.abs(deltaX) > 50 && Math.abs(deltaY) < 100 && deltaTime < 300) {
                if (deltaX > 0) {
                    this.handleSwipeRight();
                } else {
                    this.handleSwipeLeft();
                }
            }
            
            startX = startY = null;
        }, { passive: true });
    }

    // Speech Recognition
    initSpeechRecognition() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            console.log('Speech recognition not supported');
            return;
        }

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        this.recognition = new SpeechRecognition();
        
        this.recognition.continuous = false;
        this.recognition.interimResults = true;
        this.recognition.lang = 'ar-SA';

        this.recognition.onstart = () => {
            console.log('🎤 Speech recognition started');
            this.updateRecordingUI(true);
        };

        this.recognition.onresult = (event) => {
            let finalTranscript = '';
            let interimTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    interimTranscript += transcript;
                }
            }

            this.handleSpeechResult(finalTranscript, interimTranscript);
        };

        this.recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            this.updateRecordingUI(false);
            this.showError('حدث خطأ في التعرف على الصوت');
        };

        this.recognition.onend = () => {
            console.log('🎤 Speech recognition ended');
            this.updateRecordingUI(false);
        };
    }

    toggleRecording() {
        if (!this.recognition) {
            this.showError('التعرف على الصوت غير مدعوم في هذا المتصفح');
            return;
        }

        if (this.isRecording) {
            this.stopSpeechRecognition();
        } else {
            this.startSpeechRecognition();
        }
    }

    startSpeechRecognition() {
        if (!this.recognition) {
            this.showError('التعرف على الصوت غير مدعوم في هذا المتصفح');
            return;
        }

        try {
            this.isRecording = true;
            this.recognition.start();
        } catch (error) {
            console.error('Failed to start speech recognition:', error);
            this.showError('فشل في بدء التعرف على الصوت');
            this.isRecording = false;
        }
    }

    stopSpeechRecognition() {
        if (this.recognition) {
            this.isRecording = false;
            this.recognition.stop();
        }
    }

    // Legacy Methods (Enhanced)
    switchTab(tab) {
        if (!this.elements.tabBtns) return;
        
        this.elements.tabBtns.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });

        if (tab === 'text') {
            if (this.elements.textTab) this.elements.textTab.classList.add('active');
            if (this.elements.audioTab) this.elements.audioTab.classList.remove('active');
        } else {
            if (this.elements.textTab) this.elements.textTab.classList.remove('active');
            if (this.elements.audioTab) this.elements.audioTab.classList.add('active');
        }
    }

    handleAudioFileSelect(e) {
        const file = e.target.files[0];
        if (file && this.elements.audioFileName) {
            this.elements.audioFileName.textContent = file.name;
            if (this.elements.convertAudioBtn) {
                this.elements.convertAudioBtn.disabled = false;
            }
        }
    }

    // Text to Sign Translation (Enhanced)
    async translateText(text, options = {}) {
        if (!text) {
            text = this.elements.textInput ? this.elements.textInput.value.trim() : '';
        }
        
        if (!text) {
            this.showError('رجاءً أدخل نصًا للترجمة');
            return null;
        }

        // Auto-detect language from text if not explicitly provided
        const detectedLang = options.language || this.detectLanguageFromText(text) || this.state.currentLang;
        // Update currentLang so UI labels/direction can reflect choice
        this.state.currentLang = detectedLang;

        const translationData = {
            text: text.trim(),
            language: detectedLang,
            speed: options.speed || 'normal',
            style: options.style || 'standard'
        };

        try {
            // Show loading state
            this.showLoadingState('جاري الترجمة...');

            let response;
            if (this.isOnline) {
                response = await fetch('/api/convert-text', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCSRFToken()
                    },
                    body: JSON.stringify(translationData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                this.state.signSequence = result.sequence || [];
                this.displaySignSequence();
                this.hideLoadingState();
                return result;
            } else {
                // Offline mode - store for later sync
                const offlineTranslation = {
                    id: Date.now(),
                    data: translationData,
                    timestamp: new Date().toISOString()
                };
                
                this.pendingTranslations.push(offlineTranslation);
                this.saveToLocalStorage('pendingTranslations', this.pendingTranslations);
                
                this.hideLoadingState();
                this.showError('غير متصل - سيتم إرسال الترجمة عند الاتصال');
                
                // Return mock result for offline demo
                const mockResult = this.getMockTranslation(text, detectedLang);
                this.state.signSequence = mockResult.sequence || [];
                this.displaySignSequence();
                return mockResult;
            }
        } catch (error) {
            console.error('Translation failed:', error);
            this.hideLoadingState();
            this.showError('فشل في ترجمة النص');
            return null;
        }
    }

    async convertText() {
        return await this.translateText();
    }

    async convertAudio() {
        const file = this.elements.audioInput ? this.elements.audioInput.files[0] : null;
        
        if (!file) {
            this.showError('الرجاء اختيار ملف صوتي');
            return;
        }

        this.showLoadingState('جاري تحويل الصوت...');

        try {
            const formData = new FormData();
            formData.append('audio', file);

            const response = await fetch('/api/convert-audio', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error('Failed to convert audio');
            }

            const data = await response.json();
            this.state.signSequence = data.sequence || [];
            this.displaySignSequence();
        } catch (error) {
            console.error('Error converting audio:', error);
            this.showError('حدث خطأ أثناء تحويل الصوت');
        } finally {
            this.hideLoadingState();
        }
    }

    getMockTranslation(text) {
        // Simple mock translation for offline demo
        const words = text.split(' ').slice(0, 5); // Limit to 5 words
        const sequence = words.map((word, index) => ({
            text: word,
            src: `/storage/signs/placeholder.png`,
            duration: 1000,
            order: index,
            language: this.state.currentLang
        }));

        return {
            success: true,
            sequence: sequence,
            originalText: text,
            offline: true
        };
    }

    // Simple client-side language detection: returns 'ar' or 'en'
    detectLanguageFromText(text) {
        if (!text) return null;
        // If contains Arabic characters, consider Arabic
        // Use a Unicode range instead of \p{Arabic} for broader JS compatibility
        const arabicRegex = /[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF]/;
        if (arabicRegex.test(text)) return 'ar';
        // If contains any Latin letters, consider English
        if (/[A-Za-z]/.test(text)) return 'en';
        return null;
    }

    // Display Sign Sequence (Enhanced)
    displaySignSequence() {
        if (this.state.signSequence.length === 0) {
            this.showError('لا توجد إشارات للعرض');
            return;
        }

        this.state.currentIndex = 0;
        this.showSign(this.state.currentIndex);
        
        // Enable playback controls
        if (this.elements.playBtn) this.elements.playBtn.disabled = false;
        if (this.elements.pauseBtn) this.elements.pauseBtn.disabled = false;
        if (this.elements.playSignBtn) this.elements.playSignBtn.disabled = false;
        if (this.elements.pauseSignBtn) this.elements.pauseSignBtn.disabled = false;
        if (this.elements.replayBtn) this.elements.replayBtn.disabled = false;

        // Update sign container for new mobile layout
        // Prefer sign container inside the currently visible tab (if any),
        // otherwise fall back to global ids used elsewhere.
        let signContainer = document.querySelector('.tab-content:not([style*="display: none"]) .sign-container');
        if (!signContainer) {
            signContainer = document.getElementById('signContainer') || document.getElementById('signList');
        }
        // Determine language from returned sequence (fallback to app state)
        let seqLang = this.state.signSequence[0] && this.state.signSequence[0].language ? this.state.signSequence[0].language : this.state.currentLang;
        seqLang = seqLang === 'en' ? 'en' : 'ar';

        // Respect forced direction preference if set (auto|ltr|rtl)
        let effectiveDir = 'auto';
        if (this.forceDir && (this.forceDir === 'ltr' || this.forceDir === 'rtl')) {
            effectiveDir = this.forceDir;
        } else {
            effectiveDir = seqLang === 'en' ? 'ltr' : 'rtl';
        }

        // Set text input direction based on effective direction
        if (this.elements && this.elements.textInput) {
            this.elements.textInput.dir = effectiveDir === 'ltr' ? 'ltr' : 'rtl';
            this.elements.textInput.style.textAlign = effectiveDir === 'ltr' ? 'left' : 'right';
        }

        if (signContainer) {
            // Apply container direction class so images render LTR or RTL
            signContainer.classList.remove('dir-ltr', 'dir-rtl');
            signContainer.classList.add(effectiveDir === 'ltr' ? 'dir-ltr' : 'dir-rtl');

            signContainer.innerHTML = '';
            this.state.signSequence.forEach((sign, index) => {
                const img = document.createElement('img');
                // Encode URI to ensure non-ASCII filenames (Arabic) are requested correctly
                try {
                    img.src = sign.src ? encodeURI(sign.src) : '/storage/signs/placeholder.png';
                } catch (e) {
                    img.src = sign.src || '/storage/signs/placeholder.png';
                }
                img.onerror = function(){ this.src = '/storage/signs/placeholder.png'; };
                img.alt = sign.text || '';
                img.className = 'sign-image';
                img.style.opacity = index === 0 ? '1' : '0.5';
                if (index === 0) img.classList.add('active');
                signContainer.appendChild(img);
            });
        }
    }

    showSign(index) {
        if (index < 0 || index >= this.state.signSequence.length) {
            return;
        }

        const sign = this.state.signSequence[index];
        
        // Update legacy sign container
        if (this.elements.signContainer) {
            const safeSrc = sign.src ? encodeURI(sign.src) : '/storage/signs/placeholder.png';
            this.elements.signContainer.innerHTML = `
                <img src="${safeSrc}" onerror="this.src='/storage/signs/placeholder.png'" alt="${sign.text}" class="sign-image" />
            `;
        }

        // Update mobile sign container
    const signContainer = document.getElementById('signContainer') || document.getElementById('signList');
        if (signContainer) {
            const images = signContainer.querySelectorAll('.sign-image');
            images.forEach((img, i) => {
                img.style.opacity = i === index ? '1' : '0.5';
                img.classList.toggle('active', i === index);
            });
        }
        
        if (this.elements.subtitles) {
            this.elements.subtitles.textContent = sign.text;
        }
        
        const progress = ((index + 1) / this.state.signSequence.length) * 100;
        if (this.elements.progress) {
            this.elements.progress.style.width = `${progress}%`;
        }
    }

    playSequence() {
        if (this.state.signSequence.length === 0) {
            return;
        }

        this.state.isPlaying = true;
        if (this.elements.playBtn) this.elements.playBtn.disabled = true;
        if (this.elements.playSignBtn) this.elements.playSignBtn.disabled = true;
        if (this.elements.pauseBtn) this.elements.pauseBtn.disabled = false;
        if (this.elements.pauseSignBtn) this.elements.pauseSignBtn.disabled = false;

        const interval = 1000 / this.state.speed;

        this.state.playInterval = setInterval(() => {
            if (this.state.currentIndex < this.state.signSequence.length) {
                this.showSign(this.state.currentIndex);
                this.state.currentIndex++;
            } else {
                this.pauseSequence();
                this.state.currentIndex = 0;
            }
        }, interval);
    }

    pauseSequence() {
        this.state.isPlaying = false;
        if (this.elements.playBtn) this.elements.playBtn.disabled = false;
        if (this.elements.playSignBtn) this.elements.playSignBtn.disabled = false;
        if (this.elements.pauseBtn) this.elements.pauseBtn.disabled = true;
        if (this.elements.pauseSignBtn) this.elements.pauseSignBtn.disabled = true;

        if (this.state.playInterval) {
            clearInterval(this.state.playInterval);
            this.state.playInterval = null;
        }
    }

    replaySequence() {
        this.state.currentIndex = 0;
        this.playSequence();
    }

    // UI Helper Methods
    showLoadingState(message = 'جاري التحميل...') {
        // Legacy loading overlay
        if (this.elements.loadingOverlay) {
            this.elements.loadingOverlay.classList.add('active');
        }

        // Modern loading overlay
        const existingLoader = document.querySelector('.loading-overlay');
        if (existingLoader) return;

        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-text">${message}</div>
            </div>
        `;
        
        document.body.appendChild(loader);
    }

    hideLoadingState() {
        // Legacy loading overlay
        if (this.elements.loadingOverlay) {
            this.elements.loadingOverlay.classList.remove('active');
        }

        // Modern loading overlay
        const loader = document.querySelector('.loading-overlay');
        if (loader) {
            loader.remove();
        }
    }

    showConnectionStatus(message, type = 'info') {
        const statusBar = document.createElement('div');
        statusBar.className = `connection-status ${type}`;
        statusBar.textContent = message;
        
        document.body.appendChild(statusBar);
        
        setTimeout(() => {
            statusBar.remove();
        }, 3000);
    }

    showError(message) {
        // Also show legacy alert for compatibility
        if (typeof alert !== 'undefined') {
            alert(message);
        }

        const errorToast = document.createElement('div');
        errorToast.className = 'error-toast';
        errorToast.innerHTML = `
            <div class="error-content">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(errorToast);
        
        setTimeout(() => {
            errorToast.remove();
        }, 5000);
    }

    showSuccess(message) {
        const successToast = document.createElement('div');
        successToast.className = 'success-toast';
        successToast.innerHTML = `
            <div class="success-content">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(successToast);
        
        setTimeout(() => {
            successToast.remove();
        }, 3000);
    }

    updateRecordingUI(isRecording) {
        const recordBtn = document.getElementById('recordBtn');
        if (!recordBtn) return;

        if (isRecording) {
            recordBtn.classList.add('recording');
            recordBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="6" y="6" width="12" height="12" fill="currentColor"/>
                </svg>
                إيقاف التسجيل
            `;
        } else {
            recordBtn.classList.remove('recording');
            recordBtn.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="12" y1="19" x2="12" y2="23" stroke="currentColor" stroke-width="1.5"/>
                    <line x1="8" y1="23" x2="16" y2="23" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                تسجيل صوتي
            `;
        }
    }

    // Language and Translations
    toggleLanguage() {
        this.state.currentLang = this.state.currentLang === 'ar' ? 'en' : 'ar';
        this.updateLanguage();
    }

    // --- PWA install UI helpers ---
    isAppInstalled() {
        // iOS Safari
        const iosStandalone = window.navigator.standalone === true;
        // display-mode
        const displayStandalone = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;
        // localStorage flag set on appinstalled
        const flagged = localStorage.getItem && localStorage.getItem('aisl-pwa-installed') === '1';
        return iosStandalone || displayStandalone || flagged;
    }

    renderInstallButton() {
        if (this.installBtn) return; // already rendered

        // create a persistent install button fixed in header/bottom-right
        const createBtnAndAttach = () => {
            if (!document.body) return false;
            const btn = document.createElement('button');
            btn.id = 'pwaInstallBtn';
            btn.className = 'pwa-install-btn';
            btn.type = 'button';
            btn.title = 'Install AISL App';
            // accessibility
            btn.tabIndex = 0;
            btn.setAttribute('aria-label', 'تثبيت التطبيق');
            btn.addEventListener('click', (e) => this.handleInstallClick(e));

            // Add a visible icon + label container so it's noticeable even when text is short
            btn.innerHTML = `
                <span class="pwa-icon" aria-hidden="true" style="display:flex;align-items:center;gap:8px;padding:0 6px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 7l5-5 5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="pwa-label" style="font-size:13px;line-height:1;">تثبيت</span>
                </span>
            `;

            // ensure visible above overlays
            btn.style.zIndex = 140000;
            console.debug('AISL: created PWA install button', btn);

            document.body.appendChild(btn);
            this.installBtn = btn;
            this.updateInstallButtonState();
            return true;
        };

        // If body is not yet available (script loaded in head), wait for DOMContentLoaded
        if (!document.body) {
            window.addEventListener('DOMContentLoaded', () => {
                createBtnAndAttach();
            });
        } else {
            createBtnAndAttach();
        }
    }

    updateInstallButtonState() {
    if (!this.installBtn) return;
    console.debug('AISL: updateInstallButtonState', { installed: this.isAppInstalled(), hasPrompt: !!this.deferredPrompt });
    const labelEl = this.installBtn.querySelector('.pwa-label');
        if (this.isAppInstalled()) {
            if (labelEl) labelEl.textContent = 'مثبّت';
            this.installBtn.disabled = true;
            this.installBtn.classList.add('installed');
        } else if (this.deferredPrompt) {
            if (labelEl) labelEl.textContent = 'تثبيت';
            this.installBtn.disabled = false;
            this.installBtn.classList.remove('installed');
        } else {
            // Not installable right now - show guidance label
            if (labelEl) labelEl.textContent = 'أضف التطبيق';
            this.installBtn.disabled = false;
            this.installBtn.classList.remove('installed');
        }
    }

    async handleInstallClick(e) {
        // If the app is already installed, do nothing
        if (this.isAppInstalled()) return;

        if (this.deferredPrompt) {
            try {
                this.deferredPrompt.prompt();
                const choice = await this.deferredPrompt.userChoice;
                console.log('Install choice', choice);
                if (choice.outcome === 'accepted') {
                    try { localStorage.setItem('aisl-pwa-installed','1'); } catch(e){}
                }
                this.deferredPrompt = null;
            } catch (err) {
                console.error('Install prompt failed', err);
            }
        } else {
            // No prompt available: show guidance banner if present
            const installBanner = document.getElementById('installBanner');
            if (installBanner) {
                installBanner.style.display = 'block';
            } else {
                // lightweight toast
                this.showSuccess('PWA installation is not available on this browser');
            }
        }

        this.updateInstallButtonState();
    }

    updateLanguage() {
        const translations = {
            ar: {
                title: 'AISL - تطبيق لغة الإشارة',
                textTab: 'نص إلى إشارة',
                audioTab: 'إشارة إلى نص',
                textPlaceholder: 'اكتب النص المراد تحويله إلى لغة الإشارة...',
                convertText: 'تحويل إلى لغة الإشارة',
                convertAudio: 'تحويل الصوت إلى لغة الإشارة',
                uploadAudio: 'اختر ملف صوتي (MP3 أو WAV)',
                placeholder: 'سيتم عرض حركات لغة الإشارة هنا',
                loading: 'جاري التحويل...',
                speed: 'السرعة:',
                langBtn: 'English'
            },
            en: {
                title: 'AISL - Sign Language App',
                textTab: 'Text to Sign',
                audioTab: 'Sign to Text',
                textPlaceholder: 'Type text to convert to sign language...',
                convertText: 'Convert to Sign Language',
                convertAudio: 'Convert Audio to Sign Language',
                uploadAudio: 'Choose audio file (MP3 or WAV)',
                placeholder: 'Sign language gestures will appear here',
                loading: 'Converting...',
                speed: 'Speed:',
                langBtn: 'العربية'
            }
        };

        const t = translations[this.state.currentLang];
        
        document.documentElement.lang = this.state.currentLang;
        document.documentElement.dir = this.state.currentLang === 'ar' ? 'rtl' : 'ltr';
        document.body.dataset.lang = this.state.currentLang;

        // Update UI elements if they exist
        const titleElement = document.querySelector('.logo h1') || document.querySelector('h1');
        if (titleElement) titleElement.textContent = t.title;
        
        if (this.elements.langBtn) this.elements.langBtn.textContent = t.langBtn;
        if (this.elements.textInput) this.elements.textInput.placeholder = t.textPlaceholder;
        
        // Update other elements as needed
        this.updateElementText('.tab-btn[data-tab="text"]', t.textTab);
        this.updateElementText('.tab-btn[data-tab="audio"]', t.audioTab);
    }

    // --- Direction preference helpers ---
    loadForceDirPreference() {
        try {
            const v = localStorage.getItem('aisl-force-dir');
            if (v === 'ltr' || v === 'rtl' || v === 'auto') return v;
        } catch (e) {
            // ignore
        }
        return 'auto';
    }

    saveForceDirPreference(value) {
        try {
            localStorage.setItem('aisl-force-dir', value);
            this.forceDir = value;
            this.updateForceToggleUI();
        } catch (e) {
            console.error('Failed to save forceDir preference', e);
        }
    }

    toggleForceDirectionMode() {
        const order = ['auto','ltr','rtl'];
        const idx = order.indexOf(this.forceDir);
        const next = order[(idx + 1) % order.length];
        this.saveForceDirPreference(next);
        this.showSuccess(next === 'auto' ? 'اتجاه تلقائي' : (next === 'ltr' ? 'من اليسار لليمين' : 'من اليمين لليسار'));
    }

    updateForceToggleUI() {
        if (!this.dirToggleBtn) return;
        const v = this.forceDir || 'auto';
        // Display a short label (Arabic) but tooltip has details
        const label = v === 'auto' ? 'Auto' : (v === 'ltr' ? 'LTR' : 'RTL');
        this.dirToggleBtn.textContent = label;
        this.dirToggleBtn.setAttribute('aria-pressed', v !== 'auto');
    }

    updateElementText(selector, text) {
        const element = document.querySelector(selector);
        if (element) {
            // Preserve icons and update only text content
            const textNode = Array.from(element.childNodes).find(node => node.nodeType === Node.TEXT_NODE);
            if (textNode) {
                textNode.textContent = text;
            } else {
                element.textContent = text;
            }
        }
    }

    // Data Management
    saveToLocalStorage(key, data) {
        try {
            localStorage.setItem(key, JSON.stringify(data));
        } catch (error) {
            console.error('Failed to save to localStorage:', error);
        }
    }

    loadFromLocalStorage(key, defaultValue = null) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : defaultValue;
        } catch (error) {
            console.error('Failed to load from localStorage:', error);
            return defaultValue;
        }
    }

    loadSettings() {
        return this.loadFromLocalStorage('aisl-settings', {
            language: 'ar',
            theme: 'light',
            notifications: true,
            sounds: true,
            autoTranslate: false,
            signSpeed: 'normal'
        });
    }

    saveSettings() {
        this.saveToLocalStorage('aisl-settings', this.settings);
    }

    // Offline Support
    initOfflineSupport() {
        this.pendingTranslations = this.loadFromLocalStorage('pendingTranslations', []);
        
        // Register background sync if supported
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            navigator.serviceWorker.ready.then(registration => {
                return registration.sync.register('translate-text');
            }).catch(error => {
                console.error('Background sync registration failed:', error);
            });
        }
    }

    async syncPendingData() {
        if (!this.isOnline || this.pendingTranslations.length === 0) {
            return;
        }

        console.log('🔄 Syncing pending translations...');
        
        const successfulSyncs = [];
        
        for (const translation of this.pendingTranslations) {
            try {
                const response = await fetch('/api/convert-text', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCSRFToken()
                    },
                    body: JSON.stringify(translation.data)
                });

                if (response.ok) {
                    successfulSyncs.push(translation.id);
                    console.log('✅ Synced translation:', translation.id);
                }
            } catch (error) {
                console.error('Failed to sync translation:', translation.id, error);
            }
        }

        // Remove successfully synced translations
        this.pendingTranslations = this.pendingTranslations.filter(
            t => !successfulSyncs.includes(t.id)
        );
        
        this.saveToLocalStorage('pendingTranslations', this.pendingTranslations);
        
        if (successfulSyncs.length > 0) {
            this.showSuccess(`تم مزامنة ${successfulSyncs.length} ترجمة`);
        }
    }

    // Performance Monitoring
    initPerformanceMonitoring() {
        // Monitor page load performance
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                console.log('📊 Page load performance:', {
                    loadTime: perfData.loadEventEnd - perfData.loadEventStart,
                    domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
                    totalTime: perfData.loadEventEnd - perfData.fetchStart
                });
            }, 0);
        });

        // Monitor memory usage (if available)
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                if (memory.usedJSHeapSize > memory.jsHeapSizeLimit * 0.9) {
                    console.warn('⚠️ High memory usage detected');
                    this.optimizeMemoryUsage();
                }
            }, 30000); // Check every 30 seconds
        }
    }

    optimizeMemoryUsage() {
        // Clear old cached data
        const maxCacheAge = 24 * 60 * 60 * 1000; // 24 hours
        const now = Date.now();
        
        // Clear old translations
        this.pendingTranslations = this.pendingTranslations.filter(
            t => now - new Date(t.timestamp).getTime() < maxCacheAge
        );
        
        this.saveToLocalStorage('pendingTranslations', this.pendingTranslations);
        
        console.log('🧹 Memory optimization completed');
    }

    // Utility Methods
    getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    // Navigation and Gestures
    handleSwipeLeft() {
        console.log('👈 Swipe left detected');
    }

    handleSwipeRight() {
        console.log('👉 Swipe right detected');
    }

    handleNavigation(state) {
        console.log('🧭 Navigation:', state);
    }

    pauseNonEssentialTasks() {
        console.log('⏸️ Pausing non-essential tasks');
    }

    resumeNonEssentialTasks() {
        console.log('▶️ Resuming non-essential tasks');
    }

    loadUserPreferences() {
        console.log('👤 Loading user preferences');
    }

    handleSpeechResult(finalTranscript, interimTranscript) {
        const textInput = document.getElementById('textInput');
        if (textInput) {
            if (finalTranscript) {
                textInput.value = finalTranscript;
                // Auto-translate if enabled
                if (this.settings.autoTranslate) {
                    this.translateText(finalTranscript);
                }
            }
        }
    }

    showUpdateNotification() {
        const notification = document.createElement('div');
        notification.className = 'update-notification';
        notification.innerHTML = `
            <div class="update-content">
                <span>تحديث جديد متاح</span>
                <button onclick="window.location.reload()">تحديث</button>
            </div>
        `;
        document.body.appendChild(notification);
    }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.aisl = new AISLApp();
});

// Add CSS for UI components
const AISL_STYLE = document.createElement('style');
AISL_STYLE.textContent = `
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-content {
        background: var(--bg-card, white);
        padding: 24px;
        border-radius: var(--radius-lg, 12px);
        text-align: center;
        color: var(--text-primary, #333);
    }

    .loading-spinner {
        width: 32px;
        height: 32px;
        border: 3px solid var(--border-light, #e5e7eb);
        border-top: 3px solid var(--primary-blue, #4f46e5);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 12px;
    }

    .connection-status {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 8px 16px;
        border-radius: var(--radius-md, 8px);
        color: white;
        font-size: 14px;
        z-index: 1000;
    }

    .connection-status.success {
        background: var(--secondary-green, #10b981);
    }

    .connection-status.warning {
        background: var(--secondary-orange, #f59e0b);
    }

    .error-toast, .success-toast {
        position: fixed;
        bottom: 100px;
        left: 20px;
        right: 20px;
        padding: 16px;
        border-radius: var(--radius-md, 8px);
        color: white;
        z-index: 1000;
        animation: slideUp 0.3s ease;
    }

    .error-toast {
        background: var(--secondary-red, #ef4444);
    }

    .success-toast {
        background: var(--secondary-green, #10b981);
    }

    .error-content, .success-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .update-notification {
        position: fixed;
        bottom: 100px;
        left: 20px;
        right: 20px;
        background: var(--primary-blue, #4f46e5);
        color: white;
        padding: 16px;
        border-radius: var(--radius-md, 8px);
        z-index: 1000;
    }

    .update-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .update-content button {
        background: white;
        color: var(--primary-blue, #4f46e5);
        border: none;
        padding: 8px 16px;
        border-radius: var(--radius-sm, 6px);
        font-weight: 500;
        cursor: pointer;
    }

    .recording {
        background: var(--secondary-red, #ef4444) !important;
        color: white !important;
        animation: pulse 1s infinite;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    /* Sign container direction and image styles */
    #signContainer, #signList {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        padding: 8px;
    }

    /* LTR: images appear left-to-right. RTL: reverse the visual order. */
    .dir-ltr { flex-direction: row; }
    .dir-rtl { flex-direction: row-reverse; }

    .sign-image {
        max-width: 100px;
        max-height: 120px;
        object-fit: contain;
        transition: opacity 0.2s ease, transform 0.2s ease;
        border-radius: 8px;
    }

    .sign-image.active {
        transform: scale(1.02);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .dir-toggle {
        margin-left: 8px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.08);
        color: inherit;
        padding: 6px 8px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
    }
    .dir-toggle[aria-pressed="true"] {
        background: rgba(0,0,0,0.08);
    }
    /* PWA install floating action button */
    .pwa-install-btn {
        position: fixed;
        right: calc(env(safe-area-inset-right, 16px) + 16px);
        bottom: calc(env(safe-area-inset-bottom, 16px) + 16px);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--primary-blue, #4f46e5);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        cursor: pointer;
    z-index: 140000;
        font-weight: 600;
        font-size: 14px;
        padding: 0 12px;
    }
    .pwa-install-btn.installed {
        background: rgba(0,0,0,0.12);
        color: #fff;
        opacity: 0.95;
        cursor: default;
    }
    .pwa-install-btn[disabled] { opacity: 0.7; cursor: default; }
`;
document.head.appendChild(AISL_STYLE);


}
