@extends('layouts.main')

@section('title','لوحة التحكم')

@section('content')
  <div class="mobile-card large">
    <div style="text-align: center; padding: 20px 0;">
      <div style="width: 80px; height: 80px; background: var(--accent-orange); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="currentColor" stroke-width="1.5"/>
          <path d="M4 21v-1c0-2.761 4-4 8-4s8 1.239 8 4v1" stroke="currentColor" stroke-width="1.5"/>
        </svg>
      </div>
      <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">مرحبًا، {{ auth()->user()->name }}</h2>
      <p style="color: var(--text-secondary); margin-bottom: 24px;">أنت الآن مسجل الدخول في حسابك</p>
      <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('profile.edit') }}" class="btn btn-primary" style="min-width: 140px;">تعديل الحساب</a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline">
          @csrf
          <button class="btn" style="min-width: 140px;">تسجيل الخروج</button>
        </form>
      </div>
      <a href="/" class="btn" style="margin-top: 12px; background: none; border: none; color: var(--accent-orange);">العودة للتطبيق</a>
    </div>
  </div>

  <!-- Stats Section -->
  <div class="mobile-card">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number purple">24</div>
        <div class="stat-label">إشارة متعلمة</div>
      </div>
      <div class="stat-card">
        <div class="stat-number green">85%</div>
        <div class="stat-label">نسبة الإنجاز</div>
      </div>
      <div class="stat-card">
        <div class="stat-number orange">12</div>
        <div class="stat-label">محادثة نشطة</div>
      </div>
    </div>
  </div>

  <!-- Quick Access -->
  <div class="mobile-card">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">وصول سريع</h3>
    <div class="quick-actions">
      <a href="/signs" class="quick-action">
        <div class="quick-action-icon" style="background: var(--secondary-purple);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L9 7V9C9 10.1 9.9 11 11 11V22H13V11C14.1 11 15 10.1 15 9Z" fill="currentColor"/>
          </svg>
        </div>
        <div class="quick-action-title">مكتبة الإشارات</div>
      </a>

      <a href="/profile" class="quick-action">
        <div class="quick-action-icon" style="background: var(--secondary-green);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M4 21v-1c0-2.761 4-4 8-4s8 1.239 8 4v1" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>
        <div class="quick-action-title">الملف الشخصي</div>
      </a>

      <a href="/settings" class="quick-action">
        <div class="quick-action-icon" style="background: var(--secondary-orange);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 4.29 17l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 7 4.29l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06A2 2 0 1 1 19.71 7l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.26.604.844.987 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="1.2"/>
          </svg>
        </div>
        <div class="quick-action-title">الإعدادات</div>
      </a>

      <a href="/help" class="quick-action">
        <div class="quick-action-icon" style="background: var(--secondary-red);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
            <path d="M12 17v-6" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="12" cy="7" r="1" fill="currentColor"/>
          </svg>
        </div>
        <div class="quick-action-title">المساعدة</div>
      </a>
    </div>
  </div>

@endsection
