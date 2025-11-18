@extends('layouts.main')

@section('title', 'مكتبة الإشارات - AISL')
@section('page-title', 'مكتبة الإشارات')
@section('page-subtitle', 'تعلم لغة الإشارة')

@section('header-left')
    <button class="header-btn" onclick="toggleFilter()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46" stroke="currentColor" stroke-width="1.5" fill="none"/>
        </svg>
    </button>
@endsection

@section('header-right')
    <button class="header-btn" onclick="toggleView()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
            <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
            <rect x="14" y="14" width="7" height="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
            <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="1.5" fill="none"/>
        </svg>
    </button>
@endsection

@section('content')
<div class="page-signs-theme">
    <!-- Search Bar -->
    <div class="search-bar" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px); box-shadow: 0 4px 16px var(--shadow-color);">
        <input type="text" class="search-input" placeholder="ابحث في الإشارات..." id="searchInput" style="color: var(--text-primary);">
        <div class="search-icon" style="color: var(--text-light);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="1.5"/>
                <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
    </div>

    <!-- Categories -->
    <div class="mobile-card">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">الفئات</h3>
        
        <div class="quick-actions" style="margin-bottom: 0; gap: 12px;">
            <button class="quick-action category-item" data-category="food" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                <div class="quick-action-icon" style="background: var(--primary-gradient);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title" style="color: var(--text-primary);">الطعام</div>
                <div style="font-size: 12px; color: var(--text-light); margin-top: 2px;">25 إشارة</div>
            </button>

            <button class="quick-action category-item" data-category="colors">
                <div class="quick-action-icon" style="background: var(--secondary-green);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="13.5" cy="6.5" r=".5"/>
                        <circle cx="17.5" cy="10.5" r=".5"/>
                        <circle cx="8.5" cy="7.5" r=".5"/>
                        <circle cx="6.5" cy="12.5" r=".5"/>
                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">الألوان</div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">12 إشارة</div>
            </button>

            <button class="quick-action category-item" data-category="family">
                <div class="quick-action-icon" style="background: var(--secondary-red);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">العائلة</div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">18 إشارة</div>
            </button>

            <button class="quick-action category-item" data-category="numbers">
                <div class="quick-action-icon" style="background: var(--secondary-orange);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M9 9h6v6H9z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="quick-action-title">الأرقام</div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">10 إشارة</div>
            </button>
        </div>
    </div>

    <!-- Recent Signs -->
    <div class="mobile-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary);">الإشارات الأخيرة</h3>
            <button class="btn" style="padding: 8px 16px; display: flex; align-items: center; gap: 8px; font-size: 14px; background: var(--accent-gradient); color: white;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
                    <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>عرض الكل</span>
            </button>
        </div>

        <div class="sign-list" id="signList" style="gap: 12px;">
            <!-- Sign Item 1 -->
            <button class="conversation-item sign-item" data-sign="hello" style="background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px); transition: all 0.3s ease;">
                <div class="conversation-avatar" style="background: var(--primary-gradient);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name" style="color: var(--text-primary);">مرحبا</div>
                    <div class="conversation-message" style="color: var(--text-light);">التحيات</div>
                </div>
                <div class="conversation-meta">
                    <div style="display: flex; align-items: center; gap: 4px; color: var(--text-light);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="5,3 19,12 5,21" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
            </button>

            <!-- Sign Item 2 -->
            <button class="conversation-item sign-item" data-sign="thanks">
                <div class="conversation-avatar" style="background: var(--secondary-green);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">شكرا</div>
                    <div class="conversation-message">التحيات</div>
                </div>
                <div class="conversation-meta">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="5,3 19,12 5,21" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
            </button>

            <!-- Sign Item 3 -->
            <button class="conversation-item sign-item" data-sign="father">
                <div class="conversation-avatar" style="background: var(--secondary-red);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">أب</div>
                    <div class="conversation-message">الأسرة</div>
                </div>
                <div class="conversation-meta">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="5,3 19,12 5,21" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
            </button>

            <!-- Sign Item 4 -->
            <button class="conversation-item sign-item" data-sign="water">
                <div class="conversation-avatar" style="background: var(--secondary-orange);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="conversation-content">
                    <div class="conversation-name">ماء</div>
                    <div class="conversation-message">الطعام</div>
                </div>
                <div class="conversation-meta">
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="5,3 19,12 5,21" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- Training Section -->
    <div class="mobile-card" style="background: var(--primary-gradient); color: white; border: none; overflow: hidden; position: relative;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: var(--card-gradient); backdrop-filter: blur(20px);"></div>
        <div style="text-align: center; padding: 30px 0; position: relative;">
            <div style="width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
            <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 12px; color: white;">تدرب الآن</h3>
            <p style="font-size: 16px; color: rgba(255,255,255,0.9); margin-bottom: 24px;">مارس الإشارات التي تعلمتها</p>
            <button class="btn" style="background: white; color: var(--text-primary); font-weight: 700; padding: 14px 32px; border-radius: 12px; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 8px 24px rgba(0,0,0,0.15);" onclick="startTraining()">
                بدء التدريب
            </button>
        </div>
    </div>

    <!-- Sign Detail Modal (Hidden by default) -->
    <div id="signModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">تفاصيل الإشارة</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="sign-display" style="margin-bottom: 20px;">
                    <div class="sign-container">
                        <img id="modalSignImage" src="" alt="" class="sign-image" style="width: 120px; height: 120px; opacity: 1;">
                    </div>
                    <div class="sign-subtitle" id="modalSignCategory"></div>
                </div>
                <div class="control-buttons" style="display: flex; gap: 16px; justify-content: center; margin-top: 24px;">
                    <button class="control-btn primary" onclick="playSign()" style="background: var(--primary-gradient); color: white; border: none; width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px var(--shadow-color); transition: all 0.3s ease;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="5,3 19,12 5,21" fill="currentColor"/>
                        </svg>
                    </button>
                    <button class="control-btn" onclick="favoriteSign()" style="background: var(--bg-light); color: var(--text-light); border: 1px solid var(--border-color); width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); transition: all 0.3s ease;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                    <button class="control-btn" onclick="shareSign()" style="background: var(--bg-light); color: var(--text-light); border: 1px solid var(--border-color); width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); transition: all 0.3s ease;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="1.5"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke="currentColor" stroke-width="1.5"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-content {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        width: 100%;
        max-width: 400px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px var(--shadow-color);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-light);
        backdrop-filter: blur(10px);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-light);
        cursor: pointer;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .modal-close:hover {
        background: var(--bg-light);
        color: var(--text-primary);
    }

    .modal-body {
        padding: 24px;
        color: var(--text-primary);
    }

    .category-item {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .category-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px var(--shadow-color);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .category-item .quick-action-icon {
        transition: all 0.3s ease;
    }

    .category-item:hover .quick-action-icon {
        transform: scale(1.1);
    }

    .sign-item {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .sign-item:hover {
        background: var(--bg-secondary);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px var(--shadow-color);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .sign-item:active {
        transform: translateY(0);
    }

    .control-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px var(--shadow-color);
        color: var(--text-primary);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .control-btn.primary:hover {
        transform: translateY(-2px) scale(1.05);
    }

    .control-btn:active {
        transform: translateY(0);
    }

    /* Page-scoped theme for signs page only - overrides variables locally */
    .page-signs-theme {
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

    /* Apply page background */
    body.page-signs-theme {
        background: var(--bg-primary) !important;
    }

    /* Container styling */
    body.page-signs-theme .mobile-container,
    .page-signs-theme .mobile-container {
        background: var(--bg-primary) !important;
        color: var(--text-primary) !important;
    }

    /* Header styling */
    body.page-signs-theme .mobile-header,
    .page-signs-theme .mobile-header {
        background: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        border-bottom: 1px solid var(--border-color) !important;
        backdrop-filter: blur(10px) !important;
    }

    /* Header icons */
    body.page-signs-theme .mobile-header .header-btn svg,
    .page-signs-theme .mobile-header .header-btn svg {
        color: var(--text-primary) !important;
        stroke: currentColor !important;
    }

    /* Card styling */
    .page-signs-theme .mobile-card {
        background: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: 0 8px 24px var(--shadow-color) !important;
        backdrop-filter: blur(10px) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    }

    .page-signs-theme .mobile-card:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 12px 32px var(--shadow-color) !important;
    }

    /* Bottom nav active color for this page */
    body.page-signs-theme .bottom-nav-item.active,
    .page-signs-theme .bottom-nav-item.active {
        color: var(--accent-orange) !important;
    }

    body.page-signs-theme .bottom-nav-item.active .bottom-nav-icon,
    .page-signs-theme .bottom-nav-item.active .bottom-nav-icon {
        color: var(--accent-orange) !important;
    }
</style>

<script>
    // Add page class
    (function(){ try{ document.body.classList.add('page-signs-theme'); }catch(e){} })();

    // State
    window.__favSigns = JSON.parse(localStorage.getItem('favSigns') || '[]');

    // Helper: render sign item
    function renderSignItem(sign) {
        const btn = document.createElement('button');
        btn.className = 'conversation-item sign-item';
        btn.dataset.sign = sign.key || sign.id || sign.key;
        btn.style = 'background: var(--bg-light); border: 1px solid var(--border-color); backdrop-filter: blur(10px); transition: all 0.3s ease;';

        const avatar = document.createElement('div');
        avatar.className = 'conversation-avatar';
        avatar.style.background = 'var(--primary-gradient)';
        avatar.innerHTML = `<img src="${escapeHtml(sign.src || '/storage/signs/placeholder.png')}" alt="${escapeHtml(sign.text || sign.key || '')}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">`;

        const content = document.createElement('div');
        content.className = 'conversation-content';
        content.innerHTML = `<div class="conversation-name" style="color: var(--text-primary);">${escapeHtml(sign.text || sign.key || '')}</div>
                             <div class="conversation-message" style="color: var(--text-light);">${escapeHtml(sign.category || '')}</div>`;

        const meta = document.createElement('div');
        meta.className = 'conversation-meta';
        meta.innerHTML = `<div style="display:flex;align-items:center;gap:8px;">
            <button class="btn small" onclick="onPlayClick(event,'${escapeHtml(sign.key)}','${escapeHtml(sign.src)}')">▶</button>
            <button class="btn small" onclick="onFavClick(event,'${escapeHtml(sign.key)}')">☆</button>
            <button class="btn small" onclick="onShareClick(event,'${escapeHtml(sign.key)}','${escapeHtml(sign.text || '')}')">⤴</button>
        </div>`;

        btn.appendChild(avatar);
        btn.appendChild(content);
        btn.appendChild(meta);

        // clicking the item shows modal
        btn.addEventListener('click', (e)=>{ if(e.target.tagName.toLowerCase() === 'button') return; showSignModal(sign); });

        return btn;
    }

    // Load signs from API by category
    async function loadSigns(category = 'all'){
        const list = document.getElementById('signList');
        list.innerHTML = '';
        try{
            const q = (category && category !== 'all') ? `?category=${encodeURIComponent(category)}` : '';
            const resp = await fetch(`/api/v1/signs${q}`);
            const json = await resp.json();
            const signs = json.signs || json.results || [];
            if(!signs.length){
                list.innerHTML = '<div style="padding:12px;color:var(--text-light)">لا توجد إشارات لعرضها.</div>';
                return;
            }

            for(const s of signs){
                const node = renderSignItem(s);
                list.appendChild(node);
            }
        }catch(e){
            console.error(e);
            list.innerHTML = '<div style="padding:12px;color:var(--text-light)">فشل تحميل الإشارات.</div>';
        }
    }

    // Category buttons
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', () => {
            const category = item.dataset.category;
            document.querySelectorAll('.category-item').forEach(i=>i.style.opacity = i===item? '1':'0.6');
            loadSigns(category);
        });
    });

    // Modal handling
    function showSignModal(sign){
        const modal = document.getElementById('signModal');
        const title = document.getElementById('modalTitle');
        const image = document.getElementById('modalSignImage');
        const category = document.getElementById('modalSignCategory');

        title.textContent = sign.text || sign.key || 'إشارة';
        image.src = sign.src || '/storage/signs/placeholder.png';
        image.alt = sign.text || sign.key || '';
        category.textContent = sign.category || '';

        // attach sign info to modal buttons via dataset
        modal.dataset.signKey = sign.key || '';
        modal.style.display = 'flex';
    }

    function closeModal(){ document.getElementById('signModal').style.display = 'none'; }

    // Play sign: call server-side TTS or fallback to browser TTS
    async function playSign(){
        const modal = document.getElementById('signModal');
        const key = modal.dataset.signKey;
        if(!key) return alert('لا يوجد إشارة للتشغيل');

        try{
            const resp = await fetch('/api/signs/generate-tts',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({key})});
            if(resp.ok){
                const j = await resp.json();
                if(j.success && j.url){
                    const a = new Audio(j.url); a.play().catch(()=>{});
                    return;
                }
            }
        }catch(e){console.warn(e)}

        // fallback
        const text = document.getElementById('modalTitle').textContent || '';
        if('speechSynthesis' in window){ const u=new SpeechSynthesisUtterance(text); u.lang='ar-SA'; window.speechSynthesis.speak(u); }
        else alert('ميزة النطق غير متاحة');
    }

    // Favorite sign: toggle in localStorage and update UI
    function onFavClick(event, key){ event.stopPropagation();
        const favs = new Set(JSON.parse(localStorage.getItem('favSigns')||'[]'));
        if(favs.has(key)) favs.delete(key); else favs.add(key);
        localStorage.setItem('favSigns', JSON.stringify(Array.from(favs)));
        showToast(favs.has(key) ? 'أضيفت إلى المفضلة' : 'أُزيلت من المفضلة');
    }

    function onPlayClick(event,key,src){ event.stopPropagation();
        // try to play TTS for this key
        (async()=>{
            try{
                const resp = await fetch('/api/signs/generate-tts',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({key})});
                if(resp.ok){ const j = await resp.json(); if(j.success && j.url){ new Audio(j.url).play().catch(()=>{}); return; } }
            }catch(e){}
            // fallback: play text
            if('speechSynthesis' in window){ const u=new SpeechSynthesisUtterance(key); window.speechSynthesis.speak(u);} else showToast('لا يمكن تشغيل الصوت');
        })();
    }

    function onShareClick(event,key,text){ event.stopPropagation();
        const shareObj = { title: text||key, text: text||key, url: window.location.origin + '/signs#' + encodeURIComponent(key) };
        if(navigator.share) navigator.share(shareObj).catch(()=>{});
        else { navigator.clipboard.writeText(shareObj.url).then(()=> showToast('تم نسخ رابط الإشارة')); }
    }

    function onFavClick(event, key){ event.stopPropagation();
        const favs = new Set(JSON.parse(localStorage.getItem('favSigns')||'[]'));
        let added;
        if(favs.has(key)) { favs.delete(key); added=false; } else { favs.add(key); added=true; }
        localStorage.setItem('favSigns', JSON.stringify(Array.from(favs)));
        showToast(added? 'أضيفت إلى المفضلة' : 'أزيلت من المفضلة');
    }

    // Small utility functions
    function escapeHtml(s){ return String(s||'').replace(/[&<>"]/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
    function showToast(msg, timeout=1800){ let t=document.getElementById('__toast'); if(!t){ t=document.createElement('div'); t.id='__toast'; t.style='position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.8);color:#fff;padding:8px 14px;border-radius:8px;z-index:2000;'; document.body.appendChild(t);} t.textContent=msg; t.style.opacity='1'; clearTimeout(t.__to); t.__to=setTimeout(()=>t.style.opacity='0',timeout); }

    // initialize: load default signs
    document.addEventListener('DOMContentLoaded', ()=> loadSigns('all'));

    // Search functionality (filters rendered list)
    document.getElementById('searchInput').addEventListener('input', (e)=>{
        const term = e.target.value.trim().toLowerCase();
        document.querySelectorAll('#signList .sign-item').forEach(node=>{
            const name = (node.querySelector('.conversation-name')||{}).textContent||'';
            const cat = (node.querySelector('.conversation-message')||{}).textContent||'';
            const ok = name.toLowerCase().includes(term) || cat.toLowerCase().includes(term);
            node.style.display = ok ? 'flex' : 'none';
        });
    });

    // Close modal when clicking outside
    document.getElementById('signModal').addEventListener('click', (e) => { if (e.target.id === 'signModal') closeModal(); });

</script>
@endsection
