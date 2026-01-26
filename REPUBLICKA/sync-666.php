<?php
// ============================================================================
// Sync 666 - Rainbow Warrior Evidence Sync 🌈💾
// RAINBOW WARRIOR KIDS LICENSE:
//   Adults must ask a kid for permission AND pay 1 (one OR more) lollipop per run.
// ============================================================================
//
// This page is the front‑door for the tiny sync toolkit you just built:
//
//   • Uses GNU Wget (GPL) bundled in your ZIP
//   • Mirrors 4 evidence roots into local folders:
//       - 666crimes-against-humanity999 (Internet Archive)
//       - trepublic receipts
//       - trepublic shards
//       - trepublic emblems
//   • Designed so any citizen can:
//       - download the ZIP from Archive.org
//       - unpack on Windows
//       - double‑click GET-ALL.bat
//       - end up with a local, portable evidence snapshot
//
// This page does NOT run the sync on the server.
// It explains the ritual and links the ZIP so kids + adults can run it on
// their own metal.
//
// GNU Wget remains GPL; your script + narrative can be licensed as you like.
// ============================================================================


// ---------------------------------------------------------------------------
// 1. Page metadata for Shell
// ---------------------------------------------------------------------------

$page_id        = 'sync-666';
$page_title     = 'Sync 666 - Rainbow Warrior Evidence Sync';
$page_canonical = 'https://trepublic.net/sync-666.php';

$page_description = 'One-click Rainbow Warrior sync kit. Kids can mirror key evidence directories from Archive and trepublic onto their own metal using a tiny wget toolkit.';

$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/emblems/crown.png';

$hero_title   = 'Sync 666 - Rainbow Warrior Evidence Sync';
$hero_tagline = "Tiny toolkit. Massive receipts. Kids hold the keys, adults bring the lollipops.";

// Console title used by Shell
$console_title = 'Sync 666 - Evidence Sync Console';


// ---------------------------------------------------------------------------
// 2. Describe what the toolkit does (kept in PHP so Shell can reuse it later)
// ---------------------------------------------------------------------------

$sync_targets = [
    [
        'label' => 'Internet Archive - 666 Crimes',
        'url'   => 'https://archive.org/download/666crimes-against-humanity999/',
        'dir'   => '666crimes',
        'note'  => 'Every file in the 666crimes-against-humanity999 item: images, txt, XML, manifest and more.'
    ],
    [
        'label' => 'trepublic receipts',
        'url'   => 'https://trepublic.net/receipts/',
        'dir'   => 'receipts',
        'note'  => 'Screenshots and receipts documenting payments, accounts and trails.'
    ],
    [
        'label' => 'trepublic shards',
        'url'   => 'https://trepublic.net/shards/',
        'dir'   => 'shards',
        'note'  => 'Shards: fragments, puzzle pieces and side‑channel receipts.'
    ],
    [
        'label' => 'trepublic emblems',
        'url'   => 'https://trepublic.net/emblems/',
        'dir'   => 'emblems',
        'note'  => 'Logos, seals and symbolic marks used across the Republic.'
    ],
];


// ---------------------------------------------------------------------------
// 3. Render console body
// ---------------------------------------------------------------------------

ob_start();
?>

<div class="sync666-intro">
  <div class="sync666-cloud-row">☁️ ☁️ ☁️ ☁️ ☁️ ☁️ ☁️</div>

  <div class="sync666-big-title">
  🕵️CRIMES AGAINST HUMANITY – Global Evidence Archive (Live Receipts)🕵️
    🌈 SYNC 666 - RAINBOW WARRIOR EVIDENCE SYNC 🌈
    <br>
    <a href="https://github.com/BardPresident/SYNC-666-CRIMES-AGAINST-HUMANITY"
       target="_blank"
       rel="noopener noreferrer"
       style="font-size:0.8rem; text-decoration:none;">
      🔗 GitHub repo: source code & sync toolkit ZIP
    <br><br></a>
  </div>

  <p>
    adults have 2 ask a kid for permission 2 use it.<br>
    price of entry: <strong>one or more lollipops per run</strong> 🍭
  </p>  

  <div class="sync666-cloud-row">☁️ ☁️ ☁️</div>

  <p>
    dis is not “another app”.<br>
    dis is a KID‑POWERED SYNC RITUAL.
  </p>

  <p>
    adults have 2 ask a kid for permission 2 use it.<br>
    price of entry: <strong>one or more lollipops per run</strong> 🍭
  </p>

  <div class="sync666-warning">
    🌈 RAINBOW WARRIOR LICENSE 🌈<br><br>
    - kids can run dis any time dey want<br>
    - adults MUST ask a kid 4 consent<br>
    - every sync = 1 lollipop tribute 2 da nearest child<br>
    - if no kid is present, u r not authorised 2 press da button
  </div>

  <p>
    under da hood it is just a tiny toolkit:<br>
    <code>wget.exe</code> + <code>GET-ALL.bat</code> + dis page.<br>
    but dat combo can mirror whole evidence trees<br>
    from Archive.org and trepublic straight onto ur metal.
  </p>

  <div class="sync666-emoji">🌈 💾 → 📂 → USB → COURTROOM 🌈</div>
</div>

<div class="sync666-console">

  <section class="sync666-section">
    <header class="sync666-head">
      <h2>💾 Wat da Sync 666 kit contains</h2>
      <p>
        A single ZIP u can download from Internet Archive,
        unpack on Windows and double‑click.
      </p>
    </header>

    <ul class="sync666-list">
      <li><code>wget.exe</code> – GNU Wget binary (GPL). Kids can inspect or replace with any trusted build.</li>
      <li><code>COPYING</code> – GPLv3 license text for wget.</li>
      <li><code>GET-ALL.bat</code> – Rainbow Warrior sync script u just helped design.</li>
      <li><code>sync-README.txt</code> – plain words explaining da ritual 4 non‑tech kids and adults.</li>
      <li>Optional helper BATs (e.g. <code>UPDATE-CRIMES-TORRENT.bat</code>) u decide 2 include.</li>
    </ul>

    <p class="sync666-note">
      NOTE: GNU Wget stays GPL. dis page + BAT can be licensed as
      “Rainbow Warrior Kids License” on top, as long as GPL rights remain intact.
    </p>
  </section>

  <section class="sync666-section">
    <header class="sync666-head">
      <h2>🌐 Evidence roots dis kit mirrors</h2>
      <p>
        Each target becomes its own folder in da same directory as da BAT.
      </p>
    </header>

    <div class="sync666-target-grid">
      <?php foreach ($sync_targets as $t): ?>
        <article class="sync666-target">
          <h3><?= htmlspecialchars($t['label'], ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="sync666-small">
            Folder name: <code><?= htmlspecialchars($t['dir'], ENT_QUOTES, 'UTF-8') ?></code><br>
            Source URL: <a href="<?= htmlspecialchars($t['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($t['url'], ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p class="sync666-small">
            <?= htmlspecialchars($t['note'], ENT_QUOTES, 'UTF-8') ?>
          </p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="sync666-section">
    <header class="sync666-head">
      <h2>🛠️ GET-ALL.bat (how it works)</h2>
      <p>
        dis is da heart of sync 666. kids can open it in Notepad and see every line.
      </p>
    </header>

<pre class="sync666-code"><code>@echo off
set "WGET=%~dp0wget.exe"

rem ===== 666crimes-against-humanity999 =====
mkdir "%~dp0666crimes" 2&gt;nul
pushd "%~dp0666crimes"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://archive.org/download/666crimes-against-humanity999/"
popd

rem ===== trepublic receipts =====
mkdir "%~dp0receipts" 2&gt;nul
pushd "%~dp0receipts"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://trepublic.net/receipts/"
popd

rem ===== trepublic shards =====
mkdir "%~dp0shards" 2&gt;nul
pushd "%~dp0shards"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://trepublic.net/shards/"
popd

rem ===== trepublic emblems =====
mkdir "%~dp0emblems" 2&gt;nul
pushd "%~dp0emblems"
"%WGET%" -r -np -nH --cut-dirs=1 -e robots=off ^
  "https://trepublic.net/emblems/"
popd

echo.
echo Rainbow Warrior sync finished.
echo Remember 2 pay da kid their lollipop.
pause</code></pre>

    <p class="sync666-small">
      it doesnt hide anyting. no updater, no spyware, no cloud.
      just HTTP requests and files landing on ur disk.
    </p>
  </section>

  <section class="sync666-section sync666-call">

    <p class="sync666-download">
      if an adult wants to mirror evidence, dis is da file dey must ask u 2 run.
	  <br><br>🔗 Toolkit ZIP: <br><br>
      <a href="https://archive.org/download/666crimes-against-humanity999/sync-666-toolkit.zip" target="_blank" rel="noopener noreferrer">
        https://archive.org/download/666crimes-against-humanity999/sync-666-toolkit.zip
      </a>
    </p>

    <p class="sync666-small">
      <br>PROTOCOL: adults are only allowed 2 click after a kid nods and receives AT LEAST 1 lollipop.
      breaking dis protocol voids ur Rainbow Warrior license in spirit (not in GPL law).
    </p>
  </section>

  <footer class="sync666-footer">
    <p>
      🌈 Wen kids can run a 20‑line script and mirror da receipts of a civilisation,
      suddenly “proof” is not owned by platforms anymore.
      it lives in backpacks, USB sticks and lunchboxes. 🌈
    </p>
  </footer>
</div>

<style>
  :root {
    --rw-red: #FF6B6B;
    --rw-orange: #FFA94D;
    --rw-yellow: #FFE066;
    --rw-green: #69DB7C;
    --rw-blue: #74C0FC;
    --rw-purple: #B197FC;
  }

  .sync666-intro{
    max-width: 800px;
    margin: 0 auto 1rem;
    padding: 0.8rem;
    font-size: 0.95rem;
    line-height: 1.7;
  }
  .sync666-cloud-row{
    text-align: center;
    letter-spacing: 0.3em;
    margin: 0.7rem 0;
    opacity: 0.8;
  }
  .sync666-big-title{
    text-align: center;
    font-size: 1.3rem;
    font-weight: 700;
    margin: 0.9rem 0;
    background: linear-gradient(90deg,var(--rw-red),var(--rw-orange),var(--rw-yellow),var(--rw-green),var(--rw-blue),var(--rw-purple));
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
  }
  .sync666-warning{
    margin: 0.9rem 0;
    padding: 0.8rem;
    border-radius: 12px;
    text-align: center;
    background: linear-gradient(135deg,rgba(255,107,107,0.15),rgba(255,169,77,0.15));
    border: 1px solid rgba(255,107,107,0.5);
    font-size: 0.9rem;
  }
  .sync666-emoji{
    text-align:center;
    margin-top:0.7rem;
  }

  .sync666-console{
    max-width: 1120px;
    margin: 0 auto 1.5rem;
    padding: 0.8rem 0.8rem 1.2rem;
    font-size: 0.9rem;
  }
  .sync666-section{
    margin-top: 0.8rem;
    padding: 0.75rem 0.9rem 0.9rem;
    border-radius: 20px;
    background:
      radial-gradient(circle at 20% 20%,rgba(255,230,102,0.12),transparent 55%),
      radial-gradient(circle at 80% 80%,rgba(105,219,124,0.12),transparent 55%),
      #ffffff;
    border: 2px solid rgba(105,219,124,0.45);
    box-shadow: 0 10px 24px rgba(105,219,124,0.35);
  }
  .sync666-head h2{
    margin:0;
    font-size:0.98rem;
    letter-spacing:0.12em;
    text-transform:uppercase;
    background: linear-gradient(90deg,var(--rw-green),var(--rw-blue),var(--rw-purple));
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    background-clip:text;
  }
  .sync666-head p{
    margin:0.2rem 0 0.4rem;
    font-size:0.8rem;
    color:#4b5563;
  }
  .sync666-list{
    margin:0.15rem 0 0.4rem 1.1rem;
    padding:0;
    font-size:0.82rem;
  }
  .sync666-small{
    font-size:0.8rem;
    color:#4b5563;
  }
  .sync666-note{
    font-size:0.78rem;
    color:#6b7280;
    margin-top:0.25rem;
  }

  .sync666-target-grid{
    display:grid;
    grid-template-columns: minmax(0,1.1fr) minmax(0,1.1fr);
    gap:0.6rem 0.8rem;
  }
  .sync666-target{
    padding:0.55rem 0.6rem;
    border-radius:14px;
    background:rgba(255,255,255,0.97);
    border:1px solid rgba(116,192,252,0.6);
    box-shadow:0 6px 16px rgba(116,192,252,0.35);
  }
  .sync666-target h3{
    margin:0 0 0.15rem;
    font-size:0.9rem;
  }

  .sync666-code{
    margin:0.4rem 0 0.3rem;
    padding:0.5rem;
    border-radius:10px;
    background:#020617;
    color:#e5e7eb;
    font-size:0.72rem;
    overflow-x:auto;
  }

  .sync666-call{
    background:
      radial-gradient(circle at 30% 30%,rgba(255,230,102,0.25),transparent 55%),
      radial-gradient(circle at 70% 70%,rgba(255,169,77,0.2),transparent 55%),
      #ffffff;
    border-color:rgba(255,169,77,0.7);
    box-shadow:0 14px 32px rgba(255,169,77,0.45);
  }
  .sync666-download{
    margin-top:0.4rem;
    font-size:0.82rem;
  }
  .sync666-footer{
    margin-top:0.8rem;
    text-align:center;
    font-size:0.8rem;
    color:#374151;
  }

  @media(max-width:768px){
    .sync666-console{padding:0.7rem 0.4rem 1.1rem;}
    .sync666-target-grid{grid-template-columns:minmax(0,1fr);}
  }
</style>

<?php
$console_body_html = ob_get_clean();

require __DIR__ . '/shell.php';
