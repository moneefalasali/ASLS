@extends('layouts.main')

@section('title','تسجيل الدخول')
@section('page-title','تسجيل الدخول')

@section('content')
        <style>
        /* Local auth page overrides (applies only on this page) */
        /* Center content area and tighten header for desktop */
        .mobile-header { padding: 10px 18px; }
        .mobile-content { display: flex; justify-content: center; padding-top: 28px; }
        .auth-page { width: 100%; max-width: 560px; margin: 0 12px; }
        .auth-page .mobile-card { padding: 20px; border: none; box-shadow: 0 12px 30px rgba(17,24,39,0.08); border-radius: 14px; }
        .auth-brand { margin-bottom: 14px; }
        .auth-brand img { width: 88px; height: 88px; }
        .auth-title { font-size: 20px; font-weight: 700; text-align: center; }
        .auth-page .form-input { font-size: 16px; padding: 12px 14px; }
        .auth-page .btn-full { padding: 14px 16px; font-size: 15px; border-radius: 10px; }
        .auth-page .link { display: inline-block; margin-top: 10px; }
        /* Improve footer spacing on auth pages */
        footer, .site-footer { margin-top: 30px; }
        @media (max-width: 520px) {
            .auth-brand img { width: 72px; height: 72px; }
            .auth-title { font-size: 18px; }
        }
        </style>

        <div class="mobile-card auth-page">
        <div class="auth-brand">
            <img src="/frontend/logo.svg" alt="AISL" />
            <div class="auth-title">تسجيل الدخول</div>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input name="email" type="email" class="form-input" value="{{ old('email') }}" required autofocus />
            </div>

            <div class="form-group">
                <label class="form-label">كلمة المرور</label>
                <input name="password" type="password" class="form-input" required />
            </div>

            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:8px;">
                <label style="font-size:14px;"><input type="checkbox" name="remember" /> تذكرني</label>
                <a class="link" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
            </div>

            <div style="margin-top:16px;">
                <button class="btn btn-primary btn-full">دخول</button>
                <a href="{{ route('register') }}" class="btn btn-secondary btn-full" style="margin-top:10px; display:block; text-align:center;">إنشاء حساب</a>
            </div>
        </form>
    </div>

@endsection
