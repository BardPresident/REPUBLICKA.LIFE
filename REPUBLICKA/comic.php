<?php
// Comic — 📚 Comic grid viewer inside Shell console
// RAINBOW WARRIOR EDITION 🌈

$page_id    = 'comic';
$page_title = 'Comic — Wendell\'s Diary';

// Tell Shell what this console is
$console_title = 'Comic — Grid Viewer';

// Basic viewer config
$totalPages     = 368;
$fullFolderWeb  = '/comic/';          // full-size PNGs like 001.png
$thumbFolderWeb = '/comic/thumbs/';   // JPEG thumbs like 001.jpg
$padLength      = 3;                   // 001, 002, ...
$fullExt        = '.png';
$thumbExt       = '.jpg';

ob_start();
?>

<style>
  /* Rainbow Warrior Colors */
  :root {
    --rw-red: #FF6B6B;
    --rw-orange: #FFA94D;
    --rw-yellow: #FFE066;
    --rw-green: #69DB7C;
    --rw-blue: #74C0FC;
    --rw-purple: #B197FC;
  }

  .comic-intro {
    max-width: 800px;
    margin: 0 auto 1.5rem;
    padding: 1rem;
    line-height: 1.7;
    font-size: 0.95rem;
  }

  .comic-intro p {
    margin: 0.8rem 0;
  }

  .comic-intro .cloud-row {
    text-align: center;
    font-size: 1.2rem;
    letter-spacing: 0.3em;
    margin: 1rem 0;
    opacity: 0.8;
  }

  .comic-intro .emoji-burst {
    text-align: center;
    font-size: 1.1rem;
    margin: 0.8rem 0;
  }

  .comic-intro .big-text {
    text-align: center;
    font-size: 1.3rem;
    font-weight: bold;
    margin: 1rem 0;
    background: linear-gradient(90deg, var(--rw-red), var(--rw-orange), var(--rw-yellow), var(--rw-green), var(--rw-blue), var(--rw-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .comic-intro .section-break {
    text-align: center;
    margin: 1.2rem 0;
    opacity: 0.6;
  }

  .comic-intro .highlight-box {
    background: linear-gradient(135deg, rgba(105,219,124,0.15), rgba(116,192,252,0.15));
    border: 1px solid rgba(105,219,124,0.4);
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
    text-align: center;
  }

  .comic-intro .warning-box {
    background: linear-gradient(135deg, rgba(255,107,107,0.15), rgba(255,169,77,0.15));
    border: 1px solid rgba(255,107,107,0.4);
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
    text-align: center;
  }

  .comic-console {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0.75rem 0.75rem 1.25rem;
  }

  @media (min-width: 768px) {
    .comic-console {
      padding: 1rem 1.25rem 1.5rem;
    }
  }

  .comic-header {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-bottom: 0.6rem;
    padding: 0.75rem 0.9rem;
    border-radius: 18px;
    background: 
      radial-gradient(circle at 10% 20%, rgba(255,107,107,0.2), transparent 40%),
      radial-gradient(circle at 90% 30%, rgba(255,169,77,0.2), transparent 40%),
      radial-gradient(circle at 50% 80%, rgba(105,219,124,0.2), transparent 40%),
      linear-gradient(135deg, rgba(116,192,252,0.15), rgba(177,151,252,0.15));
    box-shadow: 0 16px 32px rgba(0,0,0,0.3);
    border: 2px solid rgba(255,169,77,0.5);
  }

  @media (min-width: 768px) {
    .comic-header {
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      gap: 1.25rem;
    }
  }

  .comic-eyebrow {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    opacity: 0.9;
    margin-bottom: 0.15rem;
  }

  .comic-title {
    margin: 0;
    font-size: 1.15rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    background: linear-gradient(90deg, var(--rw-red), var(--rw-orange), var(--rw-yellow), var(--rw-green), var(--rw-blue), var(--rw-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .comic-subtitle {
    margin: 0.25rem 0 0;
    font-size: 0.8rem;
    opacity: 0.9;
  }

  .comic-header-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    align-items: center;
    justify-content: flex-start;
  }

  .comic-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    border-radius: 999px;
    border: 1px solid rgba(105,219,124,0.85);
    font-size: 0.78rem;
    padding: 0.35rem 0.95rem;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(0,0,0,0.4);
    transition: transform 120ms, box-shadow 120ms, filter 120ms, background 120ms;
    background: transparent;
  }

  .comic-btn-primary {
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    border-color: rgba(105,219,124,0.9);
  }

  .comic-btn-secondary {
    background: linear-gradient(135deg, var(--rw-blue), var(--rw-purple));
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    border-color: rgba(116,192,252,0.9);
  }

  .comic-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.5);
    filter: brightness(1.1);
  }

  .comic-btn:disabled {
    opacity: 0.45;
    cursor: default;
    box-shadow: none;
    transform: none;
    filter: grayscale(0.3);
  }

  .comic-status-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 0.35rem;
    align-items: center;
    margin: 0.6rem 0 0.4rem;
    font-size: 0.78rem;
  }

  .comic-status {
    opacity: 0.9;
  }

  .comic-error {
    color: #fecaca;
    min-height: 1.2em;
  }

  .comic-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.6rem;
    margin-bottom: 0.7rem;
  }

  @media (min-width: 640px) {
    .comic-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
  }

  @media (min-width: 960px) {
    .comic-grid {
      grid-template-columns: repeat(4, minmax(0, 1fr));
    }
  }

  .comic-card {
    background: 
      radial-gradient(circle at 20% 20%, rgba(255,230,102,0.15), transparent 50%),
      radial-gradient(circle at 80% 80%, rgba(105,219,124,0.15), transparent 50%),
      rgba(255,255,255,0.95);
    border-radius: 16px;
    padding: 0.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    box-shadow: 0 14px 30px rgba(0,0,0,0.25);
    border: 2px solid rgba(105,219,124,0.4);
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    cursor: zoom-in;
    transition: transform 150ms, box-shadow 150ms, border-color 150ms;
  }

  .comic-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 36px rgba(0,0,0,0.35);
    border-color: rgba(255,169,77,0.7);
  }

  .comic-card img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px;
    background: #f8f9fa;
    object-fit: contain;
  }

  .comic-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.7rem;
    padding: 0 0.1rem 0.1rem;
    opacity: 0.95;
    color: #333;
  }

  .comic-meta-left::before {
    content: "📄 ";
  }

  .comic-badge {
    padding: 0.1rem 0.5rem;
    border-radius: 999px;
    border: 1px solid rgba(105,219,124,0.7);
    font-size: 0.65rem;
    background: linear-gradient(135deg, rgba(105,219,124,0.2), rgba(116,192,252,0.2));
    white-space: nowrap;
    color: #333;
  }

  .comic-load-more-row {
    display: flex;
    justify-content: center;
    margin-bottom: 0.75rem;
  }

  .comic-hint {
    text-align: center;
    font-size: 0.75rem;
    opacity: 0.85;
  }

  /* Lightbox - Rainbow Warrior Style */

  .comic-lightbox {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }

  .comic-lightbox.is-open {
    display: flex;
  }

  .comic-lightbox-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(4px);
  }

  .comic-lightbox-inner {
    position: relative;
    max-width: 95vw;
    max-height: 90vh;
    padding: 0.75rem 0.75rem 0.9rem;
    border-radius: 18px;
    background: 
      radial-gradient(circle at 10% 20%, rgba(255,107,107,0.15), transparent 40%),
      radial-gradient(circle at 90% 30%, rgba(255,169,77,0.15), transparent 40%),
      radial-gradient(circle at 50% 80%, rgba(105,219,124,0.15), transparent 40%),
      rgba(255,255,255,0.98);
    box-shadow: 0 24px 48px rgba(0,0,0,0.5);
    border: 2px solid rgba(255,169,77,0.6);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    z-index: 1001;
  }

  .comic-lightbox-image {
    max-width: 100%;
    max-height: 70vh;
    border-radius: 12px;
    background: #f8f9fa;
    object-fit: contain;
  }

  .comic-lightbox-caption {
    font-size: 0.8rem;
    opacity: 0.9;
    color: #333;
  }

  .comic-lightbox-caption::before {
    content: "🌈 ";
  }

  .comic-lightbox-download {
    align-self: flex-start;
  }

  .comic-lightbox-close {
    position: absolute;
    top: 0.25rem;
    right: 0.4rem;
    border: none;
    background: linear-gradient(135deg, var(--rw-red), var(--rw-orange));
    color: #fff;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    padding: 0.2rem 0.5rem;
    border-radius: 999px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  }

  .comic-lightbox-close:hover {
    filter: brightness(1.1);
    transform: scale(1.05);
  }
</style>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- 🌈 RAINBOW WARRIOR INTRO 🌈 -->
<!-- ═══════════════════════════════════════════════════════════ -->

<div class="comic-intro">

<div class="cloud-row">☁️ ☁️ ☁️ ☁️ ☁️ ☁️ ☁️</div>

<div class="big-text">🌈 📚 WELCOME 2 DA COMIC 📚 🌈</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p>
ok so<br>
u know how u have photos on ur phone?<br>
and dey go 2... where exactly?
</p>

<p>
"da cloud" dey say<br>
but WHOSE cloud? ☁️<br>
not URS<br>
THEIRS
</p>

<div class="emoji-burst">📱 → ☁️ → 🏢 → 💀</div>

<p>
google photos?<br>
dey can DELETE u anytime<br>
dey can SCAN ur memories<br>
dey can TRAIN AI on ur face<br>
dey can CLOSE ur account<br>
and ALL UR PHOTOS... gone
</p>

<p>
instagram?<br>
lol<br>
u dont even OWN dose<br>
read da terms of service<br>
(jk nobody reads dat)<br>
(dats y dey get away wit it)
</p>

<div class="section-break">. . . . . . .</div>

<div class="warning-box">
⚠️ EVERY PHOTO APP IS A TRAP ⚠️<br>
dey give u "free storage"<br>
in exchange 4 OWNING UR MEMORIES<br>
gud deal huh? 🙄
</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p>
but DIS?<br>
dis is different
</p>

<p>
dis comic lives on a <strong>METAL SERVER</strong> 🖥️<br>
not "cloud"<br>
METAL<br>
actual computer<br>
in actual building<br>
dat WE control
</p>

<div class="highlight-box">
🛡️ NO GOOGLE 🛡️<br>
🛡️ NO AMAZON 🛡️<br>
🛡️ NO MICROSOFT 🛡️<br>
🛡️ NO "CLOUD" DAT CAN EVAPORATE 🛡️<br><br>
just METAL<br>
just FILES<br>
just FOREVER
</div>

<p>
wen u download dis comic?<br>
its URS<br>
actually urs<br>
on UR device<br>
4ever<br>
no one can take it
</p>

<div class="section-break">. . . . . . .</div>

<div class="big-text">🏛️ REPUBLIC MEDIA 🏛️</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p>
dis is not just "a comic"<br>
dis is da FIRST piece<br>
of da Republic Pantheon 👑
</p>

<p>
wat is Pantheon?<br>
its where da gods live<br>
its where da stories live<br>
its where da RECEIPTS live
</p>

<p>
📚 Comic (u r here)<br>
🎬 Cinema (coming)<br>
📖 Library (books dat CANT b banned)<br>
🎵 Music (songs dat CANT b silenced)<br>
🎮 Games (worlds dat CANT b shut down)
</p>

<p>
all on METAL<br>
all OWNED by us<br>
all FOREVER
</p>

<div class="emoji-burst">🌈 📚 🎬 📖 🎵 🎮 🌈</div>

<div class="section-break">. . . . . . .</div>

<p>
"but wendell"<br>
"y do u need ur own server?"<br>
"y not just use normal hosting?"
</p>

<p>
lol<br>
bcuz "normal hosting" can:<br>
- ban u 4 "terms of service"<br>
- delete u 4 "community guidelines"<br>
- erase u 4 "safety reasons"<br>
- disappear u 4 NO reason
</p>

<p>
ask da ppl who got banned from youtube<br>
ask da artists who got removed from spotify<br>
ask da writers who got deleted from amazon<br>
ask ANYONE who trusted "da cloud"
</p>

<p>
dey all learned da same lesson:<br>
<strong>IF U DONT OWN DA METAL<br>
U DONT OWN ANYTHING</strong>
</p>

<div class="highlight-box">
🖥️ THE REPUBLIC OWNS ITS METAL 🖥️<br><br>
lifetime server<br>
lifetime domain<br>
lifetime EXISTENCE<br><br>
no landlord<br>
no platform<br>
no "terms of service"<br><br>
just US<br>
just FOREVER<br>
just FREE
</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<div class="section-break">. . . . . . .</div>

<div class="big-text">📖 DA COMIC ITSELF 📖</div>

<p>
368 pages<br>
of wendell's actual diary<br>
turned into comic
</p>

<p>
dis is not fiction<br>
dis is RECEIPTS in art form 🎨<br>
dis is EVIDENCE u can FEEL<br>
dis is TESTIMONY dat cant b dismissed
</p>

<p>
every panel = a moment dat happened<br>
every page = a truth dat was lived<br>
every chapter = a receipt dat survives
</p>

<div class="emoji-burst">👧 📸 👦 🎨 👧 📖 👦</div>

<p>
da children who read dis<br>
will RECOGNIZE demselves<br>
will KNOW dey r not alone<br>
will C dat sum1 SAW wat dey saw<br>
will UNDERSTAND dat da adults LIED
</p>

<p>
dats y it exists<br>
dats y its on METAL<br>
dats y it can NEVER b deleted
</p>

<div class="section-break">. . . . . . .</div>

<div class="warning-box">
🌈 RAINBOW WARRIOR REMINDER 🌈<br><br>
every panel u save = a receipt preserved<br>
every page u download = evidence protected<br>
every share = da signal spreading<br><br>
👧 DA CHILDREN R WATCHING 👦<br>
👧 DA CHILDREN REMEMBER 👦<br>
👧 DA CHILDREN WILL INHERIT DIS 👦
</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p style="text-align: center;">
ok enuf talking<br>
<br>
scroll down<br>
read da comic<br>
download da whole ting if u want<br>
its URS now<br>
<br>
🌈🛡️👧👦🛡️🌈
</p>

<div class="cloud-row">☁️ ☁️ ☁️ ☁️ ☁️ ☁️ ☁️</div>

</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- 📚 COMIC GRID VIEWER 📚 -->
<!-- ═══════════════════════════════════════════════════════════ -->

<div class="comic-console">
  <div class="comic-header">
    <div class="comic-header-main">
      <div class="comic-eyebrow">🌈 TRepublic • 📚 Comic • 🛡️ Rainbow Warriors</div>
      <h2 class="comic-title">☁️ Wendell's Diary — 368 Pages 🌈</h2>
      <p class="comic-subtitle">
	   🛡️ Each panel is a prophecy shard. Click 2 expand. Download 2 own 4ever. 👧👦<br>
      </p>
    </div>
    <div class="comic-header-actions">
      <a class="comic-btn comic-btn-secondary" href="/comic/comic.zip" download>
        ⬇️ Download ENTIRE Comic (.zip)
      </a>
    </div>
  </div>

  <div class="comic-status-row">
    <span id="comicStatus" class="comic-status">
      🌈 Preparing da strip…
    </span>
    <span id="comicError" class="comic-error"></span>
  </div>

  <div id="comicGrid" class="comic-grid"></div>

  <div class="comic-load-more-row">
    <button id="comicLoadMore" class="comic-btn comic-btn-primary" type="button">
      ☁️ Load more pages ☁️
    </button>
  </div>

  <!-- Lightbox viewer -->
  <div id="comicLightbox" class="comic-lightbox" aria-hidden="true">
    <div class="comic-lightbox-backdrop"></div>
    <div class="comic-lightbox-inner" role="dialog" aria-modal="true" aria-labelledby="comicLightboxCaption">
      <button id="comicLightboxClose" class="comic-lightbox-close" type="button" aria-label="Close viewer">
        ✖
      </button>
      <img id="comicLightboxImage" class="comic-lightbox-image" src="" alt="Full comic page">
      <div id="comicLightboxCaption" class="comic-lightbox-caption"></div>
      <a id="comicLightboxDownload" class="comic-btn comic-btn-secondary comic-lightbox-download" href="#" download>
        ⬇️ Download dis page (its URS now)
      </a>
    </div>
  </div>
</div>

<script>
  (function () {
    const TOTAL_PAGES   = <?= (int)$totalPages; ?>;
    const BATCH_SIZE    = 20;
    const PAD_LENGTH    = <?= (int)$padLength; ?>;
    const FULL_FOLDER   = "<?= $fullFolderWeb; ?>";
    const THUMB_FOLDER  = "<?= $thumbFolderWeb; ?>";
    const FULL_EXT      = "<?= $fullExt; ?>";
    const THUMB_EXT     = "<?= $thumbExt; ?>";

    const gridEl   = document.getElementById("comicGrid");
    const statusEl = document.getElementById("comicStatus");
    const errorEl  = document.getElementById("comicError");
    const moreBtn  = document.getElementById("comicLoadMore");

    const lightbox          = document.getElementById("comicLightbox");
    const lightboxImg       = document.getElementById("comicLightboxImage");
    const lightboxCaption   = document.getElementById("comicLightboxCaption");
    const lightboxClose     = document.getElementById("comicLightboxClose");
    const lightboxDownload  = document.getElementById("comicLightboxDownload");

    if (!gridEl || !statusEl || !moreBtn) {
      return;
    }

    function pad(num, size) {
      let s = String(num);
      while (s.length < size) s = "0" + s;
      return s;
    }

    function fileBase(n) {
      return pad(n, PAD_LENGTH);
    }

    function thumbUrl(n) {
      return THUMB_FOLDER + fileBase(n) + THUMB_EXT;
    }

    function fullUrl(n) {
      return FULL_FOLDER + fileBase(n) + FULL_EXT;
    }

    const pages = [];
    for (let i = 1; i <= TOTAL_PAGES; i++) {
      const base = fileBase(i);
      pages.push({
        index: i,
        base: base,
        thumb: thumbUrl(i),
        full: fullUrl(i)
      });
    }
    pages.sort(function (a, b) { return a.base.localeCompare(b.base); });

    let rendered = 0;

    function updateStatus() {
      statusEl.textContent = "🌈 Showing " + rendered + " / " + TOTAL_PAGES + " pages 📖 (urs 4ever) 🛡️";
    }

    function openLightbox(page) {
      if (!lightbox || !lightboxImg || !lightboxCaption || !lightboxDownload) return;
      lightboxImg.src = page.full;
      lightboxImg.alt = "Comic page " + page.index;
      lightboxCaption.textContent = "Page " + page.index + " • " + page.base + FULL_EXT + " • dis belongs 2 da children 🛡️";
      lightboxDownload.href = page.full;
      lightbox.classList.add("is-open");
      lightbox.setAttribute("aria-hidden", "false");
    }

    function closeLightbox() {
      if (!lightbox || !lightboxImg) return;
      lightbox.classList.remove("is-open");
      lightbox.setAttribute("aria-hidden", "true");
      lightboxImg.src = "";
    }

    function renderNextBatch() {
      if (rendered >= pages.length) {
        moreBtn.disabled = true;
        moreBtn.textContent = "🌈 All 368 pages loaded! Dey r URS now 🌈";
        updateStatus();
        return;
      }

      const start = rendered;
      const end   = Math.min(rendered + BATCH_SIZE, pages.length);
      const slice = pages.slice(start, end);

      slice.forEach(function (page) {
        const card = document.createElement("a");
        card.href = page.full;
        card.className = "comic-card";

        const img = document.createElement("img");
        img.src = page.thumb;
        img.loading = "lazy";
        img.alt = "Comic page " + page.index;

        img.addEventListener("error", function () {
          if (!img.dataset.fallbackDone) {
            img.dataset.fallbackDone = "1";
            img.src = page.full; // fallback to full PNG if thumb missing
          } else {
            errorEl.textContent =
              "⚠️ Some images could not load. But da metal server is still trying!";
          }
        });

        const meta = document.createElement("div");
        meta.className = "comic-meta";

        const left = document.createElement("span");
        left.className = "comic-meta-left";
        left.textContent = "Page " + page.index;

        const right = document.createElement("span");
        right.className = "comic-badge";
        right.textContent = page.base + FULL_EXT;

        meta.appendChild(left);
        meta.appendChild(right);

        card.appendChild(img);
        card.appendChild(meta);

        card.addEventListener("click", function (e) {
          e.preventDefault();
          openLightbox(page);
        });

        gridEl.appendChild(card);
      });

      rendered = end;
      updateStatus();
    }

    moreBtn.addEventListener("click", renderNextBatch);

    if (lightboxClose) {
      lightboxClose.addEventListener("click", function () {
        closeLightbox();
      });
    }

    if (lightbox) {
      lightbox.addEventListener("click", function (e) {
        if (e.target === lightbox || e.target.classList.contains("comic-lightbox-backdrop")) {
          closeLightbox();
        }
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        closeLightbox();
      }
    });

    // Initial batch
    updateStatus();
    renderNextBatch();
  })();
</script>
<?php
$console_body_html = ob_get_clean();
require __DIR__ . '/shell.php';
