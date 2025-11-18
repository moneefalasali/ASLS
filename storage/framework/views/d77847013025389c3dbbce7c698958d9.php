

<?php $__env->startSection('title','لوحة إشارات الكلمات'); ?>
<?php $__env->startSection('page-title','لوحة إشارات الكلمات'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div style="padding:12px 14px;display:flex;flex-direction:column;gap:12px;">
        <div style="display:flex;gap:8px;align-items:center;">
            <label style="font-weight:600">اختر إشارة ثم اضغط تحويل</label>
            <select id="kbLanguage" style="margin-left:auto;padding:6px 8px;border-radius:6px;border:1px solid #ddd">
                <option value="ar">العربية</option>
                <option value="en">English</option>
            </select>
            <button id="refreshKeyboard" class="btn" style="margin-left:8px">تحديث</button>
            <button id="openCamera" class="btn" style="margin-left:6px" title="الميزة قادمة قريبًا">كاميرا (قريبًا)</button>
        </div>

        <div id="keyboardGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(96px,1fr));gap:10px;">
            <!-- images will be loaded here -->
        </div>

        <div style="display:flex;gap:8px;align-items:center;justify-content:flex-end">
            <button id="clearSelection" class="btn">مسح الاختيار</button>
            <button id="convertSelected" class="btn btn-primary">تحويل</button>
        </div>

        <div id="resultPanel" style="display:none;flex-direction:column;gap:8px;padding:8px;border-radius:8px;background:#fff;box-shadow:0 6px 18px rgba(0,0,0,0.06)">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <strong>نص الإشارة</strong>
                <div style="display:flex;gap:8px">
                    <button id="resultPlay" class="btn small">تشغيل</button>
                    <button id="resultCopy" class="btn small">نسخ</button>
                </div>
            </div>
            <div id="selectedText" style="min-height:34px;font-size:1.05rem;color:#111"></div>
        </div>

        <div id="kbMessage" style="color:var(--text-secondary);font-size:0.95rem"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const grid = document.getElementById('keyboardGrid');
    const langSelect = document.getElementById('kbLanguage');
    const refreshBtn = document.getElementById('refreshKeyboard');
    const cameraBtn = document.getElementById('openCamera');
    const convertBtn = document.getElementById('convertSelected');
    const clearBtn = document.getElementById('clearSelection');
    const resultPanel = document.getElementById('resultPanel');
    const selectedTextEl = document.getElementById('selectedText');
    const resultPlay = document.getElementById('resultPlay');
    const resultCopy = document.getElementById('resultCopy');
    const kbMessage = document.getElementById('kbMessage');

    let signs = [];
    let selectedSign = null;

    async function loadSigns(){
        grid.innerHTML = '';
        kbMessage.textContent = 'جاري التحميل...';
        const lang = langSelect.value || 'ar';
        try{
            // If requesting words category, prefer folder-based signs (images in storage/signs/words)
            let resp;
            if (true && (new URLSearchParams({}).toString(), 'words') && ("words" === 'words')) {
                // call folder endpoint which returns all images from storage/signs/words
                resp = await fetch(`/api/v1/signs/from-folder/words?language=${encodeURIComponent(lang)}`);
            } else {
                resp = await fetch(`/api/v1/signs?category=words&language=${encodeURIComponent(lang)}&per_page=50`);
            }
            if(!resp.ok) throw new Error('فشل في جلب الإشارات');
            const json = await resp.json();
            signs = json.signs || json.results || [];
            if(!Array.isArray(signs)) signs = [];

            if(signs.length === 0){
                kbMessage.textContent = 'لا توجد إشارات محفوظة لهذه اللغة.';
                return;
            }

            kbMessage.textContent = '';
            for(const s of signs){
                // create cell that only shows the image (no visible text)
                const cell = document.createElement('div');
                cell.className = 'kb-cell';
                cell.style = 'border:1px solid transparent;border-radius:8px;padding:6px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:6px;background:#fff;';

                const img = document.createElement('img');
                // normalize src: use as-is if starts with http or /storage, otherwise prefix /storage/
                let srcVal = s.src || s.path || '';
                if (!srcVal) srcVal = '';
                if (srcVal && !srcVal.startsWith('http') && !srcVal.startsWith('/')) {
                    srcVal = '/storage/' + srcVal.replace(/^\/+/, '');
                }
                img.src = srcVal || '/storage/signs/placeholder.png';
                img.alt = '';
                img.setAttribute('data-src', srcVal);
                img.style = 'width:72px;height:72px;object-fit:contain;border-radius:6px;';
                img.className = 'sign-image';

                // Do not append textual label to the UI (images only)
                cell.appendChild(img);

                // add a small caption (word) under the image so users can see the text
                const caption = document.createElement('div');
                caption.className = 'kb-caption';
                caption.style = 'font-size:0.85rem;color:var(--text-secondary);text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:100%;';
                // Prefer explicit text property, otherwise derive from filename
                let label = (s.text && s.text.trim()) ? s.text.trim() : '';
                if (!label) {
                    const ds = img.getAttribute('data-src') || img.src || '';
                    try {
                        const fname = ds.split('/').pop() || '';
                        label = decodeURIComponent(fname.replace(/\.[^/.]+$/, '')).replace(/[_\-]/g, ' ');
                    } catch(e) { label = ''; }
                }
                caption.textContent = label;
                cell.appendChild(caption);

                cell.addEventListener('click', async ()=>{
                    // toggle selection
                    const previously = grid.querySelector('.kb-cell.selected');
                    if(previously) previously.classList.remove('selected');
                    cell.classList.add('selected');
                    // store minimal selectedSign (we may enrich with identify call)
                    selectedSign = s;
                    resultPanel.style.display = 'flex';
                    // try to show text if available, otherwise fetch via identify endpoint
                    const textAvailable = s.text && s.text.trim().length > 0;
                    if (textAvailable) {
                        selectedTextEl.textContent = s.text;
                    } else {
                        selectedTextEl.textContent = '...';
                        try {
                            const identifyResp = await fetch('/api/signs/identify', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                                body: JSON.stringify({ src: img.getAttribute('data-src') || img.src })
                            });
                            if (identifyResp.ok) {
                                const idJson = await identifyResp.json();
                                if (idJson.success && idJson.sign) {
                                    selectedSign = Object.assign(selectedSign || {}, idJson.sign);
                                    selectedTextEl.textContent = idJson.sign.text || idJson.sign.key || '';
                                } else {
                                    selectedTextEl.textContent = '';
                                }
                            } else {
                                selectedTextEl.textContent = '';
                            }
                        } catch(e){
                            console.warn('identify failed', e);
                            selectedTextEl.textContent = '';
                        }
                    }
                });

                grid.appendChild(cell);
            }
        }catch(err){
            console.error(err);
            kbMessage.textContent = 'حدث خطأ أثناء تحميل الإشارات.';
        }
    }

    refreshBtn.addEventListener('click', loadSigns);
    langSelect.addEventListener('change', loadSigns);

    if(cameraBtn){
        cameraBtn.addEventListener('click', ()=>{
            // Inform the user the camera feature will be available soon
            kbMessage.textContent = 'ميزة فتح الكاميرا ستتوفر قريبًا.';
            // Optional small visual feedback
            cameraBtn.classList.add('pulse');
            setTimeout(()=>{
                if(kbMessage.textContent === 'ميزة فتح الكاميرا ستتوفر قريبًا.') kbMessage.textContent = '';
                cameraBtn.classList.remove('pulse');
            }, 3000);
        });
    }

    clearBtn.addEventListener('click', ()=>{
        const prev = grid.querySelector('.kb-cell.selected');
        if(prev) prev.classList.remove('selected');
        selectedSign = null;
        resultPanel.style.display = 'none';
        selectedTextEl.textContent = '';
    });

    convertBtn.addEventListener('click', async ()=>{
        if(!selectedSign){ alert('الرجاء اختيار إشارة أولاً'); return; }
        // Display text (already set) and attempt to play server-side TTS
        const txt = selectedSign.text || selectedSign.key || '';
        selectedTextEl.textContent = txt;
        resultPanel.style.display = 'flex';
        kbMessage.textContent = 'جاري إنشاء/nتشغيل الصوت...';

        try{
            const payload = { key: selectedSign.key };
            const resp = await fetch('/api/signs/generate-tts', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '' },
                body: JSON.stringify(payload)
            });

            if(resp.ok){
                const data = await resp.json();
                if(data.success && data.url){
                    // play audio from returned URL
                    const audio = new Audio(data.url);
                    audio.play().catch(e=>console.warn('Audio play failed',e));
                    kbMessage.textContent = '';
                    return;
                }
            }

            // fallback to browser TTS
            if('speechSynthesis' in window){
                const utter = new SpeechSynthesisUtterance(txt);
                utter.lang = (langSelect.value === 'en') ? 'en-US' : 'ar-SA';
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utter);
                kbMessage.textContent = '';
            } else {
                kbMessage.textContent = 'لا يوجد اصوات متاحة للتشغيل';
            }
        }catch(e){
            console.error('convert error',e);
            kbMessage.textContent = 'فشل إنشاء الصوت — يتم استخدام النطق المحلي إذا أمكن';
            // try browser TTS
            if('speechSynthesis' in window){
                const utter = new SpeechSynthesisUtterance(txt);
                utter.lang = (langSelect.value === 'en') ? 'en-US' : 'ar-SA';
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utter);
            }
        }
    });

    resultPlay.addEventListener('click', ()=>{
        const txt = selectedTextEl.textContent || '';
        if(!txt) return;
        if('speechSynthesis' in window){
            const utter = new SpeechSynthesisUtterance(txt);
            utter.lang = (langSelect.value === 'en') ? 'en-US' : 'ar-SA';
            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utter);
        } else {
            alert('ميزة النطق غير مدعومة في هذا المتصفح');
        }
    });

    resultCopy.addEventListener('click', async ()=>{
        const txt = selectedTextEl.textContent || '';
        if(!txt) return;
        try{ await navigator.clipboard.writeText(txt); alert('تم نسخ النص'); } catch(e){ alert('فشل النسخ'); }
    });

    // initial load
    loadSigns();
});
</script>

<style>
.kb-cell.selected{ border-color: #4F46E5; box-shadow: 0 6px 18px rgba(79,70,229,0.12); }
.kb-cell img{ transition: transform .12s ease; }
.kb-cell:hover img{ transform: scale(1.03); }

/* small pulse to call attention to the coming-soon button when clicked */
.pulse{ animation: pulse-anim .9s ease-in-out; }
@keyframes pulse-anim{ 0%{ transform: scale(1); } 50%{ transform: scale(1.04); } 100%{ transform: scale(1); } }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\a_i_s_l_project\si\resources\views/word_keyboard.blade.php ENDPATH**/ ?>