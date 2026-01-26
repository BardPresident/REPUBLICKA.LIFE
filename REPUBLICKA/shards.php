<?php
// ============================================================================
// Shards GOLD — Scroll Shard Desktop for The Republic 💎
// Location: /shards.php
//
// Roles:
//   • Royal Emblem strip (from /emblems/) — oldest → newest
//   • Citizen Shard Desktop (from /shards/) — newest → oldest
//   • Citizen Upload Dock → /shards/mod/ for manual approval
//
//   No databases. No external hosts. Filesystem is law.
//   Move a file from /shards/mod/ → /shards/ to approve it.
//
// ============================================================================


// ---------------------------------------------------------------------------
// 0. BASIC CONFIG (ROOT-LEVEL EMBLEMS)
// ---------------------------------------------------------------------------

$SHARD_DIR        = __DIR__ . '/shards';
$SHARD_EMBLEM_DIR = __DIR__ . '/emblems';
$SHARD_MOD_DIR    = __DIR__ . '/shards/mod';

$SHARD_URL_BASE        = '/shards';
$SHARD_EMBLEM_URL_BASE = '/emblems';
$SHARD_MOD_URL_BASE    = '/shards/mod';

@is_dir($SHARD_DIR)        || @mkdir($SHARD_DIR, 0755, true);
@is_dir($SHARD_EMBLEM_DIR) || @mkdir($SHARD_EMBLEM_DIR, 0755, true);
@is_dir($SHARD_MOD_DIR)    || @mkdir($SHARD_MOD_DIR, 0755, true);


// ---------------------------------------------------------------------------
// 1. HELPERS
// ---------------------------------------------------------------------------

function trep_slugify($str) {
    $str = strtolower(trim($str));
    $str = preg_replace('~[^\pL0-9]+~u', '-', $str);
    $str = trim($str, '-');
    $str = preg_replace('~-+~', '-', $str);
    return $str === '' ? 'shard' : $str;
}

/**
 * Parse shard filename into title/author/date.
 *
 * Preferred pattern:
 *   YYYYMMDD-title--by-author.ext
 */
function trep_parse_shard_meta($path, $defaultAuthor = 'The Republic') {
    $filename = basename($path);
    $name     = pathinfo($filename, PATHINFO_FILENAME);

    $title     = 'Untitled Shard';
    $author    = $defaultAuthor;
    $published = '';

    if (preg_match('/^(\d{8})-(.+?)--by-(.+)$/i', $name, $m)) {
        $dateRaw  = $m[1];
        $titleRaw = $m[2];
        $authRaw  = $m[3];

        $title  = ucwords(str_replace('-', ' ', $titleRaw));
        $author = ucwords(str_replace('-', ' ', $authRaw));

        $dt = DateTime::createFromFormat('Ymd', $dateRaw);
        if ($dt) {
            $published = $dt->format('Y-m-d');
        }
    } else {
        if (preg_match('/^(\d{4})(\d{2})(\d{2})[-_]+(.+)$/', $name, $m)) {
            $y = $m[1]; $mo = $m[2]; $d = $m[3];
            $rest = $m[4];
            $title = ucwords(str_replace(['-', '_'], ' ', $rest));
            $published = sprintf('%04d-%02d-%02d', $y, $mo, $d);
        } else {
            $pretty = preg_replace('/[_\-]+/', ' ', $name);
            $title  = ucwords(trim($pretty));
        }
        $author = $defaultAuthor;
    }

    if ($published === '') {
        $ts = @filemtime($path);
        if ($ts) {
            $published = gmdate('Y-m-d', $ts);
        }
    }

    return [
        'file'      => $filename,
        'title'     => $title,
        'author'    => $author,
        'published' => $published,
    ];
}

function trep_build_shard_cards(array $files, $urlBase, $defaultAuthor, $kind = 'citizen') {
    if (empty($files)) {
        return '<div class="shards-empty">No shards in this deck yet.</div>';
    }

    $html = '';
    foreach ($files as $path) {
        $meta   = trep_parse_shard_meta($path, $defaultAuthor);
        $file   = htmlspecialchars($meta['file'], ENT_QUOTES, 'UTF-8');
        $src    = htmlspecialchars($urlBase . '/' . $meta['file'], ENT_QUOTES, 'UTF-8');
        $title  = htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($meta['author'], ENT_QUOTES, 'UTF-8');
        $pub    = htmlspecialchars($meta['published'], ENT_QUOTES, 'UTF-8');

        $html .= '<article class="shard-card" data-kind="' . $kind . '"'
              .           ' data-src="' . $src . '"'
              .           ' data-title="' . $title . '"'
              .           ' data-meta="' . htmlspecialchars($metaLine, ENT_QUOTES, 'UTF-8') . '">'
              .   '<div class="shard-thumb-shell">'
              .     '<div class="shard-thumb-inner">'
              .       '<img src="' . $src . '" alt="' . $title . '" loading="lazy">'
              .     '</div>'
              .   '</div>'
              .   '<div class="shard-caption">'
              .     '<div class="shard-caption-title">' . $title . '</div>'
              .     '<div class="shard-caption-meta">' . htmlspecialchars($metaLine, ENT_QUOTES, 'UTF-8') . '</div>'
              .   '</div>'
              . '</article>';
    }

    return $html;
}


// ---------------------------------------------------------------------------
// 2. HANDLE UPLOAD → /shards/mod/
// ---------------------------------------------------------------------------

$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shard_submit'])) {
    $title  = trim($_POST['shard_title'] ?? '');
    $credit = trim($_POST['shard_credit'] ?? '');

    if (!isset($_FILES['shard_file']) || !is_uploaded_file($_FILES['shard_file']['tmp_name'])) {
        $upload_message = '⚠️ No shard image uploaded.';
    } else {
        $file = $_FILES['shard_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_message = '⚠️ Upload error code ' . (int)$file['error'] . '.';
        } else {
            $maxBytes = 40 * 1024 * 1024; // 40 MB
            if ($file['size'] > $maxBytes) {
                $upload_message = '⚠️ Shard is too large. Please keep it under ~40 MB.';
            } else {
                $finfo = @getimagesize($file['tmp_name']);
                if (!$finfo) {
                    $upload_message = '⚠️ File is not a recognised image.';
                } else {
                    $mime = $finfo['mime'] ?? '';
                    $ext  = '.jpg';
                    if ($mime === 'image/png')  $ext = '.png';
                    if ($mime === 'image/webp') $ext = '.webp';

                    $datePart   = gmdate('Ymd');
                    $titleSlug  = trep_slugify($title !== '' ? $title : 'citizen-shard');
                    $creditSlug = trep_slugify($credit !== '' ? $credit : 'anonymous-citizen');

                    $baseName = $datePart . '-' . $titleSlug . '--by-' . $creditSlug;
                    $target   = $SHARD_MOD_DIR . '/' . $baseName . $ext;

                    $i = 1;
                    while (file_exists($target)) {
                        $target = $SHARD_MOD_DIR . '/' . $baseName . '-' . $i . $ext;
                        $i++;
                    }

                    if (@move_uploaded_file($file['tmp_name'], $target)) {
                        $upload_message =
                            '✅ Shard received and stored for review as '
                            . basename($target)
                            . '. Move it into /shards/ to approve.';
                    } else {
                        $upload_message = '⚠️ Could not move shard into /shards/mod/.';
                    }
                }
            }
        }
    }
}

$upload_message_html = '';
if ($upload_message !== '') {
    $upload_message_html =
        '<div class="shards-upload-message">'
      . htmlspecialchars($upload_message, ENT_QUOTES, 'UTF-8')
      . '</div>';
}


// ---------------------------------------------------------------------------
// 3. DISCOVER SHARDS FROM FILESYSTEM
// ---------------------------------------------------------------------------

// Emblems strip: oldest → newest
$emblem_files = [];
if (is_dir($SHARD_EMBLEM_DIR)) {
    $emblem_files = glob($SHARD_EMBLEM_DIR . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
    usort($emblem_files, function($a, $b) {
        return filemtime($a) <=> filemtime($b);
    });
}
$emblem_cards_html = trep_build_shard_cards($emblem_files, $SHARD_EMBLEM_URL_BASE, 'The Republic', 'emblem');

// Citizen shards: newest → oldest
$citizen_files = [];
if (is_dir($SHARD_DIR)) {
    $citizen_files = glob($SHARD_DIR . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
    $citizen_files = array_filter($citizen_files, function($p) use ($SHARD_DIR) {
        return strpos($p, $SHARD_DIR . '/mod/') === false;
    });
    usort($citizen_files, function($a, $b) {
        return filemtime($b) <=> filemtime($a);
    });
}
$citizen_cards_html = trep_build_shard_cards($citizen_files, $SHARD_URL_BASE, 'Unknown Citizen', 'citizen');


// ---------------------------------------------------------------------------
// 4. SHELL METADATA
// ---------------------------------------------------------------------------

$page_title       = 'The Republic | Shards';
$page_canonical   = 'https://trepublic.net/shards.php';
$page_description = 'Shards: Scroll Shard Desktop of The Republic. Long-form scroll cartridges with credits baked into the filename itself.';

$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/emblems/heart.png';

$hero_title   = 'Shards';
$hero_tagline = '💎 Scroll Shard Desktop of The Republic';

$console_title = 'Shard Desktop & Citizen Upload Dock';


// ---------------------------------------------------------------------------
// 5. CONSOLE BODY HTML (NO AGE GATE)
// ---------------------------------------------------------------------------

$console_body_html = <<<'HTML'
<div class="shards-console" id="shardsConsole">

  <!-- INTRO -->
  <section class="shards-intro">
    <div class="shards-intro-title">🩷 What is a shard?</div>
    <div class="shards-intro-body">
      A shard is not text. It is a <strong>single tall, scrollable image</strong> —
      a whole book, page, ritual, or contract captured as one continuous strip.
      This desktop stores those long images. Each shard carries its own credits
      baked into the filename. No external host can strip them.
    </div>
  </section>

  <!-- ROYAL EMBLEMS -->
  <section class="shards-emblems">
    <header class="shards-section-header">
      <div class="title">👑 Royal Emblems & Standards</div>
      <div class="hint">
        Oldest to newest. Canonical emblems and standard shard formats.
        Click to open, zoom, or download for citizen use.
      </div>
    </header>
    <div class="shards-row shards-row-center">
      {{EMBLEM_CARDS}}
    </div>
  </section>

  <!-- SHARD DESKTOP -->
  <section class="shards-garden">
    <header class="shards-section-header">
      <div class="title">🖥️ Shard Desktop</div>
      <div class="hint">
        Files from <code>/shards/</code>. Newest first. Move a shard here from
        <code>/shards/mod/</code> to approve it. Click any card to open the zoomable viewer.
      </div>
    </header>
    <div class="shards-row shards-row-center">
      {{CITIZEN_CARDS}}
    </div>
  </section>

  <!-- UPLOAD DOCK -->
  <section class="shards-upload">
    <header class="shards-section-header">
      <div class="title">📨 Submit a shard</div>
      <div class="hint">
        Finished shard images only (JPG / PNG / WEBP). They are stored in
        <code>/shards/mod/</code> for manual review. Approvals are just
        a file move into <code>/shards/</code>.
      </div>
    </header>

    {{UPLOAD_MESSAGE}}

    <form class="shards-upload-form" method="post" enctype="multipart/form-data">
      <div class="upload-row">
        <label>
          <span>📛 Shard title</span>
          <input type="text" name="shard_title" maxlength="160"
                 placeholder="e.g. Republic Foundations Index" required>
        </label>
      </div>

      <div class="upload-row">
        <label>
          <span>✍️ Your name / credits</span>
          <input type="text" name="shard_credit" maxlength="160"
                 placeholder="e.g. Wendell Charles NeSmith" required>
        </label>
      </div>

      <div class="upload-row">
        <label>
          <span>🖼 Shard image file (JPG / PNG / WEBP)</span>
          <input type="file" name="shard_file" required
                 accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        </label>
      </div>

      <div class="upload-row upload-submit">
        <button type="submit" name="shard_submit">⚒️ Upload shard for review</button>
      </div>
    </form>

  </section>

  <!-- HOW TO USE SHARDS -->
  <section class="shards-howto">
    <header class="shards-section-header">
      <div class="title">🧭 How to work with Shards</div>
      <div class="hint">
        Pick one role at a time. You do not have to be everything at once.
      </div>
    </header>
    <ol class="shards-steps">
      <li>As a <strong>reader</strong>, treat shards like scrolls in a temple — full-context texts, not social snippets.</li>
      <li>As a <strong>creator</strong>, design shards so your name and date live in the filename, not a fragile UI.</li>
      <li>As an <strong>archivist</strong>, mirror the shards that matter most to you across at least two servers or drives you control so no single host can erase them.</li>
    </ol>

    <div class="shards-pill-row">
      <button type="button" class="shards-pill" id="shardsPathReader">📖 I am a reader</button>
      <button type="button" class="shards-pill" id="shardsPathCreator">✍️ I am a creator</button>
      <button type="button" class="shards-pill" id="shardsPathArchivist">🗃 I am an archivist</button>
    </div>

    <div id="shardsPathOutput" class="shards-path-output"></div>
  </section>

  <!-- VIEWER MODAL -->
  <div class="shard-viewer-backdrop" id="shard-viewer" aria-hidden="true">
    <div class="shard-viewer-shell">
      <header class="shard-viewer-bar">
        <div class="shard-viewer-left">
          <div class="shard-viewer-title" id="shard-viewer-title">Shard Title</div>
          <div class="shard-viewer-sub" id="shard-viewer-sub">Meta line…</div>
        </div>

        <div class="shard-viewer-center">
          <button type="button" class="shard-viewer-btn" id="shard-zoom-out">−</button>
          <span class="shard-zoom-display" id="shard-zoom-display">100%</span>
          <button type="button" class="shard-viewer-btn" id="shard-zoom-reset">100</button>
          <button type="button" class="shard-viewer-btn" id="shard-zoom-in">+</button>
        </div>

        <div class="shard-viewer-right">
          <a href="#" class="shard-viewer-btn shard-download" id="shard-download" download>⬇️</a>
          <button type="button" class="shard-viewer-btn shard-close" id="shard-close">✕</button>
        </div>
      </header>

      <div class="shard-viewer-viewport" id="shard-viewport">
        <div class="shard-viewer-inner">
          <img id="shard-viewer-img" src="" alt="">
        </div>
      </div>
    </div>
  </div>

</div>

<style>
  .shards-console {
    max-width: 980px;
    margin: 0 auto;
    font-size: 0.95rem;
    position: relative;
  }

  .shards-intro,
  .shards-emblems,
  .shards-garden,
  .shards-upload,
  .shards-howto {
    margin-bottom: 18px;
    padding: 10px 12px 12px;
    border-radius: 14px;
    border: 1px solid rgba(251,113,133,0.95) !important;
    background: linear-gradient(145deg,#fce7f3 0,#f9a8d4 35%,#fb7185 70%,#e879f9 100%) !important;
    box-shadow: 0 8px 20px rgba(131,24,67,0.45);
    color: #3b0828;
  }

  .shards-intro-title {
    font-weight: 800;
    margin-bottom: 4px;
    font-size: 1rem;
  }
  .shards-intro-body { opacity: 0.95; }

  .shards-section-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 4px;
    align-items: baseline;
    margin-bottom: 8px;
  }
  .shards-section-header .title {
    font-weight: 800;
    font-size: 1.03rem;
  }
  .shards-section-header .hint {
    font-size: 0.8rem;
    opacity: 0.92;
  }

  .shards-row { display: flex; flex-wrap: wrap; gap: 10px; }
  .shards-row-center { justify-content: center; }

  .shards-empty { font-size: 0.86rem; opacity: 0.9; }

  /* Shard cards */
  .shard-card {
    position: relative;
    flex: 0 0 260px;
    max-width: 100%;
    border-radius: 12px;
    background: radial-gradient(circle at top, #fecdd3 0, #f9a8d4 40%, #f472b6 80%);
    border: 1px solid rgba(190,24,93,0.9);
    box-shadow: 0 10px 22px rgba(131,24,67,0.7);
    color: #3b0828;
    cursor: pointer;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .shard-thumb-shell { padding: 5px 5px 0; }
  .shard-thumb-inner {
    background: #0f172a;
    border-radius: 9px;
    overflow: hidden;
    border: 1px solid rgba(252,231,243,0.9);
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .shard-card[data-kind="emblem"] .shard-thumb-inner { height: 110px; }
  .shard-thumb-inner img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .shard-caption { padding: 6px 8px 8px; text-align: center; }
  .shard-caption-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 2px; }
  .shard-caption-meta { font-size: 0.78rem; opacity: 0.93; }

  /* Upload form */
  .shards-upload-form { display: flex; flex-direction: column; gap: 8px; }
  .upload-row label { display: flex; flex-direction: column; gap: 2px; font-size: 0.83rem; }
  .upload-row span { font-weight: 600; }

  .upload-row input[type="text"],
  .upload-row input[type="file"] {
    padding: 5px 7px;
    border-radius: 999px;
    border: 1px solid rgba(251,113,133,0.95);
    background: rgba(254,242,242,0.95);
    color: #3b0828;
    font-size: 0.9rem;
  }
  .upload-row input[type="file"] {
    border-radius: 999px;
    background: rgba(255,247,250,0.98);
  }

  .upload-submit { margin-top: 4px; }
  .upload-submit button {
    padding: 6px 16px;
    border-radius: 999px;
    border: 1px solid rgba(190,24,93,0.9);
    background: radial-gradient(circle at top left,#fb7185,#ec4899,#a855f7);
    color: #fdf2ff;
    font-weight: 800;
    cursor: pointer;
    font-size: 0.9rem;
    box-shadow: 0 8px 20px rgba(131,24,67,0.65);
  }
  .upload-submit button:hover { filter: brightness(1.05); }

  .shards-upload-message {
    margin-bottom: 8px;
    padding: 6px 9px;
    border-radius: 10px;
    background: rgba(187,247,208,0.95);
    border: 1px solid rgba(22,163,74,0.8);
    font-size: 0.83rem;
    color: #064e3b;
  }
  .shards-upload-footnote { margin-top: 6px; font-size: 0.78rem; opacity: 0.9; }

  /* How-to */
  .shards-steps { list-style: decimal; margin: 4px 0 0 1.1em; padding: 0; font-size: 0.8rem; }
  .shards-steps li { margin-bottom: 3px; }

  .shards-pill-row { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; font-size: 0.8rem; }
  .shards-pill {
    border-radius: 999px;
    padding: 3px 10px;
    border: 1px solid rgba(244,114,182,0.95);
    background: rgba(254,242,242,0.96);
    color: #4a0433;
    cursor: pointer;
    font-weight: 600;
  }
  .shards-pill:hover { background: #ffe4f1; }
  .shards-path-output { margin-top: 6px; font-size: 0.8rem; opacity: 0.96; }

  /* Viewer */
  .shard-viewer-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.88);
    display: none;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 14px;
  }
  .shard-viewer-backdrop.active { display: flex; }

  .shard-viewer-shell {
    width: min(1120px, 100%);
    height: min(96vh, 900px);
    display: flex;
    flex-direction: column;
    background: #020617;
    border-radius: 14px;
    border: 1px solid rgba(248,187,208,0.9);
    box-shadow: 0 18px 40px rgba(0,0,0,0.9);
  }

  .shard-viewer-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 10px;
    border-bottom: 1px solid rgba(248,187,208,0.9);
    background: radial-gradient(circle at top left,#be185d 0,#020617 55%);
    color: #fce7f3;
  }

  .shard-viewer-left { min-width: 0; flex: 1 1 auto; }
  .shard-viewer-title { font-weight: 600; font-size: 0.96rem; }
  .shard-viewer-sub { font-size: 0.78rem; opacity: 0.9; }

  .shard-viewer-center {
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
    margin-left: 24px;
    margin-right: 120px;
  }
  .shard-viewer-right {
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
    margin-left: auto;
  }

  .shard-viewer-btn {
    border-radius: 999px;
    border: 1px solid rgba(248,187,208,0.9);
    background: rgba(15,23,42,0.95);
    color: #fce7f3;
    padding: 3px 9px;
    font-size: 0.78rem;
    cursor: pointer;
    min-width: 34px;
    text-align: center;
  }
  .shard-viewer-btn.shard-close {
    background: #f97373;
    border-color: #fecaca;
    color: #111827;
    font-weight: 700;
  }
  .shard-viewer-btn.shard-download {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .shard-zoom-display {
    min-width: 52px;
    text-align: center;
    font-size: 0.78rem;
    border-radius: 999px;
    border: 1px solid rgba(248,187,208,0.9);
    padding: 2px 6px;
    background: rgba(15,23,42,0.9);
  }

  .shard-viewer-viewport {
    flex: 1 1 auto;
    overflow: auto;
    padding: 6px;
    background: #000;
    border-radius: 0 0 14px 14px;
  }
  .shard-viewer-inner {
    min-height: 100%;
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
  }
  #shard-viewer-img {
    display: block;
    width: 100%;
    max-width: none;
    height: auto;
    image-rendering: auto;
  }

  @media (max-width: 720px) {
    .shard-card { flex: 0 0 100%; }
    .shard-viewer-shell {
      width: 100%;
      height: 100%;
      border-radius: 0;
    }
    .shard-viewer-viewport { border-radius: 0; }
    .shard-viewer-center { margin-right: 40px; }
  }
</style>

<script>
(function(){
  // Role helper
  var outEl   = document.getElementById('shardsPathOutput');
  var btnRead = document.getElementById('shardsPathReader');
  var btnMake = document.getElementById('shardsPathCreator');
  var btnArc  = document.getElementById('shardsPathArchivist');

  function setPath(html) { if (outEl) outEl.innerHTML = html; }

  if (btnRead) btnRead.addEventListener('click', function(){
    setPath('📖 <strong>Reader:</strong> Open a few shards and read them end-to-end. Treat the scroll as one spell, not fragments.');
  });
  if (btnMake) btnMake.addEventListener('click', function(){
    setPath('✍️ <strong>Creator:</strong> Keep date, title, and your name in the filename. The cartridge is your credit line.');
  });
  if (btnArc) btnArc.addEventListener('click', function(){
    setPath('🗃 <strong>Archivist:</strong> Mirror high-value shards across at least two places you control. Redundancy is sovereignty.');
  });

  // Viewer + SUPER ZOOM (width-based)
  var cards       = document.querySelectorAll('.shard-card');
  var viewer      = document.getElementById('shard-viewer');
  var img         = document.getElementById('shard-viewer-img');
  var viewport    = document.getElementById('shard-viewport');
  var titleEl     = document.getElementById('shard-viewer-title');
  var subEl       = document.getElementById('shard-viewer-sub');
  var zoomOut     = document.getElementById('shard-zoom-out');
  var zoomIn      = document.getElementById('shard-zoom-in');
  var zoomReset   = document.getElementById('shard-zoom-reset');
  var zoomDisplay = document.getElementById('shard-zoom-display');
  var dlLink      = document.getElementById('shard-download');
  var closeBtn    = document.getElementById('shard-close');

  if (!viewer || !img || !viewport) return;

  function viewerActive(){ return viewer.classList.contains('active'); }

  var zoom      = 1;
  var MIN_ZOOM  = 0.20;
  var MAX_ZOOM  = 80.0;
  var STEP_UP   = 1.25;
  var STEP_DOWN = 1 / STEP_UP;

  function updateZoomDisplay() {
    if (!zoomDisplay) return;
    zoomDisplay.textContent = Math.round(zoom * 100) + '%';
  }

  function applyZoom(newZoom) {
    newZoom = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, newZoom));
    var oldZoom = zoom;
    if (Math.abs(newZoom - oldZoom) < 0.001) return;

    var ratio    = newZoom / oldZoom;
    var prevTop  = viewport.scrollTop;
    var prevLeft = viewport.scrollLeft;

    zoom = newZoom;
    img.style.width = (zoom * 100) + '%';
    updateZoomDisplay();

    viewport.scrollTop  = prevTop  * ratio;
    viewport.scrollLeft = prevLeft * ratio;
  }

  function openViewer(src, title, meta) {
    img.src             = src;
    img.alt             = title || '';
    if (titleEl) titleEl.textContent = title || 'Shard';
    if (subEl)   subEl.textContent   = meta || '';
    if (dlLink)  dlLink.href         = src;

    zoom = 1;
    img.style.width = '100%';
    viewport.scrollTop = 0;
    viewport.scrollLeft = 0;
    updateZoomDisplay();

    viewer.classList.add('active');
    viewer.setAttribute('aria-hidden', 'false');
  }

  function closeViewer() {
    viewer.classList.remove('active');
    viewer.setAttribute('aria-hidden', 'true');
  }

  cards.forEach(function(card){
    card.addEventListener('click', function(){
      var src   = card.getAttribute('data-src');
      var title = card.getAttribute('data-title') || '';
      var meta  = card.getAttribute('data-meta') || '';
      if (src) openViewer(src, title, meta);
    });
  });

  if (zoomIn)    zoomIn.addEventListener('click', function(){ applyZoom(zoom * STEP_UP); });
  if (zoomOut)   zoomOut.addEventListener('click', function(){ applyZoom(zoom * STEP_DOWN); });
  if (zoomReset) zoomReset.addEventListener('click', function(){ applyZoom(1); });

  if (closeBtn) closeBtn.addEventListener('click', closeViewer);

  viewer.addEventListener('click', function(e){
    if (e.target === viewer) closeViewer();
  });

  function globalKeyHandler(e) {
    if (!viewerActive()) return;

    if (e.key === 'Escape') {
      e.preventDefault(); e.stopPropagation();
      closeViewer(); return;
    }

    if (e.key === '+' || e.key === '=' || e.key === '-' || e.key === '0') {
      e.preventDefault(); e.stopPropagation();

      if (e.key === '+' || e.key === '=') applyZoom(zoom * STEP_UP);
      else if (e.key === '-')            applyZoom(zoom * STEP_DOWN);
      else                               applyZoom(1);
    }
  }

  function globalWheelHandler(e) {
    if (!viewerActive()) return;

    if (e.ctrlKey || e.metaKey || e.shiftKey) {
      e.preventDefault(); e.stopPropagation();
      if (e.deltaY < 0) applyZoom(zoom * STEP_UP);
      else              applyZoom(zoom * STEP_DOWN);
    }
  }

  window.addEventListener('keydown', globalKeyHandler, { capture: true });
  window.addEventListener('wheel',   globalWheelHandler, { passive: false, capture: true });

})();
</script>
HTML;

// Inject dynamic HTML
$console_body_html = str_replace('{{EMBLEM_CARDS}}',   $emblem_cards_html,   $console_body_html);
$console_body_html = str_replace('{{CITIZEN_CARDS}}',  $citizen_cards_html,  $console_body_html);
$console_body_html = str_replace('{{UPLOAD_MESSAGE}}', $upload_message_html, $console_body_html);


// ---------------------------------------------------------------------------
// 6. HAND OFF TO SHELL
// ---------------------------------------------------------------------------

require __DIR__ . '/shell.php';
