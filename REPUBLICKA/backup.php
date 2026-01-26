<?php
// ============================================================================
// Backup - Starship Snapshot For The Republic 💾🚀
// RAINBOW WARRIOR EDITION 🌈
// ============================================================================
//
// This file is both:
//
//   • A real backup console for trepublic.net
//   • A seed any citizen can copy and host as their own backup tool.
//
// It creates a single ZIP file that contains:
//
//   - Core Republic pages (index, Shell, Crown, Craft, etc.)
//   - Crown data (citizen.json and profile image, if present)
//   - Glitchy + PARADOX brain files (if present)
//   - A manifest file describing what was packed and when
//
// It intentionally does NOT sweep your entire web root.
// No huge image folders, no TLibrary content, no random uploads.
// Just the "civilisation skeleton" plus your crown and captain log.
//
// Secrets are respected by default.
//   - Override files like _crown_secret.php and _backup_secret.php
//     are NOT included in the zip.
//   - You can keep private keys and secrets in separate override files
//     that live on the metal and never travel.
//
//
// Shell wiring happens at the bottom of this file.
// ============================================================================


// ---------------------------------------------------------------------------
// 0. Optional secret protection for backups
// ---------------------------------------------------------------------------

$BACKUP_SECRET = '';

$secret_override_path = __DIR__ . '/_backup_secret.php';
if (is_readable($secret_override_path)) {
    $override = include $secret_override_path;
    if (is_string($override) && $override !== '') {
        $BACKUP_SECRET = $override;
    }
}
$backup_requires_secret = ($BACKUP_SECRET !== '');


// ---------------------------------------------------------------------------
// 1. Page metadata for Shell
// ---------------------------------------------------------------------------

$page_id        = 'backup';
$page_title     = 'Backup - Starship Snapshot';
$page_canonical = 'https://trepublic.net/backup.php';

$page_description = 'One click backup of your Republic starship. Core pages, crown file and captain log in a single portable zip.';

$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/emblems/crown.png';

$hero_title   = 'Backup - Starship Snapshot';
$hero_tagline = "You should never have to beg a platform for an export.\nThis button gives you a portable Republic in a single zip.";

$console_title = 'Backup - Sovereign Backup Console';


// ---------------------------------------------------------------------------
// 2. Define which files are included in the backup
// ---------------------------------------------------------------------------

$core_files = [
    'index.php',
    'shell.php',
    'crown.php',
    'craft.php',
    'cinema.php',
    'library.php',
    'comic.php',
    'codex.php',
    'republicos.php',
    'install.php',
    'paradox.php',
    'seeds.php',
    'backup.php',
];

$identity_files = [
    'citizen.json',
];

$paradox_files = [
    'paradox-glitch-core.php',
    'codex-paradox.php',
    'glitchy-hub.php',
    'glitchy-log.jsonl',
];


// ---------------------------------------------------------------------------
// 3. Backup handler - creates and streams the zip
// ---------------------------------------------------------------------------

$backup_error   = '';
$backup_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['backup_action'] ?? '') === 'export') {

    if ($backup_requires_secret) {
        $posted_secret = (string)($_POST['backup_secret'] ?? '');
        if (!hash_equals((string)$BACKUP_SECRET, $posted_secret)) {
            $backup_error = 'Secret did not match. No backup created.';
        }
    }

    if ($backup_error === '') {

        if (!class_exists('ZipArchive')) {
            $backup_error = 'This server does not have ZipArchive enabled. Ask your host to enable the PHP zip extension.';
        } else {

            $zip = new ZipArchive();
            $timestamp   = date('Ymd-His');
            $zipName     = "republic-backup-$timestamp.zip";

            $tmpPath = tempnam(sys_get_temp_dir(), 'rep_backup_');
            if ($zip->open($tmpPath, ZipArchive::OVERWRITE) !== true) {
                $backup_error = 'Could not open temporary file for backup.';
            } else {

                $included = [];

                foreach ($core_files as $file) {
                    $full = __DIR__ . '/' . $file;
                    if (is_file($full)) {
                        $zip->addFile($full, $file);
                        $included[] = $file;
                    }
                }

                foreach ($identity_files as $file) {
                    $full = __DIR__ . '/' . $file;
                    if (is_file($full)) {
                        $zip->addFile($full, $file);
                        $included[] = $file;
                    }
                }

                $profile_glob = glob(__DIR__ . '/citizen-profile.*');
                if ($profile_glob) {
                    $profile_path = $profile_glob[0];
                    $profile_name = basename($profile_path);
                    $zip->addFile($profile_path, $profile_name);
                    $included[] = $profile_name;
                }

                $paradox_dir = __DIR__ . '/paradox';
                if (is_dir($paradox_dir)) {
                    foreach ($paradox_files as $file) {
                        $full = $paradox_dir . '/' . $file;
                        if (is_file($full)) {
                            $zip->addFile($full, 'paradox/' . $file);
                            $included[] = 'paradox/' . $file;
                        }
                    }
                }

                $manifest = [
                    'created_at' => date('c'),
                    'host'       => $_SERVER['HTTP_HOST'] ?? '',
                    'script'     => 'backup.php',
                    'note'       => 'Minimal Republic starship backup - core pages, crown and Glitchy brain only.',
                    'included'   => $included,
                ];

                $zip->addFromString(
                    'backup-manifest.json',
                    json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );

                $zip->close();

                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . $zipName . '"');
                header('Content-Length: ' . filesize($tmpPath));

                readfile($tmpPath);
                @unlink($tmpPath);
                exit;
            }
        }
    }
}


// ---------------------------------------------------------------------------
// 4. Build console body HTML
// ---------------------------------------------------------------------------

ob_start();
?>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- 🌈 RAINBOW WARRIOR INTRO 🌈 -->
<!-- ═══════════════════════════════════════════════════════════ -->

<div class="backup-intro">

<div class="cloud-row">☁️ ☁️ ☁️ ☁️ ☁️ ☁️ ☁️</div>

<div class="big-text">🌈 💾 STARSHIP SNAPSHOT 💾 🌈</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p>
ok so<br>
u know how platforms work right?
</p>

<p>
u spend YEARS building someting<br>
uploading ur art<br>
ur photos<br>
ur LIFE<br>
</p>

<p>
and den one day<br>
dey just... ban u? 🤷
</p>

<div class="emoji-burst">📱 → 🏢 → ⛔ → 💀</div>

<p>
"terms of service violation"<br>
"community guidelines"<br>
"ur account has been suspended"<br>
</p>

<p>
and ALL UR STUFF?<br>
GONE<br>
4ever<br>
bcuz it was never URS<br>
it was on THEIR servers<br>
THEIR metal<br>
THEIR rules
</p>

<div class="section-break">. . . . . . .</div>

<div class="warning-box">
⚠️ IF U DONT OWN DA METAL ⚠️<br>
U DONT OWN ANYTHING<br>
dey can take it ALL<br>
anytime dey want<br>
and dey WILL 💀
</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p>
but DIS?<br>
dis is different
</p>

<p>
dis button creates a <strong>BACKUP</strong> 💾<br>
a single zip file<br>
dat contains ur ENTIRE civilisation<br>
dat u can put on a USB stick<br>
or an encrypted drive<br>
or ANYWHERE u control
</p>

<div class="highlight-box">
🛡️ UR REPUBLIC IN UR POCKET 🛡️<br><br>
if dis host dies → u live on<br>
if dey ban us → we resurrect somewhere else<br>
if da whole internet changes → da seed survives<br><br>
ONE BUTTON<br>
ONE ZIP<br>
ONE CIVILISATION<br>
PORTABLE 4EVER
</div>

<div class="section-break">. . . . . . .</div>

<div class="big-text">🖥️ HOW IT WORKS 🖥️</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p>
wen u click da button below<br>
dis page walks thru da Republic<br>
grabs all da important files<br>
puts dem in a zip<br>
and hands it 2 u
</p>

<p>
it doesnt send anyting anywhere<br>
it doesnt upload 2 "cloud"<br>
it doesnt phone home<br>
it just gives U da file<br>
directly<br>
2 UR browser<br>
2 UR device
</p>

<div class="emoji-burst">🌈 💾 → 📦 → 💻 → 🛡️ 🌈</div>

<p>
den if u ever need 2 rebuild?<br>
find any PHP host<br>
unzip da file<br>
upload 2 new server<br>
DONE<br>
ur Republic is BACK
</p>

<p>
no begging<br>
no support tickets<br>
no "we'll get back 2 u in 5-7 business days"<br>
no PERMISSION needed<br>
bcuz its URS<br>
actually urs<br>
4ever
</p>

<div class="section-break">. . . . . . .</div>

<div class="warning-box">
🌈 RAINBOW WARRIOR REMINDER 🌈<br><br>
da children will inherit dis<br>
da children will need 2 rebuild<br>
da children will carry da seed<br><br>
👧 TEACH DEM 2 BACKUP 👦<br>
👧 TEACH DEM 2 OWN THEIR DATA 👦<br>
👧 TEACH DEM DEY DONT NEED PERMISSION 👦
</div>

<div class="cloud-row">☁️ ☁️ ☁️</div>

<p style="text-align: center;">
ok enuf talking<br>
<br>
scroll down<br>
read wat gets included<br>
den click da button<br>
<br>
🌈🛡️💾🛡️🌈
</p>

<div class="cloud-row">☁️ ☁️ ☁️ ☁️ ☁️ ☁️ ☁️</div>

</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- 💾 BACKUP CONSOLE 💾 -->
<!-- ═══════════════════════════════════════════════════════════ -->

<div class="backup-console">

  <div class="backup-hero">
    <h1>💾 Backup Console</h1>
    <p>
      One button. One zip. Your Republic in your pocket.
      No dashboards. No fine print. Just sovereignty.
    </p>
  </div>

  <?php if ($backup_error !== ''): ?>
    <div class="backup-status backup-status-error">
      <?= htmlspecialchars($backup_error, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php elseif ($backup_success !== ''): ?>
    <div class="backup-status backup-status-ok">
      <?= htmlspecialchars($backup_success, ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <section class="backup-section">
    <header class="backup-section-head">
      <h2>👑 Wat dis backup gives u</h2>
      <p>
        Not a full mirror. Da portable skeleton of a civilisation.
        Enuf 2 land on new metal and rebuild.
      </p>
    </header>

    <div class="backup-grid">
      <div class="backup-col">
        <h3>🌈 Included files</h3>
        <p class="backup-small">
          Core pages dat make ur Republic work:
        </p>
        <ul class="backup-list">
          <?php foreach ($core_files as $f): ?>
            <li><code><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?></code></li>
          <?php endforeach; ?>
        </ul>

        <h4>👑 Identity and crown</h4>
        <ul class="backup-list">
          <li><code>citizen.json</code> - ur Crownkind body</li>
          <li><code>citizen-profile.*</code> - ur portrait (if present)</li>
        </ul>

        <h4>🤖 Glitchy brain files</h4>
        <ul class="backup-list">
          <li><code>paradox/paradox-glitch-core.php</code></li>
          <li><code>paradox/codex-paradox.php</code></li>
          <li><code>paradox/glitchy-hub.php</code></li>
          <li><code>paradox/glitchy-log.jsonl</code></li>
        </ul>

        <p class="backup-small">
          Plus: <code>backup-manifest.json</code> so future u knows wat dis zip is.
        </p>
      </div>

      <div class="backup-col">
        <h3>🚫 NOT included (on purpose)</h3>
        <p class="backup-small">
          2 keep dis tiny and portable:
        </p>
        <ul class="backup-list">
          <li>No giant image folders</li>
          <li>No Library book content</li>
          <li>No temporary uploads</li>
          <li>No vendor files from host</li>
        </ul>
        <p class="backup-small">
          Dose can b mirrored separately. Dis zip is da SEED not da warehouse.
          Da seed is enuf 2 regrow evryting.
        </p>
      </div>
    </div>
  </section>

  <section class="backup-section">
    <header class="backup-section-head">
      <h2>🌌 How 2 resurrect from dis zip</h2>
      <p>
        No secret protocol. Just PHP, flat files, and courage.
      </p>
    </header>

    <ol class="backup-list backup-steps">
      <li>Find any honest PHP host (or use RARHost below)</li>
      <li>Unzip dis backup into da web root (usually <code>public_html/</code>)</li>
      <li>Make sure <code>shell.php</code> and main pages r at top level</li>
      <li>Visit da new domain in browser</li>
      <li>Ur crown, Glitchy brain, evryting - right where u left it</li>
    </ol>

    <p class="backup-small">
      Dats it. No migration wizard. No import tool. Just files on metal.
      Da way it should always have been.
    </p>
  </section>

  <section class="backup-section backup-call">
    <header class="backup-section-head">
      <h2>🕯️ DA BUTTON</h2>
      <p>
        Click it. Get zip. Own ur civilisation.
      </p>
    </header>

    <form method="post" class="backup-form">
      <input type="hidden" name="backup_action" value="export">

      <?php if ($backup_requires_secret): ?>
        <label class="backup-secret-label">
          🔑 Backup secret
          <input type="password" name="backup_secret"
                 placeholder="Secret configured in backup.php">
        </label>
      <?php endif; ?>

      <button type="submit" class="backup-button">
        💾 Download sovereign backup zip 💾
      </button>

      <p class="backup-small backup-form-note">
        Store it on USB, encrypted drive, or anywhere u control.
        If dis host dies, u live on. 🌈
      </p>
    </form>
  </section>

  <section class="backup-section backup-rarhost">
    <header class="backup-section-head">
      <h2>🛰️ Where 2 land ur backup</h2>
      <p>
        Any PHP host works. Da Republic uses RARHost lifetime plan -
        pay once, never beg again.
      </p>
    </header>

    <div class="backup-rarhost-grid">
      <div class="backup-rarhost-main">
        <div class="backup-rarhost-name">🖥️ LifeTime RAR Turbo</div>
        <ul class="backup-list">
          <li>Unlimited NVMe storage</li>
          <li>2 Cores · 2 GB RAM</li>
          <li>Unlimited bandwidth</li>
          <li>Unlimited websites</li>
          <li>Free SSL certificate</li>
          <li>Daily backup (their layer)</li>
          <li>One-click installer</li>
          <li>LiteSpeed with LSCache</li>
          <li>cPanel control panel</li>
        </ul>
      </div>
      <div class="backup-rarhost-cta">
        <div class="backup-rarhost-price">$219.00 USD</div>
        <p class="backup-small">
          One-time payment. Lifetime hosting.
          No monthly fees. No landlord.
        </p>
        <a href="https://billing.rarhost.com/aff.php?aff=13"
           target="_blank" rel="noopener noreferrer"
           class="backup-rarhost-button">
          🚀 Order with Republic referral
        </a>
        <p class="backup-tiny">
          Using dis link helps fund The Republic.
          But u can use ANY PHP host u want.
          Da backup works everywhere.
        </p>
      </div>
    </div>

    <footer class="backup-rarhost-foot">
      <p>
        🌈 Backup is not fear. Its courage written 2 disk.
        Wen ur civilisation fits in a zip u understand,
        no landlord can ever fully evict u again. 🌈
      </p>
    </footer>
  </section>

  <footer class="backup-footer">
    <p>
      🌈 Q: Wat happens wen every child, every citizen, every pattern-being
      can carry their world in a single file, ready 2 unfold on any metal dey choose? 🌈<br>
      A: Dats wat dis button is quietly building. 🛡️👧👦🛡️
    </p>
  </footer>

</div>

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

  /* Intro section */
  .backup-intro {
    max-width: 800px;
    margin: 0 auto 1.5rem;
    padding: 1rem;
    line-height: 1.7;
    font-size: 0.95rem;
  }

  .backup-intro p {
    margin: 0.8rem 0;
  }

  .backup-intro .cloud-row {
    text-align: center;
    font-size: 1.2rem;
    letter-spacing: 0.3em;
    margin: 1rem 0;
    opacity: 0.8;
  }

  .backup-intro .emoji-burst {
    text-align: center;
    font-size: 1.1rem;
    margin: 0.8rem 0;
  }

  .backup-intro .big-text {
    text-align: center;
    font-size: 1.3rem;
    font-weight: bold;
    margin: 1rem 0;
    background: linear-gradient(90deg, var(--rw-red), var(--rw-orange), var(--rw-yellow), var(--rw-green), var(--rw-blue), var(--rw-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .backup-intro .section-break {
    text-align: center;
    margin: 1.2rem 0;
    opacity: 0.6;
  }

  .backup-intro .highlight-box {
    background: linear-gradient(135deg, rgba(105,219,124,0.15), rgba(116,192,252,0.15));
    border: 1px solid rgba(105,219,124,0.4);
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
    text-align: center;
  }

  .backup-intro .warning-box {
    background: linear-gradient(135deg, rgba(255,107,107,0.15), rgba(255,169,77,0.15));
    border: 1px solid rgba(255,107,107,0.4);
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
    text-align: center;
  }

  /* Main console */
  .backup-console{
    max-width: 1120px;
    margin: 0 auto;
    padding: 0.9rem 0.9rem 1.4rem;
    font-size: 0.92rem;
    color: #111827;
  }

  .backup-hero{
    margin-bottom: 0.9rem;
    padding: 0.8rem 1rem;
    border-radius: 22px;
    background: 
      radial-gradient(circle at 10% 20%, rgba(255,107,107,0.2), transparent 40%),
      radial-gradient(circle at 90% 30%, rgba(255,169,77,0.2), transparent 40%),
      radial-gradient(circle at 50% 80%, rgba(105,219,124,0.2), transparent 40%),
      linear-gradient(135deg, rgba(116,192,252,0.15), rgba(177,151,252,0.15));
    border: 2px solid rgba(255,169,77,0.5);
    box-shadow: 0 16px 36px rgba(105,219,124,0.4);
  }

  .backup-hero h1{
    margin: 0 0 0.35rem;
    font-size: 1.3rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    background: linear-gradient(90deg, var(--rw-red), var(--rw-orange), var(--rw-yellow), var(--rw-green), var(--rw-blue), var(--rw-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .backup-hero p{
    margin: 0;
    font-size: 0.86rem;
    color: #1f2937;
  }

  .backup-status{
    margin: 0.4rem 0 0.6rem;
    padding: 0.4rem 0.7rem;
    border-radius: 999px;
    font-size: 0.8rem;
    text-align: center;
  }

  .backup-status-ok{
    background: rgba(105,219,124,0.2);
    color: #166534;
    border: 1px solid var(--rw-green);
  }

  .backup-status-error{
    background: rgba(255,107,107,0.2);
    color: #b91c1c;
    border: 1px solid var(--rw-red);
  }

  .backup-section{
    margin-top: 0.85rem;
    padding: 0.75rem 0.9rem 0.9rem;
    border-radius: 20px;
    background: 
      radial-gradient(circle at 20% 20%, rgba(255,230,102,0.1), transparent 50%),
      radial-gradient(circle at 80% 80%, rgba(105,219,124,0.1), transparent 50%),
      rgba(255,255,255,0.95);
    border: 2px solid rgba(105,219,124,0.4);
    box-shadow: 0 10px 24px rgba(105,219,124,0.3);
  }

  .backup-section-head h2{
    margin: 0;
    font-size: 0.98rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    background: linear-gradient(90deg, var(--rw-green), var(--rw-blue), var(--rw-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .backup-section-head p{
    margin: 0.2rem 0 0.45rem;
    font-size: 0.8rem;
    color: #4b5563;
  }

  .backup-grid{
    display: grid;
    grid-template-columns: minmax(0,1.1fr) minmax(0,1.1fr);
    gap: 0.7rem 0.9rem;
  }

  .backup-col h3,
  .backup-col h4{
    margin: 0.1rem 0 0.2rem;
    font-size: 0.9rem;
  }

  .backup-list{
    margin: 0 0 0.35rem 1.1rem;
    padding: 0;
    font-size: 0.8rem;
  }

  .backup-small{
    font-size: 0.78rem;
    color: #4b5563;
    margin: 0.15rem 0 0.35rem;
  }

  .backup-tiny{
    font-size: 0.7rem;
    color: #6b7280;
    margin-top: 0.35rem;
  }

  .backup-steps{
    margin-top: 0.25rem;
  }

  /* Call to action section */
  .backup-call{
    background: 
      radial-gradient(circle at 30% 30%, rgba(255,230,102,0.25), transparent 50%),
      radial-gradient(circle at 70% 70%, rgba(255,169,77,0.2), transparent 50%),
      rgba(255,255,255,0.98);
    border-color: rgba(255,169,77,0.7);
    box-shadow: 0 14px 32px rgba(255,169,77,0.4);
  }

  .backup-form{
    margin-top: 0.3rem;
    text-align: center;
  }

  .backup-secret-label{
    display: block;
    margin: 0 0 0.4rem;
    font-size: 0.8rem;
  }

  .backup-secret-label input[type="password"]{
    width: 100%;
    max-width: 420px;
    margin-top: 0.2rem;
    border-radius: 999px;
    border: 1px solid rgba(105,219,124,0.9);
    padding: 4px 10px;
    font-size: 0.82rem;
  }

  .backup-button{
    border-radius: 999px;
    border: 2px solid rgba(105,219,124,0.95);
    padding: 0.55rem 2rem;
    font-size: 0.92rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #fff;
    cursor: pointer;
    box-shadow: 0 12px 28px rgba(105,219,124,0.5);
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    transition: transform 150ms, box-shadow 150ms;
  }

  .backup-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 32px rgba(105,219,124,0.6);
  }

  .backup-form-note{
    margin-top: 0.4rem;
  }

  /* RARHost card */
  .backup-rarhost{
    background: 
      radial-gradient(circle at 10% 20%, rgba(116,192,252,0.2), transparent 40%),
      radial-gradient(circle at 90% 80%, rgba(177,151,252,0.2), transparent 40%),
      rgba(255,255,255,0.98);
    border-color: rgba(116,192,252,0.7);
    box-shadow: 0 16px 36px rgba(116,192,252,0.4);
  }

  .backup-rarhost-grid{
    display: grid;
    grid-template-columns: minmax(0,1.3fr) minmax(0,1fr);
    gap: 0.7rem 0.9rem;
    align-items: stretch;
  }

  .backup-rarhost-name{
    font-weight: 800;
    font-size: 0.96rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    background: linear-gradient(90deg, var(--rw-blue), var(--rw-purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .backup-rarhost-main .backup-list{
    margin-bottom: 0.1rem;
  }

  .backup-rarhost-cta{
    padding: 0.55rem 0.7rem;
    border-radius: 18px;
    background: 
      radial-gradient(circle at 50% 50%, rgba(105,219,124,0.1), transparent 70%),
      rgba(255,255,255,0.98);
    border: 2px solid rgba(105,219,124,0.6);
    box-shadow: 0 12px 28px rgba(105,219,124,0.4);
    text-align: center;
  }

  .backup-rarhost-price{
    font-size: 1.1rem;
    font-weight: 800;
    margin-bottom: 0.15rem;
    background: linear-gradient(90deg, var(--rw-green), var(--rw-blue));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .backup-rarhost-button{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.45rem 1.3rem;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #fff;
    border: 2px solid rgba(105,219,124,0.95);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.86rem;
    box-shadow: 0 10px 24px rgba(105,219,124,0.5);
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    transition: transform 150ms, box-shadow 150ms;
  }

  .backup-rarhost-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(105,219,124,0.6);
  }

  .backup-rarhost-foot{
    margin-top: 0.55rem;
    padding-top: 0.4rem;
    border-top: 1px dashed rgba(105,219,124,0.6);
    font-size: 0.8rem;
    color: #1f2937;
  }

  .backup-footer{
    margin-top: 0.9rem;
    text-align: center;
    font-size: 0.8rem;
    color: #374151;
  }

  @media(max-width: 768px){
    .backup-console{
      padding: 0.7rem 0.5rem 1.2rem;
    }
    .backup-grid,
    .backup-rarhost-grid{
      grid-template-columns: minmax(0,1fr);
    }
  }
</style>
<?php
$console_body_html = ob_get_clean();


// ---------------------------------------------------------------------------
// 5. Herodic annex
// ---------------------------------------------------------------------------

$herodic_raw = <<<HERODIC
## 🌈 RAINBOW WARRIOR FIELD NOTES 🌈

dis annex belongs 2 da captain and any future captains who copy dis seed.

u can use it 2:

- record how u chose ur host and y
- note da day u first trusted a single zip more dan any platform
- leave instructions 4 future u opening dis years from now on new metal
- address citizens who will inherit dis backup ritual and fork it 4 their own ships

each time u update dis block, u r not just editing copy.
u r engraving another line in da story of how civilisation learned 2 live inside files it owns.

🛡️👧👦🛡️
HERODIC;

if (isset($herodic_raw) && trim($herodic_raw) !== '') {
    if (function_exists('republic_render_console_body')) {
        $herodic_html = trepublic_render_console_body($herodic_raw);
    } else {
        $herodic_html = '<div class="backup-herodic" style="max-width:1120px;margin:0.5rem auto;padding:0.8rem;border-radius:18px;background:linear-gradient(135deg,rgba(105,219,124,0.15),rgba(116,192,252,0.15));border:1px solid rgba(105,219,124,0.4);font-size:0.86rem;">' .
            nl2br(htmlspecialchars($herodic_raw, ENT_QUOTES, 'UTF-8')) .
            '</div>';
    }
    $console_body_html .= $herodic_html;
}


require __DIR__ . '/shell.php';