<!doctype html>


<?php $__env->startSection('title','AISL'); ?>
<?php $__env->startSection('page-title','AISL'); ?>

<?php $__env->startSection('header-left'); ?>
    <?php if(auth()->guard()->check()): ?>

    <?php else: ?>
    
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="mobile-card large">
        <div class="tab-navigation" role="tablist">
            <button class="tab-btn active" data-target="textTab">نص</button>
            <button class="tab-btn" data-target="audioTab">صوت</button>
        </div>

        <div id="textTab" class="tab-content active">
            <div class="form-group">
                <label class="form-label">أدخل النص (عربي أو إنجليزي)</label>
                <textarea id="text" class="form-input form-textarea" placeholder="اكتب هنا..."></textarea>
            </div>

            <div class="form-group" style="display:flex;gap:8px;align-items:center;">
                <select id="language" class="form-input" style="width:auto;padding:8px;border-radius:8px;">
                    <option value="ar">العربية</option>
                    <option value="en">English</option>
                </select>
                <button id="convert" class="btn btn-primary">تحويل النص</button>
                <a href="<?php echo e(route('word.keyboard')); ?>" class="btn btn-secondary">تحويل الإشارة إلى نص</a>
            </div>
        </div>

        <div id="audioTab" class="tab-content" style="display:none;">
            <div class="form-group">
                <label class="form-label">اسحب او اختر ملف صوتي (mp3/wav)</label>
                <input id="audio" type="file" accept="audio/*" />
                <div id="fileName" class="form-text" style="margin-top:8px;color:var(--text-secondary);"></div>
            </div>

            <div class="form-group">
                <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
                    <button id="startRecord" class="btn">بدء التسجيل</button>
                    <button id="stopRecord" class="btn" disabled>إيقاف التسجيل</button>
                    <button id="reRecord" class="btn" disabled>إعادة التسجيل</button>
                    <button id="playPreview" class="btn" disabled>تشغيل المعاينة</button>
                    <a id="downloadAudio" class="btn" style="display:none;">تحميل</a>
                </div>
                <div style="margin-top:8px;text-align:center;color:var(--text-secondary)">مدة التسجيل: <span id="recordTimer">00:00</span></div>
                <canvas id="waveform" width="600" height="80" style="width:100%;max-width:600px;margin-top:8px;border-radius:6px;background:linear-gradient(90deg,#fff,#f7fbff)"></canvas>
                <div style="margin-top:10px;display:flex;gap:8px;justify-content:center">
                    <button id="startRecognition" class="btn">التعرف بالصوت (Speech-to-Text)</button>
                    <button id="stopRecognition" class="btn" disabled>إيقاف التعرف</button>
                </div>
                <div id="liveTranscript" style="margin-top:8px;color:var(--text-secondary);min-height:24px"></div>
            </div>

            <div class="form-group">
                <button id="uploadAudio" class="btn btn-primary">تحويل الصوت</button>
            </div>
        </div>
    </div>

    <div class="welcome-frame">
        <div class="welcome-card">
            <div class="card output-section">
        <div class="output-header">
            <h2>نتيجة التحويل</h2>
            <div class="controls">
                <button id="play" class="control-btn">▶</button>
                <button id="pause" class="control-btn">❚❚</button>
                <div class="speed-control">السرعة: <input id="speed" type="range" min="0.5" max="2" step="0.25" value="1" id="speedSlider"/></div>
            </div>
        </div>

        <div class="mobile-card large">
            <div class="mobile-card large" id="output">
                <!-- initial placeholder so the welcome card shows a centered large image by default -->
                <div class="large-sign-wrapper">
                    <div class="placeholder-overlay" aria-hidden="true">
                        <div class="placeholder-icon" aria-hidden="true">
                            <!-- simple sign-hand icon (decorative) -->

                        </div>
                        <div class="placeholder-text">سيتم عرض الإشارة هنا</div>
                    </div>
                </div>
            </div>
            <div class="subtitles" id="subtitle"></div>
        </div>
            </div>
        </div>
    </div>

    <script>
        const apiConvertText = '/api/convert-text';
        const apiConvertAudio = '/api/convert-audio';
        let sequence = [];
        let index = 0; let timer = null; let playing = false; let speed = 1;

        // Tab switching (text/audio)
        document.querySelectorAll('.tab-navigation .tab-btn').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                document.querySelectorAll('.tab-navigation .tab-btn').forEach(b=>b.classList.remove('active'));
                btn.classList.add('active');
                const target = btn.getAttribute('data-target');
                document.querySelectorAll('.tab-content').forEach(tc=>{ tc.style.display = (tc.id === target) ? 'block' : 'none'; tc.classList.toggle('active', tc.id===target); });
            });
        });

        document.getElementById('convert').addEventListener('click', async ()=>{
            try{
                const text = document.getElementById('text').value.trim();
                const language = document.getElementById('language').value;
                if(!text) return alert('رجاءً أدخل نصًا');

                const resp = await fetch(apiConvertText, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({text,language})});
                if(!resp.ok){
                    const txt = await resp.text().catch(()=>null);
                    console.error('convert-text failed', resp.status, txt);
                    return alert('فشل تحويل النص (رمز: ' + resp.status + ')');
                }

                const json = await resp.json().catch(err=>{ throw new Error('Invalid JSON response: '+err.message); });
                sequence = json.sequence || [];
                // Prefer whole-word signs when possible by collapsing character runs
                if (window.collapseCharactersToWordSigns) {
                    try { sequence = await window.collapseCharactersToWordSigns(sequence, language); } catch(e){ console.warn('collapseCharactersToWordSigns failed', e); }
                }
                index = 0;
                // enable large centered view for converted sign output and debug
                try {
                    const outSection = document.querySelector('.output-section');
                    console.log('Convert result sequence length =', sequence.length);
                    const pref = (localStorage.getItem('aisl-welcome-large') === '1');
                    if (outSection && pref) {
                        outSection.classList.add('large-sign-view');
                        console.log('.output-section.classList after add:', outSection.className);
                    } else if (!pref){
                        console.debug('Welcome large view disabled by preference; not enabling large-sign-view');
                    } else {
                        console.warn('convert: .output-section element not found');
                    }
                } catch(e){ console.warn('convert: error adding large-sign-view', e); }
                renderSequence();
            }catch(err){
                console.error('Error in convert handler', err);
                // Fallback: create a simple placeholder sequence so user sees images
                try{
                    const txt = document.getElementById('text').value.trim();
                    const words = txt ? txt.split(/\s+/).slice(0,12) : [];
                    sequence = words.length ? words.map((w,i)=>({ text: w, src: '/storage/signs/placeholder.png', duration: 1000 })) : [{ text: txt || '??', src: '/storage/signs/placeholder.png', duration: 1000 }];
                    index = 0;
                    try {
                        const outSection = document.querySelector('.output-section');
                        const pref = (localStorage.getItem('aisl-welcome-large') === '1');
                        if (outSection && pref) {
                            outSection.classList.add('large-sign-view');
                        }
                    } catch(e){}
                    renderSequence();
                }catch(e){
                    alert('حدث خطأ عند محاولة تحويل النص: ' + (err.message || err));
                }
            }
        });

        document.getElementById('uploadAudio').addEventListener('click', async ()=>{
            const file = document.getElementById('audio').files[0]; if(!file) return alert('اختر ملفًا');
            await sendAudioFile(file);
        });

        document.getElementById('audio').addEventListener('change', (e)=>{ const f=e.target.files[0]; document.getElementById('fileName').textContent = f?f.name:''});

    // Recording with MediaRecorder
        let mediaRecorder = null;
        let recordedChunks = [];
    let lastBlob = null; // store last recording for preview/re-record
        const startBtn = document.getElementById('startRecord');
        const stopBtn = document.getElementById('stopRecord');
    const reRecordBtn = document.getElementById('reRecord');
    const playPreviewBtn = document.getElementById('playPreview');
    const downloadBtn = document.getElementById('downloadAudio');
    const qualitySelect = document.getElementById('qualitySelect');
    let previewAudio = null;

        // WebAudio analyser for waveform
        let audioCtx = null; let analyser = null; let sourceNode = null; let drawId = null;
        const canvas = document.getElementById('waveform'); const cctx = canvas.getContext('2d');
        function startDrawing(){
            if(!analyser) return;
            const bufferLength = analyser.fftSize; const dataArray = new Uint8Array(bufferLength);
            function draw(){
                drawId = requestAnimationFrame(draw);
                analyser.getByteTimeDomainData(dataArray);
                cctx.fillStyle = '#fff'; cctx.fillRect(0,0,canvas.width,canvas.height);
                cctx.lineWidth = 2; cctx.strokeStyle = '#4F46E5';
                cctx.beginPath();
                const sliceWidth = canvas.width / bufferLength; let x=0;
                for(let i=0;i<bufferLength;i++){
                    const v = dataArray[i]/128.0; const y = v * canvas.height/2;
                    if(i===0) cctx.moveTo(x,y); else cctx.lineTo(x,y);
                    x += sliceWidth;
                }
                cctx.lineTo(canvas.width, canvas.height/2); cctx.stroke();
            }
            draw();
        }

        startBtn.addEventListener('click', async ()=>{
            try{
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                recordedChunks = [];
                mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.ondataavailable = (e)=>{ if(e.data && e.data.size>0) recordedChunks.push(e.data); };
                mediaRecorder.onstop = async ()=>{
                    const type = qualitySelect.value === 'wav' ? 'audio/webm' : 'audio/webm';
                    const ext = qualitySelect.value === 'wav' ? 'webm' : 'webm';
                    const blob = new Blob(recordedChunks, { type });
                    // keep for preview / re-record
                    lastBlob = blob;
                    // enable preview controls
                    playPreviewBtn.disabled = false; reRecordBtn.disabled = false; downloadBtn.style.display='inline-block';
                    const url = URL.createObjectURL(blob);
                    downloadBtn.href = url; downloadBtn.download = 'recording.' + ext;
                    // create audio element for preview
                    if(previewAudio){ try{ previewAudio.pause(); }catch(e){} }
                    previewAudio = new Audio(url);
                };
                mediaRecorder.start();
                // setup analyser
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioCtx.createAnalyser(); analyser.fftSize = 2048;
                sourceNode = audioCtx.createMediaStreamSource(stream);
                sourceNode.connect(analyser);
                startDrawing();
                // timer
                let seconds = 0; const timerEl = document.getElementById('recordTimer');
                const timerInterval = setInterval(()=>{ seconds++; const mm = String(Math.floor(seconds/60)).padStart(2,'0'); const ss = String(seconds%60).padStart(2,'0'); timerEl.textContent = mm+':'+ss; }, 1000);
                mediaRecorder._timerInterval = timerInterval;
                startBtn.disabled = true; stopBtn.disabled = false;
                document.getElementById('liveTranscript').textContent = 'جاري التسجيل...';
            } catch(err){ alert('تعذر الوصول للميكروفون: '+err.message); }
        });

        stopBtn.addEventListener('click', ()=>{
            if(mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
            // stop analyser and drawing
            if(drawId) cancelAnimationFrame(drawId);
            if(sourceNode) try{ sourceNode.disconnect(); }catch(e){}
            if(analyser) analyser.disconnect();
            if(audioCtx) audioCtx.close(); audioCtx=null; analyser=null; sourceNode=null;
            // clear timer
            if(mediaRecorder && mediaRecorder._timerInterval) clearInterval(mediaRecorder._timerInterval);
            document.getElementById('recordTimer').textContent = '00:00';
            cctx.clearRect(0,0,canvas.width,canvas.height);
            startBtn.disabled = false; stopBtn.disabled = true;
            document.getElementById('liveTranscript').textContent = '';
        });

        // Re-record (clear lastBlob and allow recording again)
        reRecordBtn.addEventListener('click', ()=>{
            lastBlob = null; playPreviewBtn.disabled = true; reRecordBtn.disabled = true; downloadBtn.style.display='none';
            if(previewAudio){ try{ previewAudio.pause(); }catch(e){} previewAudio=null; }
            document.getElementById('fileName').textContent = '';
        });

        // preview playback
        playPreviewBtn.addEventListener('click', ()=>{
            if(!previewAudio) return;
            if(previewAudio.paused){ previewAudio.play(); playPreviewBtn.textContent = 'إيقاف المعاينة'; }
            else { previewAudio.pause(); playPreviewBtn.textContent = 'تشغيل المعاينة'; }
        });

        async function sendAudioFile(file, filename=null){
            const fd = new FormData();
            if(filename) fd.append('audio', file, filename); else fd.append('audio', file);
            fd.append('language', document.getElementById('language').value);
            showLoading(true);
            try{
                const resp = await fetch(apiConvertAudio, { method:'POST', body: fd });
                const json = await resp.json();
                sequence = json.sequence || [];
                index = 0;
                // Put transcription into subtitle and into the main text input so user can edit/convert
                const transcription = json.transcription || '';
                document.getElementById('subtitle').textContent = transcription;
                try { document.getElementById('text').value = transcription; } catch (e) { /* ignore */ }
                renderSequence();
            }catch(e){ alert('خطأ في رفع الملف: '+e.message);}finally{ showLoading(false); }
        }

        // SpeechRecognition (browser) integration
        let recognition = null;
        const startRecBtn = document.getElementById('startRecognition');
        const stopRecBtn = document.getElementById('stopRecognition');
        if(window.SpeechRecognition || window.webkitSpeechRecognition){
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SR();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.onresult = (event)=>{
                let interim = '';
                let final = '';
                for(let i=event.resultIndex;i<event.results.length;i++){
                    const res = event.results[i];
                    if(res.isFinal) final += res[0].transcript + ' ';
                    else interim += res[0].transcript;
                }
                document.getElementById('liveTranscript').textContent = final + interim;
                // when final text appears, send to convert-text
                if(final.trim()){
                    document.getElementById('text').value = final.trim();
                    // call convert-text
                    (async ()=>{
                        const language = document.getElementById('language').value;
                        const resp = await fetch(apiConvertText, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({text: final.trim(), language})});
                        const json = await resp.json();
                        sequence = json.sequence || [];
                        if (window.collapseCharactersToWordSigns) {
                            try { sequence = await window.collapseCharactersToWordSigns(sequence, language); } catch(e){ console.warn('collapseCharactersToWordSigns failed', e); }
                        }
                        index=0; renderSequence();
                    })();
                }
            };
            recognition.onerror = (e)=>{ console.warn('Recognition error', e); };

            startRecBtn.addEventListener('click', ()=>{
                if(!recognition) return alert('التعرف الصوتي غير مدعوم في متصفحك');
                recognition.lang = document.getElementById('language').value === 'ar' ? 'ar-SA' : 'en-US';
                recognition.start(); startRecBtn.disabled=true; stopRecBtn.disabled=false; document.getElementById('liveTranscript').textContent='جاري التعرف...';
            });

            stopRecBtn.addEventListener('click', ()=>{ if(recognition) recognition.stop(); startRecBtn.disabled=false; stopRecBtn.disabled=true; });
        } else {
            startRecBtn.disabled = true; stopRecBtn.disabled = true; document.getElementById('liveTranscript').textContent = 'ميزة التعرف بالصوت غير متاحة';
        }

        function renderSequence(){
            const out = document.getElementById('output');
            out.innerHTML = '';
            const largeMode = document.querySelector('.output-section')?.classList.contains('large-sign-view');
            console.log('renderSequence: largeMode=', largeMode, 'sequenceLen=', sequence.length);

                if(largeMode){
                // show main big image and thumbnails
                out.innerHTML = '';
                const mainWrapper = document.createElement('div');
                mainWrapper.className = 'large-sign-wrapper';
                const mainImg = document.createElement('img');
                const firstSign = sequence[index] || sequence[0];
                mainImg.src = (firstSign && firstSign.src) ? firstSign.src : '/storage/signs/placeholder.png';
                mainImg.alt = (firstSign && firstSign.text) ? firstSign.text : '';
                mainImg.className = 'sign-image large';
                mainWrapper.appendChild(mainImg);
                out.appendChild(mainWrapper);
                const thumbs = document.createElement('div');
                thumbs.className = 'sign-thumbs';
                sequence.forEach((s,i)=>{
                    const t = document.createElement('img');
                    t.src = s.src || '/storage/signs/placeholder.png';
                    t.alt = s.text || '';
                    t.className = i===index ? 'sign-image thumb active' : 'sign-image thumb';
                    t.style.opacity = i === index ? '1' : '0.6';
                    t.addEventListener('click', ()=>{
                        index = i;
                        mainImg.src = t.src;
                        mainImg.alt = t.alt;
                        document.querySelectorAll('.sign-thumbs .sign-image.thumb').forEach((node, j)=>{ node.style.opacity = (j===i?1:0.6); });
                        updateProgress();
                    });
                    thumbs.appendChild(t);
                });
                out.appendChild(thumbs);
                // Safety: if mainWrapper missing for any reason, ensure we have a main image above thumbs
                if (!document.querySelector('#output .large-sign-wrapper')){
                    try{
                        const fallbackMainWrapper = document.createElement('div');
                        fallbackMainWrapper.className = 'large-sign-wrapper';
                        const fallbackImg = document.createElement('img');
                        fallbackImg.className = 'sign-image large';
                        fallbackImg.src = (sequence[0] && sequence[0].src) ? sequence[0].src : '/storage/signs/placeholder.png';
                        fallbackImg.alt = (sequence[0] && sequence[0].text) ? sequence[0].text : '';
                        fallbackMainWrapper.appendChild(fallbackImg);
                        out.insertBefore(fallbackMainWrapper, thumbs);
                    }catch(e){ console.warn('renderSequence fallback mainWrapper creation failed', e); }
                }
                // mark welcome card as having sequence (tells placeholder overlay to hide)
                try{ const card = document.querySelector('.welcome-card'); if(card) card.classList.add('has-sequence'); } catch(e){}
            } else {
                sequence.forEach((s,i)=>{
                    const img = document.createElement('img');
                    img.src = s.src || '/storage/signs/placeholder.png';
                    img.alt = s.text || '';
                    img.className = 'sign-image';
                    img.style.opacity = i===index?1:0.5;
                    out.appendChild(img);
                });
            }
            // If not in large mode, make sure welcome card doesn't claim to have a sequence (so placeholder shows)
            try{ const card = document.querySelector('.welcome-card'); if(card && !largeMode) card.classList.remove('has-sequence'); } catch(e){}

            updateProgress();
        }
        function updateProgress(){ document.getElementById('subtitle').textContent = (sequence[index] && sequence[index].text) || '' }

        document.getElementById('play').addEventListener('click', ()=>{ if(!sequence.length) return; playing=true; startPlayback(); });
        document.getElementById('pause').addEventListener('click', ()=>{ playing=false; stopPlayback(); });
        document.getElementById('speed').addEventListener('input', (e)=>{ speed = parseFloat(e.target.value); if(playing){ stopPlayback(); startPlayback(); } });
        function startPlayback(){
            stopPlayback();
            timer = setInterval(()=>{
                index = (index+1)%sequence.length;
                const largeMode = document.querySelector('.output-section')?.classList.contains('large-sign-view');
                if(largeMode){
                    try{
                        // Update main large image and thumbnails
                        const mainImg = document.querySelector('#output .large-sign-wrapper .sign-image.large');
                        if(mainImg) mainImg.src = sequence[index].src || '/storage/signs/placeholder.png';
                        const thumbImgs = document.querySelectorAll('#output .sign-image.thumb');
                        thumbImgs.forEach((im,i)=>{ im.style.opacity = i===index? '1' : '0.6'; });
                    }catch(e){ console.warn('startPlayback (largeMode) error', e); }
                }else{
                    const imgs=document.querySelectorAll('#output img'); imgs.forEach((im,i)=>im.style.opacity = i===index?1:0.4);
                }
                updateProgress();
            }, 1000/ speed);
        }
        function stopPlayback(){ if(timer) clearInterval(timer); timer=null; }

        if('serviceWorker' in navigator){ navigator.serviceWorker.register('/sw.js').catch(()=>{}); }

        // allow tapping the output to close the large centered view
        try{
            const outEl = document.getElementById('output');
            outEl && outEl.addEventListener('click', ()=>{
                const container = document.querySelector('.output-section');
                if(container && container.classList.contains('large-sign-view')){
                    container.classList.remove('large-sign-view');
                    renderSequence();
                }
            });
        }catch(e){}

        // Welcome frame toggle & close controls
        try{
            const toggleBtn = document.getElementById('welcomeFrameToggle');
            const closeBtn = document.getElementById('welcomeFrameClose');
            const outputSection = document.querySelector('.output-section');
            // initialize from preference
            if(toggleBtn){
                const pref = localStorage.getItem('aisl-welcome-large') === '1';
                toggleBtn.setAttribute('aria-pressed', pref ? 'true' : 'false');
                if(pref && outputSection) outputSection.classList.add('large-sign-view');
                toggleBtn.addEventListener('click', ()=>{
                    const pressed = toggleBtn.getAttribute('aria-pressed') === 'true';
                    const newPressed = !pressed;
                    toggleBtn.setAttribute('aria-pressed', newPressed ? 'true' : 'false');
                    try{ localStorage.setItem('aisl-welcome-large', newPressed ? '1' : '0'); } catch(e){}
                    if(outputSection){
                        if(newPressed) outputSection.classList.add('large-sign-view'); else outputSection.classList.remove('large-sign-view');
                        renderSequence();
                    }
                });
            }
            if(closeBtn){
                closeBtn.addEventListener('click', ()=>{
                    if(outputSection) outputSection.classList.remove('large-sign-view');
                    if(toggleBtn) toggleBtn.setAttribute('aria-pressed','false');
                    try{ localStorage.setItem('aisl-welcome-large','0'); } catch(e){}
                    renderSequence();
                });
            }
            // Allow closing with Escape key
            document.addEventListener('keydown', (e)=>{
                if (e.key === 'Escape'){
                    if(outputSection && outputSection.classList.contains('large-sign-view')){
                        outputSection.classList.remove('large-sign-view');
                        if(toggleBtn) toggleBtn.setAttribute('aria-pressed','false');
                        try{ localStorage.setItem('aisl-welcome-large','0'); } catch(e){}
                        renderSequence();
                    }
                }
            });
        }catch(e){ console.warn('Welcome frame controls init failed', e); }

    </script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\a_i_s_l_project\si\resources\views/welcome.blade.php ENDPATH**/ ?>