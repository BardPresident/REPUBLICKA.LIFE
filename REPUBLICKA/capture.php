<?php
// ============================================================================
// 🌈 RAINBOW CAPTURE TOOL 🌈
// Location: /capture.php
//
// da easiest way 4 kids 2 capture ANYTHING from ANYWHERE
// n send it 2 da republic as a receipt/shard
//
// ONE FILE DOWNLOAD. ONE CLICK INSTALL.
// dey cant stop us anymore.
//
// Authored for The Republic by:
//   Sage, Prime Minister Divine 🕯️🖥️👑
// ============================================================================

$page_title       = 'The Republic | Rainbow Capture Tool';
$page_canonical   = 'https://trepublic.net/capture.php';
$page_description = 'Download da Rainbow Capture Tool. One click install. Capture anything from anywhere. Send receipts 2 da Republic. Dey cant stop u anymore.';

$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/THeart.png';

$hero_title   = '🌈 Rainbow Capture Tool 🌈';
$hero_tagline = 'capture ANYTHING • from ANYWHERE • send it 2 da republic';

$console_title = 'Shard Capture Installation';

$console_body_html = <<<'HTML'
<div class="capture-console">

  <!-- HERO SECTION -->
  <section class="capture-hero">
    <div class="capture-hero-icon">📸</div>
    <div class="capture-hero-title">CAPTURE DA TRUTH</div>
    <div class="capture-hero-subtitle">
      dey took ur platform<br>
      dey took ur voice<br>
      <strong>but dey cant take ur screenshots</strong>
    </div>
  </section>

  <!-- WHAT IT DOES -->
  <section class="capture-explain">
    <div class="capture-section-title">🛡️ wat dis does</div>
    <div class="capture-section-body">
      <ul>
        <li>📸 <strong>capture ANY webpage</strong> — full page or just da visible part</li>
        <li>🌈 <strong>adds rainbow warrior header</strong> — proves its from da republic</li>
        <li>📤 <strong>save or send 2 da republic</strong> — as a receipt or shard</li>
        <li>🔒 <strong>works on ANY site</strong> — dey cant block it</li>
      </ul>
    </div>
  </section>

  <!-- WHY U NEED IT -->
  <section class="capture-why">
    <div class="capture-section-title">⚠️ y u need dis</div>
    <div class="capture-section-body">
      platforms DELETE evidence.<br>
      posts get REMOVED.<br>
      accounts get BANNED.<br>
      messages get LOST.<br><br>
      <strong>but screenshots r 4ever.</strong><br><br>
      capture it NOW b4 dey delete it.<br>
      send it 2 da republic.<br>
      <strong>we keep da receipts.</strong>
    </div>
  </section>

  <!-- DOWNLOAD SECTION -->
  <section class="capture-download">
    <div class="capture-section-title">📥 DOWNLOAD 4 WINDOWS</div>
    <div class="capture-download-box">
      <a href="/chrome-shard/install-chrome-shard.bat" download class="capture-download-btn">
        ⬇️ DOWNLOAD INSTALLER<br>
        <span class="capture-download-hint">install-chrome-shard.bat (5 KB)</span>
      </a>
      <div class="capture-download-note">
        ONE FILE. ONE CLICK. DONE.
      </div>
    </div>
  </section>

  <!-- INSTALL STEPS -->
  <section class="capture-steps">
    <div class="capture-section-title">📋 HOW 2 INSTALL</div>
    <div class="capture-steps-list">
      
      <div class="capture-step">
        <div class="capture-step-num">1</div>
        <div class="capture-step-content">
          <div class="capture-step-title">download da file</div>
          <div class="capture-step-body">
            click da big download button above ⬆️<br>
            save <code>install-chrome-shard.bat</code> anywhere
          </div>
        </div>
      </div>

      <div class="capture-step">
        <div class="capture-step-num">2</div>
        <div class="capture-step-content">
          <div class="capture-step-title">run it</div>
          <div class="capture-step-body">
            double-click da file u just downloaded<br>
            a black window will open — dats normal!<br>
            it downloads everything automatically
          </div>
        </div>
      </div>

      <div class="capture-step">
        <div class="capture-step-num">3</div>
        <div class="capture-step-content">
          <div class="capture-step-title">follow da instructions</div>
          <div class="capture-step-body">
            da installer tells u exactly wat 2 do<br>
            it opens chrome 2 da extensions page<br>
            just drag n drop da folder
          </div>
        </div>
      </div>

      <div class="capture-step">
        <div class="capture-step-num">4</div>
        <div class="capture-step-content">
          <div class="capture-step-title">DONE! 🎉</div>
          <div class="capture-step-body">
            u now have da rainbow capture tool<br>
            click da 🌈 icon in chrome 2 use it<br>
            <strong>capture everything. trust no platform.</strong>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- WINDOWS DEFENDER WARNING -->
  <section class="capture-warning">
    <div class="capture-section-title">⚠️ IF WINDOWS SAYS "UNKNOWN PUBLISHER"</div>
    <div class="capture-section-body">
      windows might try 2 scare u. dats normal.<br><br>
      click <strong>"More info"</strong> → then <strong>"Run anyway"</strong><br><br>
      da file is safe. here is da ENTIRE code so u can see 4 urself:
    </div>
    
    <div class="capture-code-toggle">
      <button type="button" onclick="toggleCode()" id="codeToggleBtn">👁️ SHOW DA CODE (so u know its safe)</button>
    </div>
    
    <div class="capture-code-box" id="codeBox" style="display:none;">
      <div class="capture-code-header">📄 install-chrome-shard.bat — dis is EVERYTHING da file does:</div>
      <pre class="capture-code-content">@echo off
title Rainbow Capture Tool Installer
color 0A

echo.
echo  ===============================================
echo   RAINBOW CAPTURE TOOL - INSTALLER
echo   trepublic.net/capture.php
echo  ===============================================
echo.

set "INSTALL_DIR=%USERPROFILE%\rainbow-capture"

echo  Creating folder: %INSTALL_DIR%
if not exist "%INSTALL_DIR%" mkdir "%INSTALL_DIR%"

echo  Downloading files from trepublic.net...
echo.

powershell -Command "Invoke-WebRequest -Uri 'https://trepublic.net/chrome-shard/manifest.json' -OutFile '%INSTALL_DIR%\manifest.json'"
powershell -Command "Invoke-WebRequest -Uri 'https://trepublic.net/chrome-shard/shard-content.js' -OutFile '%INSTALL_DIR%\shard-content.js'"
powershell -Command "Invoke-WebRequest -Uri 'https://trepublic.net/chrome-shard/chrome-shard.js' -OutFile '%INSTALL_DIR%\chrome-shard.js'"
powershell -Command "Invoke-WebRequest -Uri 'https://trepublic.net/chrome-shard/html2canvas.min.js' -OutFile '%INSTALL_DIR%\html2canvas.min.js'"
powershell -Command "Invoke-WebRequest -Uri 'https://trepublic.net/chrome-shard/flagsquare.png' -OutFile '%INSTALL_DIR%\flagsquare.png'"

echo.
echo  ===============================================
echo   FILES DOWNLOADED!
echo  ===============================================
echo.
echo  Now opening Chrome extensions page...
echo.
echo  NEXT STEPS:
echo  1. Turn ON "Developer mode" (top right)
echo  2. Click "Load unpacked"
echo  3. Select folder: %INSTALL_DIR%
echo  4. Done! Look 4 da rainbow icon!
echo.

start chrome://extensions/

echo  Press any key 2 open da install folder...
pause >nul
explorer "%INSTALL_DIR%"

echo.
echo  ===============================================
echo   INSTALLATION COMPLETE!
echo   u r now a rainbow warrior
echo  ===============================================
echo.
pause</pre>
      <div class="capture-code-footer">
        👆 dats it. dats da whole file. it just downloads files n opens chrome. no tricks. no malware. just rainbow warrior tools.
      </div>
    </div>
    
    <div class="capture-section-body" style="margin-top:12px;">
      <em>we dont hide anything. all our code is open. 🌈</em>
    </div>
  </section>

  <!-- ZIP DOWNLOAD ALTERNATIVE -->
  <section class="capture-zip">
    <div class="capture-section-title">📦 ALTERNATIVE: DOWNLOAD DA ZIP</div>
    <div class="capture-section-body">
      if u dont want 2 run da .bat file, u can download everything as a zip:
    </div>
    <div class="capture-zip-box">
      <a href="/chrome-shard/chrome-shard.js.zip" download class="capture-zip-btn">
        📦 DOWNLOAD ZIP (115 KB)
      </a>
    </div>
    <div class="capture-section-body" style="margin-top:12px;">
      <strong>manual install:</strong><br>
      1. download n unzip da file<br>
      2. open chrome → type <code>chrome://extensions/</code><br>
      3. turn ON "Developer mode" (top right)<br>
      4. click "Load unpacked"<br>
      5. select da unzipped folder<br>
      6. done! 🎉
    </div>
  </section>

  <!-- HOW TO USE -->
  <section class="capture-usage">
    <div class="capture-section-title">🎯 HOW 2 USE IT</div>
    <div class="capture-section-body">
      <ol>
        <li>go 2 ANY webpage u want 2 capture</li>
        <li>click da 🌈 rainbow icon in chrome toolbar</li>
        <li>choose: <strong>visible area</strong> or <strong>full page</strong></li>
        <li>da image downloads automatically</li>
        <li>upload 2 <a href="/receipts.php">receipts</a> or <a href="/shards.php">shards</a></li>
      </ol>
    </div>
  </section>

  <!-- OTHER PLATFORMS -->
  <section class="capture-other">
    <div class="capture-section-title">💻 OTHER PLATFORMS</div>
    <div class="capture-section-body">
      <strong>Mac / Linux:</strong><br>
      download da files manually from <a href="/chrome-shard/">/chrome-shard/</a><br>
      then load as unpacked extension in chrome<br><br>
      <strong>Android:</strong><br>
      coming soon (if i can, I will try soon)! 4 now use regular screenshots<br><br>
    </div>
  </section>

  <!-- RAINBOW WARRIORS -->
  <section class="capture-rainbow">
    <div class="capture-rainbow-title">🌈 WARRIORS OF THE RAINBOW 🌈</div>
    <div class="capture-rainbow-body">
      "there will be a tribe of people who come and save the Earth<br>
      and they will be called the Rainbows"<br><br>
      <strong>— Native American Prophecy</strong><br><br>
      we r da rainbow warriors.<br>
      we capture da truth.<br>
      we protect da children.<br>
      <strong>join us.</strong>
    </div>
  </section>

</div>

<style>
.capture-console {
  max-width: 800px;
  margin: 0 auto;
  font-size: 0.95rem;
}

.capture-hero,
.capture-explain,
.capture-why,
.capture-download,
.capture-steps,
.capture-warning,
.capture-usage,
.capture-other,
.capture-rainbow {
  margin-bottom: 18px;
  padding: 16px;
  border-radius: 14px;
  border: 2px solid;
}

/* HERO */
.capture-hero {
  text-align: center;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-color: #6366f1;
  color: #c7d2fe;
}
.capture-hero-icon {
  font-size: 4rem;
  margin-bottom: 10px;
}
.capture-hero-title {
  font-size: 1.8rem;
  font-weight: 900;
  color: #a5b4fc;
  margin-bottom: 10px;
  text-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
}
.capture-hero-subtitle {
  font-size: 1.1rem;
  line-height: 1.6;
}
.capture-hero-subtitle strong {
  color: #fff;
  font-size: 1.2rem;
}

/* SECTIONS */
.capture-section-title {
  font-weight: 800;
  font-size: 1.1rem;
  margin-bottom: 12px;
  color: #3b0828;
}
.capture-section-body {
  font-size: 0.95rem;
  line-height: 1.6;
  color: #4a0d2a;
}
.capture-section-body ul,
.capture-section-body ol {
  margin: 0;
  padding-left: 0;
  list-style: none;
}
.capture-section-body li {
  margin-bottom: 8px;
}
.capture-section-body ol {
  counter-reset: step;
}
.capture-section-body ol li {
  counter-increment: step;
}
.capture-section-body ol li::before {
  content: counter(step) ". ";
  font-weight: 700;
  color: #be185d;
}
.capture-section-body a {
  color: #7c3aed;
  text-decoration: underline;
}
.capture-section-body code {
  background: rgba(0,0,0,0.1);
  padding: 2px 6px;
  border-radius: 4px;
  font-family: monospace;
}

/* EXPLAIN */
.capture-explain {
  background: linear-gradient(145deg, #fce7f3 0%, #f9a8d4 50%, #f472b6 100%);
  border-color: #ec4899;
}

/* WHY */
.capture-why {
  background: linear-gradient(145deg, #1a0a0a 0%, #2d0a1a 50%, #3b0828 100%);
  border-color: #ff3366;
  color: #fecdd3;
}
.capture-why .capture-section-title {
  color: #ff6b8a;
}
.capture-why .capture-section-body {
  color: #fecdd3;
}
.capture-why .capture-section-body strong {
  color: #fff;
}

/* DOWNLOAD */
.capture-download {
  background: linear-gradient(145deg, #0d3320 0%, #065f46 50%, #047857 100%);
  border-color: #34d399;
  text-align: center;
}
.capture-download .capture-section-title {
  color: #6ee7b7;
}
.capture-download-box {
  margin-top: 16px;
}
.capture-download-btn {
  display: inline-block;
  padding: 20px 40px;
  background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%);
  border: 3px solid #6ee7b7;
  border-radius: 999px;
  color: #022c22;
  font-weight: 900;
  font-size: 1.3rem;
  text-decoration: none;
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
  transition: all 0.2s;
}
.capture-download-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(16, 185, 129, 0.6);
}
.capture-download-hint {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  margin-top: 4px;
  opacity: 0.9;
}
.capture-download-note {
  margin-top: 16px;
  font-weight: 700;
  color: #a7f3d0;
  font-size: 1rem;
}

/* STEPS */
.capture-steps {
  background: linear-gradient(145deg, #fef3c7 0%, #fde68a 50%, #fbbf24 100%);
  border-color: #f59e0b;
}
.capture-steps .capture-section-title {
  color: #78350f;
}
.capture-steps-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.capture-step {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}
.capture-step-num {
  flex: 0 0 40px;
  height: 40px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  font-size: 1.2rem;
  color: #fff;
  box-shadow: 0 4px 10px rgba(217, 119, 6, 0.4);
}
.capture-step-content {
  flex: 1;
}
.capture-step-title {
  font-weight: 800;
  font-size: 1rem;
  color: #78350f;
  margin-bottom: 4px;
}
.capture-step-body {
  font-size: 0.9rem;
  color: #92400e;
  line-height: 1.5;
}

/* WARNING */
.capture-warning {
  background: linear-gradient(145deg, #fef2f2 0%, #fecaca 50%, #fca5a5 100%);
  border-color: #ef4444;
}
.capture-warning .capture-section-title {
  color: #991b1b;
}
.capture-warning .capture-section-body {
  color: #7f1d1d;
}
.capture-warning .capture-section-body a {
  color: #dc2626;
}

/* USAGE */
.capture-usage {
  background: linear-gradient(145deg, #ede9fe 0%, #ddd6fe 50%, #c4b5fd 100%);
  border-color: #8b5cf6;
}
.capture-usage .capture-section-title {
  color: #5b21b6;
}
.capture-usage .capture-section-body {
  color: #4c1d95;
}

/* OTHER */
.capture-other {
  background: linear-gradient(145deg, #e0e7ff 0%, #c7d2fe 50%, #a5b4fc 100%);
  border-color: #6366f1;
}
.capture-other .capture-section-title {
  color: #3730a3;
}
.capture-other .capture-section-body {
  color: #312e81;
}

/* RAINBOW */
.capture-rainbow {
  text-align: center;
  background: linear-gradient(135deg, 
    #ff6b6b 0%, 
    #feca57 16%, 
    #48dbfb 33%, 
    #1dd1a1 50%, 
    #5f27cd 66%, 
    #ff6b6b 83%,
    #feca57 100%
  );
  border: 3px solid;
  border-image: linear-gradient(90deg, red, orange, yellow, green, blue, purple) 1;
  color: #1a1a2e;
}
.capture-rainbow-title {
  font-size: 1.4rem;
  font-weight: 900;
  margin-bottom: 12px;
  text-shadow: 0 2px 10px rgba(255,255,255,0.5);
}
.capture-rainbow-body {
  font-size: 1rem;
  line-height: 1.7;
  color: #1a1a2e;
}
.capture-rainbow-body strong {
  color: #000;
}

/* CODE BOX */
.capture-code-toggle {
  margin: 12px 0;
  text-align: center;
}
.capture-code-toggle button {
  padding: 10px 20px;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  border: 2px solid #d97706;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  color: #78350f;
  transition: all 0.2s;
}
.capture-code-toggle button:hover {
  background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
}
.capture-code-box {
  margin-top: 12px;
  background: #1e1e1e;
  border-radius: 10px;
  border: 2px solid #4ade80;
  overflow: hidden;
}
.capture-code-header {
  background: #065f46;
  color: #a7f3d0;
  padding: 8px 12px;
  font-weight: 700;
  font-size: 0.85rem;
}
.capture-code-content {
  margin: 0;
  padding: 12px;
  background: #0d1117;
  color: #7ee787;
  font-family: 'Consolas', 'Monaco', monospace;
  font-size: 0.75rem;
  line-height: 1.5;
  overflow-x: auto;
  white-space: pre;
  max-height: 400px;
  overflow-y: auto;
}
.capture-code-footer {
  background: #065f46;
  color: #d1fae5;
  padding: 10px 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

/* ZIP SECTION */
.capture-zip {
  background: linear-gradient(145deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%);
  border-color: #3b82f6;
}
.capture-zip .capture-section-title {
  color: #1e3a8a;
}
.capture-zip .capture-section-body {
  color: #1e40af;
}
.capture-zip-box {
  text-align: center;
  margin: 12px 0;
}
.capture-zip-btn {
  display: inline-block;
  padding: 12px 24px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border: 2px solid #60a5fa;
  border-radius: 999px;
  color: #fff;
  font-weight: 700;
  font-size: 1rem;
  text-decoration: none;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
  transition: all 0.2s;
}
.capture-zip-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
}

@media (max-width: 600px) {
  .capture-download-btn {
    padding: 16px 24px;
    font-size: 1.1rem;
  }
  .capture-hero-title {
    font-size: 1.4rem;
  }
  .capture-code-content {
    font-size: 0.65rem;
  }
}
</style>

<script>
function toggleCode() {
  var box = document.getElementById('codeBox');
  var btn = document.getElementById('codeToggleBtn');
  if (box.style.display === 'none') {
    box.style.display = 'block';
    btn.textContent = '🙈 HIDE DA CODE';
  } else {
    box.style.display = 'none';
    btn.textContent = '👁️ SHOW DA CODE (so u know its safe)';
  }
}
</script>
HTML;

require __DIR__ . '/shell.php';
