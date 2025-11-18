@extends('admin.layout')
@section('title','التحميلات')
@section('content')
<h3>قائمة التحميلات</h3>
<div class="uploads-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:12px;margin-top:12px">
  @foreach($uploads as $up)
  <div class="card">
    <div style="display:flex;gap:12px;align-items:center">
      <div style="flex:0 0 80px;height:80px;display:flex;align-items:center;justify-content:center;background:var(--background);border-radius:8px">
        <img src="{{ $up->path }}" style="max-width:76px;max-height:76px;object-fit:contain" onerror="this.src='/frontend/placeholder.png'" />
      </div>
      <div>
        <div><strong>الملف:</strong> {{ $up->original_name }}</div>
        <div><strong>النص:</strong> {{ $up->transcription }}</div>
        <div style="color:var(--text-secondary)">{{ $up->created_at }}</div>
      </div>
    </div>
    <div style="margin-top:8px;text-align:left">
      <a href="{{ route('admin.uploads.show', $up) }}" class="btn">عرض</a>
    </div>
  </div>
  @endforeach
</div>

<div style="margin-top:12px">{{ $uploads->links() }}</div>

@endsection
