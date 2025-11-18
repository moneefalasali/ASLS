@extends('layouts.main')

@section('title', 'المحادثات - AISL')
@section('page-title', 'المحادثات')
@section('page-subtitle', 'تواصل مع الآخرين')

@section('header-left')
    <button class="header-btn" onclick="createNewConversation()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="1.5"/>
            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    </button>
@endsection

@section('header-right')
    <button class="header-btn" onclick="toggleMenu()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="1.5"/>
            <line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="1.5"/>
            <line x1="3" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    </button>
@endsection

@section('content')
<div class="page-conversations-theme">
    <!-- Search Bar -->
    <div class="search-bar" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px); box-shadow: 0 4px 16px var(--shadow-color);">
        <input type="text" class="search-input" placeholder="ابحث في المحادثات..." id="searchInput" style="color: var(--text-primary);">
        <div class="search-icon" style="color: var(--text-light);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/>
                <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mobile-card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">إجراءات سريعة</h3>
        
        <div class="quick-actions" style="margin-bottom: 0;">
            <button class="quick-action" onclick="quickTranslate()">
                <div class="quick-action-icon" style="background: var(--secondary-orange);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0 0 14.07 6H17V4h-7V2H8v2H1v2h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">ترجمة سريعة</div>
            </button>            <button class="quick-action" onclick="voiceMessage()">
                <div class="quick-action-icon" style="background: var(--secondary-purple);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 15c3 0 4-2.5 4-6 0-3.5-1-6-4-6s-4 2.5-4 6c0 3.5 1 6 4 6z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M6 9v0a6 6 0 0 0 12 0v0" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 18v4" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">رسالة صوتية</div>
            </button>

            <button class="quick-action" onclick="aiAssistant()">
                <div class="quick-action-icon" style="background: var(--secondary-green);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 2v2m0 16v2M2 12h2m16 0h2m-4.2-7.8l1.4-1.4M6.8 17.2l-1.4 1.4m0-14.4l1.4 1.4m10.4 10.4l1.4 1.4" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">مساعد ذكي</div>
            </button>

            <button class="quick-action" onclick="createGroup()">
                <div class="quick-action-icon" style="background: var(--secondary-red);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">مجموعة جديدة</div>
            </button>
        </div>
    </div>

<style>
    /* Page-scoped theme for conversations page */
    .page-conversations-theme {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --accent-gradient: linear-gradient(135deg, #6B8DD6 0%, #8E37D7 100%);
        --card-gradient: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
        --text-primary: #FFFFFF;
        --text-secondary: rgba(255, 255, 255, 0.8);
        --text-light: rgba(255, 255, 255, 0.6);
        --bg-primary: #1a1b1e;
        --bg-secondary: #2a2b2e;
        --bg-card: rgba(255, 255, 255, 0.1);
        --bg-light: rgba(255, 255, 255, 0.05);
        --border-color: rgba(255, 255, 255, 0.1);
        --shadow-color: rgba(0, 0, 0, 0.2);
        --secondary-purple: #8B5CF6;
        --secondary-green: #10B981;
        --secondary-red: #EF4444;
        --secondary-orange: #F59E0B;
    }

    /* Container styling */
    body.page-conversations-theme {
        background: var(--bg-primary) !important;
    }

    .page-conversations-theme .mobile-container {
        background: var(--bg-primary) !important;
        color: var(--text-primary) !important;
    }

    .page-conversations-theme .mobile-header {
        background: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        border-bottom: 1px solid var(--border-color) !important;
        backdrop-filter: blur(10px) !important;
    }

    .page-conversations-theme .mobile-card {
        background: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: 0 8px 24px var(--shadow-color) !important;
        backdrop-filter: blur(10px) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }

    .page-conversations-theme .quick-action {
        background: var(--bg-light) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-primary) !important;
        transition: all 0.3s ease !important;
    }

    .page-conversations-theme .quick-action:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 12px 32px var(--shadow-color) !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
</style>

    <!-- Statistics -->
    <div class="mobile-card">
        <div class="stats-grid">
            <div class="stat-card" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px);">
                <div class="stat-number" style="color: var(--secondary-purple); font-size: 24px; font-weight: 700;">156</div>
                <div class="stat-label" style="color: var(--text-light);">إشارات مترجمة</div>
            </div>
            <div class="stat-card" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px);">
                <div class="stat-number" style="color: var(--secondary-green); font-size: 24px; font-weight: 700;">8</div>
                <div class="stat-label" style="color: var(--text-light);">رسائل غير مقروءة</div>
            </div>
            <div class="stat-card" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px);">
                <div class="stat-number" style="color: var(--secondary-red); font-size: 24px; font-weight: 700;">12</div>
                <div class="stat-label" style="color: var(--text-light);">محادثات نشطة</div>
            </div>
        </div>
    </div>

    <!-- Recent Conversations -->
    <div class="mobile-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary);">المحادثات الأخيرة</h3>
            <div style="color: var(--primary-blue); font-size: 14px; display: flex; align-items: center; gap: 4px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                عرض الكل
            </div>
        </div>

        <div class="conversation-list">
            <!-- Conversation 1 -->
            <div class="conversation-item" onclick="openConversation('ahmed')">
                <div class="conversation-avatar" style="background: var(--secondary-green);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">أحمد محمد</div>
                    <div class="conversation-message">شكراً لك على المساعدة</div>
                </div>
                <div class="conversation-meta">
                    <div class="conversation-time">10:30 ص</div>
                    <div class="conversation-badge">2</div>
                </div>
            </div>

            <!-- Conversation 2 -->
            <div class="conversation-item" onclick="openConversation('aisl-assistant')">
                <div class="conversation-avatar" style="background: var(--primary-blue);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">مساعد AISL</div>
                    <div class="conversation-message">كيف يمكنني مساعدتك اليوم؟</div>
                </div>
                <div class="conversation-meta">
                    <div class="conversation-time">09:45 ص</div>
                    <div style="width: 8px; height: 8px; background: var(--secondary-green); border-radius: 50%;"></div>
                </div>
            </div>

            <!-- Conversation 3 -->
            <div class="conversation-item" onclick="openConversation('fatima')">
                <div class="conversation-avatar" style="background: var(--secondary-green);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">فاطمة أحمد</div>
                    <div class="conversation-message">هل يمكنك تعليمي إشارة جديدة؟</div>
                </div>
                <div class="conversation-meta">
                    <div class="conversation-time">أمس</div>
                    <div class="conversation-badge">1</div>
                </div>
            </div>

            <!-- Group Conversation -->
            <div class="conversation-item" onclick="openConversation('learning-group')">
                <div class="conversation-avatar" style="background: var(--secondary-purple);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">مجموعة تعلم الإشارات</div>
                    <div class="conversation-message">تم إضافة إشارات جديدة</div>
                </div>
                <div class="conversation-meta">
                    <div class="conversation-time">أمس</div>
                    <div class="conversation-badge">5</div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Assistant Banner -->
    <div class="mobile-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="flex: 1;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">مساعد AISL الذكي</div>
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 16px;">متاح دائماً لمساعدتك في الترجمة</div>
                <button class="btn" style="background: white; color: var(--primary-blue); font-weight: 600; padding: 8px 16px; font-size: 14px;" onclick="openConversation('aisl-assistant')">
                    تحدث الآن
                </button>
            </div>
            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Create new conversation - prompt for a name and add to the list
    function createNewConversation() {
        const name = prompt('أدخل اسم المحادثة أو المشارك:');
        if (!name) return;

        // create a new conversation item DOM and prepend to list
        const list = document.querySelector('.conversation-list');
        const id = 'conv-' + Date.now();
        const item = document.createElement('div');
        item.className = 'conversation-item';
        item.setAttribute('onclick', `openConversation('${id}')`);
        item.innerHTML = `
            <div class="conversation-avatar" style="background: var(--secondary-green);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="1.5" fill="currentColor"/>
                </svg>
            </div>
            <div class="conversation-content">
                <div class="conversation-name">${escapeHtml(name)}</div>
                <div class="conversation-message">محادثة جديدة</div>
            </div>
            <div class="conversation-meta">
                <div class="conversation-time">الآن</div>
            </div>
        `;
        list.prepend(item);

        // store mock conversation data in memory for openConversation
        window.__conversations = window.__conversations || {};
        window.__conversations[id] = { name, messages: [] };
        // open it immediately
        openConversation(id);
    }

    // Toggle menu
    // Add page theme class to body
    (function(){
        try {
            document.body.classList.add('page-conversations-theme');
        } catch(e){}
    })();

    function toggleMenu() {
        // Simple slide-in menu mock (toggle a class on body)
        document.body.classList.toggle('menu-open');
        // For demo, show a small toast
        showToast('قائمة الخيارات (تجريبي)');
    }

    // Quick actions
    function quickTranslate() {
        // Redirect to home page with text tab active
        window.location.href = '/#translate';
    }

    function voiceMessage() {
        // Prompt to record a short voice note (simplified)
        const text = prompt('اكتب ملاحظة صوتية (محاكاة) أو اضغط إلغاء:');
        if (!text) return;
        // Add as a new conversation mock
        const id = 'voice-' + Date.now();
        window.__conversations = window.__conversations || {};
        window.__conversations[id] = { name: 'ملاحظة صوتية', messages: [{ sender: 'me', text, time: 'الآن' }] };
        showToast('تم إنشاء رسالة صوتية تجريبية');
    }

    function aiAssistant() {
        openConversation('aisl-assistant');
    }

    function createGroup() {
        const name = prompt('اسم المجموعة الجديدة:');
        if (!name) return;
        showToast('تم إنشاء مجموعة "' + name + '" (تجريبي)');
    }

    // Open conversation
    function openConversation(conversationId) {
        // Mock conversation data
        const conversations = {
            'ahmed': {
                name: 'أحمد محمد',
                messages: [
                    { sender: 'ahmed', text: 'مرحبا، كيف حالك؟', time: '10:25 ص', type: 'text' },
                    { sender: 'me', text: 'أهلاً أحمد، بخير والحمد لله', time: '10:26 ص', type: 'text' },
                    { sender: 'ahmed', text: 'شكراً لك على المساعدة', time: '10:30 ص', type: 'text' }
                ]
            },
            'aisl-assistant': {
                name: 'مساعد AISL',
                messages: [
                    { sender: 'assistant', text: 'مرحباً! أنا مساعد AISL الذكي. كيف يمكنني مساعدتك اليوم؟', time: '09:45 ص', type: 'text' },
                    { sender: 'assistant', text: 'يمكنني مساعدتك في ترجمة النصوص إلى لغة الإشارة أو العكس', time: '09:45 ص', type: 'text' }
                ]
            },
            'fatima': {
                name: 'فاطمة أحمد',
                messages: [
                    { sender: 'fatima', text: 'هل يمكنك تعليمي إشارة جديدة؟', time: 'أمس', type: 'text' }
                ]
            },
            'learning-group': {
                name: 'مجموعة تعلم الإشارات',
                messages: [
                    { sender: 'admin', text: 'تم إضافة إشارات جديدة للمكتبة', time: 'أمس', type: 'announcement' },
                    { sender: 'member1', text: 'شكراً للإضافة الجديدة', time: 'أمس', type: 'text' }
                ]
            }
        };

        window.__conversations = window.__conversations || {};
        const conversation = conversations[conversationId] || window.__conversations[conversationId];
        if (conversation) {
            // show a simple chat panel using prompt for demo
            const previous = conversation.messages.map(m => `${m.time || ''} ${m.sender || ''}: ${m.text}`).join('\n');
            const reply = prompt(`محادثة مع: ${conversation.name}\n----\n${previous}\n\nأضف رسالة جديدة:`);
            if (reply) {
                conversation.messages.push({ sender: 'me', text: reply, time: 'الآن' });
                showToast('تم إضافة الرسالة');
            }
        } else {
            alert('المحادثة غير موجودة');
        }
    }

    // small helper to show temporary toasts
    function showToast(msg, timeout = 2000) {
        let t = document.getElementById('__toast');
        if (!t) {
            t = document.createElement('div');
            t.id = '__toast';
            t.style = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.8);color:#fff;padding:8px 14px;border-radius:8px;z-index:2000;';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        clearTimeout(t.__to);
        t.__to = setTimeout(()=>{ t.style.opacity = '0'; }, timeout);
    }

    // helper to escape html when inserting user input
    function escapeHtml(s){ return String(s).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const conversationItems = document.querySelectorAll('.conversation-item');
        
        conversationItems.forEach(item => {
            const name = item.querySelector('.conversation-name').textContent.toLowerCase();
            const message = item.querySelector('.conversation-message').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || message.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Check for URL hash to auto-open specific features
    if (window.location.hash === '#translate') {
        // Auto-redirect to home page for translation
        setTimeout(() => {
            window.location.href = '/';
        }, 100);
    }
</script>
@endsection
