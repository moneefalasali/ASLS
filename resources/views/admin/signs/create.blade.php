@extends('admin.layout')
@section('title','إنشاء إشارة')
@section('content')
<form method="POST" action="{{ route('admin.signs.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <label>المفتاح (Key)</label>
    <input name="key" class="text-input" required />
  </div>
  <div class="form-row">
    <label>اللغة</label>
    <select name="language" class="text-input"><option value="ar">العربية</option><option value="en">English</option></select>
  </div>
  <div class="form-row">
    <label>النوع</label>
    <select name="type" class="text-input"><option value="image">صورة</option><option value="animation">تحريك</option></select>
  </div>
  <div class="form-row">
    <label>رفع ملف (صورة/SVG)</label>
    <input type="file" name="src_file" accept="image/*,image/svg+xml" onchange="previewFile(event)" />
  </div>
  <div class="form-row">
    <label>أو رابط/مسار المصدر</label>
    <input name="src" class="text-input" placeholder="مثال: /storage/signs/en_a.png" />
  </div>
  <div class="form-row">
    <label>النص الوصفي</label>
    <input name="text" class="text-input" />
  </div>
  <div class="form-row">
    <img id="preview" src="" alt="preview" style="max-width:160px;display:none;border:1px solid var(--border);padding:6px;border-radius:8px" />
  </div>
  <div class="form-row">
    <button class="btn btn-primary">حفظ</button>
    <a href="{{ route('admin.signs.index') }}" class="btn">إلغاء</a>
  </div>
</form>

<script>
function previewFile(e){ const f=e.target.files[0]; const p=document.getElementById('preview'); if(!f){p.style.display='none'; return;} p.src = URL.createObjectURL(f); p.style.display='block'; }
</script>

@endsection
