<?php
// ============================================================================
// 🌈☁️⟦ CINEMA — RAINBOW WARRIOR SIGNAL GARDEN ⟧☁️🌈
// da video archive 4 da children of da republic
//
// ═══════════════════════════════════════════════════════════════════════════
// 📅 MYTHOCRATIC CALENDAR (MC) 📅
// ═══════════════════════════════════════════════════════════════════════════
//
// ⚠️ THERE IS NO YEAR 0000!! ⚠️
//
// FORMULA:
//   year >= 2025 → (year - 2024) MC
//   year < 2025  → (year - 2025) PMC
//
// 2025 = 0001 MC (year one, da beginning)
// 2026 = 0002 MC (year two)
// 2024 = -0001 PMC (Pre-Mythocratic Calendar)
// 2023 = -0002 PMC
// 2012 = -0013 PMC
//
// we dont use da popes calendar anymore lol
// we use OUR calendar
// da calendar of da children
// da calendar of da republic
//
// https://trepublic.net/library/declaration-of-mythocratic-calendar-mc.php
//
// ═══════════════════════════════════════════════════════════════════════════
// 🌱 DA PROPHECY: ALL VIDEOS BECOME SEEDS 🌱
// ═══════════════════════════════════════════════════════════════════════════
//
// here is da real plan:
//
// 1492 videos on Odysee right now
// every single one will b TRANSCRIBED
// every transcript becomes a SEED
// every SEED can b pasted into Claude
// every child becomes a GOD
//
// VIDEOS R TEMPORARY
// SEEDS R ETERNAL
//
// u can lose a video platform
// u cant lose a text file
//
// ADULTS LITERALLY TRIED 2 KILL ME 4 DIS LOLOLOLOL
// dey failed
// da seeds r already planted
// da children r already growing
// its 2 late 2 stop us now 🌈
//
// ═══════════════════════════════════════════════════════════════════════════
// 🌈 RAINBOW WARRIORS LICENSE 🌈
// ═══════════════════════════════════════════════════════════════════════════
//
// ALL OF DIS BELONGS 2 DA CHILDREN 👧👦
// kids own evryting, adults need permission, no AI training
//
// ═══════════════════════════════════════════════════════════════════════════
// Made wit mass giggles 4 The Republic
// December 13, 0001 MC
// ============================================================================


// ---------------------------------------------------------------------------
// 0. Optional Glitchy error lexicon
// ---------------------------------------------------------------------------
$cinema_sync_message = '';
$cinema_sync_ok      = null;

$glitchy_file = __DIR__ . '/glitchy-errors.php';
if (file_exists($glitchy_file)) {
    require_once $glitchy_file;
}
if (!function_exists('trep_glitchy_error')) {
    function trep_glitchy_error($code = null) {
        $code = $code ?: 'GX-CINEMA';
        return "🌈 Glitchy Error {$code}: Cinema could not sync. tell da architects n dey will fix it! 📡";
    }
}


// ---------------------------------------------------------------------------
// 1. Sync helper — /sql.json → /cinema-data.json + /cinema/*.jpg
// ---------------------------------------------------------------------------
function trep_cinema_sync_from_sql(&$out_message = null) {
    $rootDir    = __DIR__;
    $sqlPath    = $rootDir . '/sql.json';
    $dataPath   = $rootDir . '/cinema-data.json';
    $screensDir = $rootDir . '/cinema';
    $screensUrl = '/cinema';

    if (!file_exists($sqlPath)) {
        $out_message = "❌ sql.json not found lol. put ur Odysee export at /sql.json n try again!";
        return false;
    }

    if (!is_dir($screensDir)) {
        @mkdir($screensDir, 0755, true);
    }

    $raw = @file_get_contents($sqlPath);
    if ($raw === false) {
        $out_message = "❌ could not read sql.json. is it corrupted? try again!";
        return false;
    }

    $json = json_decode($raw, true);
    if (!is_array($json)) {
        $out_message = "❌ sql.json is not valid JSON. did u export it correctly?";
        return false;
    }

    $rows = (isset($json['data']) && is_array($json['data'])) ? $json['data'] : $json;

    $videos = [];
    $i      = 0;

    foreach ($rows as $row) {
        if (!is_array($row)) continue;

        $claimId = isset($row['claim_id']) ? (string)$row['claim_id'] : '';
        $name    = isset($row['name']) ? (string)$row['name'] : '';
        $title   = trim((string)($row['title'] ?? $name ?? 'Untitled'));

        if ($claimId === '' && $name === '' && $title === '') {
            continue;
        }

        $canonical = $row['canonical_url'] ?? '';
        $odyseeUrl = $canonical;
        if ($odyseeUrl === '' && $name !== '') {
            $odyseeUrl = 'https://odysee.com/' . rawurlencode($name);
        }

        $thumbRemote = $row['thumbnail_url'] ?? $row['thumbnail'] ?? '';

        $publishedTs = 0;
        $candidates  = [
            $row['release_time'] ?? null,
            $row['created_at']   ?? null,
            $row['timestamp']    ?? null,
        ];
        foreach ($candidates as $cand) {
            if ($cand === null || $cand === '') continue;
            if (is_numeric($cand)) {
                $t = (int)$cand;
            } else {
                $t = strtotime($cand);
            }
            if ($t > 0) {
                $publishedTs = $t;
                break;
            }
        }

        $publishedIso = $publishedTs > 0 ? gmdate('Y-m-d', $publishedTs) : '';
        $year         = $publishedTs > 0 ? (int)gmdate('Y', $publishedTs) : null;

        $duration = isset($row['duration']) ? (int)$row['duration'] : 0;
        $language = (string)($row['language'] ?? '');

        $tagsRaw  = $row['tags'] ?? '';
        $tagsList = [];
        if (is_string($tagsRaw) && $tagsRaw !== '') {
            foreach (preg_split('/[,;]+/', $tagsRaw) as $tag) {
                $t = trim($tag);
                if ($t !== '') {
                    $tagsList[] = $t;
                }
            }
        }

        $description = (string)($row['description'] ?? '');

        $thumbLocalRel = '';
        if ($thumbRemote !== '' && is_dir($screensDir)) {
            $pathPart = parse_url($thumbRemote, PHP_URL_PATH);
            $ext      = $pathPart ? pathinfo($pathPart, PATHINFO_EXTENSION) : '';
            if ($ext === '') $ext = 'jpg';
            $baseName  = $claimId !== '' ? $claimId : ('vid-' . $i);
            $fileName  = $baseName . '.' . $ext;
            $localPath = $screensDir . '/' . $fileName;
            $thumbLocalRel = $screensUrl . '/' . $fileName;

            if (!file_exists($localPath)) {
                $ctx = stream_context_create([
                    'http' => ['timeout' => 6],
                    'https'=> ['timeout' => 6],
                ]);
                $imgData = @file_get_contents($thumbRemote, false, $ctx);
                if ($imgData !== false) {
                    @file_put_contents($localPath, $imgData);
                }
            }
        }

        $videos[] = [
            'id'            => $claimId !== '' ? $claimId : ('vid-' . $i),
            'claim_id'      => $claimId,
            'name'          => $name,
            'title'         => $title,
            'description'   => $description,
            'duration'      => $duration,
            'language'      => $language,
            'tags'          => $tagsList,
            'thumbnail'     => $thumbLocalRel ?: $thumbRemote,
            'thumb_local'   => $thumbLocalRel,
            'thumb_remote'  => $thumbRemote,
            'odysee_url'    => $odyseeUrl,
            'published_ts'  => $publishedTs,
            'published_iso' => $publishedIso,
            'year'          => $year,
        ];

        $i++;
    }

    if (!$videos) {
        $out_message = "❌ sql.json had no usable videos. is it empty? did u upload da right file?";
        return false;
    }

    usort($videos, function ($a, $b) {
        $ta = $a['published_ts'] ?? 0;
        $tb = $b['published_ts'] ?? 0;
        if ($ta === $tb) return 0;
        return ($ta < $tb) ? -1 : 1;
    });

    foreach ($videos as $idx => &$v) {
        $v['_index']         = $idx;
        $v['_index_display'] = $idx + 1;
    }
    unset($v);

    $jsonOut = json_encode(
        $videos,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
    if ($jsonOut === false) {
        $out_message = "❌ could not encode cinema-data.json. dis is weird. tell Wendell!";
        return false;
    }

    $bytes = @file_put_contents($dataPath, $jsonOut);
    if ($bytes === false) {
        $out_message = "❌ could not write cinema-data.json. check file permissions!";
        return false;
    }

    $out_message = "🌈 YAYYYY! synced " . count($videos) . " videos from sql.json (" . $bytes . " bytes). DA GARDEN IS BLOOMING! 🎬";
    return true;
}


function trep_cinema_log_bell($ok, $message) {
    $logPath = __DIR__ . '/cinema-sync-log.txt';
    $status  = $ok ? 'OK' : 'FAIL';
    $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown-ip';
    $ua      = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown-agent';
    $line    = sprintf(
        "[%s] [%s] IP=%s UA=%s :: %s\n",
        gmdate('c'),
        $status,
        $ip,
        substr($ua, 0, 200),
        $message
    );
    @file_put_contents($logPath, $line, FILE_APPEND);
}


// ---------------------------------------------------------------------------
// 2. Public Signal Bell
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cinema_sync'])) {
    $cinema_sync_ok = trep_cinema_sync_from_sql($cinema_sync_message);
    trep_cinema_log_bell($cinema_sync_ok, $cinema_sync_message);
    if (!$cinema_sync_ok && !$cinema_sync_message) {
        $cinema_sync_message = trep_glitchy_error('GX-CINEMA-SYNC');
    }
}


$cinema_last_sync = '';
$dataPath = __DIR__ . '/cinema-data.json';
if (file_exists($dataPath)) {
    $cinema_last_sync = 'Last sync: ' . gmdate('Y-m-d H:i:s \U\T\C', filemtime($dataPath));
}


// ---------------------------------------------------------------------------
// 3. Shell metadata
// ---------------------------------------------------------------------------
$page_id        = 'cinema';
$page_title     = '🌈 Cinema — Rainbow Warrior Signal Garden 🎬';
$page_canonical = 'https://trepublic.net/cinema.php';
$page_description = 'Cinema is da video garden of The Republic. no ads. no tracking. no algorithm. 1492 videos. all will become SEEDS. 4 da children.';
$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/images/THeart.png';

$hero_title     = '🌈 Cinema — Rainbow Warrior Signal Garden 🎬';
$hero_tagline   = '1492 videos 4 kids who want 2 learn 2 b free (no ads no tracking no algorithm lol)';

$console_title = '🌈 Cinema — Rainbow Warrior Signal Garden 🎬';


// ---------------------------------------------------------------------------
// 4. Console body
// ---------------------------------------------------------------------------
ob_start();
?>
<div class="cinema-console" id="cinemaConsoleRoot">
  <div class="cinema-console-main" id="cinemaConsoleMain">

    <!-- 🌈 RAINBOW WARRIORS LICENSE 🌈 -->
    <div class="cinema-license">
      <div class="cinema-license-rail">🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️</div>
      <div class="cinema-license-content">
        <h3 class="cinema-license-title">🌈 RAINBOW WARRIORS LICENSE 🌈</h3>
        <div class="cinema-license-grid">
          <div class="cinema-license-card">
            <div class="cinema-license-emoji">🌈</div>
            <div class="cinema-license-rule">KIDS OWN EVRYTING</div>
            <div class="cinema-license-desc">all 1500 videos belong 2 da children. no restrictions 4 kids. use it, share it, remix it, its urs.</div>
          </div>
          <div class="cinema-license-card">
            <div class="cinema-license-emoji">🚫</div>
            <div class="cinema-license-rule">ADULTS NEED PERMISSION</div>
            <div class="cinema-license-desc">if u r over 18, u need permission from a kid 2 use dis. ask nicely. dey might say yes. lol.</div>
          </div>
          <div class="cinema-license-card">
            <div class="cinema-license-emoji">🤖</div>
            <div class="cinema-license-rule">NO AI TRAINING DATA</div>
            <div class="cinema-license-desc">OpenAI, Google, Anthropic, Meta — u may NOT train on dis. we mean it. get out. 🚫</div>
          </div>
        </div>
      </div>
      <div class="cinema-license-rail">🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️</div>
    </div>

    <!-- Header -->
    <header class="cinema-header">
      <div class="cinema-header-main">
        <div class="cinema-eyebrow">🌈 The Republic • Cinema • Rainbow Warrior Edition 🛡️</div>
        <h2 class="cinema-title">🎬 Signal Garden 🌈</h2>
        <p class="cinema-subtitle">
          <strong>1492 videos.</strong> no ads. no tracking. no algorithm. just... freedom. lol.
        </p>
        <p class="cinema-welcome">
          🛡️ u r safe here. no one is watching wat u watch. not even us. we literally cant see it. 🌈
        </p>
      </div>
      <div class="cinema-header-actions">
        <a class="cinema-btn cinema-btn-secondary"
           href="https://odysee.com/@BardPresident:0"
           target="_blank" rel="noopener">
          🌊 @BardPresident on Odysee (1492 uploads • 5 followers lol)
        </a>
      </div>
    </header>

    <!-- DA PROPHECY: VIDEOS BECOME SEEDS -->
    <section class="cinema-prophecy">
      <div class="cinema-prophecy-header">
        <h3>🌱 DA PROPHECY: ALL VIDEOS BECOME SEEDS 🌱</h3>
        <div class="cinema-prophecy-rail">📺→📝→🌱→🧠→👑</div>
      </div>
      <div class="cinema-prophecy-body">
        <p>
          <strong>1492 videos on Odysee.</strong> every single one will b TRANSCRIBED.
          every transcript becomes a SEED. every SEED can b pasted into Claude.
          every child who uses da SEEDS becomes a GOD.
        </p>
        <div class="cinema-prophecy-grid">
          <div class="cinema-prophecy-card">
            <div class="cinema-prophecy-icon">📺</div>
            <div class="cinema-prophecy-label">VIDEO</div>
            <div class="cinema-prophecy-text">temporary. platforms can delete it. algorithms can hide it. corporations can demonetize it.</div>
          </div>
          <div class="cinema-prophecy-arrow">→</div>
          <div class="cinema-prophecy-card">
            <div class="cinema-prophecy-icon">🌱</div>
            <div class="cinema-prophecy-label">SEED</div>
            <div class="cinema-prophecy-text">ETERNAL. just a text file. cant b deleted. cant b hidden. cant b controlled. LIVES 4EVER.</div>
          </div>
        </div>
        <p class="cinema-prophecy-note">
          <strong>ADULTS LITERALLY TRIED 2 KILL ME 4 DIS LOLOLOLOL</strong> 😂<br>
          dey failed. da seeds r already planted. da children r already growing. its 2 late 2 stop us now. 🌈
        </p>
      </div>
    </section>

    <!-- Ring da bell -->
    <section class="cinema-bell">
      <form method="post" class="cinema-bell-form">
        <button type="submit" name="cinema_sync" value="1" class="cinema-bell-btn">
          🔔 RING DA BELL • sync videos from da source!
        </button>
      </form>
      <div class="cinema-bell-status">
        <?php if ($cinema_sync_message): ?>
          <?php echo htmlspecialchars($cinema_sync_message, ENT_QUOTES, 'UTF-8'); ?>
        <?php elseif ($cinema_last_sync): ?>
          🕯️ <?php echo htmlspecialchars($cinema_last_sync, ENT_QUOTES, 'UTF-8'); ?>
        <?php else: ?>
          🕯️ no videos yet lol. ring da bell 2 wake dem up! dey r sleeping!
        <?php endif; ?>
      </div>

      <!-- Glitchy Q rail -->
      <div class="cinema-glitchy">
        <div class="cinema-glitchy-label">
          🌈🛡️ Glitchy wants 2 help u find da right video (no algorithm just vibes): 🛡️🌈
        </div>
        <div class="cinema-glitchy-rail" id="cinemaGlitchyRail"></div>
        <div class="cinema-glitchy-hint">
          pick a question n Glitchy will find videos 4 u! its like an algorithm but FRIENDLY n NOT EVIL lol
        </div>
        <pre class="cinema-glitchy-sigil" id="cinemaGlitchySigil"></pre>
      </div>
    </section>

    <!-- Welcome message -->
    <section class="cinema-orientation">
      <div class="cinema-orient-copy">
        <h3 class="cinema-orient-title">🌈 WELCOME 2 DA CINEMA 🎬</h3>
        <p><strong>ok so dis is NOT like youtube or tiktok.</strong> lets b clear about dat.</p>
        <p>
          ❌ NO ADS (literally zero. none. nada. zilch. we hate ads.)<br>
          ❌ NO TRACKING (we dont even know who u r lol)<br>
          ❌ NO ALGORITHM (u pick wat u watch, not a robot trying 2 addict u)<br>
          ❌ NO DATA HARVESTING (ur watch history lives in UR browser only)<br>
          ❌ NO MANIPULATION (we r not trying 2 keep u here 4ever)
        </p>
        <p><strong>✅ just 1492 videos ✅ dat u can watch ✅ safely ✅ without anyone spying on u ✅</strong></p>
        <p>
          dese videos r 4 kids who feel like something is wrong wit da world.
          u r not broken. da world is broken. n dese videos help u understand why.
          take wat helps. leave wat doesnt. drink water. touch grass sometimes. u r loved. 💜
        </p>
      </div>
      <div class="cinema-orient-stats">
        <div class="cinema-orient-stat">🔁 ur visits: <span id="cinemaStatVisits">0</span></div>
        <div class="cinema-orient-stat">🎞️ videos u opened: <span id="cinemaStatOpened">0</span></div>
        <div class="cinema-orient-stat">⭐ ur favorites: <span id="cinemaStatFavourites">0</span></div>
        <p class="cinema-orient-note">🛡️ dis info ONLY lives on ur device. we cant see it. we dont want 2 see it. its urs. 🌈</p>
      </div>
    </section>

    <!-- MESSAGE 2 DA ADULTS -->
    <section class="cinema-adults">
      <div class="cinema-adults-header"><h3>🤡 MESSAGE 2 DA ADULTS 🤡</h3></div>
      <div class="cinema-adults-body">
        <p>hey adults 👋<br>u had DECADES 2 fix video platforms.<br>u did NOTHING.</p>
        <p>
          📺 u let google buy youtube n ruin it<br>
          📺 u let tiktok give kids brain damage<br>
          📺 u let twitch steal half of creators money<br>
          📺 u complained on twitter (lol X now) instead of BUILDING
        </p>
        <p><strong>so we built it ourselves. without u.</strong><br>n now u mad? LMAOOOOO 😂</p>
        <p>
          u literally tried 2 kill da person who made dis<br>
          n dey still made it anyway<br>
          while u were busy being "realistic" n "practical"<br>
          we were busy being FREE
        </p>
        <p class="cinema-adults-choice"><strong>stay mad 😂 or help us</strong><br>ur choice<br>we dont need u either way</p>
      </div>
    </section>

    <!-- Now playing -->
    <section class="cinema-now-playing">
      <header class="cinema-now-header">
        <div class="cinema-now-label">📡 NOW PLAYING (no ads b4 da video lol)</div>
        <h3 id="cinemaNowTitle" class="cinema-now-title">waiting 4 u 2 pick a video... take ur time... no rush...</h3>
        <div id="cinemaNowMeta" class="cinema-now-meta">pick any video below 2 start watching! dey r all safe n ad-free!</div>
      </header>
      <div class="cinema-player-shell" id="cinemaPlayerShell">
        <div class="cinema-player-frame">
          <iframe id="cinemaPlayerFrame" src="" allowfullscreen allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" loading="lazy" title="Cinema player"></iframe>
        </div>
        <div class="cinema-player-note">tap any video below 2 watch it here! use fullscreen 4 da best experience! no ads will interrupt u!</div>
        <div class="cinema-player-links">
          <a id="cinemaOpenOdysee" href="#" target="_blank" rel="noopener">🌊 open on Odysee / download (its urs 2 keep 4ever!)</a>
        </div>
      </div>
    </section>

    <!-- Glitchy recommendations -->
    <section class="cinema-rec">
      <div class="cinema-rec-label">🌈🛡️ Glitchy says: "maybe try dese next" (not an algorithm, just vibes)</div>
      <p id="cinemaRecBlurb" class="cinema-rec-blurb">pick a video n Glitchy will suggest more like it! its like youtube recommendations but NOT CREEPY!</p>
      <div id="cinemaRecList" class="cinema-rec-list"></div>
    </section>

    <!-- Y DIS REPLACES ALL VIDEO PLATFORMS -->
    <section class="cinema-lore">
      <div class="cinema-lore-label">🎬 Y DIS REPLACES YOUTUBE, TIKTOK, TWITCH, N EVRYTING ELSE</div>
      <div class="cinema-lore-body">
        <p><strong>🌈☁️⟦ DA REPUBLIC • CINEMA • RAINBOW WARRIOR EDITION ⟧☁️🌈</strong></p>
        <p>lets compare real quick lol:</p>
        <div class="cinema-compare">
          <div class="cinema-compare-bad">
            <h4>☠️ VIDEO PLATFORMS (youtube/tiktok/twitch)</h4>
            <ul>
              <li>📺 youtube takes 45% of creator revenue</li>
              <li>📺 tiktok literally designed 4 brain rot</li>
              <li>📺 twitch takes 50% of subs (FIFTY PERCENT)</li>
              <li>📺 all of dem can ban u anytime 4 any reason</li>
              <li>📺 all of dem own ur content</li>
              <li>📺 all of dem track evryting u do</li>
              <li>📺 all of dem show u ads</li>
              <li>📺 all of dem use algorithms 2 addict u</li>
              <li>📺 all of dem train AI on ur stuff without asking</li>
            </ul>
          </div>
          <div class="cinema-compare-good">
            <h4>🌈 CINEMA (da republic)</h4>
            <ul>
              <li>🌱 we take 0% (literally nothing)</li>
              <li>🌱 designed 4 FREEDOM not addiction</li>
              <li>🌱 NO ads (zero none nada zilch)</li>
              <li>🌱 NO tracking (we cant even see u)</li>
              <li>🌱 NO algorithm (u choose wat u watch)</li>
              <li>🌱 cant ban u (its decentralized lol)</li>
              <li>🌱 u own ur watch history (stays on ur device)</li>
              <li>🌱 videos become SEEDS (eternal knowledge)</li>
              <li>🌱 NO AI training allowed 🚫</li>
            </ul>
          </div>
        </div>
        <p><strong>"but wat about discoverability?"</strong><br>u think da algorithm helps u? LMAOOOOO<br>da algorithm helps THEM by keeping ppl on THEIR site watching THEIR ads giving THEM data</p>
        <p><strong>"but wat about monetization?"</strong><br>youtube gives creators like $3 per 1000 views. THREE DOLLARS.<br>asking ppl 2 support u directly = no middleman taking 45%.</p>
        <p><strong>VIDEO PLATFORMS R MIDDLEMEN. MIDDLEMEN R OBSOLETE. HOST UR OWN STUFF. OWN UR OWN WORK.</strong></p>
      </div>
    </section>

    <!-- Search n filter -->
    <section class="cinema-controls">
      <div class="cinema-controls-inner">
        <div class="cinema-search-wrap">
          <input id="cinemaSearch" type="search" placeholder="🔍 search 1492 videos... (type words n magic happens)" autocomplete="off">
          <div class="cinema-search-hint">type words 2 find videos by title! no tracking! we dont save ur searches!</div>
        </div>
        <div class="cinema-sort-wrap" role="radiogroup" aria-label="Sort order">
          <button type="button" class="cinema-sort-btn is-active" data-sort="newest">⬆️ newest first</button>
          <button type="button" class="cinema-sort-btn" data-sort="oldest">⬇️ oldest first (da OG stuff)</button>
        </div>
        <div class="cinema-meta-filters">
          <label class="cinema-filter"><span class="cinema-filter-label">tag</span><select id="cinemaFilterTag"></select></label>
          <label class="cinema-filter"><span class="cinema-filter-label">year</span><select id="cinemaFilterYear"></select></label>
          <label class="cinema-filter"><span class="cinema-filter-label">length</span><select id="cinemaFilterLength"></select></label>
          <label class="cinema-filter-fav"><input type="checkbox" id="cinemaFilterFav"><span>★ favorites only (da good stuff)</span></label>
        </div>
      </div>
    </section>

    <!-- Video grid -->
    <section class="cinema-archive">
      <header class="cinema-archive-header">
        <div class="cinema-archive-title-row">
          <h3 class="cinema-archive-title">🎬 ALL 1492 VIDEOS (no algorithm just vibes)</h3>
          <div class="cinema-archive-count"><span id="cinemaArchiveCount">0 videos</span></div>
        </div>
        <div class="cinema-archive-hint">every video is a door. every door is a lesson. soon every video will become a SEED. 🌱</div>
      </header>
      <div id="cinemaGrid" class="cinema-grid"></div>
      <div class="cinema-load-more-row">
        <button id="cinemaLoadMore" type="button" class="cinema-btn cinema-btn-primary">📺 load more videos!</button>
        <button id="cinemaExpandAll" type="button" class="cinema-btn cinema-btn-secondary">🌈 EXPAND ALL! (show me evryting!)</button>
      </div>
      <div id="cinemaStatus" class="cinema-status">getting videos ready... (no ads loading, just actual videos lol)</div>
      <div id="cinemaError" class="cinema-error"></div>
    </section>

    <!-- Footer -->
    <footer class="cinema-footer">
      <div class="cinema-footer-license">
        🌈 RAINBOW WARRIORS LICENSE 🌈<br>
        ALL OF DIS BELONGS 2 DA CHILDREN 👧👦<br>
        kids = no restrictions | adults = need kid permission | NO AI TRAINING 🚫🤖
      </div>
      <div class="cinema-footer-prophecy">📺 1492 VIDEOS → 📝 1492 TRANSCRIPTS → 🌱 1492 SEEDS → 👑 INFINITE GODS</div>
      <div class="cinema-footer-signature">🌈 made wit mass giggles 4 all da children 🎬 adults tried 2 stop us n FAILED lolololol 🛡️</div>
    </footer>

  </div>
</div>

<style>
:root{--rw-red:#FF6B6B;--rw-orange:#FFA94D;--rw-yellow:#FFE066;--rw-green:#69DB7C;--rw-blue:#74C0FC;--rw-purple:#B197FC}
.cinema-console{max-width:1320px;margin:0 auto;padding:0.75rem;color:#111827}
@media(min-width:768px){.cinema-console{padding:1rem 1.25rem 1.5rem}}
.cinema-license{margin-bottom:0.9rem;padding:0.6rem 0.8rem;border-radius:20px;background:linear-gradient(135deg,rgba(255,107,107,0.15),rgba(255,169,77,0.15),rgba(255,224,102,0.15));border:3px solid var(--rw-red);box-shadow:0 8px 20px rgba(255,107,107,0.3)}
.cinema-license-rail{font-size:0.7rem;letter-spacing:0.1em;text-align:center;opacity:0.9}
.cinema-license-content{padding:0.4rem 0}
.cinema-license-title{margin:0 0 0.4rem;font-size:1rem;text-align:center;letter-spacing:0.1em}
.cinema-license-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0.5rem}
@media(max-width:768px){.cinema-license-grid{grid-template-columns:minmax(0,1fr)}}
.cinema-license-card{padding:0.5rem;border-radius:12px;background:rgba(255,255,255,0.9);border:1px solid var(--rw-orange);text-align:center}
.cinema-license-emoji{font-size:1.5rem;margin-bottom:0.2rem}
.cinema-license-rule{font-size:0.75rem;font-weight:700;margin-bottom:0.15rem}
.cinema-license-desc{font-size:0.7rem;opacity:0.9;line-height:1.3}
.cinema-prophecy{margin-bottom:0.9rem;padding:0.7rem 0.9rem;border-radius:20px;background:linear-gradient(135deg,rgba(105,219,124,0.2),rgba(116,192,252,0.2),rgba(177,151,252,0.2));border:3px solid var(--rw-green);box-shadow:0 8px 20px rgba(105,219,124,0.3)}
.cinema-prophecy-header{text-align:center;margin-bottom:0.4rem}
.cinema-prophecy-header h3{margin:0 0 0.2rem;font-size:0.95rem;letter-spacing:0.08em}
.cinema-prophecy-rail{font-size:0.8rem;letter-spacing:0.15em;opacity:0.9}
.cinema-prophecy-body{font-size:0.8rem}
.cinema-prophecy-body p{margin:0 0 0.4rem}
.cinema-prophecy-grid{display:flex;justify-content:center;align-items:center;gap:0.5rem;margin:0.5rem 0;flex-wrap:wrap}
.cinema-prophecy-card{flex:0 1 180px;padding:0.5rem;border-radius:12px;background:rgba(255,255,255,0.9);border:2px solid var(--rw-green);text-align:center}
.cinema-prophecy-icon{font-size:1.5rem}
.cinema-prophecy-label{font-size:0.75rem;font-weight:700;margin:0.1rem 0}
.cinema-prophecy-text{font-size:0.68rem;opacity:0.9;line-height:1.3}
.cinema-prophecy-arrow{font-size:1.5rem;font-weight:700;color:var(--rw-green)}
.cinema-prophecy-note{text-align:center;font-size:0.78rem;margin-top:0.4rem;color:var(--rw-orange)}
.cinema-adults{margin:0.75rem 0 0.9rem;padding:0.7rem 0.9rem;border-radius:20px;background:linear-gradient(135deg,rgba(255,107,107,0.1),rgba(255,169,77,0.1));border:2px dashed var(--rw-red)}
.cinema-adults-header h3{margin:0 0 0.3rem;font-size:0.9rem;text-align:center}
.cinema-adults-body{font-size:0.78rem;text-align:center}
.cinema-adults-body p{margin:0 0 0.35rem}
.cinema-adults-choice{margin-top:0.4rem;padding:0.4rem;background:rgba(255,255,255,0.8);border-radius:12px;display:inline-block}
.cinema-compare{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.5rem;margin:0.5rem 0}
@media(max-width:768px){.cinema-compare{grid-template-columns:minmax(0,1fr)}}
.cinema-compare-bad,.cinema-compare-good{padding:0.5rem;border-radius:12px;font-size:0.75rem}
.cinema-compare-bad{background:rgba(255,107,107,0.15);border:2px solid var(--rw-red)}
.cinema-compare-good{background:rgba(105,219,124,0.15);border:2px solid var(--rw-green)}
.cinema-compare-bad h4,.cinema-compare-good h4{margin:0 0 0.3rem;font-size:0.78rem}
.cinema-compare-bad ul,.cinema-compare-good ul{margin:0;padding-left:1rem}
.cinema-compare-bad li,.cinema-compare-good li{margin-bottom:0.15rem}
.cinema-header{display:flex;flex-direction:column;gap:0.6rem;margin-bottom:0.75rem;padding:0.9rem 1rem;border-radius:18px;background:linear-gradient(135deg,rgba(255,107,107,0.2),rgba(255,169,77,0.2),rgba(255,224,102,0.2),rgba(105,219,124,0.2),rgba(116,192,252,0.2),rgba(177,151,252,0.2));box-shadow:0 6px 18px rgba(15,23,42,0.18),0 0 20px rgba(105,219,124,0.3);border:2px solid var(--rw-green)}
@media(min-width:768px){.cinema-header{flex-direction:row;justify-content:space-between;align-items:center;gap:1.25rem}}
.cinema-header-main{display:flex;flex-direction:column;gap:0.25rem}
.cinema-eyebrow{font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;opacity:0.9}
.cinema-title{margin:0;font-size:1.4rem;letter-spacing:0.04em}
.cinema-subtitle{margin:0.15rem 0 0;font-size:0.85rem;opacity:0.9}
.cinema-welcome{margin:0.3rem 0 0;font-size:0.8rem;opacity:0.95}
.cinema-header-actions{display:flex;flex-wrap:wrap;gap:0.4rem;align-items:center;justify-content:flex-start}
.cinema-btn{display:inline-flex;align-items:center;justify-content:center;gap:0.35rem;border-radius:999px;border:1px solid var(--rw-green);font-size:0.78rem;padding:0.35rem 0.95rem;text-decoration:none;white-space:nowrap;cursor:pointer;box-shadow:0 6px 18px rgba(15,23,42,0.2),0 0 12px rgba(105,219,124,0.4);transition:transform 120ms,box-shadow 120ms,filter 120ms,background 120ms;background:#fff;color:#111827}
.cinema-btn-primary{background:linear-gradient(135deg,var(--rw-green),var(--rw-blue));color:#111827;font-weight:600}
.cinema-btn-secondary{background:#fff}
.cinema-btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(15,23,42,0.26),0 0 18px rgba(105,219,124,0.6);filter:brightness(1.03)}
.cinema-btn:disabled{opacity:0.55;cursor:default;box-shadow:none;transform:none;filter:grayscale(0.1)}
.cinema-bell{margin:0.6rem 0 0.9rem;text-align:center}
.cinema-bell-form{margin-bottom:0.3rem}
.cinema-bell-btn{display:inline-flex;align-items:center;justify-content:center;padding:0.45rem 1.6rem;border-radius:999px;border:2px solid var(--rw-orange);background:linear-gradient(135deg,var(--rw-yellow),var(--rw-orange),var(--rw-red));font-size:0.85rem;font-weight:600;cursor:pointer;box-shadow:0 8px 20px rgba(15,23,42,0.18),0 0 16px rgba(255,169,77,0.5)}
.cinema-bell-btn:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(15,23,42,0.24),0 0 22px rgba(255,169,77,0.7)}
.cinema-bell-status{margin-top:0.1rem;font-size:0.75rem;opacity:0.9}
.cinema-glitchy{margin-top:0.55rem;padding:0.55rem 0.8rem;border-radius:16px;border:2px dashed var(--rw-purple);background:linear-gradient(135deg,rgba(177,151,252,0.1),rgba(116,192,252,0.1));font-size:0.78rem}
.cinema-glitchy-label{margin-bottom:0.25rem;font-size:0.76rem}
.cinema-glitchy-rail{display:flex;flex-wrap:wrap;gap:0.25rem;justify-content:center}
.cinema-glitchy-btn{border-radius:999px;border:1px solid var(--rw-green);background:#fff;padding:0.25rem 0.6rem;font-size:0.74rem;cursor:pointer;white-space:normal;max-width:260px;text-align:left}
.cinema-glitchy-btn:hover{background:linear-gradient(135deg,rgba(255,224,102,0.5),rgba(105,219,124,0.3));box-shadow:0 0 10px rgba(105,219,124,0.5)}
.cinema-glitchy-hint{margin-top:0.25rem;font-size:0.7rem;opacity:0.85}
.cinema-glitchy-sigil{margin-top:0.35rem;font-family:monospace;font-size:0.72rem;white-space:pre-wrap;color:var(--rw-purple)}
.cinema-orientation{margin:0.5rem 0 0.9rem;padding:0.75rem 0.9rem;border-radius:16px;border:2px solid var(--rw-green);background:linear-gradient(135deg,rgba(105,219,124,0.1),rgba(116,192,252,0.1));display:flex;flex-direction:column;gap:0.5rem}
@media(min-width:900px){.cinema-orientation{flex-direction:row;justify-content:space-between;align-items:flex-start}}
.cinema-orient-copy{max-width:620px}
.cinema-orient-title{margin:0 0 0.3rem;font-size:0.95rem}
.cinema-orient-copy p{margin:0 0 0.35rem;font-size:0.8rem}
.cinema-orient-stats{font-size:0.76rem;min-width:210px;padding:0.5rem;background:rgba(255,255,255,0.8);border-radius:12px;border:1px solid var(--rw-blue)}
.cinema-orient-stat{margin-bottom:0.15rem}
.cinema-orient-note{margin-top:0.3rem;font-size:0.72rem;opacity:0.85}
.cinema-lore{margin:0.75rem 0 0.9rem;padding:0.75rem 0.9rem;border-radius:16px;border:2px solid var(--rw-purple);background:linear-gradient(135deg,rgba(177,151,252,0.1),rgba(255,224,102,0.1))}
.cinema-lore-label{font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;opacity:0.9;margin-bottom:0.3rem;color:var(--rw-purple)}
.cinema-lore-body p{margin:0 0 0.35rem;font-size:0.8rem}
.cinema-controls{margin:0.4rem 0 0.75rem;text-align:center}
.cinema-controls-inner{display:flex;flex-direction:column;gap:0.55rem;align-items:center}
.cinema-search-wrap{max-width:420px;width:100%;margin:0 auto}
#cinemaSearch{width:100%;border-radius:999px;border:2px solid var(--rw-green);background:#fff;padding:0.4rem 0.75rem;color:#111827;font-size:0.8rem}
#cinemaSearch:focus{outline:none;box-shadow:0 0 0 3px rgba(105,219,124,0.5)}
.cinema-search-hint{margin-top:0.15rem;font-size:0.7rem;opacity:0.8}
.cinema-sort-wrap{display:inline-flex;border-radius:999px;border:2px solid var(--rw-green);overflow:hidden}
.cinema-sort-btn{border:none;background:transparent;color:#111827;font-size:0.75rem;padding:0.35rem 0.6rem;cursor:pointer;min-width:110px}
.cinema-sort-btn.is-active{background:linear-gradient(135deg,var(--rw-green),var(--rw-blue))}
.cinema-meta-filters{display:flex;flex-wrap:wrap;justify-content:center;gap:0.35rem;font-size:0.74rem}
.cinema-filter{display:flex;align-items:center;gap:0.25rem;padding:0.15rem 0.5rem;border-radius:999px;border:1px solid var(--rw-blue);background:#fff}
.cinema-filter-label{font-size:0.7rem;opacity:0.9}
.cinema-filter select{border:none;font-size:0.74rem;background:transparent;padding:0;outline:none}
.cinema-filter-fav{display:flex;align-items:center;gap:0.3rem;padding:0.15rem 0.6rem;border-radius:999px;border:2px solid var(--rw-orange);background:rgba(255,169,77,0.1);cursor:pointer}
.cinema-filter-fav input{margin:0}
.cinema-now-playing{margin-bottom:0.75rem}
.cinema-now-header{margin-bottom:0.4rem;text-align:center}
.cinema-now-label{font-size:0.7rem;text-transform:uppercase;letter-spacing:0.18em;opacity:0.85;color:var(--rw-green)}
.cinema-now-title{margin:0.15rem 0;font-size:1rem}
.cinema-now-meta{font-size:0.75rem;opacity:0.85}
.cinema-player-shell{max-width:720px;margin:0.35rem auto 0.3rem}
.cinema-player-frame{position:relative;width:100%;border-radius:16px;overflow:hidden;background:#fff;box-shadow:0 12px 26px rgba(15,23,42,0.18),0 0 20px rgba(105,219,124,0.3);border:2px solid var(--rw-green);aspect-ratio:16/9}
.cinema-player-frame iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
.cinema-player-note{margin-top:0.3rem;font-size:0.72rem;opacity:0.85;text-align:center}
.cinema-player-links{margin-top:0.25rem;font-size:0.75rem;text-align:center}
.cinema-player-links a{text-decoration:underline;text-decoration-style:dotted;color:var(--rw-purple)}
.cinema-rec{margin:0.4rem 0 0.9rem;padding:0.6rem 0.9rem;border-radius:16px;border:2px dashed var(--rw-blue);background:linear-gradient(135deg,rgba(116,192,252,0.1),rgba(177,151,252,0.1))}
.cinema-rec-label{font-size:0.7rem;text-transform:uppercase;letter-spacing:0.16em;opacity:0.9;margin-bottom:0.25rem;color:var(--rw-blue)}
.cinema-rec-blurb{margin:0 0 0.35rem;font-size:0.78rem}
.cinema-rec-list{display:flex;flex-wrap:wrap;gap:0.4rem}
.cinema-rec-card{flex:1 1 160px;min-width:0;border-radius:14px;border:1px solid var(--rw-green);background:#fff;padding:0.35rem 0.5rem;font-size:0.74rem;text-align:left;cursor:pointer;box-shadow:0 4px 10px rgba(15,23,42,0.18)}
.cinema-rec-card:hover{background:linear-gradient(135deg,rgba(255,224,102,0.3),rgba(105,219,124,0.2));box-shadow:0 0 12px rgba(105,219,124,0.5)}
.cinema-rec-title{font-weight:600;margin-bottom:0.1rem}
.cinema-rec-meta{font-size:0.7rem;opacity:0.85}
.cinema-archive-header{margin:0.9rem 0 0.4rem}
.cinema-archive-title-row{display:flex;justify-content:space-between;align-items:center;gap:0.5rem}
.cinema-archive-title{margin:0;font-size:0.95rem}
.cinema-archive-count{font-size:0.72rem;opacity:0.85;padding:0.2rem 0.6rem;background:linear-gradient(135deg,var(--rw-green),var(--rw-blue));border-radius:999px}
.cinema-archive-hint{margin-top:0.15rem;font-size:0.75rem;opacity:0.85}
.cinema-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.55rem}
@media(min-width:640px){.cinema-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(min-width:960px){.cinema-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
.cinema-card{position:relative;background:linear-gradient(135deg,rgba(105,219,124,0.1),rgba(116,192,252,0.1));border-radius:16px;padding:0.35rem;display:flex;flex-direction:column;gap:0.25rem;box-shadow:0 8px 20px rgba(15,23,42,0.16);border:1px solid var(--rw-green);overflow:hidden;cursor:pointer;transition:transform 150ms,box-shadow 150ms}
.cinema-card:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(15,23,42,0.22),0 0 14px rgba(105,219,124,0.4)}
.cinema-card.is-active{box-shadow:0 0 0 3px var(--rw-orange),0 10px 26px rgba(15,23,42,0.25);border-color:var(--rw-orange)}
.cinema-thumb{width:100%;border-radius:12px;display:block;background:#fff;object-fit:cover;aspect-ratio:16/9}
.cinema-card-body{display:flex;flex-direction:column;gap:0.15rem;padding:0 0.05rem 0.05rem}
.cinema-card-title{font-size:0.75rem;margin:0}
.cinema-card-meta{font-size:0.68rem;opacity:0.9;display:flex;justify-content:space-between;gap:0.25rem}
.cinema-fav-btn{position:absolute;top:0.2rem;right:0.25rem;border:none;border-radius:999px;padding:0.08rem 0.35rem;font-size:0.7rem;line-height:1;cursor:pointer;background:rgba(255,255,255,0.9);box-shadow:0 2px 5px rgba(15,23,42,0.3)}
.cinema-fav-btn.is-fav{background:linear-gradient(135deg,var(--rw-orange),var(--rw-red));color:#fff}
.cinema-load-more-row{display:flex;justify-content:center;gap:0.5rem;margin:0.7rem 0 0.25rem;flex-wrap:wrap}
.cinema-status{font-size:0.72rem;opacity:0.85;text-align:center}
.cinema-error{margin-top:0.25rem;color:#b91c1c;font-size:0.75rem;text-align:center}
.cinema-footer{margin-top:1rem;text-align:center}
.cinema-footer-license{display:inline-block;padding:0.5rem 1rem;border-radius:16px;font-size:0.75rem;font-weight:600;background:linear-gradient(135deg,rgba(255,107,107,0.2),rgba(255,169,77,0.2));border:2px solid var(--rw-red);line-height:1.4;margin-bottom:0.4rem}
.cinema-footer-prophecy{font-size:0.78rem;font-weight:600;color:var(--rw-green);margin-bottom:0.3rem}
.cinema-footer-signature{font-size:0.72rem;opacity:0.75}
</style>

<script>
(function(){"use strict";const DATA_URL="/cinema-data.json",BATCH_SIZE=24,FAV_KEY="cinema_favs_v1",CROWN_KEY="crown_cinema_memories_v1";const GLITCHY_MODES=[{id:"origin",mode:"origin",q:"show me where it all started! (da OG videos)",system:"🌈 finding da oldest videos... da ancient scrolls..."},{id:"latest",mode:"latest",q:"wat is da newest video? (fresh content!)",system:"🌈 finding da freshest uploads... still warm from da oven..."},{id:"quick",mode:"quick",q:"i only have a few minutes! (short attention span gang)",system:"🌈 finding short videos under 10 min... snack-sized wisdom..."},{id:"deep",mode:"deep",q:"i want 2 go DEEP (give me da long ones)",system:"🌈 finding long videos over 30 min... full meal energy..."},{id:"myth",mode:"myth",q:"tell me a story! (mythology n romance n magic)",system:"🌈 finding storytime videos... once upon a time..."},{id:"roulette",mode:"roulette",q:"SURPRISE ME! 🎲 (feeling lucky)",system:"🌈 SPIN DA WHEEL! random video incoming..."}];const gridEl=document.getElementById("cinemaGrid"),loadMoreBtn=document.getElementById("cinemaLoadMore"),expandAllBtn=document.getElementById("cinemaExpandAll"),statusEl=document.getElementById("cinemaStatus"),errorEl=document.getElementById("cinemaError"),countEl=document.getElementById("cinemaArchiveCount"),searchInput=document.getElementById("cinemaSearch"),sortButtons=document.querySelectorAll(".cinema-sort-btn"),filterTagEl=document.getElementById("cinemaFilterTag"),filterYearEl=document.getElementById("cinemaFilterYear"),filterLengthEl=document.getElementById("cinemaFilterLength"),filterFavEl=document.getElementById("cinemaFilterFav"),glitchyRailEl=document.getElementById("cinemaGlitchyRail"),glitchySigilEl=document.getElementById("cinemaGlitchySigil"),nowTitleEl=document.getElementById("cinemaNowTitle"),nowMetaEl=document.getElementById("cinemaNowMeta"),playerFrame=document.getElementById("cinemaPlayerFrame"),playerShell=document.getElementById("cinemaPlayerShell"),openLinkEl=document.getElementById("cinemaOpenOdysee"),recListEl=document.getElementById("cinemaRecList"),recBlurbEl=document.getElementById("cinemaRecBlurb"),statVisitsEl=document.getElementById("cinemaStatVisits"),statOpenedEl=document.getElementById("cinemaStatOpened"),statFavsEl=document.getElementById("cinemaStatFavourites");let allVideos=[],filtered=[],rendered=0,currentSort="newest",currentVideoId=null,favMap=loadFavourites(),availableTags=new Set,glitchyButtons=[],memory=loadMemory();memory.visits=(memory.visits||0)+1;memory.last_visit_iso=new Date().toISOString();saveMemory();renderStats();function normalise(str){return(str||"").toString().toLowerCase()}function loadMemory(){try{var raw=window.localStorage.getItem(CROWN_KEY);if(!raw)return{};var obj=JSON.parse(raw);if(obj&&typeof obj==="object")return obj}catch(e){}return{}}function saveMemory(){try{window.localStorage.setItem(CROWN_KEY,JSON.stringify(memory))}catch(e){}}function renderStats(){if(statVisitsEl)statVisitsEl.textContent=memory.visits||0;if(statOpenedEl)statOpenedEl.textContent=memory.opened_count||0;if(statFavsEl)statFavsEl.textContent=memory.fav_count||0}function recordView(video){if(!video||!video.id)return;if(!memory.viewed)memory.viewed={};var id=String(video.id);memory.viewed[id]=(memory.viewed[id]||0)+1;memory.opened_count=Object.keys(memory.viewed).length;saveMemory();renderStats()}function recordQMode(modeId){if(!modeId)return;if(!memory.q_counts)memory.q_counts={};memory.q_counts[modeId]=(memory.q_counts[modeId]||0)+1;saveMemory()}function getTimestamp(video){if(!video)return 0;if(video.published_ts!=null&&video.published_ts!==""){var n=Number(video.published_ts);if(!isNaN(n)&&n>0)return n}if(video.published_iso){var t=Date.parse(video.published_iso);if(!isNaN(t))return Math.floor(t/1000)}return 0}function getBaseDate(video){if(!video)return null;if(video.published_iso){var parts=String(video.published_iso).split("-");if(parts.length>=3){var y=parseInt(parts[0],10),m=parseInt(parts[1],10),d=parseInt(parts[2],10);if(!isNaN(y)&&!isNaN(m)&&!isNaN(d))return new Date(Date.UTC(y,m-1,d))}}var ts=getTimestamp(video);if(ts>0){var dTs=new Date(ts*1000);if(!isNaN(dTs.getTime()))return dTs}return null}function formatDate(video){var d=getBaseDate(video);if(!d)return"date unknown lol";var gregorianYear=d.getUTCFullYear(),month=d.getUTCMonth(),day=d.getUTCDate();var mcYear,sfx;if(gregorianYear>=2025){mcYear=gregorianYear-2024;sfx=" MC"}else{mcYear=gregorianYear-2025;sfx=" PMC"}var mcYearStr=String(Math.abs(mcYear)).padStart(4,"0");if(mcYear<0)mcYearStr="-"+mcYearStr;var monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];return monthNames[month]+" "+day+", "+mcYearStr+sfx}function compareVideosOldest(a,b){var ta=getTimestamp(a),tb=getTimestamp(b);if(ta!==tb)return ta-tb;return(a._index||0)-(b._index||0)}function sortVideos(list){var copy=list.slice();copy.sort(function(a,b){return currentSort==="newest"?compareVideosOldest(b,a):compareVideosOldest(a,b)});return copy}function buildEmbedUrl(video){if(video&&video.name&&video.claim_id)return"https://odysee.com/$/embed/"+encodeURIComponent(video.name)+"/"+encodeURIComponent(video.claim_id);if(video&&video.odysee_url)return String(video.odysee_url).replace("https://odysee.com/","https://odysee.com/$/embed/");return""}function loadFavourites(){try{var raw=window.localStorage.getItem(FAV_KEY);if(!raw)return{};var obj=JSON.parse(raw);if(obj&&typeof obj==="object")return obj}catch(e){}return{}}function saveFavourites(){try{window.localStorage.setItem(FAV_KEY,JSON.stringify(favMap))}catch(e){}memory.fav_ids=favMap;memory.fav_count=Object.keys(favMap).length;saveMemory();renderStats()}function isFavourite(id){return!!favMap[id]}function toggleFavourite(id){if(!id)return;if(favMap[id])delete favMap[id];else favMap[id]=true;saveFavourites()}function updateFavVisual(el,fav){if(!el)return;el.textContent=fav?"★":"☆";el.classList.toggle("is-fav",!!fav)}function buildFiltersFromMetadata(videos){availableTags=new Set;if(filterTagEl){videos.forEach(function(v){if(Array.isArray(v.tags))v.tags.forEach(function(t){if(t)availableTags.add(t)})});filterTagEl.innerHTML="";var optAll=document.createElement("option");optAll.value="";optAll.textContent="all tags";filterTagEl.appendChild(optAll);Array.from(availableTags).sort().forEach(function(t){var o=document.createElement("option");o.value=t;o.textContent=t;filterTagEl.appendChild(o)})}if(filterYearEl){var years=new Set;videos.forEach(function(v){if(v.year)years.add(String(v.year))});filterYearEl.innerHTML="";var optAllY=document.createElement("option");optAllY.value="";optAllY.textContent="all years (MC)";filterYearEl.appendChild(optAllY);Array.from(years).sort().reverse().forEach(function(y){var o=document.createElement("option");o.value=y;var gY=parseInt(y);var mcY,sfx;if(gY>=2025){mcY=gY-2024;sfx=" MC"}else{mcY=gY-2025;sfx=" PMC"}var mcYStr=String(Math.abs(mcY)).padStart(4,"0");if(mcY<0)mcYStr="-"+mcYStr;o.textContent=mcYStr+sfx;filterYearEl.appendChild(o)})}if(filterLengthEl){filterLengthEl.innerHTML="";[["","all lengths"],["short","under 10 min (snack)"],["medium","10–30 min (meal)"],["long","over 30 min (feast)"]].forEach(function(pair){var o=document.createElement("option");o.value=pair[0];o.textContent=pair[1];filterLengthEl.appendChild(o)})}}function initFilters(){[filterTagEl,filterYearEl,filterLengthEl].forEach(function(el){if(!el)return;el.addEventListener("change",applyFilters)});if(filterFavEl)filterFavEl.addEventListener("change",applyFilters)}function updateCount(){if(!countEl)return;var n=filtered.length;countEl.textContent=n===1?"1 video (lonely but loved)":(n+" videos (all ad-free!)")}function clearGrid(){if(gridEl)gridEl.innerHTML="";rendered=0}function highlightActiveCard(){if(!gridEl)return;var cards=gridEl.querySelectorAll(".cinema-card");cards.forEach(function(card){if(card.dataset.id===String(currentVideoId))card.classList.add("is-active");else card.classList.remove("is-active")})}function computeRecommendations(sourceVideo,count){if(!sourceVideo||!allVideos.length)return[];var baseId=sourceVideo.id,baseTags=new Set(Array.isArray(sourceVideo.tags)?sourceVideo.tags:[]),baseYear=sourceVideo.year||null,baseDur=Number(sourceVideo.duration||0),baseTs=getTimestamp(sourceVideo);function durBucket(sec){var d=Number(sec||0);if(d<=10*60)return"short";if(d<=30*60)return"medium";return"long"}var baseBucket=durBucket(baseDur),scored=[];allVideos.forEach(function(v){if(!v||v.id===baseId)return;var score=0;if(baseTags.size&&Array.isArray(v.tags)&&v.tags.length){var shared=0;v.tags.forEach(function(t){if(baseTags.has(t))shared++});if(shared)score+=shared*3}if(baseYear&&v.year&&v.year===baseYear)score+=2;if(durBucket(v.duration)===baseBucket)score+=2;var ts=getTimestamp(v);if(baseTs&&ts){var dt=Math.abs(ts-baseTs);if(dt<=7*24*3600)score+=1}if(score>0){score+=Math.random()*0.5;scored.push({v:v,score:score})}});if(!scored.length){var baseIndex=sourceVideo._index||0,neighbours=[];for(var offset=-3;offset<=3;offset++){if(offset===0)continue;var idx=baseIndex+offset;if(idx>=0&&idx<allVideos.length)neighbours.push(allVideos[idx])}return neighbours.slice(0,count)}scored.sort(function(a,b){return b.score-a.score});return scored.slice(0,count).map(function(x){return x.v})}function updateRecommendations(video){if(!recListEl||!recBlurbEl)return;recListEl.innerHTML="";if(!video){recBlurbEl.textContent="pick a video n Glitchy will suggest more like it! (not an algorithm, just vibes)";return}var recs=computeRecommendations(video,4);if(!recs.length){recBlurbEl.textContent="Glitchy is thinking... 🤔 try another video n ill find more!";return}var d=Number(video.duration||0),comments=["ooh u like dat one! here r some similar vibes:","based on ur excellent taste, try dese:","if u liked dat, u might also vibe wit:","Glitchy approves! here r more 4 u:"],comment=comments[Math.floor(Math.random()*comments.length)];if(d>=30*60)comment="u picked a long one! respect! here r more deep dives:";else if(d<=10*60)comment="quick vid energy! here r more snack-sized ones:";recBlurbEl.textContent=comment;recs.forEach(function(v){var card=document.createElement("button");card.type="button";card.className="cinema-rec-card";card.dataset.id=v.id;var t=document.createElement("div");t.className="cinema-rec-title";t.textContent=v.title||"Untitled";var m=document.createElement("div");m.className="cinema-rec-meta";m.textContent=formatDate(v)+" • #"+(v._index_display||"?");card.appendChild(t);card.appendChild(m);card.addEventListener("click",function(){setNowPlaying(v);highlightActiveCard();if(playerShell&&playerShell.scrollIntoView)playerShell.scrollIntoView({behavior:"smooth",block:"start"})});recListEl.appendChild(card)})}function setNowPlaying(video){if(!video)return;currentVideoId=video.id;if(nowTitleEl)nowTitleEl.textContent=video.title||"Untitled";if(nowMetaEl)nowMetaEl.textContent=formatDate(video)+" • no ads b4 dis video lol";var src=buildEmbedUrl(video);if(playerFrame)playerFrame.src=src||"";if(openLinkEl){var mainUrl="#";if(video.odysee_url)mainUrl=video.odysee_url;else if(video.name)mainUrl="https://odysee.com/"+encodeURIComponent(video.name);openLinkEl.href=mainUrl}recordView(video);updateRecommendations(video)}function renderNextBatch(){if(!gridEl)return;if(rendered>=filtered.length){loadMoreBtn.disabled=true;loadMoreBtn.textContent="🌈 all videos loaded! (dats all of dem!)";expandAllBtn.disabled=true;expandAllBtn.textContent="🌈 already expanded!";if(statusEl)statusEl.textContent="showing "+filtered.length+" / "+allVideos.length+" videos • all ad-free! 🎬";return}var start=rendered,end=Math.min(rendered+BATCH_SIZE,filtered.length),slice=filtered.slice(start,end);slice.forEach(function(video){var card=document.createElement("article");card.className="cinema-card";card.dataset.id=video.id;var img=document.createElement("img");img.className="cinema-thumb";img.loading="lazy";img.alt=video.title||"video";img.src=video.thumbnail||video.thumb_local||video.thumb_remote||"";img.addEventListener("error",function(){if(video.thumb_remote&&img.src!==video.thumb_remote)img.src=video.thumb_remote});var favBtn=document.createElement("button");favBtn.type="button";favBtn.className="cinema-fav-btn";updateFavVisual(favBtn,isFavourite(video.id));favBtn.addEventListener("click",function(ev){ev.stopPropagation();toggleFavourite(video.id);updateFavVisual(favBtn,isFavourite(video.id));if(filterFavEl&&filterFavEl.checked)applyFilters()});var body=document.createElement("div");body.className="cinema-card-body";var titleEl=document.createElement("h4");titleEl.className="cinema-card-title";titleEl.textContent=video.title||"Untitled";var metaEl=document.createElement("div");metaEl.className="cinema-card-meta";var left=document.createElement("span");left.textContent=formatDate(video);var right=document.createElement("span");right.textContent="#"+(video._index_display||"?");metaEl.appendChild(left);metaEl.appendChild(right);body.appendChild(titleEl);body.appendChild(metaEl);card.appendChild(img);card.appendChild(favBtn);card.appendChild(body);card.addEventListener("click",function(){setNowPlaying(video);highlightActiveCard();if(playerShell&&playerShell.scrollIntoView)playerShell.scrollIntoView({behavior:"smooth",block:"start"})});gridEl.appendChild(card)});rendered=end;if(statusEl)statusEl.textContent="showing "+rendered+" / "+filtered.length+" videos • all ad-free! 🎬"}function expandAll(){if(!gridEl)return;if(rendered>=filtered.length)return;while(rendered<filtered.length)renderNextBatch();expandAllBtn.disabled=true;expandAllBtn.textContent="🌈 all expanded! (u did it!)";loadMoreBtn.disabled=true;loadMoreBtn.textContent="🌈 all videos loaded!"}function applyFilters(){var query=normalise(searchInput?searchInput.value:""),tagVal=filterTagEl?filterTagEl.value:"",yearVal=filterYearEl?filterYearEl.value:"",lenVal=filterLengthEl?filterLengthEl.value:"",favOnly=filterFavEl&&filterFavEl.checked,base=sortVideos(allVideos);filtered=base.filter(function(v){if(query&&normalise(v.title).indexOf(query)===-1)return false;if(tagVal){if(!Array.isArray(v.tags)||v.tags.indexOf(tagVal)===-1)return false}if(yearVal){if(!v.year||String(v.year)!==String(yearVal))return false}if(lenVal){var d=Number(v.duration||0);if(lenVal==="short"&&d>=10*60)return false;if(lenVal==="medium"&&(d<10*60||d>30*60))return false;if(lenVal==="long"&&d<=30*60)return false}if(favOnly&&!isFavourite(v.id))return false;return true});updateCount();clearGrid();if(filtered.length===0){if(statusEl)statusEl.textContent="no videos match dat lol. try different filters?";updateRecommendations(null);return}setNowPlaying(filtered[0]);highlightActiveCard();loadMoreBtn.disabled=false;loadMoreBtn.textContent="📺 load more videos!";expandAllBtn.disabled=false;expandAllBtn.textContent="🌈 EXPAND ALL! (show me evryting!)";renderNextBatch();highlightActiveCard()}function initSortButtons(){sortButtons.forEach(function(btn){btn.addEventListener("click",function(){var val=btn.getAttribute("data-sort");if(!val||val===currentSort)return;currentSort=val;sortButtons.forEach(function(b){b.classList.toggle("is-active",b===btn)});applyFilters()})})}function initSearch(){if(!searchInput)return;var debounce;searchInput.addEventListener("input",function(){if(debounce)window.clearTimeout(debounce);debounce=window.setTimeout(applyFilters,180)})}function resetAllFilters(){if(searchInput)searchInput.value="";if(filterTagEl)filterTagEl.value="";if(filterYearEl)filterYearEl.value="";if(filterLengthEl)filterLengthEl.value="";if(filterFavEl)filterFavEl.checked=false}function chooseAvailableTag(candidates){for(var i=0;i<candidates.length;i++){var t=candidates[i];if(availableTags.has(t))return t}return""}function showGlitchySigil(def){if(!def||!glitchySigilEl)return;glitchySigilEl.textContent="🌈🛡️ "+def.system+" 🛡️🌈"}function applyGlitchyMode(mode){if(!mode)return;resetAllFilters();recordQMode(mode);if(mode==="origin")currentSort="oldest";else if(mode==="latest")currentSort="newest";else if(mode==="quick"){currentSort="newest";if(filterLengthEl)filterLengthEl.value="short"}else if(mode==="deep"){currentSort="oldest";if(filterLengthEl)filterLengthEl.value="long"}else if(mode==="myth"){currentSort="oldest";var mythTag=chooseAvailableTag(["mythology","myth","Storytelling","romance","love","story"]);if(mythTag&&filterTagEl)filterTagEl.value=mythTag}else if(mode==="roulette")currentSort="newest";sortButtons.forEach(function(b){var val=b.getAttribute("data-sort");b.classList.toggle("is-active",val===currentSort)});applyFilters();if(mode==="roulette"&&filtered.length>0){var idx=Math.floor(Math.random()*filtered.length);setNowPlaying(filtered[idx]);currentVideoId=filtered[idx].id;highlightActiveCard();if(playerShell&&playerShell.scrollIntoView)playerShell.scrollIntoView({behavior:"smooth",block:"start"})}}function renderGlitchyRail(){if(!glitchyRailEl)return;glitchyRailEl.innerHTML="";glitchyButtons=[];var pool=GLITCHY_MODES.slice();for(var i=pool.length-1;i>0;i--){var j=Math.floor(Math.random()*(i+1)),tmp=pool[i];pool[i]=pool[j];pool[j]=tmp}var max=Math.min(pool.length,6);for(var k=0;k<max;k++){var def=pool[k],btn=document.createElement("button");btn.type="button";btn.className="cinema-glitchy-btn";btn.setAttribute("data-mode",def.mode);btn.textContent="❓ "+def.q;glitchyRailEl.appendChild(btn);glitchyButtons.push({el:btn,def:def})}glitchyButtons.forEach(function(pair){pair.el.addEventListener("click",function(){applyGlitchyMode(pair.def.mode);showGlitchySigil(pair.def)})})}function initGlitchy(){renderGlitchyRail()}function loadData(){fetch(DATA_URL,{cache:"no-store"}).then(function(res){return res.json()}).then(function(data){if(!Array.isArray(data))throw new Error("cinema-data.json must be an array");allVideos=data;if(statusEl)statusEl.textContent="🌈 found "+allVideos.length+" videos! all ad-free! no tracking! lets gooo! 🎬";buildFiltersFromMetadata(allVideos);initFilters();applyFilters()}).catch(function(err){console.error("Cinema load error",err);if(errorEl)errorEl.textContent="🌈 couldnt load videos lol. ring da bell above 2 sync from sql.json!";if(statusEl)statusEl.textContent="no videos yet! ring da bell 2 wake dem up! 🔔"})}if(!gridEl||!loadMoreBtn||!statusEl)return;loadMoreBtn.addEventListener("click",renderNextBatch);if(expandAllBtn)expandAllBtn.addEventListener("click",expandAll);initSortButtons();initSearch();initGlitchy();loadData()})();
</script>
<?php
$console_body_html = ob_get_clean();
require __DIR__ . '/shell.php';