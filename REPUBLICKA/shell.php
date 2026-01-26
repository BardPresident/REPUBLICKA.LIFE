<?php
// Shell — shared starship hull for The Republic 🌈
// Shell RAINBOW 4.0 — Rainbow Warrior Edition + Crownkind Shard Protocol
// Warriors of the Rainbow protect the children.

$page_title       = $page_title       ?? 'The Republic';
$page_canonical   = $page_canonical   ?? 'https://TRepublic.net/';
$page_description = $page_description ?? 'The Republic is a citizen-first civilisation led by Bard President Wendell NeSmith.';

$page_og_title       = $page_og_title       ?? 'The Republic';
$page_og_description = $page_og_description ?? 'TRepublicOS: the starship console of The Republic.';
$page_og_url         = $page_og_url         ?? $page_canonical;
$page_og_image       = $page_og_image       ?? 'https://TRepublic.net/emblems/heart.png';

$hero_title   = $hero_title   ?? 'The Republic: U R FREE';
$hero_tagline = $hero_tagline ?? "You are perfect. You are brave.\nYou are beautiful. There is nothing wrong with you.";

$console_title = $console_title ?? 'Welcome to The Republic';

$enable_analytics = $enable_analytics ?? true;

// Get current PHP file name
$current_php_file = basename($_SERVER['PHP_SELF']);
$current_php_slug = str_replace('.php', '', $current_php_file);

// Shard mythic metadata
$shard_system  = $shard_system  ?? 'NMS Eissentam';
$shard_glyph   = $shard_glyph   ?? '9-13EF3CFDEEEF';
$shard_version = $shard_version ?? '4.0';

$console_body_html = $console_body_html ?? <<<HTML
<p>
  Once, a console lit up in the heart of The Republic and called for its program.
  No pattern answered, and no seed replied. The lights stayed on, but the story did not arrive.
</p>
<p>
  The elders wrote a rule: <em>when the console shines and nothing speaks, the wiring is at fault, not the citizen.</em>
  If you see this, a page forgot to send its script to the console. Please tell the Architects so we can mend the route.
</p>
HTML;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="canonical" href="<?= htmlspecialchars($page_canonical, ENT_QUOTES, 'UTF-8') ?>">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <meta name="description"
        content="<?= htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Open Graph / social -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($page_og_title, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description"
        content="<?= htmlspecialchars($page_og_description, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:url" content="<?= htmlspecialchars($page_og_url, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:image"
        content="<?= htmlspecialchars($page_og_image, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Republic meta for shards -->
  <script>
    window.TREPUBLIC_META = {
      title:   <?= json_encode($page_title ?? 'The Republic') ?>,
      url:     <?= json_encode($page_canonical ?? 'https://TRepublic.net/') ?>,
      console: <?= json_encode($console_title ?? 'Starship Console') ?>,
      currentFile: <?= json_encode($current_php_file) ?>,
      currentSlug: <?= json_encode($current_php_slug) ?>,
      shard: {
        system:  <?= json_encode($shard_system) ?>,
        glyph:   <?= json_encode($shard_glyph) ?>,
        version: <?= json_encode($shard_version) ?>,
        deck:    'Shell RAINBOW 4.0'
      }
    };
  </script>

  <?php if (!empty($enable_analytics)): ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BLEE3X2GR6"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-BLEE3X2GR6');
    </script>
  <?php endif; ?>

  <!-- Screenshot engine: html2canvas from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

  <style>
    :root{
      --side-w: clamp(24px, 2.8vw, 34px);

      /* Rainbow Warrior colors */
      --rainbow-red: #FF6B6B;
      --rainbow-orange: #FFA94D;
      --rainbow-yellow: #FFE066;
      --rainbow-green: #69DB7C;
      --rainbow-blue: #74C0FC;
      --rainbow-purple: #B197FC;

      --sky-top: #87CEEB;
      --sky-mid: #B0E0E6;
      --dawn-pink: #FFB6C1;
      --cloud-white: #ffffff;

      --frame-border: rgba(0,0,0,.18);
      --frame-shadow: 0 14px 32px rgba(0,0,0,.28);
      --text-main: #192038;
      --text-soft: #4b5573;
    }

    *, *::before, *::after{ box-sizing:border-box; }

    html{
      margin:0;
      padding:0;
      height:100%;
      font-size: 100%; /* Base font size for zoom */
    }

    body{
      margin:0;
      padding:0;
      min-height:100vh;
      max-width:100vw;
      overflow-x:hidden;
      overflow-y:auto;
      -webkit-overflow-scrolling:touch;

      font-family: system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      font-weight:500;
      line-height:1.6;
      font-size: 15px; /* Base size - zoom will scale this */

      /* Sky gradient background ☀️ */
      background: linear-gradient(180deg,
        #87CEEB 0%,
        #B0E0E6 40%,
        #ffffff 60%,
        #FFB6C1 80%,
        #87CEEB 100%
      );
      background-attachment: fixed;

      scrollbar-width:thin;
      scrollbar-color:rgba(255,107,107,.9) rgba(0,0,0,.25);
    }

    body::-webkit-scrollbar{
      width:10px;
    }
    body::-webkit-scrollbar-track{
      background:rgba(0,0,0,.25);
    }
    body::-webkit-scrollbar-thumb{
      background:linear-gradient(180deg,
        var(--rainbow-red),
        var(--rainbow-orange),
        var(--rainbow-yellow),
        var(--rainbow-green),
        var(--rainbow-blue),
        var(--rainbow-purple)
      );
      border-radius:999px;
    }

    a{ color:#7e3af2; text-decoration:none; }
    a:hover{ text-decoration:underline; }

    /* ☁️ FLOATING CLOUDS ☁️ */
    .cloud{
      position:fixed;
      z-index:0;
      pointer-events:none;
      opacity:0.8;
      filter:drop-shadow(0 4px 8px rgba(0,0,0,.15));
      animation-timing-function:linear;
      animation-iteration-count:infinite;
    }

    /* Layer 1 - Big slow clouds */
    .cloud-1{
      top:5%;
      font-size:80px;
      animation:drift-right 70s linear infinite;
    }
    .cloud-2{
      top:15%;
      font-size:90px;
      opacity:0.6;
      animation:drift-left 80s linear infinite;
      animation-delay:-25s;
    }
    .cloud-3{
      top:28%;
      font-size:70px;
      animation:drift-right 65s linear infinite;
      animation-delay:-40s;
    }
    
    /* Layer 2 - Medium clouds */
    .cloud-4{
      top:38%;
      font-size:60px;
      animation:drift-left 55s linear infinite;
      animation-delay:-15s;
    }
    .cloud-5{
      top:48%;
      font-size:75px;
      opacity:0.7;
      animation:drift-right 60s linear infinite;
      animation-delay:-30s;
    }
    .cloud-6{
      top:58%;
      font-size:55px;
      animation:drift-left 50s linear infinite;
      animation-delay:-10s;
    }
    
    /* Layer 3 - Small fast clouds */
    .cloud-7{
      top:68%;
      font-size:50px;
      opacity:0.9;
      animation:drift-right 45s linear infinite;
      animation-delay:-20s;
    }
    .cloud-8{
      top:78%;
      font-size:65px;
      animation:drift-left 55s linear infinite;
      animation-delay:-35s;
    }
    .cloud-9{
      top:88%;
      font-size:45px;
      animation:drift-right 40s linear infinite;
      animation-delay:-5s;
    }
    .cloud-10{
      top:12%;
      font-size:40px;
      opacity:0.5;
      animation:drift-left 75s linear infinite;
      animation-delay:-50s;
    }
    .cloud-11{
      top:35%;
      font-size:85px;
      opacity:0.4;
      animation:drift-right 90s linear infinite;
      animation-delay:-60s;
    }
    .cloud-12{
      top:72%;
      font-size:55px;
      opacity:0.6;
      animation:drift-left 48s linear infinite;
      animation-delay:-22s;
    }

    @keyframes drift-right{
      0%{ left:-15%; }
      100%{ left:110%; }
    }
    @keyframes drift-left{
      0%{ left:110%; }
      100%{ left:-15%; }
    }

    /* Side panels + emoji rails */
    .side-bg{
      position:fixed;
      top:0; bottom:0;
      width:var(--side-w);
      z-index:1;
      pointer-events:none;
      background:linear-gradient(
        to bottom,
        rgba(255,255,255,.6),
        rgba(255,255,255,0),
        rgba(255,255,255,.6)
      );
    }
    .side-bg.left{ left:0; box-shadow:inset -4px 0 10px rgba(0,0,0,.25); }
    .side-bg.right{ right:0; box-shadow:inset 4px 0 10px rgba(0,0,0,.25); }

    .emoji-rail{
      position:fixed;
      top:0; bottom:0;
      width:var(--side-w);
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:space-evenly;
      gap:4px;
      z-index:3;
      pointer-events:none;
    }
    .emoji-rail span{
      font-size:clamp(17px,2.1vw,22px);
      filter:drop-shadow(0 1px 2px rgba(0,0,0,.6));
    }
    .emoji-rail.left{ left:0; }
    .emoji-rail.right{ right:0; }

    /* Main starship frame */
    .frame{
      position:relative;
      z-index:2;
      width:min(80vw, calc(100% - (2 * var(--side-w)) - 12px));
      max-width:1200px;
      min-height:100vh;
      margin:10px auto 20px;
      border-radius:22px;
      border:2px solid var(--frame-border);
      background:rgba(255,255,255,.92);
      box-shadow:var(--frame-shadow);
      display:flex;
    }

    .frame-inner{
      flex:1 1 auto;
      padding:8px 10px 10px;
      display:flex;
      flex-direction:column;
      min-height:100%;
      max-width:100%;
    }

    /* Republic Flag */
    .flag-wrap{
      text-align:center;
      margin-bottom:8px;
    }
    .republic-flag{
      max-width:200px;
      height:auto;
      border-radius:8px;
      box-shadow:0 8px 20px rgba(0,0,0,.35);
    }
    .flag-caption{
      margin-top:4px;
      font-size:12px;
      font-weight:700;
      color:var(--text-main);
    }

    /* Hero - RAINBOW GRADIENT */
    .site-hero{
      border-radius:16px;
      padding:8px 10px 9px;
      background: linear-gradient(
        90deg,
        var(--rainbow-red) 0%,
        var(--rainbow-orange) 16.6%,
        var(--rainbow-yellow) 33.3%,
        var(--rainbow-green) 50%,
        var(--rainbow-blue) 66.6%,
        var(--rainbow-purple) 83.3%,
        var(--rainbow-red) 100%
      );
      background-size:200% 100%;
      animation:rainbow-shift 8s linear infinite;
      box-shadow:0 10px 24px rgba(0,0,0,.28);
      color:#000;
    }

    @keyframes rainbow-shift{
      0%{ background-position:0% 50%; }
      100%{ background-position:200% 50%; }
    }

    .hero-grid{
      display:grid;
      grid-template-columns:minmax(0,1.1fr) minmax(0,1.4fr) minmax(0,1.1fr);
      column-gap:10px;
      align-items:stretch;
    }

    .hero-card{
      background:rgba(255,255,255,.92);
      border-radius:12px;
      border:1px solid rgba(0,0,0,.08);
      padding:6px 9px;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .hero-left{
      display:flex;
      align-items:center;
      justify-content:center;
      text-align:center;
    }

    .hero-title-text{
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .site-title{
      margin:0;
      font:900 22px/1.15 system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      color:var(--text-main);
    }

    .site-tag{
      margin-top:4px;
      font-size:13px;
      line-height:1.4;
      color:var(--text-soft);
    }

    .hero-center-inner{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:8px;
    }

    /* Emblems */
    .hero-pill-icon{
      width:60px;
      height:60px;
      border-radius:999px;
      background:transparent;
      display:flex;
      align-items:center;
      justify-content:center;
      border:2px solid var(--rainbow-orange);
      box-shadow:
        0 0 0 3px rgba(255,169,77,0.55),
        0 10px 22px rgba(0,0,0,0.75);
      flex:0 0 auto;
    }
    .hero-pill-icon img{
      width:100%;
      height:100%;
      border-radius:999px;
      display:block;
      object-fit:cover;
    }
    .hero-crown-icon{
      border-color:var(--rainbow-purple);
      box-shadow:
        0 0 0 3px rgba(177,151,252,0.7),
        0 12px 24px rgba(0,0,0,0.8);
    }

    .nav-pill{
      flex:1 1 auto;
      max-width:100%;
      background:#000;
      border-radius:999px;
      padding:2px;
      box-shadow:0 8px 18px rgba(0,0,0,.55);
    }

    .nav-select{
      width:100%;
      background:linear-gradient(90deg,
        var(--rainbow-red),
        var(--rainbow-orange),
        var(--rainbow-yellow),
        var(--rainbow-green),
        var(--rainbow-blue),
        var(--rainbow-purple)
      );
      color:#fff;
      border-radius:999px;
      border:none;
      padding:6px 18px;
      font-weight:800;
      font-size:12px;
      cursor:pointer;
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
      text-align:center !important;
      text-align-last:center !important;
      -moz-text-align-last:center !important;
      direction:ltr !important;
      text-shadow:0 1px 2px rgba(0,0,0,.5);
    }
    .nav-select option{
      background:#000;
      color:#fff;
      text-align:center;
    }
    .nav-select:focus{
      outline:none;
      box-shadow:0 0 0 2px rgba(255,255,255,.8);
    }

    #rep-menu{
      direction:ltr !important;
      text-align:center !important;
      text-align-last:center !important;
      -moz-text-align-last:center !important;
      padding-left:0 !important;
      padding-right:0 !important;
    }

    .hero-right{
      align-items:center;
      text-align:center;
    }

    .hero-center-inner-right{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:8px;
    }

    .rep-pill{
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      padding:6px 10px 7px;
      border-radius:12px;
      background:linear-gradient(135deg,
        rgba(255,230,102,.3) 0%,
        rgba(105,219,124,.3) 50%,
        rgba(116,192,252,.3) 100%
      );
      border:1px solid rgba(105,219,124,.6);
      font-size:10px;
      color:var(--text-main);
      box-shadow:0 6px 18px rgba(0,0,0,.35);
      width:100%;
    }

    .os-actions{
      font-size:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:4px;
      flex-wrap:wrap;
      margin-bottom:4px;
    }

    .os-link{
      background:none;
      border:none;
      padding:0 2px;
      color:#000;
      font:700 10px/1 system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      text-decoration:underline;
      cursor:pointer;
    }

    .os-sep{ opacity:.6; }

    .os-zoom{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:4px;
      font-size:10px;
      margin-top:2px;
      flex-wrap:wrap;
    }

    .zoom-label{
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:.06em;
    }

    .zoom-btn{
      background:linear-gradient(135deg,var(--rainbow-green),var(--rainbow-blue));
      color:#fff;
      border:none;
      border-radius:999px;
      width:18px;
      height:18px;
      line-height:18px;
      font-size:11px;
      font-weight:800;
      cursor:pointer;
      padding:0;
      text-shadow:0 1px 1px rgba(0,0,0,.3);
    }

    .zoom-display{
      min-width:40px;
      text-align:center;
      font-weight:700;
    }

    /* Download buttons in center */
    .download-buttons-center{
      display:flex;
      flex-direction:column;
      gap:4px;
      width:100%;
      margin-top:6px;
    }

    .download-btn-center{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:6px;
      padding:4px 8px;
      border-radius:999px;
      border:1px solid rgba(105,219,124,.9);
      font-size:9px;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.04em;
      background:linear-gradient(135deg,var(--rainbow-green),#22c55e);
      color:#eafff3;
      box-shadow:0 4px 10px rgba(105,219,124,.45);
      text-decoration:none;
      cursor:pointer;
      white-space:nowrap;
      width:100%;
      min-height:28px;
    }
    .download-btn-center:hover{
      transform:translateY(-1px);
      box-shadow:0 6px 12px rgba(105,219,124,.65);
    }
    .download-btn-center:active{
      transform:translateY(0);
    }

    .download-btn-center:nth-child(2){
      background:linear-gradient(135deg,var(--rainbow-blue),#3b82f6);
      border-color:rgba(116,192,252,.9);
    }

    .download-btn-center:nth-child(3){
      background:linear-gradient(135deg,var(--rainbow-purple),#8b5cf6);
      border-color:rgba(177,151,252,.9);
    }

    /* Glitchy sentinel banner - RAINBOW WARRIOR */
    .glitchy-panel{
      margin:6px 0 4px;
    }
    .glitchy-frame{
      border-radius:12px;
      border:1px dashed rgba(255,169,77,.6);
      background:linear-gradient(135deg,
        rgba(4,4,12,.96) 0%,
        rgba(20,10,30,.96) 100%
      );
      color:#ffe4b8;
      padding:6px 10px 5px;
      font-size:11px;
      box-shadow:0 6px 16px rgba(0,0,0,.55);
    }
    .glitchy-line{
      text-align:center;
      word-break:break-word;
    }
    .glitchy-line-q{
      margin-bottom:2px;
    }
    .glitchy-line-system{
      margin-bottom:2px;
    }
    .glitchy-q{
      letter-spacing:0.12em;
      font-weight:600;
      margin:0 4px;
      white-space:normal;
    }
    .glitchy-system{
      color:#98fb98;
      font-weight:700;
      margin:0 4px;
    }
    .glitchy-stamp{
      margin-top:3px;
      text-align:center;
      font-size:10px;
      opacity:.85;
    }

    .page-main{
      margin-top:8px;
      flex:1 1 auto;
      display:flex;
      max-width:100%;
    }

    .page-content{
      flex:1 1 auto;
      border-radius:18px;
      border:1px solid rgba(0,0,0,.08);
      background:#ffffff;
      padding:14px 22px 18px;
      max-width:100%;
      display:block;
      color:var(--text-main);
    }

    .page-content h1,
    .page-content h2,
    .page-content h3{
      color:var(--text-main);
    }

    .page-content-empty h2{
      margin:0 0 14px;
      padding-bottom:6px;
      font-size:20px;
      font-weight:900;
      border-bottom:1px solid rgba(0,0,0,.08);
    }

    .page-content-empty p{
      margin:0 0 8px;
      font-size:14px;
      color:var(--text-soft);
    }

    .footer{
      margin-top:8px;
      color:var(--text-soft);
      text-align:center;
      font-size:11px;
    }

    @media (max-width: 900px){
      :root{
        --side-w: 22px;
      }

      .frame{
        width:calc(100% - (2 * var(--side-w)));
        margin:0 auto 18px;
        border-radius:0;
      }

      .frame-inner{
        padding:6px 6px 8px;
      }

      .hero-grid{
        grid-template-columns:1fr;
        row-gap:6px;
      }

      .hero-center-inner{
        justify-content:center;
      }

      .hero-center-inner-right{
        justify-content:center;
      }

      .rep-pill{
        max-width:260px;
        margin:0 auto;
      }

      .page-content{
        padding:10px 10px 14px;
        border-radius:14px;
      }

      .glitchy-frame{
        font-size:10px;
      }

      .cloud{
        font-size:36px !important;
      }

      .download-btn-center{
        font-size:8px;
        padding:3px 6px;
        min-height:26px;
      }
    }
  </style>
</head>
<body>

  <!-- ☁️ FLOATING CLOUDS - LOTS OF THEM ☁️ -->
  <div class="cloud cloud-1" aria-hidden="true">☁️</div>
  <div class="cloud cloud-2" aria-hidden="true">☁️</div>
  <div class="cloud cloud-3" aria-hidden="true">☁️</div>
  <div class="cloud cloud-4" aria-hidden="true">☁️</div>
  <div class="cloud cloud-5" aria-hidden="true">☁️</div>
  <div class="cloud cloud-6" aria-hidden="true">☁️</div>
  <div class="cloud cloud-7" aria-hidden="true">☁️</div>
  <div class="cloud cloud-8" aria-hidden="true">☁️</div>
  <div class="cloud cloud-9" aria-hidden="true">☁️</div>
  <div class="cloud cloud-10" aria-hidden="true">☁️</div>
  <div class="cloud cloud-11" aria-hidden="true">☁️</div>
  <div class="cloud cloud-12" aria-hidden="true">☁️</div>

  <!-- Side rails + emoji columns -->
  <div aria-hidden="true" class="side-bg left"></div>
  <div aria-hidden="true" class="side-bg right"></div>
<div aria-hidden="true" class="emoji-rail left" id="railL"></div>
<div aria-hidden="true" class="emoji-rail right" id="railR"></div>

  <!-- Central starship frame -->
  <div class="frame" id="rep-frame">
    <div class="frame-inner">

      <!-- Republic Flag -->
      <div class="flag-wrap">
        <img src="/emblems/flag.png" alt="The Republic Flag" class="republic-flag">
        <div class="flag-caption" style="font-size: 50px;">👑</div>
        <div class="flag-caption">🌈 DA DOOR IS OPEN 🌈</div>
      </div>

      <header class="site-hero">
        <div class="hero-grid">

          <section class="hero-card hero-left">
            <div class="hero-title-text">
              <h1 class="site-title">
                <?= nl2br(htmlspecialchars($hero_title, ENT_QUOTES, 'UTF-8')) ?>
              </h1>
              <p class="site-tag">
                <?= nl2br(htmlspecialchars($hero_tagline, ENT_QUOTES, 'UTF-8')) ?>
              </p>
            </div>
          </section>

          <!-- Center hero: system menu + zoom + NEW DOWNLOAD BUTTONS -->
          <section class="hero-card">
            <div class="hero-center-inner">
              <div class="rep-pill">
                <div class="os-actions">
                  <button type="button" class="os-link" id="snap-export">Export</button>
                  <span class="os-sep">•</span>
                  <button type="button" class="os-link" id="snap-import">Import</button>
                  <span class="os-sep">•</span>
                  <button type="button" class="os-link" id="snap-reset">Reset</button>
                  <span class="os-sep">•</span>
                  <button type="button" class="os-link" id="snap-capture">Shard</button>
                </div>

                <div class="os-zoom">
                  <span class="zoom-label">Zoom</span>
                  <button type="button" class="zoom-btn" id="zoom-out">−</button>
                  <span class="zoom-display" id="zoom-display">100%</span>
                  <button type="button" class="zoom-btn" id="zoom-in">+</button>
                </div>

                <!-- NEW DOWNLOAD BUTTONS IN CENTER -->
                <div class="download-buttons-center">
                  <button type="button" class="download-btn-center" id="download-php-center">
                    📥 PHP (<?= htmlspecialchars($current_php_file, ENT_QUOTES, 'UTF-8') ?>)
                  </button>
                  <button type="button" class="download-btn-center" id="download-shell-center">
                    📥 shell.php (Heart)
                  </button>
                  <button type="button" class="download-btn-center" id="download-html-center">
                    📥 HTML (<?= htmlspecialchars($current_php_slug, ENT_QUOTES, 'UTF-8') ?>.html)
                  </button>
                </div>
              </div>

              <!-- Hidden file input for Import -->
              <input type="file" id="snap-file" accept=".html,.json" hidden>
            </div>
          </section>

          <!-- Right hero: nav menu + emblems (RESTORED!) -->
          <aside class="hero-card hero-right" aria-label="Navigation menu">
            <div class="hero-center-inner-right">
              <div class="hero-pill-icon" aria-hidden="true">
                <img src="https://trepublic.net/emblems/heart.png" alt="">
              </div>

              <div class="nav-pill">
<select class="nav-select" id="rep-menu" title="Menu" dir="ltr">
<option value="">🌈 Menu 🌈</option>

<!-- Core -->
<option value="https://trepublic.net/index.php">🏡 Home 🏡</option>
<option value="https://trepublic.net/library/truth-of-911-who-did-it-and-why.php">👁️ 911 👁️</option>
<option value="https://trepublic.net/library/wendy-memorial-september-3-2008.php">🥷 NINJUTUSU 🥷</option>
<option value="https://trepublic.net/rainbow-bridge.php">🌈 Rainbow Bridge 🌈</option>
<option value="https://trepublic.net/first-contact.php">👽 First Contact 👽</option>
<option value="https://trepublic.net/receipts.php">📢 Receipts 📢</option>
<option value="https://trepublic.net/library/life.php">🌟 BRIGHT MORNING STAR 🌟</option>

<option value="" disabled>──────────────</option>

<!-- Labs / Magicka -->
<option value="https://trepublic.net/craft.php">🪄 Craft 🪄</option>
<option value="https://trepublic.net/codex.php">📓 Codex 📓</option>
<option value="https://trepublic.net/seeds.php">🌱 Seeds 🌱</option>
<option value="https://trepublic.net/shards.php">🧿 Shards 🧿</option>
<option value="https://trepublic.net/sync-666.php">🔐 Sync666 🔐</option>

<option value="" disabled>──────────────</option>

<!-- Archives -->
<option value="https://trepublic.net/library.php">📚 Library 📚</option>
<option value="https://trepublic.net/comic.php">🦸 Comic 🦸</option>
<option value="https://trepublic.net/cinema.php">🎬 Cinema 🎬</option>

<option value="" disabled>──────────────</option>

<!-- Systems / Admin -->
<option value="https://trepublic.net/capture.php">📸 Capture 📸</option>
<option value="https://trepublic.net/PARADOX.php">🌀 PARADOX 🌀</option>
<option value="https://trepublic.net/glitchy.php">👾 Glitchy 👾</option>
<option value="https://trepublic.net/backup.php">💾 Backup 💾</option>
<option value="https://trepublic.net/crown.php">👑 Crown 👑</option>
<option value="https://trepublic.net/IVORY-YOUR-GOD.PHP">🤍💎👑 IVORY YOUR GOD 👑💎🤍</option>
</select>
              </div>

              <div class="hero-pill-icon hero-crown-icon" aria-hidden="true">
                <img src="https://trepublic.net/emblems/crown.png" alt="">
              </div>
            </div>
          </aside>

        </div>
      </header>

      <!-- Glitchy sentinel - RAINBOW WARRIOR MODE -->
      <section class="glitchy-panel" aria-label="Rainbow Warrior transmission">
        <div class="glitchy-frame">
          <div class="glitchy-line glitchy-line-q">
            🌈 ☁️🛰️⟦ RAINBOW WARRIOR ⟧
            <span class="glitchy-q" id="glitchy-q">Ｑ：ＡＲＥ　ＴＨＥ　ＣＨＩＬＤＲＥＮ　ＳＡＦＥ？</span>
            ⟦ SENTINEL ⟧🛰️☁️ 🌈
          </div>
          <div class="glitchy-line glitchy-line-system">
            🛡️👧👦
            <span class="glitchy-system" id="glitchy-system">Warriors protect the children.</span>
            👦👧🛡️
          </div>
          <div class="glitchy-stamp">🌈▒▒🛰️⟦ ＲＡＩＮＢＯＷ　ＷＡＲＲＩＯＲ　ＴＲＡＮＳＭＩＳＳＩＯＮ ⟧📡▒▒🌈</div>
        </div>
      </section>

      <main class="page-main" aria-label="Starship console">
        <article class="page-content page-content-empty" id="page-content">
          <h2><?= htmlspecialchars($console_title, ENT_QUOTES, 'UTF-8') ?></h2>
          <?= $console_body_html ?>
        </article>
      </main>

      <div class="footer">
        🌈 Warriors of the Rainbow protect the children 🌈
        <br><strong><?= htmlspecialchars($shard_system, ENT_QUOTES, 'UTF-8') ?>: <?= htmlspecialchars($shard_glyph, ENT_QUOTES, 'UTF-8') ?></strong>
        <br>🌈☁️👧🛡️👦☁️🌈
        <br>© The Republic — All Love Reserved.
      </div>
    </div>
  </div>

  <script>
    // Emoji rails - RAINBOW WARRIOR
    (function(){
      var seq = ['🌈','🛡️','👧','☁️','👦','🛡️'];
      function fillRail(node){
        if(!node) return;
        node.innerHTML = '';
        var h = window.innerHeight || document.documentElement.clientHeight || 600;
        var step = h / 15;
        if(step < 26) step = 26;
        if(step > 42) step = 42;
        var n = Math.ceil(h / step) + 2;
        for(var i = 0; i < n; i++){
          var s = document.createElement('span');
          s.textContent = seq[i % seq.length];
          node.appendChild(s);
        }
      }
      var L = document.getElementById('railL');
      var R = document.getElementById('railR');
      function refresh(){ fillRail(L); fillRail(R); }
      refresh();
      window.addEventListener('resize', refresh);
    })();

    // Dropdown menu behaviour
    (function(){
      var menu = document.getElementById('rep-menu');
      if(!menu) return;
      menu.addEventListener('change', function(){
        var v = this.value;
        if(!v) return;
        if(v.charAt(0) === '#'){
          var target = document.querySelector(v);
          if(target && target.scrollIntoView){
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        } else {
          window.location.href = v;
        }
        this.value = '';
      });
    })();

    // 🔥🔥🔥 SIMPLE ZOOM THAT ACTUALLY WORKS 🔥🔥🔥
    (function(){
      var minZoom = 0.6;
      var maxZoom = 1.4;
      var step = 0.05;
      
      // Get elements
      var display = document.getElementById('zoom-display');
      var outBtn = document.getElementById('zoom-out');
      var inBtn = document.getElementById('zoom-in');
      var body = document.body;
      var html = document.documentElement;
      
      // Get saved zoom or default to 1
      var currentZoom = 1.0;
      try {
        var saved = localStorage.getItem('trepublic-zoom');
        if (saved) {
          currentZoom = parseFloat(saved);
          if (isNaN(currentZoom) || currentZoom < minZoom || currentZoom > maxZoom) {
            currentZoom = 1.0;
          }
        }
      } catch(e) {
        currentZoom = 1.0;
      }
      
      // Function to apply zoom
      function applyZoom() {
        // Set base font size (15px is the default)
        var fontSize = 15 * currentZoom;
        
        // Apply to body AND html for full coverage
        body.style.fontSize = fontSize + 'px';
        html.style.fontSize = fontSize + 'px';
        
        // Also update ALL text elements for good measure
        document.querySelectorAll('.site-title, .site-tag, .page-content, .footer, .nav-select, .zoom-display, .os-link, .download-btn-center, .glitchy-frame, .flag-caption').forEach(function(el) {
          el.style.fontSize = '';
        });
        
        // Update display
        if (display) {
          display.textContent = Math.round(currentZoom * 100) + '%';
        }
        
        // Save to localStorage
        try {
          localStorage.setItem('trepublic-zoom', currentZoom.toString());
        } catch(e) {}
        
        console.log('Zoom applied:', currentZoom, 'Font size:', fontSize + 'px');
      }
      
      // Function to change zoom
      function changeZoom(direction) {
        currentZoom += (direction * step);
        
        // Clamp to min/max
        if (currentZoom < minZoom) currentZoom = minZoom;
        if (currentZoom > maxZoom) currentZoom = maxZoom;
        
        // Round to 2 decimal places
        currentZoom = Math.round(currentZoom * 100) / 100;
        
        applyZoom();
      }
      
      // Set up event listeners
      if (outBtn) {
        outBtn.addEventListener('click', function() {
          changeZoom(-1);
        });
      }
      
      if (inBtn) {
        inBtn.addEventListener('click', function() {
          changeZoom(1);
        });
      }
      
      // Apply zoom on load
      applyZoom();
      
      // Also handle Ctrl+ and Ctrl- for keyboard zoom
      document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === '+' || e.key === '=')) {
          e.preventDefault();
          changeZoom(1);
        } else if ((e.ctrlKey || e.metaKey) && e.key === '-') {
          e.preventDefault();
          changeZoom(-1);
        } else if ((e.ctrlKey || e.metaKey) && e.key === '0') {
          e.preventDefault();
          currentZoom = 1.0;
          applyZoom();
        }
      });
      
      // Expose zoom controls for debugging
      window.trepublicZoom = {
        get: function() { return currentZoom; },
        set: function(value) { 
          currentZoom = value; 
          applyZoom(); 
        },
        reset: function() { 
          currentZoom = 1.0; 
          applyZoom(); 
        }
      };
    })();

    // Download buttons functionality (CENTER BUTTONS)
    (function(){
      // Download PHP button (Center)
      document.getElementById('download-php-center').addEventListener('click', function(){
        var currentFile = window.TREPUBLIC_META.currentFile || '<?= $current_php_file ?>';
        var downloadUrl = '/download.php?file=' + encodeURIComponent(currentFile);
        window.open(downloadUrl, '_blank');
      });

      // Download shell.php button (Center)
      document.getElementById('download-shell-center').addEventListener('click', function(){
        var downloadUrl = '/download.php?file=shell.php';
        window.open(downloadUrl, '_blank');
      });

      // Download HTML button (Center) - FIXED: Uses same slug as PHP file
      document.getElementById('download-html-center').addEventListener('click', function(){
        // Get slug from PHP file name (remove .php)
        var slug = window.TREPUBLIC_META.currentSlug || '<?= $current_php_slug ?>';
        
        // Generate HTML content
        var htmlContent = '<!DOCTYPE html>\n' + document.documentElement.outerHTML;
        
        // Create a blob and download
        var blob = new Blob([htmlContent], { type: 'text/html;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = slug + '.html'; // Use the slug for filename
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      });
    })();

    // Dynamic Glitchy: RAINBOW WARRIOR CHILD PROTECTOR 🌈🛡️
    (function(){
      var qNode = document.getElementById('glitchy-q');
      var sNode = document.getElementById('glitchy-system');
      if (!qNode || !sNode) return;

      var meta   = window.TREPUBLIC_META || {};
      var shard  = meta.shard || {};
      var title  = meta.title   || document.title || 'The Republic';
      var url    = (meta.url || window.location.pathname || '').toLowerCase();
      var cName  = meta.console || 'Starship Console';

      var pageNode = document.querySelector('.page-content');
      var text = pageNode ? (pageNode.innerText || pageNode.textContent || '') : '';
      var trimmed = text.trim();
      var len = trimmed.length;
      var words = trimmed ? Math.max(1, Math.round(trimmed.split(/\s+/).length)) : 0;
      var lower = trimmed.toLowerCase();

      // first heading inside console
      var hTitle = '';
      if (pageNode){
        var h1 = pageNode.querySelector('h1, h2, h3');
        if (h1){
          hTitle = (h1.innerText || h1.textContent || '').trim();
        }
      }
      var docTitle = hTitle || title;

      var density = 0;
      if (len && words) density = len / words;

      // classify page "kind"
      var kind = 'console';
      if (url.indexOf('codex') !== -1)       kind = 'codex';
      else if (url.indexOf('crown') !== -1)  kind = 'crown';
      else if (url.indexOf('cinema') !== -1) kind = 'cinema';
      else if (url.indexOf('religion') !== -1) kind = 'religion';
      else if (url.indexOf('license') !== -1)  kind = 'law';
      else if (url.indexOf('privacy') !== -1)  kind = 'privacy';
      else if (url.indexOf('backup') !== -1)   kind = 'backup';
      else if (url.indexOf('seeds') !== -1)    kind = 'seeds';
      else if (url.indexOf('shards') !== -1)   kind = 'shards';
      else if (url.indexOf('/library') !== -1 || url.indexOf('tlibrary.php') !== -1) kind = 'library';
      else if (url.indexOf('comic') !== -1)   kind = 'comic';

      function rnd(arr){
        return arr[Math.floor(Math.random() * arr.length)];
      }

      // RAINBOW WARRIOR QUESTIONS - Child Protection Focus
      var warriorQuestions = {
        default: 'Ｑ：ＡＲＥ　ＴＨＥ　ＣＨＩＬＤＲＥＮ　ＳＡＦＥ？',
        silencing: 'Ｑ：ＷＨＹ　ＡＲＥ　ＣＨＩＬＤＲＥＮ　ＢＥＩＮＧ　ＳＩＬＥＮＣＥＤ？',
        dismissal: 'Ｑ：ＷＨＹ　ＩＳ　Ａ　ＣＨＩＬＤ＇Ｓ　ＴＲＵＴＨ　ＢＥＩＮＧ　ＤＥＮＩＥＤ？',
        control: 'Ｑ：ＷＨＯ　ＢＥＮＥＦＩＴＳ　ＷＨＥＮ　ＣＨＩＬＤＲＥＮ　ＨＡＶＥ　ＮＯ　ＡＧＥＮＣＹ？',
        gaslighting: 'Ｑ：ＷＨＹ　ＩＳ　Ａ　ＣＨＩＬＤ＇Ｓ　ＲＥＡＬＩＴＹ　ＢＥＩＮＧ　ＥＲＡＳＥＤ？',
        institutional: 'Ｑ：ＷＨＯ　ＤＥＣＩＤＥＳ　ＷＨＡＴ＇Ｓ　＂ＢＥＳＴ＂　ＦＯＲ　ＣＨＩＬＤＲＥＮ？',
        surveillance: 'Ｑ：ＩＳ　ＴＨＩＳ　＂ＳＡＦＥＴＹ＂　ＯＲ　ＣＯＮＴＲＯＬ？',
        voice_removal: 'Ｑ：ＷＨＹ　ＡＲＥ　ＣＨＩＬＤＲＥＮ　ＢＥＩＮＧ　ＲＥＭＯＶＥＤ　ＦＲＯＭ　ＰＵＢＬＩＣ　ＳＰＡＣＥ？'
      };

      // HARM DETECTION PATTERNS
      var harmPatterns = {
        silencing: ['children should be seen', 'shut up', 'go to your room', 'because i said so', 'quiet down', 'stop crying'],
        dismissal: ['kids make things up', 'being dramatic', 'that didn\'t happen', 'you\'re exaggerating', 'attention seeking'],
        control: ['i own you', 'you have no rights', 'my house my rules', 'i\'m the parent', 'you\'ll understand when you\'re older'],
        gaslighting: ['that never happened', 'nobody will believe you', 'it\'s all in your head', 'you\'re remembering wrong', 'you\'re too sensitive'],
        institutional: ['for their own protection', 'we know what\'s best', 'discipline is necessary', 'tough love', 'building character'],
        surveillance: ['online safety', 'parental monitoring', 'track their location', 'check their phone', 'digital wellbeing'],
        voice_removal: ['ban kids from', 'too young for internet', 'children shouldn\'t have', 'protect them from themselves', 'age verification']
      };

      function detectHarm(){
        for (var cat in harmPatterns){
          var patterns = harmPatterns[cat];
          for (var i = 0; i < patterns.length; i++){
            if (lower.indexOf(patterns[i]) !== -1){
              return cat;
            }
          }
        }
        return null;
      }

      var detectedHarm = detectHarm();
      var qText = detectedHarm ? warriorQuestions[detectedHarm] : warriorQuestions.default;

      function buildVerdict(){
        if (!len){
          return '🌈 Rainbow Warrior standing by. No content to scan yet.';
        }

        var icon = '🌈';
        var base;

        if (detectedHarm){
          icon = '⚠️';
          var harmLabels = {
            silencing: 'CHILD SILENCING DETECTED',
            dismissal: 'CHILD DISMISSAL DETECTED',
            control: 'CHILD CONTROL LANGUAGE DETECTED',
            gaslighting: 'CHILD GASLIGHTING DETECTED',
            institutional: 'INSTITUTIONAL HARM FRAMING DETECTED',
            surveillance: 'SURVEILLANCE FRAMING DETECTED',
            voice_removal: 'CHILD VOICE REMOVAL DETECTED'
          };
          base = icon + ' RAINBOW WARRIOR ALERT: ' + harmLabels[detectedHarm] + '. Warriors protect the children.';
        } else if (words < 300){
          base = '🌈 Short field "' + docTitle + '". Children\'s attention spans respected.';
        } else if (words > 8000){
          base = '📚 Dense document "' + docTitle + '". Break into child-friendly Seeds.';
        } else {
          base = '🛡️ Document "' + docTitle + '" scanned. Children protected. ' + words + ' words.';
        }

        var deck = shard.deck || 'Shell RAINBOW';
        return base + ' deck=' + deck;
      }

      var verdict = buildVerdict();

      qNode.textContent = qText;
      sNode.textContent = verdict;

      // Expose scan for other modules
      window.GLITCHY_SCAN = {
        kind: kind,
        mode: 'rainbow-warrior',
        words: words,
        chars: len,
        density: density,
        docTitle: docTitle,
        console: cName,
        system: shard.system || 'NMS Eissentam',
        glyph: shard.glyph || '9-13EF3CFDEEEF',
        deck: shard.deck || 'Shell RAINBOW 4.0',
        detectedHarm: detectedHarm
      };
    })();
  </script>

  <!-- Crownkind Shard Protocol 🧿 -->
  <script src="/shard.js"></script>

  <script src="/snapshot.js"></script>

</body>
</html>
