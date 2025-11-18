@extends('layouts.main')

@section('title', config('app.name', 'تطبيق لغة الإشارة'))

@section('content')
    <div class="guest-wrapper" style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 0;">
        <div class="guest-card" style="width:100%;max-width:420px;background:var(--surface);padding:20px;border-radius:12px;box-shadow:var(--shadow);">
            <div style="text-align:center;margin-bottom:12px">
                <img src="/frontend/logo.svg" alt="logo" style="width:64px;height:64px;margin:0 auto;display:block" />
            </div>
            {{ $slot }}
        </div>
    </div>

@endsection
