@extends('layouts.main')

@section('title','إنشاء حساب')
@section('page-title','إنشاء حساب')

@section('content')
        <style>
        /* Local auth page overrides (register) */
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
        footer, .site-footer { margin-top: 30px; }
        @media (max-width: 520px) {
            .auth-brand img { width: 72px; height: 72px; }
            .auth-title { font-size: 18px; }
        }
        </style>

        <div class="mobile-card auth-page">
        <div class="auth-brand">
            <img src="/frontend/logo.svg" alt="AISL" />
            <div class="auth-title">إنشاء حساب</div>
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">الاسم</label>
                <input name="name" type="text" class="form-input" value="{{ old('name') }}" required autofocus />
            </div>

            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input name="email" type="email" class="form-input" value="{{ old('email') }}" required />
            </div>

            <div class="form-group">
                <label class="form-label">كلمة المرور</label>
                <input name="password" type="password" class="form-input" required />
            </div>

            <div class="form-group">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input name="password_confirmation" type="password" class="form-input" required />
            </div>

            <div style="margin-top:16px;">
                <button class="btn btn-primary btn-full">تسجيل</button>
                <a href="{{ route('login') }}" class="btn btn-secondary btn-full" style="margin-top:10px; display:block; text-align:center;">دخول</a>
            </div>
        </form>
    </div>

@endsection
