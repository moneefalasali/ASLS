@extends('layouts.main')

@section('title', 'أدوات الأيقونة والفهرسة')
@section('page-title', 'أدوات الأيقونة والفهرسة')

@section('content')
    <div class="card">
        <div style="padding:18px; display:flex; flex-direction:column; gap:12px;">
            <p>هنا يمكنك فهرسة صور الإشارات من المجلد وإجبار تطبيق خدمة العمل إلى تحديث كاش الأيقونة.</p>

            <div style="display:flex; gap:12px; align-items:center;">
                <button id="reindexBtn" class="btn btn-primary">تشغيل فهرسة الإشارات</button>
                <span id="reindexStatus" style="color:var(--text-secondary)"></span>
            </div>

            <div style="display:flex; gap:12px; align-items:center;">
                <button id="purgeSWBtn" class="btn">تحديث كاش خدمة العمل (SW)</button>
                <span id="purgeStatus" style="color:var(--text-secondary)"></span>
            </div>

            <div style="display:flex; gap:12px; align-items:center;">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="file" name="logo" id="logoFile" accept="image/*" />
                    <button id="uploadBtn" class="btn" type="submit">رفع الشعار وتوليد الأيقونات</button>
                </form>
                <span id="uploadStatus" style="color:var(--text-secondary)"></span>
            </div>

            <p style="margin-top:12px;color:var(--text-secondary);">تنبيه: هاتان الأداتان تتطلبان صلاحيات إدارية. تأكد من تشغيل هذا فقط إذا كنت مسؤولاً.</p>
        </div>
    </div>

    <script>
        document.getElementById('reindexBtn').addEventListener('click', async () => {
            const btn = document.getElementById('reindexBtn');
            const status = document.getElementById('reindexStatus');
            btn.disabled = true; status.textContent = 'جاري الفهرسة...';
            try {
                const resp = await fetch('{{ route('admin.icon.reindex') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json' } });
                const json = await resp.json();
                if (json.success) status.textContent = 'تمت الفهرسة بنجاح.'; else status.textContent = 'فشل الفهرسة: ' + (json.error || json.message);
            } catch(e) { status.textContent = 'فشل الاتصال: ' + e.message; }
            btn.disabled = false;
        });

        document.getElementById('purgeSWBtn').addEventListener('click', async () => {
            const btn = document.getElementById('purgeSWBtn');
            const status = document.getElementById('purgeStatus');
            btn.disabled = true; status.textContent = 'جاري تحديث إصدار SW...';
            try {
                const resp = await fetch('{{ route('admin.icon.purge_sw') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json' } });
                const json = await resp.json();
                if (json.success) {
                    status.textContent = 'تمت العملية. محاولة إعادة تسجيل SW...';
                    if ('serviceWorker' in navigator) {
                        const reg = await navigator.serviceWorker.getRegistration();
                        if (reg) {
                            await reg.update();
                            // optional: unregister for a clean install
                            try { await reg.unregister(); } catch(e){}
                            status.textContent = 'تم تحديث SW. يرجى إعادة تحميل الصفحة.';
                        } else {
                            status.textContent = 'لا يوجد تسجيل SW نشط. افتح الصفحة كـ PWA للتحقق.';
                        }
                    }
                } else {
                    status.textContent = 'فشل تحديث إصدار SW: ' + (json.error || json.message);
                }
            } catch(e) { status.textContent = 'فشل الاتصال: ' + e.message; }
            btn.disabled = false;
        });

        document.getElementById('uploadForm').addEventListener('submit', async (ev) => {
            ev.preventDefault();
            const file = document.getElementById('logoFile').files[0];
            const status = document.getElementById('uploadStatus');
            const btn = document.getElementById('uploadBtn');
            if (!file) { status.textContent = 'اختر ملفًا أولاً'; return; }
            btn.disabled = true; status.textContent = 'جاري رفع الشعار...';
            try {
                const fd = new FormData(); fd.append('logo', file);
                const resp = await fetch('{{ route('admin.icon.upload') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd });
                const json = await resp.json();
                if (json.success) { status.textContent = 'تم الرفع وتوليد الأيقونات بنجاح'; } else status.textContent = 'فشل الرفع: ' + (json.error || json.message);
            } catch(e) { status.textContent = 'فشل الاتصال: ' + e.message; }
            btn.disabled = false;
        });
    </script>
@endsection
