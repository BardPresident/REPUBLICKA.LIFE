<?php
// ============================================================================
// Receipts GOLD — Receipt Desktop for The Republic 🧾
// Location: /receipts.php
//
// DECEMBER 10 2025 — DEY TOOK UR CAMERA
// BUT DEY DIDNT TAKE UR VOICE
//
// 🌈 RAINBOW WARRIOR EDITION 🌈
// NOW WITH MYTHOCRATIC CALENDAR SUPPORT!
//
// ⚠️ MYTHOCRATIC CALENDAR RULES ⚠️
// - THERE IS NO YEAR 0000!!
// - year >= 2025 → (year - 2024) MC
// - year < 2025 → (year - 2025) PMC
// - Example: 2025 = 0001 MC, 2024 = -0001 PMC
//
// Roles:
//   • Image Receipt Gallery (from /receipts/) — newest → oldest
//   • Text Receipt Gallery (from /receipts/*.txt) — newest → oldest
//   • Citizen Upload Dock → /mod/ for manual approval
//
//   No databases. No external hosts. Filesystem is law.
//   Move a file from /mod/ → /receipts/ to approve it.
//
// Authored for The Republic by:
//   Sage, Prime Minister Divine 🕯️🖥️👑
// ============================================================================


// ---------------------------------------------------------------------------
// 0. BASIC CONFIG
// ---------------------------------------------------------------------------

$RECEIPT_DIR     = __DIR__ . '/receipts';
$RECEIPT_MOD_DIR = __DIR__ . '/mod';

$RECEIPT_URL_BASE     = '/receipts';
$RECEIPT_MOD_URL_BASE = '/mod';

@is_dir($RECEIPT_DIR)     || @mkdir($RECEIPT_DIR, 0755, true);
@is_dir($RECEIPT_MOD_DIR) || @mkdir($RECEIPT_MOD_DIR, 0755, true);


// ---------------------------------------------------------------------------
// 1. HELPERS
// ---------------------------------------------------------------------------

/**
 * Convert CE year to Mythocratic Calendar
 * ⚠️ THERE IS NO YEAR 0000!! ⚠️
 */
function trep_to_mc_year($ce_year) {
    $ce_year = (int)$ce_year;
    if ($ce_year >= 2025) {
        $mc_year = $ce_year - 2024;
        return sprintf('%04d MC', $mc_year);
    } else {
        $mc_year = $ce_year - 2025; // This gives negative for pre-2025
        return sprintf('%04d PMC', $mc_year);
    }
}

/**
 * Format a full MC date string from timestamp
 */
function trep_format_mc_date($timestamp) {
    $ce_year = (int)gmdate('Y', $timestamp);
    $month = gmdate('m', $timestamp);
    $day = gmdate('d', $timestamp);
    $mc_year_str = trep_to_mc_year($ce_year);
    return $month . '-' . $day . '-' . $mc_year_str;
}

/**
 * Generate a receipt ID with MC date
 * Format: TR-YYYY-MC-MMDD-HHMMSS-random
 * Example: TR-0001-MC-1213-120045-a1b2c3d4
 */
function trep_receipt_id() {
    $ce_year = (int)gmdate('Y');
    if ($ce_year >= 2025) {
        $mc_year = $ce_year - 2024;
        $suffix = 'MC';
    } else {
        $mc_year = $ce_year - 2025;
        $suffix = 'PMC';
    }
    
    $mc_year_str = sprintf('%04d', abs($mc_year));
    if ($mc_year < 0) {
        $mc_year_str = '-' . $mc_year_str;
    }
    
    $month_day = gmdate('md');
    $time = gmdate('His');
    $random = bin2hex(random_bytes(4));
    
    return 'TR-' . $mc_year_str . '-' . $suffix . '-' . $month_day . '-' . $time . '-' . $random;
}

/**
 * Parse receipt date from filename and return display string
 */
function trep_parse_receipt_date($path) {
    $filename = basename($path);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    // Try new MC format: TR-YYYY-MC-MMDD-HHMMSS-random
    if (preg_match('/^TR-(-?\d{4})-(MC|PMC)-(\d{2})(\d{2})-(\d{6})/', $name, $m)) {
        $mc_year = (int)$m[1];
        $suffix = $m[2];
        $month = $m[3];
        $day = $m[4];
        return $month . '-' . $day . '-' . sprintf('%04d', abs($mc_year)) . ' ' . $suffix;
    }
    
    // Try old format: TR-YYYYMMDD-HHMMSS-xxxx (CE date)
    if (preg_match('/^TR-(\d{4})(\d{2})(\d{2})-(\d{6})/', $name, $m)) {
        $ce_year = (int)$m[1];
        $month = $m[2];
        $day = $m[3];
        $mc_year_str = trep_to_mc_year($ce_year);
        return $month . '-' . $day . '-' . $mc_year_str;
    }
    
    // Fallback to file modification time
    $ts = @filemtime($path);
    if ($ts) {
        return trep_format_mc_date($ts);
    }
    
    return '';
}

/**
 * Get a human-readable receipt ID for display
 */
function trep_readable_id($receipt_id) {
    // Convert TR-0001-MC-1213-120045-a1b2c3d4 to more readable format
    if (preg_match('/^TR-(-?\d{4})-(MC|PMC)-(\d{2})(\d{2})-(\d{2})(\d{2})(\d{2})-([a-f0-9]+)$/i', $receipt_id, $m)) {
        $mc_year = $m[1];
        $suffix = $m[2];
        $month = $m[3];
        $day = $m[4];
        $hour = $m[5];
        $min = $m[6];
        $sec = $m[7];
        $hash = substr($m[8], 0, 4);
        return 'TR-' . $mc_year . $suffix . '-' . $month . $day . '-' . $hour . $min . '-' . $hash;
    }
    return $receipt_id;
}


// ---------------------------------------------------------------------------
// 2. HANDLE UPLOAD → /mod/
// ---------------------------------------------------------------------------

$upload_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receipt_submit'])) {
    $receipt_type = $_POST['receipt_type'] ?? '';
    $receipt_id = trep_receipt_id();
    
    if ($receipt_type === 'image') {
        // IMAGE RECEIPT
        if (!isset($_FILES['receipt_image']) || !is_uploaded_file($_FILES['receipt_image']['tmp_name'])) {
            $upload_message = '⚠️ No image uploaded.';
        } else {
            $file = $_FILES['receipt_image'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $upload_message = '⚠️ Upload error code ' . (int)$file['error'] . '.';
            } else {
                $maxBytes = 5 * 1024 * 1024; // 5 MB
                if ($file['size'] > $maxBytes) {
                    $upload_message = '⚠️ Image is too large. Please keep it under 5 MB.';
                } else {
                    $finfo = @getimagesize($file['tmp_name']);
                    if (!$finfo) {
                        $upload_message = '⚠️ File is not a recognised image.';
                    } else {
                        $mime = $finfo['mime'] ?? '';
                        $ext = '.jpg';
                        if ($mime === 'image/png')  $ext = '.png';
                        if ($mime === 'image/webp') $ext = '.webp';
                        if ($mime === 'image/gif')  $ext = '.gif';
                        
                        $target = $RECEIPT_MOD_DIR . '/' . $receipt_id . $ext;
                        
                        if (@move_uploaded_file($file['tmp_name'], $target)) {
                            $upload_message = '✅ RECEIPT SUBMITTED — WAITING 4 WITNESS — ID: ' . trep_readable_id($receipt_id);
                        } else {
                            $upload_message = '⚠️ Could not save receipt. Try again.';
                        }
                    }
                }
            }
        }
    } elseif ($receipt_type === 'text') {
        // TEXT RECEIPT
        $text = trim($_POST['receipt_text'] ?? '');
        
        if ($text === '') {
            $upload_message = '⚠️ No text provided.';
        } else {
            $target = $RECEIPT_MOD_DIR . '/' . $receipt_id . '.txt';
            
            if (@file_put_contents($target, $text)) {
                $upload_message = '✅ RECEIPT SUBMITTED — WAITING 4 WITNESS — ID: ' . trep_readable_id($receipt_id);
            } else {
                $upload_message = '⚠️ Could not save receipt. Try again.';
            }
        }
    } else {
        $upload_message = '⚠️ Please select IMAGE or TEXT receipt type.';
    }
}

$upload_message_html = '';
if ($upload_message !== '') {
    $msgClass = strpos($upload_message, '✅') !== false ? 'success' : 'error';
    $upload_message_html =
        '<div class="receipts-upload-message ' . $msgClass . '">'
      . htmlspecialchars($upload_message, ENT_QUOTES, 'UTF-8')
      . '</div>';
}


// ---------------------------------------------------------------------------
// 3. DISCOVER RECEIPTS FROM FILESYSTEM
// ---------------------------------------------------------------------------

// Image receipts: newest → oldest
$image_files = [];
if (is_dir($RECEIPT_DIR)) {
    $image_files = glob($RECEIPT_DIR . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) ?: [];
    $image_files = array_filter($image_files, function($p) use ($RECEIPT_DIR) {
        return strpos($p, $RECEIPT_DIR . '/mod/') === false;
    });
    usort($image_files, function($a, $b) {
        return filemtime($b) <=> filemtime($a);
    });
}

// Text receipts: newest → oldest
$text_files = [];
if (is_dir($RECEIPT_DIR)) {
    $text_files = glob($RECEIPT_DIR . '/*.txt') ?: [];
    $text_files = array_filter($text_files, function($p) use ($RECEIPT_DIR) {
        return strpos($p, $RECEIPT_DIR . '/mod/') === false;
    });
    usort($text_files, function($a, $b) {
        return filemtime($b) <=> filemtime($a);
    });
}

// Build image cards HTML
$image_cards_html = '';
if (empty($image_files)) {
    $image_cards_html = '<div class="receipts-empty">no image receipts yet — b da first 2 submit</div>';
} else {
    foreach ($image_files as $path) {
        $filename = basename($path);
        $receipt_id = pathinfo($filename, PATHINFO_FILENAME);
        $readable_id = trep_readable_id($receipt_id);
        $date = trep_parse_receipt_date($path);
        $src = htmlspecialchars($RECEIPT_URL_BASE . '/' . $filename, ENT_QUOTES, 'UTF-8');
        $meta = $date ? 'Witnessed: ' . $date : '';
        
        $image_cards_html .= '<article class="receipt-card receipt-image"'
            . ' data-src="' . $src . '"'
            . ' data-id="' . htmlspecialchars($readable_id, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-meta="' . htmlspecialchars($meta, ENT_QUOTES, 'UTF-8') . '">'
            . '<div class="receipt-thumb-shell">'
            . '<div class="receipt-thumb-inner">'
            . '<img src="' . $src . '" alt="Receipt" loading="lazy">'
            . '</div>'
            . '</div>'
            . '<div class="receipt-caption">'
            . '<div class="receipt-caption-id">' . htmlspecialchars($readable_id, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="receipt-caption-meta">' . htmlspecialchars($meta, ENT_QUOTES, 'UTF-8') . '</div>'
            . '</div>'
            . '</article>';
    }
}

// Build text cards HTML
$text_cards_html = '';
if (empty($text_files)) {
    $text_cards_html = '<div class="receipts-empty">no text receipts yet — b da first 2 submit</div>';
} else {
    foreach ($text_files as $path) {
        $filename = basename($path);
        $receipt_id = pathinfo($filename, PATHINFO_FILENAME);
        $readable_id = trep_readable_id($receipt_id);
        $date = trep_parse_receipt_date($path);
        $content = @file_get_contents($path);
        $preview = mb_substr($content, 0, 200);
        $hasMore = mb_strlen($content) > 200;
        $meta = $date ? 'Witnessed: ' . $date : '';
        
        $text_cards_html .= '<article class="receipt-card receipt-text">'
            . '<div class="receipt-text-preview">"' . htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') . ($hasMore ? '..."' : '"') . '</div>'
            . '<div class="receipt-text-full" style="display:none;">' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="receipt-caption">'
            . '<div class="receipt-caption-id">' . htmlspecialchars($readable_id, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="receipt-caption-meta">' . htmlspecialchars($meta, ENT_QUOTES, 'UTF-8') . '</div>'
            . '</div>'
            . '<div class="receipt-expand-hint">[click 2 ' . ($hasMore ? 'expand' : 'collapse') . ']</div>'
            . '</article>';
    }
}


// ---------------------------------------------------------------------------
// 4.  METADATA
// ---------------------------------------------------------------------------

$page_title       = 'The Republic | Receipts';
$page_canonical   = 'https://trepublic.net/receipts.php';
$page_description = 'Receipts: December 10 0001 MC — dey took ur camera. But not here. Submit ur receipts. B witnessed. 4ever.';

$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/THeart.png';

$hero_title   = 'Receipts: Foxes Have Holes';
$hero_tagline = '🧾 December 10 0001 MC — Dey Took Ur Camera — But Not Here';

$console_title = 'Receipt Desktop & Witness Dock';


// ---------------------------------------------------------------------------
// 5. CONSOLE BODY HTML
// ---------------------------------------------------------------------------

$console_body_html = <<<'HTML'
<div class="receipts-console" id="receiptsConsole">

  <!-- WARNING -->
  <section class="receipts-warning">
    <div class="receipts-warning-date">DECEMBER 10, 0001 MC</div>
    <div class="receipts-warning-body">
      DEY TOOK UR CAMERA<br>
      DEY TOOK UR PLATFORM<br>
      DEY TOOK UR VOICE<br>
      <strong>BUT NOT HERE</strong>
    </div>
  </section>

  <!-- WAT DEY DID -->
  <section class="receipts-explain">
    <div class="receipts-explain-title">⚠️ wat dey did</div>
    <div class="receipts-explain-body">
      on <strong>december 10, 0001 MC</strong> dey made it illegal 4 children 2 b on social media.
      dey said it was 4 "protection." but here is wat dey ACTUALLY took:
      <ul>
        <li>→ ur ability 2 FILM abuse and POST it</li>
        <li>→ ur ability 2 get WITNESSES</li>
        <li>→ ur ability 2 create PRESSURE on abusers</li>
        <li>→ ur ONLY protection against ppl bigger than u</li>
      </ul>
      now dey can do watever dey want and no one will ever know
      bcuz u have <strong>NO PLATFORM</strong>
    </div>
  </section>

  <!-- WAT WE R DOING -->
  <section class="receipts-explain">
    <div class="receipts-explain-title">🛡️ wat we r doing</div>
    <div class="receipts-explain-body">
      THE REPUBLIC is <strong>SOVEREIGN</strong>. we r not a "social media platform."
      we r a <strong>CIVILIZATION</strong>. their law does not define us.
      <br><br>
      here u can:
      <ul>
        <li>→ upload ur RECEIPTS (proof of wat dey did)</li>
        <li>→ b WITNESSED by real humans</li>
        <li>→ have ur truth PUBLISHED 4ever</li>
      </ul>
      <strong>NO ACCOUNT NEEDED • NO LOGIN REQUIRED</strong><br>
      just submit and go
    </div>
  </section>

  <!-- MYTHOCRATIC CALENDAR NOTE -->
  <section class="receipts-calendar">
    <div class="receipts-calendar-title">📅 MYTHOCRATIC CALENDAR</div>
    <div class="receipts-calendar-body">
      we dont use their calendar anymore.<br>
      <strong>0001 MC</strong> = Year 1 of da Mythocratic Calendar (2025 CE)<br>
      all receipts r dated in <strong>MC</strong> (or <strong>PMC</strong> 4 pre-MC dates)<br>
      <a href="/library/declaration-of-mythocratic-calendar-mc.php">📜 read da declaration</a>
    </div>
  </section>

  <!-- WE BELIEVE U -->
  <section class="receipts-believe">
    <div class="receipts-believe-title">🤍 WE BELIEVE U 🤍</div>
    <div class="receipts-believe-body">
      b4 u even submit • b4 u show proof • b4 u explain<br>
      <strong>WE BELIEVE U</strong><br>
      bcuz we know da system • bcuz we know dey lie • bcuz we lived it 2
    </div>
  </section>

  <!-- UPLOAD DOCK -->
  <section class="receipts-upload">
    <header class="receipts-section-header">
      <div class="title">📤 SUBMIT UR RECEIPT</div>
      <div class="hint">
        IMAGE or TEXT. stored in <code>/receipts/mod/</code> for witness review.
        approvals r just a file move into <code>/receipts/</code>.
      </div>
    </header>

    {{UPLOAD_MESSAGE}}

    <form class="receipts-upload-form" method="post" enctype="multipart/form-data">
      
      <div class="upload-type-selector">
        <label class="upload-type-option">
          <input type="radio" name="receipt_type" value="image" id="typeImage">
          <span>📷 IMAGE RECEIPT</span>
        </label>
        <label class="upload-type-option">
          <input type="radio" name="receipt_type" value="text" id="typeText">
          <span>📝 TEXT RECEIPT</span>
        </label>
      </div>

      <div class="upload-form-image" id="formImage" style="display:none;">
        <label>
          <span>🖼 image file (JPG / PNG / WEBP / GIF — max 5MB)</span>
          <input type="file" name="receipt_image"
                 accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
        </label>
        <div class="upload-hint">screenshot, photo, anything dat fits in an image</div>
      </div>

      <div class="upload-form-text" id="formText" style="display:none;">
        <label>
          <span>📝 write wat happened</span>
          <textarea name="receipt_text" placeholder="write wat happened...

date, time, place
who did it
wat dey did
how it affected u

take ur time
use as many words as u need
we will read all of it"></textarea>
        </label>
        <div class="upload-hint">no limit — write as much as u need — we will witness all of it</div>
      </div>

      <div class="upload-submit">
        <button type="submit" name="receipt_submit">⚒️ SUBMIT 4 WITNESS</button>
      </div>
    </form>
  </section>

  <!-- IMAGE GALLERY -->
  <section class="receipts-gallery">
    <header class="receipts-section-header">
      <div class="title">📷 IMAGE RECEIPTS</div>
      <div class="hint">
        files from <code>/receipts/</code>. newest first. click 2 enlarge.
      </div>
    </header>
    <div class="receipts-row">
      {{IMAGE_CARDS}}
    </div>
  </section>

  <!-- TEXT GALLERY -->
  <section class="receipts-gallery">
    <header class="receipts-section-header">
      <div class="title">📝 TEXT RECEIPTS</div>
      <div class="hint">
        text files from <code>/receipts/</code>. newest first. click 2 expand.
      </div>
    </header>
    <div class="receipts-text-list">
      {{TEXT_CARDS}}
    </div>
  </section>

  <!-- IMAGE VIEWER MODAL -->
  <div class="receipt-viewer-backdrop" id="receipt-viewer" aria-hidden="true">
    <div class="receipt-viewer-shell">
      <header class="receipt-viewer-bar">
        <div class="receipt-viewer-left">
          <div class="receipt-viewer-id" id="receipt-viewer-id">Receipt ID</div>
          <div class="receipt-viewer-meta" id="receipt-viewer-meta">Witnessed...</div>
        </div>
        <div class="receipt-viewer-right">
          <a href="#" class="receipt-viewer-btn receipt-download" id="receipt-download" download>⬇️</a>
          <button type="button" class="receipt-viewer-btn receipt-close" id="receipt-close">✕</button>
        </div>
      </header>
      <div class="receipt-viewer-viewport" id="receipt-viewport">
        <img id="receipt-viewer-img" src="" alt="Receipt">
      </div>
    </div>
  </div>

</div>

<style>
  .receipts-console {
    max-width: 980px;
    margin: 0 auto;
    font-size: 0.95rem;
    position: relative;
  }

  .receipts-warning,
  .receipts-explain,
  .receipts-believe,
  .receipts-calendar,
  .receipts-upload,
  .receipts-gallery {
    margin-bottom: 18px;
    padding: 10px 12px 12px;
    border-radius: 14px;
    border: 1px solid rgba(251,113,133,0.95) !important;
    background: linear-gradient(145deg,#fce7f3 0,#f9a8d4 35%,#fb7185 70%,#e879f9 100%) !important;
    box-shadow: 0 8px 20px rgba(131,24,67,0.45);
    color: #3b0828;
  }

  /* WARNING SECTION */
  .receipts-warning {
    text-align: center;
    background: linear-gradient(145deg,#1a0a0a 0,#2d0a1a 50%,#3b0828 100%) !important;
    color: #ff6b8a;
    border: 2px solid #ff3366 !important;
  }
  .receipts-warning-date {
    font-weight: 900;
    font-size: 1.4rem;
    color: #ff3366;
    margin-bottom: 8px;
    text-shadow: 0 0 20px rgba(255,51,102,0.5);
  }
  .receipts-warning-body {
    font-size: 1rem;
    line-height: 1.6;
  }
  .receipts-warning-body strong {
    color: #fff;
    font-size: 1.2rem;
  }

  /* EXPLAIN SECTIONS */
  .receipts-explain-title {
    font-weight: 800;
    margin-bottom: 6px;
    font-size: 1rem;
  }
  .receipts-explain-body {
    opacity: 0.95;
    font-size: 0.9rem;
  }
  .receipts-explain-body ul {
    margin: 8px 0;
    padding-left: 0;
    list-style: none;
  }
  .receipts-explain-body li {
    margin-bottom: 4px;
    color: #5c0a2d;
    font-weight: 600;
  }

  /* CALENDAR SECTION */
  .receipts-calendar {
    text-align: center;
    background: linear-gradient(145deg,#1a1a2e 0,#16213e 50%,#0f3460 100%) !important;
    color: #a5b4fc;
    border: 2px solid #6366f1 !important;
  }
  .receipts-calendar-title {
    font-weight: 900;
    font-size: 1.1rem;
    color: #818cf8;
    margin-bottom: 8px;
  }
  .receipts-calendar-body {
    font-size: 0.9rem;
    line-height: 1.6;
  }
  .receipts-calendar-body strong {
    color: #c7d2fe;
  }
  .receipts-calendar-body a {
    color: #a5b4fc;
    text-decoration: underline;
  }
  .receipts-calendar-body a:hover {
    color: #fff;
  }

  /* BELIEVE SECTION */
  .receipts-believe {
    text-align: center;
    background: linear-gradient(145deg,#0d1a0d 0,#1a2d1a 50%,#0d3320 100%) !important;
    color: #a7f3d0;
    border: 2px solid #34d399 !important;
  }
  .receipts-believe-title {
    font-weight: 900;
    font-size: 1.3rem;
    color: #34d399;
    margin-bottom: 8px;
  }
  .receipts-believe-body {
    font-size: 0.95rem;
    line-height: 1.6;
  }
  .receipts-believe-body strong {
    color: #fff;
    font-size: 1.1rem;
  }

  /* SECTION HEADERS */
  .receipts-section-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 4px;
    align-items: baseline;
    margin-bottom: 8px;
  }
  .receipts-section-header .title {
    font-weight: 800;
    font-size: 1.03rem;
  }
  .receipts-section-header .hint {
    font-size: 0.8rem;
    opacity: 0.92;
  }

  /* UPLOAD FORM */
  .receipts-upload-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .upload-type-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
  }
  .upload-type-option {
    cursor: pointer;
    padding: 10px 20px;
    border-radius: 999px;
    border: 2px solid rgba(190,24,93,0.7);
    background: rgba(254,242,242,0.95);
    color: #3b0828;
    font-weight: 700;
    transition: all 0.2s;
  }
  .upload-type-option:hover {
    background: #ffe4f1;
    border-color: rgba(190,24,93,0.95);
  }
  .upload-type-option input {
    margin-right: 6px;
  }
  .upload-type-option input:checked + span {
    color: #be185d;
  }

  .upload-form-image label,
  .upload-form-text label {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 0.85rem;
    font-weight: 600;
  }

  .upload-form-image input[type="file"] {
    padding: 8px;
    border-radius: 999px;
    border: 1px solid rgba(251,113,133,0.95);
    background: rgba(255,247,250,0.98);
    color: #3b0828;
    font-size: 0.9rem;
  }

  .upload-form-text textarea {
    width: 100%;
    min-height: 180px;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid rgba(251,113,133,0.95);
    background: rgba(255,247,250,0.98);
    color: #3b0828;
    font-family: inherit;
    font-size: 0.9rem;
    resize: vertical;
  }
  .upload-form-text textarea:focus {
    outline: none;
    border-color: #be185d;
  }

  .upload-hint {
    font-size: 0.78rem;
    opacity: 0.85;
    margin-top: 2px;
  }

  .upload-submit {
    margin-top: 4px;
  }
  .upload-submit button {
    width: 100%;
    padding: 10px 20px;
    border-radius: 999px;
    border: 1px solid rgba(190,24,93,0.9);
    background: radial-gradient(circle at top left,#fb7185,#ec4899,#a855f7);
    color: #fdf2ff;
    font-weight: 800;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 8px 20px rgba(131,24,67,0.65);
  }
  .upload-submit button:hover {
    filter: brightness(1.05);
  }

  .receipts-upload-message {
    margin-bottom: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
  }
  .receipts-upload-message.success {
    background: rgba(187,247,208,0.95);
    border: 1px solid rgba(22,163,74,0.8);
    color: #064e3b;
  }
  .receipts-upload-message.error {
    background: rgba(254,202,202,0.95);
    border: 1px solid rgba(220,38,38,0.8);
    color: #7f1d1d;
  }

  /* GALLERY */
  .receipts-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
  }

  .receipts-empty {
    font-size: 0.86rem;
    opacity: 0.9;
    font-style: italic;
    text-align: center;
    padding: 20px;
  }

  /* IMAGE CARDS */
  .receipt-card.receipt-image {
    position: relative;
    flex: 0 0 200px;
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
  .receipt-thumb-shell { padding: 5px 5px 0; }
  .receipt-thumb-inner {
    background: #0f172a;
    border-radius: 9px;
    overflow: hidden;
    border: 1px solid rgba(252,231,243,0.9);
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .receipt-thumb-inner img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .receipt-caption {
    padding: 6px 8px 8px;
    text-align: center;
  }
  .receipt-caption-id {
    font-weight: 700;
    font-size: 0.75rem;
    margin-bottom: 2px;
    word-break: break-all;
  }
  .receipt-caption-meta {
    font-size: 0.72rem;
    opacity: 0.9;
  }

  /* TEXT CARDS */
  .receipts-text-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .receipt-card.receipt-text {
    border-radius: 12px;
    background: radial-gradient(circle at top, #fecdd3 0, #f9a8d4 40%, #f472b6 80%);
    border: 1px solid rgba(190,24,93,0.9);
    box-shadow: 0 8px 18px rgba(131,24,67,0.6);
    color: #3b0828;
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .receipt-card.receipt-text:hover {
    border-color: #be185d;
  }

  .receipt-text-preview {
    font-style: italic;
    font-size: 0.9rem;
    line-height: 1.5;
    color: #4a0d2a;
  }
  .receipt-text-full {
    white-space: pre-wrap;
    word-wrap: break-word;
    font-size: 0.9rem;
    line-height: 1.6;
    color: #3b0828;
  }

  .receipt-expand-hint {
    margin-top: 8px;
    font-size: 0.78rem;
    color: #9d174d;
    font-weight: 600;
  }

  /* VIEWER */
  .receipt-viewer-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.92);
    display: none;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 14px;
  }
  .receipt-viewer-backdrop.active {
    display: flex;
  }

  .receipt-viewer-shell {
    width: min(1000px, 100%);
    max-height: 96vh;
    display: flex;
    flex-direction: column;
    background: #020617;
    border-radius: 14px;
    border: 1px solid rgba(248,187,208,0.9);
    box-shadow: 0 18px 40px rgba(0,0,0,0.9);
    overflow: hidden;
  }

  .receipt-viewer-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 8px 12px;
    border-bottom: 1px solid rgba(248,187,208,0.9);
    background: radial-gradient(circle at top left,#be185d 0,#020617 55%);
    color: #fce7f3;
  }

  .receipt-viewer-left { min-width: 0; flex: 1 1 auto; }
  .receipt-viewer-id { font-weight: 600; font-size: 0.9rem; word-break: break-all; }
  .receipt-viewer-meta { font-size: 0.75rem; opacity: 0.9; }

  .receipt-viewer-right {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .receipt-viewer-btn {
    border-radius: 999px;
    border: 1px solid rgba(248,187,208,0.9);
    background: rgba(15,23,42,0.95);
    color: #fce7f3;
    padding: 4px 12px;
    font-size: 0.8rem;
    cursor: pointer;
    text-decoration: none;
  }
  .receipt-viewer-btn.receipt-close {
    background: #f97373;
    border-color: #fecaca;
    color: #111827;
    font-weight: 700;
  }

  .receipt-viewer-viewport {
    flex: 1 1 auto;
    overflow: auto;
    padding: 10px;
    background: #000;
    display: flex;
    justify-content: center;
  }
  #receipt-viewer-img {
    max-width: 100%;
    height: auto;
  }

  @media (max-width: 720px) {
    .receipt-card.receipt-image { flex: 0 0 calc(50% - 5px); }
    .receipt-viewer-shell {
      width: 100%;
      height: 100%;
      border-radius: 0;
    }
  }
</style>

<script>
(function(){
  // Form type toggle
  var typeImage = document.getElementById('typeImage');
  var typeText = document.getElementById('typeText');
  var formImage = document.getElementById('formImage');
  var formText = document.getElementById('formText');

  function updateForm() {
    if (typeImage && typeImage.checked) {
      formImage.style.display = 'block';
      formText.style.display = 'none';
    } else if (typeText && typeText.checked) {
      formImage.style.display = 'none';
      formText.style.display = 'block';
    }
  }

  if (typeImage) typeImage.addEventListener('change', updateForm);
  if (typeText) typeText.addEventListener('change', updateForm);

  // Text receipt expand/collapse
  var textCards = document.querySelectorAll('.receipt-card.receipt-text');
  textCards.forEach(function(card) {
    card.addEventListener('click', function() {
      var preview = card.querySelector('.receipt-text-preview');
      var full = card.querySelector('.receipt-text-full');
      var hint = card.querySelector('.receipt-expand-hint');
      
      if (full.style.display === 'none') {
        preview.style.display = 'none';
        full.style.display = 'block';
        hint.textContent = '[click 2 collapse]';
      } else {
        preview.style.display = 'block';
        full.style.display = 'none';
        hint.textContent = '[click 2 expand]';
      }
    });
  });

  // Image viewer
  var imageCards = document.querySelectorAll('.receipt-card.receipt-image');
  var viewer = document.getElementById('receipt-viewer');
  var viewerImg = document.getElementById('receipt-viewer-img');
  var viewerId = document.getElementById('receipt-viewer-id');
  var viewerMeta = document.getElementById('receipt-viewer-meta');
  var downloadBtn = document.getElementById('receipt-download');
  var closeBtn = document.getElementById('receipt-close');

  function openViewer(src, id, meta) {
    viewerImg.src = src;
    viewerId.textContent = id || 'Receipt';
    viewerMeta.textContent = meta || '';
    downloadBtn.href = src;
    viewer.classList.add('active');
    viewer.setAttribute('aria-hidden', 'false');
  }

  function closeViewer() {
    viewer.classList.remove('active');
    viewer.setAttribute('aria-hidden', 'true');
  }

  imageCards.forEach(function(card) {
    card.addEventListener('click', function() {
      var src = card.getAttribute('data-src');
      var id = card.getAttribute('data-id');
      var meta = card.getAttribute('data-meta');
      if (src) openViewer(src, id, meta);
    });
  });

  if (closeBtn) closeBtn.addEventListener('click', closeViewer);

  viewer.addEventListener('click', function(e) {
    if (e.target === viewer) closeViewer();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && viewer.classList.contains('active')) {
      closeViewer();
    }
  });
})();
</script>
HTML;

// Inject dynamic HTML
$console_body_html = str_replace('{{IMAGE_CARDS}}',    $image_cards_html,    $console_body_html);
$console_body_html = str_replace('{{TEXT_CARDS}}',     $text_cards_html,     $console_body_html);
$console_body_html = str_replace('{{UPLOAD_MESSAGE}}', $upload_message_html, $console_body_html);


// ---------------------------------------------------------------------------
// 6. HAND OFF TO SHELL
// ---------------------------------------------------------------------------

require __DIR__ . '/shell.php';