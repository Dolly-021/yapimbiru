{{-- ============================================================
     Chatbot Bubble Widget — SMK YAPIM BIRU-BIRU
     Include this partial in any layout that should show the bot:
       @include('partials.chatbot')
     ============================================================ --}}

<style>
  /* ---------- CSS Variables ---------- */
  :root {
    --cb-navy-950: #0b1730;
    --cb-navy-800: #122a52;
    --cb-blue-600: #2f5fe0;
    --cb-blue-500: #4272ef;
    --cb-sky-200:  #cfe8ff;
    --cb-ice-50:   #f1f6ff;
    --cb-white:    #ffffff;
    --cb-ink:      #1b2740;
    --cb-ink-soft: #5b6a86;
    --cb-line:     #dfe8fb;
    --cb-success:  #2fbf87;
    --cb-radius-lg: 20px;
    --cb-radius-md: 14px;
  }

  /* ---------- Floating Action Button ---------- */
  #sekolah-fab {
    position: fixed; right: 24px; bottom: 24px; z-index: 9998;
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(155deg, var(--cb-blue-500), var(--cb-navy-800));
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 10px 26px rgba(19,54,120,.38);
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s ease;
  }
  #sekolah-fab:hover { transform: translateY(-3px) scale(1.06); }
  #sekolah-fab .ic-chat, #sekolah-fab .ic-close { transition: opacity .18s ease, transform .18s ease; }
  #sekolah-fab .ic-chat { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
  #sekolah-fab .ic-close { width: 26px; height: 26px; position: absolute; opacity: 0; transform: rotate(-45deg) scale(.6); }
  #sekolah-fab.open .ic-chat  { opacity: 0; transform: rotate(45deg) scale(.6); }
  #sekolah-fab.open .ic-close { opacity: 1; transform: rotate(0) scale(1); }
  #sekolah-fab .ping {
    position: absolute; top: -3px; right: -3px; width: 14px; height: 14px; border-radius: 50%;
    background: var(--cb-success); border: 2.5px solid var(--cb-white);
  }
  #sekolah-fab .ping::after {
    content: ''; position: absolute; inset: -2px; border-radius: 50%;
    border: 2px solid var(--cb-success); opacity: .6;
    animation: cb-ping-ring 1.8s ease-out infinite;
  }
  @keyframes cb-ping-ring { from { transform: scale(.9); opacity: .7; } to { transform: scale(2.1); opacity: 0; } }

  /* ---------- Panel ---------- */
  #sekolah-panel {
    position: fixed; right: 24px; bottom: 110px; z-index: 9999;
    width: 376px; max-width: calc(100vw - 32px);
    height: 560px; max-height: calc(100vh - 140px);
    background: var(--cb-white); border-radius: var(--cb-radius-lg);
    box-shadow: 0 24px 60px rgba(11,23,48,.28);
    display: flex; flex-direction: column; overflow: hidden;
    transform: translateY(16px) scale(.97); opacity: 0; pointer-events: none;
    transition: transform .28s cubic-bezier(.2,.9,.3,1.1), opacity .22s ease;
  }
  #sekolah-panel.open { transform: translateY(0) scale(1); opacity: 1; pointer-events: auto; }

  /* Header */
  .sk-header {
    background: linear-gradient(120deg, var(--cb-navy-950) 0%, var(--cb-blue-600) 100%);
    padding: 18px 18px 16px; position: relative; overflow: hidden; flex-shrink: 0;
  }
  .sk-header::before {
    content: ''; position: absolute; width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.12), transparent 70%);
    top: -90px; right: -40px;
  }
  .sk-header-top { display: flex; align-items: center; gap: 12px; position: relative; }
  .sk-avatar {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    background: var(--cb-white); border: 1px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
  }
  .sk-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .sk-title { flex: 1; min-width: 0; }
  .sk-title h2 {
    margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700;
    color: var(--cb-white); letter-spacing: .1px;
  }
  .sk-status { display: flex; align-items: center; gap: 5px; margin-top: 2px; }
  .sk-status .dot { width: 6px; height: 6px; border-radius: 50%; background: #5eeaa8; }
  .sk-status span { font-size: 11.5px; color: var(--cb-sky-200); font-weight: 500; }
  .sk-chips { display: flex; gap: 7px; margin-top: 14px; overflow-x: auto; padding-bottom: 2px; position: relative; }
  .sk-chips::-webkit-scrollbar { display: none; }
  .sk-chip {
    flex-shrink: 0; font-size: 12px; font-weight: 600; color: var(--cb-white);
    background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.22);
    padding: 7px 12px; border-radius: 99px; cursor: pointer; white-space: nowrap;
    transition: background .15s ease, transform .15s ease;
  }
  .sk-chip:hover { background: rgba(255,255,255,.26); transform: translateY(-1px); }

  /* Messages */
  .sk-body { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; background: var(--cb-white); }
  .sk-body::-webkit-scrollbar { width: 6px; }
  .sk-body::-webkit-scrollbar-thumb { background: var(--cb-line); border-radius: 99px; }

  .cb-msg { display: flex; gap: 8px; max-width: 88%; animation: cb-msg-in .22s ease; }
  @keyframes cb-msg-in { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
  .cb-msg.bot  { align-self: flex-start; }
  .cb-msg.user { align-self: flex-end; flex-direction: row-reverse; }
  .cb-msg .bubble-avatar {
    width: 26px; height: 26px; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
  }
  .cb-msg.bot  .bubble-avatar { background: var(--cb-white); border: 1px solid var(--cb-line); overflow: hidden; }
  .cb-msg.bot  .bubble-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .cb-msg.user .bubble-avatar { background: var(--cb-navy-800); }
  .cb-msg.user .bubble-avatar svg { width: 13px; height: 13px; stroke: var(--cb-white); }
  .cb-bubble { padding: 10px 13px; border-radius: var(--cb-radius-md); font-size: 13.3px; line-height: 1.55; word-break: break-word; }
  .cb-msg.bot  .cb-bubble { background: var(--cb-ice-50); color: var(--cb-ink); border-top-left-radius: 4px; }
  .cb-msg.user .cb-bubble { background: linear-gradient(135deg, var(--cb-blue-500), var(--cb-blue-600)); color: var(--cb-white); border-top-right-radius: 4px; }
  .cb-bubble .cb-time { display: block; margin-top: 6px; font-size: 10.5px; opacity: .6; text-align: right; }
  
  /* Markdown Styles */
  .cb-bubble p { margin: 0 0 8px 0; }
  .cb-bubble p:last-of-type { margin-bottom: 0; }
  .cb-bubble table { border-collapse: collapse; width: 100%; margin: 8px 0; background: rgba(255,255,255,0.5); border-radius: 6px; overflow: hidden; }
  .cb-msg.user .cb-bubble table { background: rgba(0,0,0,0.1); }
  .cb-bubble th, .cb-bubble td { border: 1px solid rgba(0,0,0,0.1); padding: 6px 8px; text-align: left; font-size: 12.5px; }
  .cb-msg.user .cb-bubble th, .cb-msg.user .cb-bubble td { border-color: rgba(255,255,255,0.2); }
  .cb-bubble th { background: rgba(0,0,0,0.05); font-weight: 600; }
  .cb-msg.user .cb-bubble th { background: rgba(0,0,0,0.15); }
  .cb-bubble ul, .cb-bubble ol { margin: 0 0 8px 16px; padding: 0; }
  .cb-bubble li { margin-bottom: 4px; }
  .cb-bubble pre { background: rgba(0,0,0,0.05); padding: 8px; border-radius: 6px; overflow-x: auto; margin: 8px 0; }
  .cb-msg.user .cb-bubble pre { background: rgba(0,0,0,0.2); }
  .cb-bubble code { font-family: 'Courier New', Courier, monospace; background: rgba(0,0,0,0.06); padding: 2px 4px; border-radius: 4px; font-size: 12px; }
  .cb-msg.user .cb-bubble code { background: rgba(0,0,0,0.2); }
  .cb-bubble strong { font-weight: 700; }

  .cb-typing { display: inline-flex; gap: 4px; padding: 4px 0; }
  .cb-typing span { width: 6px; height: 6px; border-radius: 50%; background: var(--cb-ink-soft); opacity: .5; animation: cb-bounce 1.1s infinite; }
  .cb-typing span:nth-child(2) { animation-delay: .15s; }
  .cb-typing span:nth-child(3) { animation-delay: .3s; }
  @keyframes cb-bounce { 0%,60%,100% { transform: translateY(0); opacity: .4; } 30% { transform: translateY(-4px); opacity: 1; } }

  /* Input bar */
  .sk-inputbar {
    border-top: 1px solid var(--cb-line); padding: 11px 12px; display: flex; gap: 8px; align-items: flex-end;
    flex-shrink: 0; background: var(--cb-white);
  }
  .sk-inputbar textarea {
    flex: 1; resize: none; border: 1px solid var(--cb-line); border-radius: 14px; padding: 10px 13px;
    font-family: 'Inter', sans-serif; font-size: 13px; max-height: 80px; outline: none; line-height: 1.4;
    transition: border-color .15s ease;
  }
  .sk-inputbar textarea:focus { border-color: var(--cb-blue-500); }
  .sk-send {
    width: 38px; height: 38px; border-radius: 11px; border: none; cursor: pointer; flex-shrink: 0;
    background: linear-gradient(135deg, var(--cb-blue-500), var(--cb-navy-800));
    display: flex; align-items: center; justify-content: center;
    transition: transform .15s ease, opacity .15s ease;
  }
  .sk-send:disabled { opacity: .45; cursor: not-allowed; }
  .sk-send:not(:disabled):hover { transform: translateY(-2px); }
  .sk-send svg { width: 16px; height: 16px; stroke: var(--cb-white); }
  .sk-foot { text-align: center; font-size: 10px; color: #a6b3cc; padding: 6px 0 10px; }

  @media (max-width: 420px) {
    #sekolah-panel { right: 12px; left: 12px; width: auto; bottom: 88px; }
    #sekolah-fab   { right: 16px; bottom: 16px; }
  }
</style>

{{-- ============ FLOATING BUBBLE ============ --}}
<button id="sekolah-fab" aria-label="Buka chatbot sekolah">
  <span class="ping"></span>
  <img class="ic-chat" src="{{ asset('images/bot-logo.png') }}" alt="Chat Logo">
  <svg class="ic-close" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.3" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
</button>

{{-- ============ PANEL ============ --}}
<div id="sekolah-panel" role="dialog" aria-label="Chatbot Asisten Sekolah">
  <div class="sk-header">
    <div class="sk-header-top">
      <div class="sk-avatar">
        <img src="{{ asset('images/bot-logo.png') }}" alt="Asisten Sekolah">
      </div>
      <div class="sk-title">
        <h2>Asisten Sekolah</h2>
        <div class="sk-status"><span class="dot"></span><span>Online · siap bantu</span></div>
      </div>
    </div>
    <div class="sk-chips">
      <div class="sk-chip" data-q="Jam berapa sekolah mulai dan selesai?">🕒 Jam sekolah</div>
      <div class="sk-chip" data-q="Bagaimana cara melihat absensi saya?">🗓️ Cara cek absensi</div>
      <div class="sk-chip" data-q="Bagaimana cara melihat dan mengumpulkan tugas?">📚 Cara lihat tugas</div>
      <div class="sk-chip" data-q="Apa kontak dan alamat sekolah?">📍 Kontak sekolah</div>
    </div>
  </div>

  <div class="sk-body" id="sk-body"></div>

  <div class="sk-inputbar">
    <textarea id="sk-input" rows="1" placeholder="Tulis pertanyaan kamu..."></textarea>
    <button class="sk-send" id="sk-send" aria-label="Kirim">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
    </button>
  </div>
  <div class="sk-foot">Asisten Sekolah dapat membuat kesalahan. Cek info penting ke pihak sekolah.</div>
</div>

<!-- Load Marked.js for Markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
(function () {
  // ====== CONFIG ======
  // Endpoint Laravel — baca dari meta CSRF yang sudah ada di layout
  const API_ENDPOINT = "/api/chat";
  const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : '';

  // ====== FAQ rule-based (dicek dulu, hemat Groq) ======
  const FAQ = [
    {
      keys: ["jam sekolah","jam masuk","jam pulang","jam berapa"],
      a: "Jam sekolah biasanya berlangsung dari pukul <b>07.00 sampai 15.00 WIB</b> (Senin–Jumat). Untuk jadwal per kelas yang lebih detail, cek menu <b>Jadwal</b> di dashboard ya."
    },
    {
      keys: ["absen","absensi","kehadiran"],
      a: "Untuk cek absensi kamu: masuk ke menu <b>Absensi</b> di dashboard, lalu pilih rentang tanggal yang ingin dilihat. Rekap kehadiran per mapel juga tersedia di sana."
    },
    {
      keys: ["tugas","pr","assignment"],
      a: "Tugas bisa dilihat di menu <b>Tugas</b> pada dashboard. Klik tugas yang ingin dikumpulkan, lalu upload file lewat tombol <b>Kumpulkan</b> sebelum deadline."
    },
    {
      keys: ["kontak","alamat","hubungi","telepon","email sekolah"],
      a: "Untuk info kontak dan alamat resmi sekolah, silakan cek halaman <b>Kontak</b> di website, atau hubungi bagian <b>Tata Usaha (TU)</b> pada jam kerja."
    },
    {
      keys: ["login","masuk akun","lupa password","reset password"],
      a: "Kalau lupa password, klik <b>Lupa Password</b> di halaman login lalu ikuti instruksi reset lewat email terdaftar. Kalau masih gagal, hubungi admin sekolah ya."
    }
  ];

  function matchFaq(text) {
    const t = text.toLowerCase();
    for (const item of FAQ) {
      if (item.keys.some(k => t.includes(k))) return item.a;
    }
    return null;
  }

  // ====== DOM Refs ======
  const fab    = document.getElementById('sekolah-fab');
  const panel  = document.getElementById('sekolah-panel');
  const body   = document.getElementById('sk-body');
  const input  = document.getElementById('sk-input');
  const sendBtn= document.getElementById('sk-send');

  let opened = false;
  fab.addEventListener('click', () => {
    opened = !opened;
    fab.classList.toggle('open', opened);
    panel.classList.toggle('open', opened);
    if (opened && body.children.length === 0) {
      addBotMessage("Halo! 👋 Aku <b>Asisten Sekolah YAPIM BIRU-BIRU</b>. Aku bisa bantu jawab pertanyaan seputar jadwal, absensi, tugas, atau info umum sekolah. Mau tanya apa hari ini?");
    }
    if (opened) setTimeout(() => input.focus(), 320);
  });

  // ====== Helpers ======
  function nowTime() {
    return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
  }
  function scrollDown() { body.scrollTop = body.scrollHeight; }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  function addUserMessage(text) {
    const el = document.createElement('div');
    el.className = 'cb-msg user';
    el.innerHTML = `
      <div class="bubble-avatar"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></div>
      <div class="cb-bubble">${escapeHtml(text)}<span class="cb-time">${nowTime()}</span></div>`;
    body.appendChild(el);
    scrollDown();
  }

  const botAvatarHtml = `<img src="{{ asset('images/bot-logo.png') }}" alt="Bot">`;

  function addBotMessage(html) {
    const el = document.createElement('div');
    el.className = 'cb-msg bot';
    el.innerHTML = `
      <div class="bubble-avatar">${botAvatarHtml}</div>
      <div class="cb-bubble">${html}<span class="cb-time">${nowTime()}</span></div>`;
    body.appendChild(el);
    scrollDown();
  }

  function addTyping() {
    const el = document.createElement('div');
    el.className = 'cb-msg bot';
    el.id = 'sk-typing';
    el.innerHTML = `
      <div class="bubble-avatar">${botAvatarHtml}</div>
      <div class="cb-bubble"><div class="cb-typing"><span></span><span></span><span></span></div></div>`;
    body.appendChild(el);
    scrollDown();
  }
  function removeTyping() { const t = document.getElementById('sk-typing'); if (t) t.remove(); }

  // ====== Chat History ======
  const chatHistory = [];

  async function callBackend(userText) {
    chatHistory.push({ role: 'user', content: userText });
    try {
      const res = await fetch(API_ENDPOINT, {
        method:  'POST',
        headers: {
          'Content-Type':  'application/json',
          'X-CSRF-TOKEN':  CSRF_TOKEN,
          'Accept':        'application/json',
        },
        body: JSON.stringify({ messages: chatHistory.slice(-10) })
      });

      if (!res.ok) {
        const errData = await res.json().catch(() => ({}));
        return errData.reply || `Maaf, ada gangguan server (kode ${res.status}). Coba lagi ya.`;
      }

      const data  = await res.json();
      const reply = (data.reply || '').trim() || 'Maaf, aku belum dapat jawaban untuk itu.';
      chatHistory.push({ role: 'assistant', content: reply });
      return reply;
    } catch (err) {
      return 'Koneksi ke server gagal. Pastikan internet kamu aktif ya.';
    }
  }

  async function handleSend(text) {
    if (!text.trim()) return;
    sendBtn.disabled = true;
    addUserMessage(text);
    input.value = '';
    autoResize();

    // Cek FAQ dulu (hemat API Groq)
    const faqAnswer = matchFaq(text);
    if (faqAnswer) {
      addTyping();
      setTimeout(() => { removeTyping(); addBotMessage(marked.parse(faqAnswer)); sendBtn.disabled = false; }, 480);
      return;
    }

    addTyping();
    const reply = await callBackend(text);
    removeTyping();
    addBotMessage(marked.parse(reply));
    sendBtn.disabled = false;
  }

  sendBtn.addEventListener('click', () => handleSend(input.value));
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(input.value); }
  });
  function autoResize() { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 80) + 'px'; }
  input.addEventListener('input', autoResize);

  document.querySelectorAll('.sk-chip').forEach(chip => {
    chip.addEventListener('click', () => handleSend(chip.dataset.q));
  });
})();
</script>
