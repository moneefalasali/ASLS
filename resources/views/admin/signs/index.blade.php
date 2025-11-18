@extends('admin.layout')
@section('title','مكتبة الإشارات')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
  <h3>قائمة الإشارات</h3>
  <a href="{{ route('admin.signs.create') }}" class="btn btn-primary">إنشاء إشارة جديدة</a>
</div>

<div class="signs-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px">
  @foreach($assets as $a)
  <div class="card">
    <div style="height:140px;display:flex;align-items:center;justify-content:center;background:var(--background);border-radius:8px;padding:8px">
      <img src="{{ $a->src }}" alt="{{ $a->text }}" style="max-height:120px;max-width:100%;object-fit:contain" onerror="this.src='/frontend/placeholder.png'"/>
    </div>
    <div style="padding-top:8px">
      <div><strong>مفتاح:</strong> {{ $a->key }}</div>
      <div><strong>اللغة:</strong> {{ $a->language }}</div>
      <div><strong>النوع:</strong> {{ $a->type }}</div>
      <div><strong>النص:</strong> {{ $a->text }}</div>
      <div style="margin-top:8px;display:flex;gap:8px">
        <a href="{{ route('admin.signs.edit', $a) }}" class="btn">تعديل</a>
        <form method="POST" action="{{ route('admin.signs.destroy', $a) }}" onsubmit="return confirm('هل أنت متأكد؟')">@csrf @method('DELETE')<button class="btn">حذف</button></form>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div style="margin-top:16px">{{ $assets->links() }}</div>

@endsection
