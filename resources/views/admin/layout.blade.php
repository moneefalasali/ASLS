@extends('layouts.main')

@section('title','لوحة الإدارة - '.(isset($title)?$title:''))

@section('content')
  <div class="admin-panel">
    <div class="admin-nav">
      <a href="{{ route('admin.signs.index') }}">الأصوات و الإشارات</a>
      <a href="{{ route('admin.users.index') }}">المستخدمون</a>
      <a href="{{ route('admin.uploads.index') }}">التحميلات</a>
    </div>

    <h2>@yield('title')</h2>
    <div class="admin-content">
      @yield('content')
    </div>
  </div>

@endsection
