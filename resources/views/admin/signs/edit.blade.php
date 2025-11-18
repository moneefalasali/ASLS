@extends('admin.layout')
@section('title','تعديل إشارة')
@section('content')
<form method="POST" action="{{ route('admin.signs.update', $sign) }}" enctype="multipart/form-data">
  @csrf @method('PATCH')
  <div class="form-row">
    <label>المفتاح (Key)</label>
    <input name="key" class="text-input" value="{{ $sign->key }}" required />
  </div>
  <div class="form-row">
    <label>اللغة</label>
    <select name="language" class="text-input"><option value="ar" {{ $sign->language=='ar'?'selected':'' }}>العربية</option><option value="en" {{ $sign->language=='en'?'selected':'' }}>English</option></select>
  </div>
  <div class="form-row">
    <label>النوع</label>
    <select name="type" class="text-input"><option value="image" {{ $sign->type=='image'?'selected':'' }}>صورة</option><option value="animation" {{ $sign->type=='animation'?'selected':'' }}>تحريك</option></select>
  </div>
  <div class="form-row">
    <label>رفع ملف جديد (اختياري)</label>
    <input type="file" name="src_file" accept="image/*,image/svg+xml" onchange="previewFile(event)" />
  </div>
  <div class="form-row">
    <label>أو رابط/مسار المصدر</label>
    <input name="src" class="text-input" value="{{ $sign->src }}" />
  </div>
  <div class="form-row">
    <label>النص الوصفي</label>
    <input name="text" class="text-input" value="{{ $sign->text }}" />
  </div>
  <div class="form-row">
    <img id="preview" src="{{ $sign->src }}" alt="preview" style="max-width:160px;border:1px solid var(--border);padding:6px;border-radius:8px" />
  </div>
  <div class="form-row">
    <button class="btn btn-primary">تحديث</button>
    <a href="{{ route('admin.signs.index') }}" class="btn">إلغاء</a>
  </div>
</form>

<script>
function previewFile(e){ const f=e.target.files[0]; const p=document.getElementById('preview'); if(!f){p.src='';p.style.display='none'; return;} p.src = URL.createObjectURL(f); p.style.display='block'; }
</script>

@endsection
