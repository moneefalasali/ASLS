@extends('layouts.main')

@section('title','تعديل الحساب')

@section('content')
  <div class="card">
    <h2>تحديث معلومات الحساب</h2>
    <div class="profile-forms">
      @include('profile.partials.update-profile-information-form')
      @include('profile.partials.update-password-form')
      @include('profile.partials.delete-user-form')
    </div>
  </div>

@endsection
