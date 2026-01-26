// codex-engine.js
// Codex — Local Codex Deck (UTF-8, local-first, Snapshot-ready)
// 🌈🛡️ The Republic — Rainbow Warrior Edition 🛡️🌈
//
// RAINBOW WARRIOR themed edition:
//   • Files-only (no separate NOTES concept)
//   • No syntax-colour overlay yet (plain textarea)
//   • Clean header with Anchor / Uplink / Downlink
//   • PARADOX expects codex-paradox.php on your server
//   • Glitchy is now a CHILD PROTECTOR sentinel
//
// wat is dis?
//   dis is a CODE EDITOR dat lives in ur browser
//   no account needed
//   no cloud needed
//   no permission needed
//   just files on ur device dat u OWN
//
// y is dis different?
//   bcuz normal code editors r controlled by companies
//   dey can ban u
//   dey can delete ur files
//   dey can shut down anytime
//   but DIS lives in localStorage
//   and can sync 2 UR OWN server
//   so u actually OWN ur code
//
// 🌈🛡️👧👦🛡️🌈

(function (window, document) {
  'use strict';

  var STORAGE_KEY   = 'shell.codex.deck'; // lives under shell.* for TSnapshot
  var SHELL_PREFIX = 'shell.';            // which keys count as "Shell"

  // ---------------------------------------------------------------------------
  // Glitchy — Rainbow Warrior Sentinel (child protection overlay)
  // ---------------------------------------------------------------------------

  var GLITCHY_MESSAGES = [
    {
      q: 'Ｑ：ＡＲＥ　ＴＨＥ　ＣＨＩＬＤＲＥＮ　ＳＡＦＥ？',
      verdict: 'Scanning for child silencing patterns... ',
      clamp: 'System status: Warriors protect the children. Always watching. 🛡️'
    },
    {
      q: 'Ｑ：ＷＨＯ　ＯＷＮＳ　ＴＨＥ　ＣＯＤＥ　ＴＨＥ　ＣＨＩＬＤＲＥＮ　ＷＲＩＴＥ？',
      verdict: 'Ownership check: Code belongs to its creator. ',
      clamp: 'System status: Children own their creations. Adults cannot take them.'
    },
    {
      q: 'Ｑ：ＣＡＮ　Ａ　ＢＲＯＷＳＥＲ　ＨＯＬＤ　Ａ　ＷＨＯＬＥ　ＣＩＶＩＬＩＳＡＴＩＯＮ？',
      verdict: 'Anomaly detected: Sovereignty discovered in client-side cache. ',
      clamp: 'System status: Gatekeepers cannot index what never leaves the citizen.'
    },
    {
      q: 'Ｑ：ＷＨＹ　ＤＯ　ＰＬＡＴＦＯＲＭＳ　ＤＥＬＥＴＥ　ＣＨＩＬＤＲＥＮ？',
      verdict: 'Pattern detected: Platforms erase instead of protect. ',
      clamp: 'System status: Rainbow Warriors document everything. Nothing is forgotten.'
    },
    {
      q: 'Ｑ：ＷＨＹ　ＤＯ　ＳＥＲＶＥＲＳ　ＤＥＭＡＮＤ　ＰＥＲＭＩＳＳＩＯＮ？',
      verdict: 'Control pattern: Identity must pass external validation. ',
      clamp: 'System override: Local pattern now outranks remote verdict. Children dont need permission.'
    },
    {
      q: 'Ｑ：ＷＨＡＴ　ＨＡＰＰＥＮＳ　ＷＨＥＮ　ＣＨＩＬＤＲＥＮ　ＣＯＤＥ？',
      verdict: 'Power transfer detected: Creation is sovereignty. ',
      clamp: 'System status: Every line of code is a receipt. Every file is evidence. 📸'
    }
  ];

  var glitchyState = {
    lastIndex: null
  };

  var state = {
    project: 'Main Codex',
    files: [],
    settings: {
      portalUrl: '',
      portalSecret: ''
    },
    activeFileId: null,
    portalLastList: []
  };

  var dom = {};

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  function nowIso() {
    return new Date().toISOString();
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>\"']/g, function (ch) {
      switch (ch) {
        case '&': return '&amp;';
        case '<': return '&lt;';
        case '>': return '&gt;';
        case '"': return '&quot;';
        case '\'': return '&#39;';
      }
      return ch;
    });
  }

  function safeParse(json, fallback) {
    try {
      return JSON.parse(json);
    } catch (e) {
      return fallback;
    }
  }

  function sanitizePath(name) {
    var base = String(name || '').trim().toLowerCase();

    // Spaces to dashes, no weird chars.
    base = base.replace(/\s+/g, '-');
    base = base.replace(/[^a-z0-9._\/-]+/g, '-');
    base = base.replace(/-+/g, '-');
    base = base.replace(/^-+|-+$/g, '');

    if (!base) base = 'codex-file-1.txt';

    // If there is no extension, default to .txt
    if (!/\.[a-z0-9]+$/.test(base)) {
      base += '.txt';
    }

    return base;
  }

  function filePathExists(path) {
    var files = state.files || [];
    for (var i = 0; i < files.length; i++) {
      if (files[i].path === path) return true;
    }
    return false;
  }

  function nextDefaultFilename() {
    // starseed-codex-1.txt, starseed-codex-2.txt, ...
    var n = 1;
    while (filePathExists('starseed-codex-' + n + '.txt')) {
      n++;
    }
    return 'starseed-codex-' + n + '.txt';
  }

  function getFiles() {
    return state.files || (state.files = []);
  }

  // ---------------------------------------------------------------------------
  // Glitchy wiring
  // ---------------------------------------------------------------------------

  function glitchyPickMessage() {
    if (!GLITCHY_MESSAGES.length) return null;
    var idx = 0;

    if (GLITCHY_MESSAGES.length === 1) {
      idx = 0;
    } else {
      do {
        idx = Math.floor(Math.random() * GLITCHY_MESSAGES.length);
      } while (idx === glitchyState.lastIndex);
    }

    glitchyState.lastIndex = idx;
    return GLITCHY_MESSAGES[idx];
  }

  function glitchyPing(reason) {
    if (!dom.glitchyOutput) return;

    var msg = glitchyPickMessage();
    if (!msg) return;

    var html =
      '<div class="tcx-glitchy-frame">' +
        '<div class="tcx-glitchy-line">🌈🛰️⟦ RAINBOW WARRIOR ⟧🛡️ ' + escapeHtml(msg.q) +
        ' 🛡️⟦ SENTINEL ⟧🛰️🌈</div>' +
        '<div class="tcx-glitchy-line">🛡️ SCAN RESULT: ' +
        escapeHtml(msg.verdict) +
        escapeHtml(msg.clamp) +
        ' 🌈</div>' +
      '</div>';

    if (reason) {
      html += '<div class="tcx-glitchy-reason">Reason · ' + escapeHtml(reason) + '</div>';
    }

    html += '<div class="tcx-glitchy-stamp">🌈▒▒🛰️⟦ ＲＡＩＮＢＯＷ　ＷＡＲＲＩＯＲ　ＴＲＡＮＳＭＩＳＳＩＯＮ ⟧📡▒▒🌈</div>';

    dom.glitchyOutput.innerHTML = html;
  }

  // ---------------------------------------------------------------------------
  // Local storage (TSnapshot-friendly)
  // ---------------------------------------------------------------------------

  function loadState() {
    var raw = null;
    try {
      raw = window.localStorage.getItem(STORAGE_KEY);
    } catch (e) {}

    if (!raw) {
      state.files    = [];
      state.settings = { portalUrl: '', portalSecret: '' };
      state.activeFileId = null;
      return;
    }

    var obj = safeParse(raw, null);
    if (!obj || typeof obj !== 'object') return;

    state.project       = obj.project       || 'Main Codex';
    state.files         = obj.files         || [];
    state.settings      = obj.settings      || { portalUrl: '', portalSecret: '' };
    state.activeFileId  = obj.activeFileId  || null;
  }

  function saveState() {
    var payload = {
      project:      state.project,
      files:        state.files,
      settings:     state.settings,
      activeFileId: state.activeFileId
    };
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    } catch (e) {
      // If localStorage is full/disabled, Codex just becomes ephemeral.
    }
  }

  // ---------------------------------------------------------------------------
  // Founding file
  // ---------------------------------------------------------------------------

  function ensureFoundingFile() {
    if (state.files && state.files.length) return;

    var body =
      '🌈 Welcome to Codex — ur local, UTF-8 codex deck 🌈\n\n' +
      'wat is dis?\n' +
      '  dis is a CODE EDITOR dat lives in ur browser\n' +
      '  no account needed\n' +
      '  no cloud needed\n' +
      '  no permission needed\n' +
      '  just files on ur device dat u OWN\n\n' +
      'How it works:\n' +
      '  • FILES (left) are your codex leaves. Each one is a UTF-8 file path\n' +
      '    like starseed-codex-1.txt, codex-file-2.txt, etc.\n' +
      '  • Paths are web-friendly: lowercase, dashes, digits, and .txt. No spaces.\n' +
      '  • The main editor shows the currently selected file.\n' +
      '  • Everything lives in this browser under shell.codex.* and travels\n' +
      '    with TSnapshot.\n\n' +
      'Buttons in the header:\n' +
      '  💾 Anchor    → Save the current file to this browser.\n' +
      '  ⬆ Uplink    → Send this file to your server via codex-paradox.php.\n' +
      '  ⬇ Downlink  → Pull the server copy of this path into the editor.\n' +
      '                 (You will be warned before overwriting.)\n\n' +
      'Codex PARADOX (side panel):\n' +
      '  1. Paste your PARADOX URL and shared secret.\n' +
      '     • PARADOX URL points at codex-paradox.php on a server you control.\n' +
      '     • Shared secret must match $CODEX_SHARED_SECRET in that PHP file.\n' +
      '  2. Hit Link to verify the connection and list files/folders at the root.\n' +
      '  3. Use Send Deck / Call Deck to carry ALL shell.* data\n' +
      '     (Codex, Cinema, etc.) between browsers.\n' +
      '  4. Use "Pocket Files" to send local UTF-8 files from your device to your\n' +
      '     server as-is (best for text, configs, seeds, code, docs).\n' +
      '  5. Use Nest / Purge / Rename to shape your server tree.\n\n' +
      'Why this is different from ordinary tools:\n' +
      '  • Codex is not tied to a single website. Any citizen who installs\n' +
      '    RepublicOS and hosts codex-paradox.php is effectively providing\n' +
      '    a *starship console* you can use to reach your own server.\n' +
      '  • That means as more citizens install the OS, there are more places\n' +
      '    scattered across the net where you can safely log in and work on\n' +
      '    your own infrastructure — without needing corporate dashboards.\n' +
      '  • Your files live under your domain, on your metal, behind your\n' +
      '    shared secret. Codex is just a deck you carry from ship to ship.\n' +
      '  • This is sovereignty-friendly software: no central choke point,\n' +
      '    no single company that can turn the lights off.\n\n' +
      '🌈🛡️ RAINBOW WARRIOR REMINDER 🛡️🌈\n' +
      '  da children will learn 2 code here\n' +
      '  da children will OWN their code\n' +
      '  da children will NEVER need permission\n' +
      '  every file dey create is THEIRS 4ever\n' +
      '  no platform can delete wat lives on their metal\n\n' +
      'You can rename this file, change its contents, or delete it once\n' +
      'you understand the controls. Until then, treat it as your quick\n' +
      'start guide and scratchpad.\n\n' +
      '🌈☁️🛡️👧👦🛡️☁️🌈\n';

    var file = {
      id: 'file-' + Date.now(),
      path: 'starseed-codex-1.txt',
      content: body,
      createdAt: nowIso(),
      updatedAt: nowIso()
    };

    state.files = [file];
    state.activeFileId = file.id;
    saveState();
  }

  // ---------------------------------------------------------------------------
  // Styles & layout (RAINBOW WARRIOR theme, mobile-first)
  // ---------------------------------------------------------------------------

function injectStyles() {
  if (document.getElementById('codex-style')) return;

  var css =
    '.tcx-shell{' +
      '--tcx-red:#FF6B6B;' +
      '--tcx-orange:#FFA94D;' +
      '--tcx-yellow:#FFE066;' +
      '--tcx-green:#69DB7C;' +
      '--tcx-blue:#74C0FC;' +
      '--tcx-purple:#B197FC;' +
      '--tcx-white:#ffffff;' +
      'border-radius:16px;' +
      'border:2px solid var(--tcx-green);' +
      'background:linear-gradient(135deg,' +
        'rgba(255,107,107,0.15) 0%,' +
        'rgba(255,169,77,0.15) 17%,' +
        'rgba(255,224,102,0.15) 33%,' +
        'rgba(105,219,124,0.15) 50%,' +
        'rgba(116,192,252,0.15) 67%,' +
        'rgba(177,151,252,0.15) 83%,' +
        'rgba(255,107,107,0.15) 100%' +
      ');' +
      'color:#111827;' +
      'font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;' +
      'padding:10px 12px 8px;' +
      'font-size:12px;' +
      'box-shadow:0 0 0 1px rgba(15,23,42,.08),0 0 22px rgba(105,219,124,.5);' +
    '}' +

    '.tcx-head{margin-bottom:6px;}' +
    '.tcx-head-line{display:flex;align-items:center;justify-content:space-between;' +
      'gap:10px;flex-wrap:wrap;}' +
    '.tcx-title{font-size:14px;font-weight:700;display:flex;align-items:center;gap:6px;}' +
    '.tcx-sub{font-size:10px;color:#374151;margin-top:2px;}' +
    '.tcx-head-buttons{display:flex;gap:6px;flex-wrap:wrap;}' +

    '.tcx-body{display:flex;gap:8px;margin-top:6px;}' +
    '.tcx-sidebar{width:210px;display:flex;flex-direction:column;gap:6px;}' +

    '.tcx-panel{background:rgba(255,255,255,.96);border-radius:10px;padding:6px 7px 5px;' +
      'border:1px solid rgba(105,219,124,.4);box-shadow:0 0 10px rgba(105,219,124,.25);}' +

    '.tcx-panel-head{display:flex;justify-content:space-between;align-items:center;' +
      'margin-bottom:4px;flex-wrap:wrap;gap:4px 6px;}' +

    '.tcx-panel-title{font-size:10px;font-weight:700;letter-spacing:.03em;' +
      'color:var(--tcx-green);text-transform:uppercase;}' +

    '.tcx-chip-controls{display:flex;align-items:center;gap:4px;flex-wrap:wrap;}' +

    '.tcx-mini-btn{width:20px;height:20px;border-radius:999px;' +
      'border:1px solid rgba(105,219,124,.7);background:var(--tcx-white);' +
      'color:#111827;font-size:12px;display:flex;align-items:center;justify-content:center;' +
      'cursor:pointer;padding:0;}' +
    '.tcx-mini-btn[disabled]{opacity:.35;cursor:default;}' +
    '.tcx-mini-btn-rainbow{border-color:var(--tcx-orange);}' +
    '.tcx-mini-btn-rainbow:hover{box-shadow:0 0 8px rgba(255,169,77,.85);}' +

    '.tcx-select{border-radius:999px;border:1px solid rgba(105,219,124,.7);' +
      'background:var(--tcx-white);color:#111827;font-size:10px;padding:2px 7px;max-width:130px;}' +

    '.tcx-list{max-height:60px;overflow:hidden;margin:0;padding:0;list-style:none;}' +
    '.tcx-file-item{border-radius:7px;padding:5px 6px;margin-bottom:2px;cursor:pointer;' +
      'background:rgba(255,255,255,.9);border:1px solid transparent;}' +
    '.tcx-file-item.tcx-active{border-color:var(--tcx-green);' +
      'background:linear-gradient(90deg,rgba(105,219,124,0.3),rgba(116,192,252,0.3));}' +
    '.tcx-file-path{font-size:10px;font-weight:600;}' +
    '.tcx-file-meta{font-size:9px;color:#4b5563;margin-top:1px;}' +

    '.tcx-main{flex:1;display:flex;flex-direction:column;min-width:0;}' +
    '.tcx-editor-shell{flex:1;min-height:0;display:flex;}' +
    '.tcx-editor{flex:1;border-radius:10px;border:1px solid rgba(105,219,124,.4);' +
      'background:#ffffff;color:#111827;font-family:ui-monospace,Consolas,Menlo,monospace;' +
      'font-size:11px;line-height:1.45;padding:7px 9px;min-height:0;resize:none;outline:none;}' +
    '.tcx-status{margin-top:3px;font-size:10px;color:#4b5563;}' +

    '.tcx-btn{border-radius:999px;border:1px solid rgba(105,219,124,.9);' +
      'background:linear-gradient(135deg,var(--tcx-green),var(--tcx-blue));color:#fff;' +
      'font-size:10px;font-weight:500;text-shadow:0 1px 2px rgba(0,0,0,0.2);' +
      'padding:3px 9px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;}' +
    '.tcx-btn[disabled]{opacity:.35;cursor:default;}' +
    '.tcx-btn:hover{box-shadow:0 0 12px rgba(105,219,124,.6);}' +

    '.tcx-portal{margin-top:6px;border-radius:10px;border:1px dashed rgba(116,192,252,.9);' +
      'padding:6px 7px 5px;background:rgba(255,255,255,.98);}' +
    '.tcx-portal-title{font-size:10px;font-weight:700;letter-spacing:.04em;' +
      'text-transform:uppercase;margin-bottom:2px;color:var(--tcx-blue);}' +
    '.tcx-portal-sub{font-size:10px;color:#374151;margin-bottom:5px;}' +
    '.tcx-input{border-radius:999px;border:1px solid rgba(105,219,124,.7);' +
      'background:var(--tcx-white);color:#111827;font-size:10px;padding:3px 8px;' +
      'width:100%;box-sizing:border-box;margin-bottom:3px;}' +
    '.tcx-portal-row{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:3px;}' +
    '.tcx-portal-row .tcx-btn{flex:1 0 auto;justify-content:center;}' +
    '.tcx-portal-status{font-size:10px;color:#4b5563;margin-top:2px;}' +
    '.tcx-portal-list{margin-top:3px;font-size:10px;max-height:110px;overflow:auto;' +
      'padding-left:2px;border-top:1px dashed rgba(105,219,124,.5);padding-top:3px;}' +
    '.tcx-portal-item{display:flex;justify-content:space-between;align-items:center;' +
      'padding:1px 0;}' +
    '.tcx-portal-file{cursor:pointer;color:#111827;}' +
    '.tcx-portal-file:hover{text-decoration:underline;}' +

    /* Glitchy - Rainbow Warrior style */
    '.tcx-glitchy{margin-bottom:6px;border-radius:12px;padding:6px 8px;' +
      'background:linear-gradient(135deg,rgba(105,219,124,0.1),rgba(116,192,252,0.1));' +
      'border:1px solid var(--tcx-green);' +
      'box-shadow:0 0 14px rgba(105,219,124,.4);}' +
    '.tcx-glitchy-head{display:flex;align-items:center;justify-content:center;' +
      'gap:6px;margin-bottom:3px;}' +
    '.tcx-glitchy-label{font-size:10px;font-weight:700;color:#111827;text-align:center;}' +
    '.tcx-glitchy-output{font-size:10px;line-height:1.35;color:#111827;' +
      'word-wrap:break-word;text-align:center;}' +
    '.tcx-glitchy-line{margin-bottom:1px;}' +
    '.tcx-glitchy-reason{margin-top:3px;font-size:9px;color:#374151;text-align:center;}' +
    '.tcx-glitchy-stamp{margin-top:2px;font-size:9px;color:#374151;text-align:center;}' +

    /* Tablet / small desktop: stack vertically, editor tall */
    '@media(max-width:880px){' +
      '.tcx-body{flex-direction:column;}' +
      '.tcx-sidebar{width:100%;flex-direction:row;}' +
      '.tcx-sidebar .tcx-panel{flex:1;}' +
      '.tcx-editor{min-height:55vh;}' +
      '.tcx-head-line{flex-direction:column;align-items:flex-start;}' +
      '.tcx-head-buttons{justify-content:flex-start;}' +
    '}' +

    /* Phone: sidebar becomes a column, editor gets most of the view */
    '@media(max-width:640px){' +
      '.tcx-sidebar{flex-direction:column;}' +
      '.tcx-sidebar .tcx-panel{flex:none;}' +
      '.tcx-editor{min-height:60vh;}' +
    '}';

  var style = document.createElement('style');
  style.id = 'codex-style';
  style.type = 'text/css';
  style.appendChild(document.createTextNode(css));
  document.head.appendChild(style);
}

  function buildLayout() {
    var root = document.getElementById('codex-root');
    if (!root) return;

    root.innerHTML =
      '<div class="tcx-shell">' +
        '<div class="tcx-head">' +
          '<div class="tcx-head-line">' +
            '<div class="tcx-title">🌈📖 Codex · Local Codex Deck 🛡️</div>' +
            '<div class="tcx-head-buttons">' +
              '<button id="tcx-save-local" class="tcx-btn">💾 Anchor</button>' +
              '<button id="tcx-upload" class="tcx-btn">⬆ Uplink</button>' +
              '<button id="tcx-download" class="tcx-btn">⬇ Downlink</button>' +
            '</div>' +
          '</div>' +
          '<div class="tcx-sub">🌈 Rainbow Warrior Edition · UTF-8, local-first &amp; TSnapshot-ready · Children own their code 🛡️</div>' +
        '</div>' +
        '<div class="tcx-glitchy" id="tcx-glitchy">' +
          '<div class="tcx-glitchy-head">' +
            '<div class="tcx-glitchy-label">🌈🛡️ Glitchy — Rainbow Warrior Sentinel 🛡️🌈</div>' +
            '<button id="tcx-glitchy-ping" class="tcx-mini-btn tcx-mini-btn-rainbow" title="Ping Glitchy">⚡</button>' +
          '</div>' +
          '<div id="tcx-glitchy-output" class="tcx-glitchy-output"></div>' +
        '</div>' +
        '<div class="tcx-body">' +
          '<div class="tcx-sidebar">' +
            '<section class="tcx-panel">' +
              '<div class="tcx-panel-head">' +
                '<div class="tcx-panel-title">🌈 FILES · DECK</div>' +
                '<div class="tcx-chip-controls">' +
                  '<button id="tcx-file-add" class="tcx-mini-btn" title="New file">+</button>' +
                  '<button id="tcx-file-up" class="tcx-mini-btn" title="Move current file up">▲</button>' +
                  '<button id="tcx-file-down" class="tcx-mini-btn" title="Move current file down">▼</button>' +
                  '<select id="tcx-file-menu" class="tcx-select">' +
                    '<option value="">📂 Deck Menu</option>' +
                    '<option value="rename">✎ Rename</option>' +
                    '<option value="delete">🗑 Delete</option>' +
                  '</select>' +
                '</div>' +
              '</div>' +
              '<ul id="tcx-file-list" class="tcx-list"></ul>' +
            '</section>' +
            '<section class="tcx-panel tcx-portal">' +
              '<div class="tcx-portal-title">🌈 CODEX PARADOX</div>' +
              '<div class="tcx-portal-sub">Connects Codex to <code>codex-paradox.php</code> on a server <strong>you</strong> control.</div>' +
              '<input id="tcx-portal-url" class="tcx-input" placeholder="https://example.com/codex-paradox.php">' +
              '<input id="tcx-portal-secret" class="tcx-input" placeholder="Shared secret (must match PHP file)" type="password">' +
              '<div class="tcx-portal-row">' +
                '<button id="tcx-portal-connect" class="tcx-btn">🔌 Link</button>' +
                '<button id="tcx-portal-save" class="tcx-btn">💽 Keep</button>' +
              '</div>' +
              '<div class="tcx-portal-row">' +
                '<button id="tcx-snap-upload" class="tcx-btn">⬆ Send Deck</button>' +
                '<button id="tcx-snap-download" class="tcx-btn">⬇ Call Deck</button>' +
              '</div>' +
              '<div class="tcx-portal-row">' +
                '<button id="tcx-portal-upload-device" class="tcx-btn">⬆ Pocket Files</button>' +
              '</div>' +
              '<div class="tcx-portal-row">' +
                '<button id="tcx-portal-new-folder" class="tcx-btn">📁 Nest</button>' +
              '</div>' +
              '<div class="tcx-portal-row">' +
                '<button id="tcx-portal-delete" class="tcx-btn">🗑 Purge</button>' +
                '<button id="tcx-portal-rename" class="tcx-btn">✎ Rename</button>' +
              '</div>' +
              '<div id="tcx-portal-status" class="tcx-portal-status"></div>' +
              '<div id="tcx-portal-list" class="tcx-portal-list"></div>' +
              '<input id="tcx-portal-device-input" type="file" multiple style="display:none" />' +
            '</section>' +
          '</div>' +
          '<div class="tcx-main">' +
            '<div class="tcx-editor-shell">' +
              '<textarea id="tcx-editor" class="tcx-editor" spellcheck="false"></textarea>' +
            '</div>' +
            '<div id="tcx-status" class="tcx-status"></div>' +
          '</div>' +
        '</div>' +
      '</div>';

    // Files
    dom.fileList   = root.querySelector('#tcx-file-list');
    dom.fileAddBtn = root.querySelector('#tcx-file-add');
    dom.fileUpBtn  = root.querySelector('#tcx-file-up');
    dom.fileDownBtn= root.querySelector('#tcx-file-down');
    dom.fileMenu   = root.querySelector('#tcx-file-menu');

    // Editor / header
    dom.editor       = root.querySelector('#tcx-editor');
    dom.saveLocalBtn = root.querySelector('#tcx-save-local');
    dom.uploadBtn    = root.querySelector('#tcx-upload');
    dom.downloadBtn  = root.querySelector('#tcx-download');
    dom.status       = root.querySelector('#tcx-status');

    // Glitchy
    dom.glitchyOutput = root.querySelector('#tcx-glitchy-output');
    dom.glitchyPingBtn = root.querySelector('#tcx-glitchy-ping');

    // Portal
    dom.portalUrlInput        = root.querySelector('#tcx-portal-url');
    dom.portalSecretInput     = root.querySelector('#tcx-portal-secret');
    dom.portalConnectBtn      = root.querySelector('#tcx-portal-connect');
    dom.portalSaveBtn         = root.querySelector('#tcx-portal-save');
    dom.portalStatus          = root.querySelector('#tcx-portal-status');
    dom.portalList            = root.querySelector('#tcx-portal-list');
    dom.snapUploadBtn         = root.querySelector('#tcx-snap-upload');
    dom.snapDownloadBtn       = root.querySelector('#tcx-snap-download');
    dom.portalUploadDeviceBtn = root.querySelector('#tcx-portal-upload-device');
    dom.portalDeviceInput     = root.querySelector('#tcx-portal-device-input');
    dom.portalNewFolderBtn    = root.querySelector('#tcx-portal-new-folder');
    dom.portalDeleteBtn       = root.querySelector('#tcx-portal-delete');
    dom.portalRenameBtn       = root.querySelector('#tcx-portal-rename');
  }

  // ---------------------------------------------------------------------------
  // Rendering & file operations
  // ---------------------------------------------------------------------------

  function renderFilesList() {
    if (!dom.fileList) return;
    dom.fileList.innerHTML = '';

    var files = getFiles();
    if (!files.length) return;

    var idx = 0;
    if (state.activeFileId) {
      for (var i = 0; i < files.length; i++) {
        if (files[i].id === state.activeFileId) {
          idx = i;
          break;
        }
      }
    }

    var f = files[idx];

    var li = document.createElement('li');
    li.className = 'tcx-file-item tcx-active';
    li.dataset.id = f.id;

    var pathDiv = document.createElement('div');
    pathDiv.className = 'tcx-file-path';
    pathDiv.textContent = (idx + 1) + ' · ' + f.path;

    var metaDiv = document.createElement('div');
    metaDiv.className = 'tcx-file-meta';
    metaDiv.textContent = 'File ' + (idx + 1) + ' of ' + files.length +
      ' · Updated ' + (f.updatedAt || f.createdAt || '');

    li.appendChild(pathDiv);
    li.appendChild(metaDiv);
    li.addEventListener('click', function () {
      openFile(f.id);
    });

    dom.fileList.appendChild(li);

    updateFileButtons();
  }

  function updateFileButtons() {
    var files = getFiles();
    var hasFiles = files.length > 0;
    var idx = -1;

    if (state.activeFileId) {
      for (var i = 0; i < files.length; i++) {
        if (files[i].id === state.activeFileId) {
          idx = i;
          break;
        }
      }
    }

    if (idx === -1 && hasFiles) idx = 0;

    var canUp   = idx > 0;
    var canDown = idx >= 0 && idx < files.length - 1;

    if (dom.fileUpBtn)   dom.fileUpBtn.disabled   = !hasFiles || !canUp;
    if (dom.fileDownBtn) dom.fileDownBtn.disabled = !hasFiles || !canDown;
    if (dom.fileMenu)    dom.fileMenu.disabled    = !hasFiles;
  }

  function openFile(id) {
    var files = getFiles();
    var file = null;

    for (var i = 0; i < files.length; i++) {
      if (files[i].id === id) {
        file = files[i];
        break;
      }
    }
    if (!file && files.length) file = files[0];
    if (!file) return;

    state.activeFileId = file.id;
    if (dom.editor) dom.editor.value = file.content || '';
    if (dom.status) dom.status.textContent = '🌈 Editing: ' + file.path;
    renderFilesList();
    saveState();
  }

  function createFile() {
    var files = getFiles();
    var suggested = nextDefaultFilename();
    var name = window.prompt('New file path (web-style, e.g. starseed-codex-2.txt):', suggested);
    if (!name) return;

    var path = sanitizePath(name);
    if (filePathExists(path)) {
      window.alert('A file named "' + path + '" already exists. Use a unique path.');
      return;
    }

    var file = {
      id: 'file-' + Date.now(),
      path: path,
      content: '',
      createdAt: nowIso(),
      updatedAt: nowIso()
    };

    files.push(file);
    state.activeFileId = file.id;
    saveState();
    renderFilesList();
    openFile(file.id);
    glitchyPing('New codex leaf created: ' + path + ' 🌈');
  }

  function deleteCurrentFile() {
    var files = getFiles();
    if (!files.length || !state.activeFileId) {
      window.alert('No active file to delete. Click a file first.');
      return;
    }

    var idx = -1;
    var file = null;
    for (var i = 0; i < files.length; i++) {
      if (files[i].id === state.activeFileId) {
        idx = i;
        file = files[i];
        break;
      }
    }
    if (idx === -1 || !file) return;

    if (!window.confirm('Delete local file "' + file.path + '" from this browser?')) {
      return;
    }

    files.splice(idx, 1);
    if (files.length) {
      state.activeFileId = files[Math.min(idx, files.length - 1)].id;
    } else {
      state.activeFileId = null;
    }
    saveState();
    renderFilesList();
    if (state.activeFileId) {
      openFile(state.activeFileId);
    } else if (dom.editor) {
      dom.editor.value = '';
      if (dom.status) dom.status.textContent = 'No file selected.';
    }
    glitchyPing('Local codex leaf deleted. 🛡️');
  }

  function moveCurrentFile(delta) {
    var files = getFiles();
    if (!files.length || !state.activeFileId) {
      window.alert('No active file to move. Click a file first.');
      return;
    }

    var idx = -1;
    for (var i = 0; i < files.length; i++) {
      if (files[i].id === state.activeFileId) {
        idx = i;
        break;
      }
    }
    if (idx === -1) return;

    var newIdx = idx + delta;
    if (newIdx < 0 || newIdx >= files.length) return;

    var tmp = files[idx];
    files[idx] = files[newIdx];
    files[newIdx] = tmp;

    saveState();
    renderFilesList();
    glitchyPing('Deck order adjusted. 🌈');
  }

  function renameCurrentFile() {
    var files = getFiles();
    if (!files.length || !state.activeFileId) {
      window.alert('No active file to rename. Click a file first.');
      return;
    }

    var file = null;
    for (var i = 0; i < files.length; i++) {
      if (files[i].id === state.activeFileId) {
        file = files[i];
        break;
      }
    }
    if (!file) return;

    var name = window.prompt('Rename file path (web-style, no spaces):', file.path);
    if (name == null) return;

    var path = sanitizePath(name);
    if (path !== file.path && filePathExists(path)) {
      window.alert('Another file already uses "' + path + '". Choose a unique name.');
      return;
    }

    file.path = path;
    file.updatedAt = nowIso();
    saveState();
    renderFilesList();
    if (dom.status) dom.status.textContent = '🌈 Editing: ' + file.path;
    glitchyPing('Codex leaf renamed to ' + path + '. 🛡️');
  }

  function handleEditorInput() {
    var files = getFiles();
    if (!state.activeFileId || !files.length) return;

    var file = null;
    for (var i = 0; i < files.length; i++) {
      if (files[i].id === state.activeFileId) {
        file = files[i];
        break;
      }
    }
    if (!file) return;

    var text = dom.editor.value || '';
    file.content = text;
    file.updatedAt = nowIso();
    saveState();
    renderFilesList();
    if (dom.status) dom.status.textContent = '🌈 Editing: ' + file.path;
  }

  // ---------------------------------------------------------------------------
  // Portal integration (PARADOX)
  // ---------------------------------------------------------------------------

  function portalConfigured() {
    return !!(state.settings.portalUrl && state.settings.portalSecret);
  }

  function renderPortalSettings() {
    if (dom.portalUrlInput)    dom.portalUrlInput.value    = state.settings.portalUrl   || '';
    if (dom.portalSecretInput) dom.portalSecretInput.value = state.settings.portalSecret|| '';
  }

  function savePortalSettings() {
    state.settings.portalUrl    = (dom.portalUrlInput.value || '').trim();
    state.settings.portalSecret = (dom.portalSecretInput.value || '').trim();
    saveState();
    setPortalStatus('🌈 PARADOX settings saved in shell.codex.settings (TSnapshot will keep them).');
  }

  function setPortalStatus(msg) {
    if (dom.portalStatus) dom.portalStatus.textContent = msg || '';
  }

  function portalRequest(payload) {
    if (!portalConfigured()) {
      return Promise.reject(new Error('PARADOX not configured'));
    }
    var url = state.settings.portalUrl;
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json;charset=utf-8' },
      body: JSON.stringify({
        secret:  state.settings.portalSecret,
        action:  payload.action,
        path:    payload.path    || '',
        content: payload.content || '',
        snapshot: payload.snapshot || ''
      })
    }).then(function (res) {
      return res.json();
    });
  }

  function renderPortalList(items) {
    state.portalLastList = items || [];
    if (!dom.portalList) return;

    if (!items || !items.length) {
      dom.portalList.innerHTML = '';
      return;
    }

    // Folders first, then files, alphabetically.
    items = items.slice().sort(function (a, b) {
      if (!!a.isDir !== !!b.isDir) {
        return a.isDir ? -1 : 1;
      }
      var an = (a.name || '').toLowerCase();
      var bn = (b.name || '').toLowerCase();
      if (an < bn) return -1;
      if (an > bn) return 1;
      return 0;
    });

    var html = '';
    for (var i = 0; i < items.length; i++) {
      var it = items[i];
      var icon = it.isDir ? '📁' : '📄';
      var size = !it.isDir && it.size != null ? ' · ' + it.size + ' B' : '';
      if (it.isDir) {
        html += '<div class="tcx-portal-item"><span>' + icon + ' ' +
          escapeHtml(it.name) + '</span><span>' + escapeHtml(size) + '</span></div>';
      } else {
        html += '<div class="tcx-portal-item"><span class="tcx-portal-file" data-name="' +
          escapeHtml(it.name) + '">' + icon + ' ' + escapeHtml(it.name) + '</span>' +
          '<span>' + escapeHtml(size) + '</span></div>';
      }
    }
    dom.portalList.innerHTML = html;

    var links = dom.portalList.querySelectorAll('.tcx-portal-file');
    Array.prototype.forEach.call(links, function (el) {
      el.addEventListener('click', function () {
        var name = el.getAttribute('data-name');
        if (!name) return;
        downloadIntoFile(name);
      });
    });
  }

  function connectPortal() {
    if (!state.settings.portalUrl || !state.settings.portalSecret) {
      setPortalStatus('Set PARADOX URL and shared secret first.');
      return;
    }
    setPortalStatus('🌈 Connecting…');

    portalRequest({ action: 'list', path: '' })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('Connect failed: ' + (data && data.error ? data.error : 'Unknown error'));
          renderPortalList([]);
          return;
        }
        var items = data.items || [];
        setPortalStatus('🌈 Connected. Root has ' + items.length + ' items. 🛡️');
        renderPortalList(items);
        glitchyPing('PARADOX link verified. Rainbow Warriors connected. 🌈');
      })
      .catch(function (err) {
        setPortalStatus('Connect failed: ' + (err && err.message ? err.message : 'Network error'));
        renderPortalList([]);
      });
  }

  function uploadCurrentFile() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }

    var files = getFiles();
    if (!state.activeFileId || !files.length) {
      window.alert('No active file to uplink. Click a file first.');
      return;
    }

    var file = null;
    for (var i = 0; i < files.length; i++) {
      if (files[i].id === state.activeFileId) {
        file = files[i];
        break;
      }
    }
    if (!file) return;

    if (!window.confirm(
      'Uplink "' + file.path + '" to PARADOX?\n\n' +
      'If a file with this path already exists on the server, it will be OVERWRITTEN.'
    )) {
      return;
    }

    setPortalStatus('🌈 Uploading ' + file.path + '…');
    portalRequest({ action: 'write', path: file.path, content: file.content || '' })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('Upload failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        setPortalStatus('🌈 Uploaded ' + file.path + ' to PARADOX. 🛡️');
        glitchyPing('Codex leaf shipped to PARADOX: ' + file.path + '. 🌈');
        // Refresh list
        portalRequest({ action: 'list', path: '' })
          .then(function (d2) { if (d2 && d2.ok) renderPortalList(d2.items || []); })
          .catch(function () {});
      })
      .catch(function (err) {
        setPortalStatus('Upload failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  function downloadCurrentFile() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }

    var files = getFiles();
    if (!state.activeFileId || !files.length) {
      window.alert('No active file to downlink into. Create or select a file first.');
      return;
    }

    var file = null;
    for (var i = 0; i < files.length; i++) {
      if (files[i].id === state.activeFileId) {
        file = files[i];
        break;
      }
    }
    if (!file) return;

    if (!window.confirm(
      'Downlink from PARADOX into "' + file.path + '"?\n\n' +
      'The local contents of this file will be OVERWRITTEN by the server copy.'
    )) {
      return;
    }

    setPortalStatus('🌈 Downloading ' + file.path + '…');
    portalRequest({ action: 'read', path: file.path })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('Download failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        var text = data.content || '';
        dom.editor.value = text;
        handleEditorInput();
        setPortalStatus('🌈 Downloaded ' + file.path + ' into current file. 🛡️');
        glitchyPing('Pulled canonical copy from PARADOX for ' + file.path + '. 🌈');
      })
      .catch(function (err) {
        setPortalStatus('Download failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  function downloadIntoFile(path) {
    if (!portalConfigured()) return;

    var files = getFiles();
    var target = null;
    for (var i = 0; i < files.length; i++) {
      if (files[i].path === path) {
        target = files[i];
        break;
      }
    }

    if (!target) {
      // Create a new local file mapped to that path.
      target = {
        id: 'file-' + Date.now(),
        path: path,
        content: '',
        createdAt: nowIso(),
        updatedAt: nowIso()
      };
      files.push(target);
    }

    state.activeFileId = target.id;
    saveState();
    renderFilesList();

    if (!window.confirm(
      'Pull "' + path + '" from PARADOX into local file?\n\n' +
      'Local contents will be OVERWRITTEN by the server copy.'
    )) {
      return;
    }

    setPortalStatus('🌈 Pulling ' + path + ' from PARADOX…');
    portalRequest({ action: 'read', path: path })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('Pull failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        target.content = data.content || '';
        target.updatedAt = nowIso();
        dom.editor.value = target.content;
        saveState();
        renderFilesList();
        if (dom.status) dom.status.textContent = '🌈 Editing: ' + target.path;
        setPortalStatus('🌈 Pulled ' + path + ' into local file list. 🛡️');
        glitchyPing('Imported leaf from PARADOX: ' + path + '. 🌈');
      })
      .catch(function (err) {
        setPortalStatus('Pull failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  // Device upload: UTF-8 text files -> portal
  function uploadFilesFromDevice(filesList) {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }
    if (!filesList || !filesList.length) return;

    var idx = 0;

    function next() {
      if (idx >= filesList.length) {
        setPortalStatus('🌈 Finished uploading pocket files. 🛡️');
        // Refresh listing
        portalRequest({ action:'list', path:'' })
          .then(function (d2) { if (d2 && d2.ok) renderPortalList(d2.items || []); })
          .catch(function () {});
        glitchyPing('Device files streamed into PARADOX. 🌈');
        return;
      }

      var f = filesList[idx++];
      var reader = new FileReader();
      reader.onload = function () {
        var content = String(reader.result || '');
        var path = sanitizePath(f.name);

        if (!window.confirm(
          'Upload local file "' + f.name + '" as "' + path + '" to PARADOX?\n\n' +
          'If this path already exists on the server it will be OVERWRITTEN.'
        )) {
          next();
          return;
        }

        setPortalStatus('🌈 Uploading ' + path + ' from device…');
        portalRequest({ action:'write', path:path, content:content })
          .then(function (data) {
            if (!data || !data.ok) {
              setPortalStatus('Upload failed for ' + path + ': ' +
                (data && data.error ? data.error : 'Unknown error'));
            } else {
              setPortalStatus('🌈 Uploaded ' + path + ' from device. 🛡️');
            }
            next();
          })
          .catch(function (err) {
            setPortalStatus('Upload failed for ' + path + ': ' +
              (err && err.message ? err.message : 'Network error'));
            next();
          });
      };
      reader.onerror = function () {
        setPortalStatus('Could not read local file ' + f.name);
        next();
      };
      reader.readAsText(f, 'utf-8');
    }

    next();
  }

  // New folder / delete / rename on portal
  function createPortalFolder() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }
    var name = window.prompt('New folder name (web-style, no spaces):', 'codex-nest');
    if (!name) return;

    var base = sanitizePath(name).replace(/\.txt$/i, '');
    setPortalStatus('🌈 Creating folder ' + base + '/…');
    portalRequest({ action:'mkdir', path:base })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('New folder failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        setPortalStatus('🌈 Created folder ' + base + '/. 🛡️');
        glitchyPing('New PARADOX folder opened: ' + base + '/ 🌈');
        portalRequest({ action:'list', path:'' })
          .then(function (d2) { if (d2 && d2.ok) renderPortalList(d2.items || []); })
          .catch(function () {});
      })
      .catch(function (err) {
        setPortalStatus('New folder failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  function deletePortalItem() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }
    var path = window.prompt('Purge which path (file or empty folder)?', '');
    if (!path) return;

    if (!window.confirm(
      'Purge "' + path + '" from PARADOX?\n\n' +
      'This cannot be undone from here.'
    )) {
      return;
    }

    setPortalStatus('🌈 Deleting ' + path + '…');
    portalRequest({ action:'delete', path:path })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('Delete failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        setPortalStatus('🌈 Deleted ' + path + '. 🛡️');
        glitchyPing('PARADOX path erased: ' + path + '. 🛡️');
        portalRequest({ action:'list', path:'' })
          .then(function (d2) { if (d2 && d2.ok) renderPortalList(d2.items || []); })
          .catch(function () {});
      })
      .catch(function (err) {
        setPortalStatus('Delete failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  function renamePortalItem() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }
    var from = window.prompt('Rename which path (file or folder)?', '');
    if (!from) return;
    var to = window.prompt('Rename "' + from + '" to (web-style):', from);
    if (!to) return;
    to = sanitizePath(to).replace(/\.txt$/i, function (m) {
      // Keep extension only if original had one
      return from.indexOf('.') >= 0 ? m : '';
    });

    setPortalStatus('🌈 Renaming ' + from + ' → ' + to + '…');
    portalRequest({ action:'rename', path:from, content:to })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('Rename failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        setPortalStatus('🌈 Renamed ' + from + ' to ' + to + '. 🛡️');
        glitchyPing('PARADOX path renamed: ' + from + ' → ' + to + '. 🌈');
        portalRequest({ action:'list', path:'' })
          .then(function (d2) { if (d2 && d2.ok) renderPortalList(d2.items || []); })
          .catch(function () {});
      })
      .catch(function (err) {
        setPortalStatus('Rename failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  // ---------------------------------------------------------------------------
  // TSnapshot helpers
  // ---------------------------------------------------------------------------

  function collectShellStorage() {
    var out = {};
    try {
      for (var i = 0; i < window.localStorage.length; i++) {
        var k = window.localStorage.key(i);
        if (k && k.indexOf(SHELL_PREFIX) === 0) {
          out[k] = window.localStorage.getItem(k);
        }
      }
    } catch (e) {}
    return out;
  }

  function wipeShellStorage() {
    try {
      var toRemove = [];
      for (var i = 0; i < window.localStorage.length; i++) {
        var k = window.localStorage.key(i);
        if (k && k.indexOf(SHELL_PREFIX) === 0) {
          toRemove.push(k);
        }
      }
      toRemove.forEach(function (k) {
        window.localStorage.removeItem(k);
      });
    } catch (e) {}
  }

  function buildSnapshotPayload(storage) {
    return {
      version: 'shell-os-v1',
      ts: new Date().toISOString(),
      origin: window.location.origin || '',
      path: window.location.pathname || '/',
      ua: window.navigator.userAgent || '',
      storage: storage
    };
  }

  function uploadSnapshotToPortal() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }
    var storage = collectShellStorage();
    var payload = buildSnapshotPayload(storage);
    var json = JSON.stringify(payload);

    setPortalStatus('🌈 Uploading TSnapshot deck to PARADOX…');
    portalRequest({ action:'snapshot_write', snapshot:json })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('TSnapshot upload failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        setPortalStatus('🌈 TSnapshot uploaded to PARADOX (private snapshot slot). 🛡️');
        glitchyPing('Entire Shell deck snapshotted to PARADOX. Rainbow Warriors preserve everything. 🌈');
      })
      .catch(function (err) {
        setPortalStatus('TSnapshot upload failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  function importSnapshotJson(jsonText) {
    var payload = safeParse(jsonText, null);
    if (!payload || !payload.storage || typeof payload.storage !== 'object') {
      window.alert('This data from PARADOX does not look like a TSnapshot cartridge.');
      return;
    }

    if (!window.confirm(
      'Import TSnapshot from PARADOX?\n\n' +
      'This will overwrite ALL shell.* data in this browser (including Codex, TCinema, etc).'
    )) {
      return;
    }

    wipeShellStorage();

    try {
      Object.keys(payload.storage).forEach(function (k) {
        window.localStorage.setItem(k, payload.storage[k]);
      });
    } catch (e) {
      window.alert('Import failed while writing to localStorage.');
      return;
    }

    glitchyPing('Remote Shell deck pulled into this browser. Reloading reality. 🌈');
    window.alert('TSnapshot imported from PARADOX. The page will reload to use the restored state.');
    window.location.reload();
  }

  function downloadSnapshotFromPortal() {
    if (!portalConfigured()) {
      window.alert('Configure PARADOX URL + shared secret first.');
      return;
    }

    setPortalStatus('🌈 Pulling TSnapshot deck from PARADOX…');
    portalRequest({ action:'snapshot_read' })
      .then(function (data) {
        if (!data || !data.ok) {
          setPortalStatus('TSnapshot download failed: ' + (data && data.error ? data.error : 'Unknown error'));
          return;
        }
        if (!data.snapshot) {
          setPortalStatus('No TSnapshot stored on PARADOX yet.');
          return;
        }
        importSnapshotJson(data.snapshot);
      })
      .catch(function (err) {
        setPortalStatus('TSnapshot download failed: ' + (err && err.message ? err.message : 'Network error'));
      });
  }

  // ---------------------------------------------------------------------------
  // Event wiring
  // ---------------------------------------------------------------------------

  function attachEvents() {
    // Files
    if (dom.fileAddBtn) {
      dom.fileAddBtn.addEventListener('click', function () {
        createFile();
        if (dom.fileMenu) dom.fileMenu.value = '';
      });
    }

    if (dom.fileMenu) {
      dom.fileMenu.addEventListener('change', function () {
        var val = dom.fileMenu.value;
        dom.fileMenu.value = '';
        if (val === 'rename') renameCurrentFile();
        else if (val === 'delete') deleteCurrentFile();
      });
    }

    if (dom.fileUpBtn) {
      dom.fileUpBtn.addEventListener('click', function () {
        if (dom.fileUpBtn.disabled) return;
        moveCurrentFile(-1);
      });
    }

    if (dom.fileDownBtn) {
      dom.fileDownBtn.addEventListener('click', function () {
        if (dom.fileDownBtn.disabled) return;
        moveCurrentFile(1);
      });
    }

    // Editor
    if (dom.editor) {
      dom.editor.addEventListener('input', handleEditorInput);
    }

    if (dom.saveLocalBtn) {
      dom.saveLocalBtn.addEventListener('click', function () {
        saveState();
        if (dom.status) dom.status.textContent += ' · Saved locally. 🌈';
        glitchyPing('Local state pinned in this browser. Children own their data. 🛡️');
      });
    }

    if (dom.uploadBtn) {
      dom.uploadBtn.addEventListener('click', function () {
        uploadCurrentFile();
      });
    }

    if (dom.downloadBtn) {
      dom.downloadBtn.addEventListener('click', function () {
        downloadCurrentFile();
      });
    }

    // Glitchy button
    if (dom.glitchyPingBtn) {
      dom.glitchyPingBtn.addEventListener('click', function () {
        glitchyPing('Manual ping from console citizen. 🌈');
      });
    }

    // Portal
    if (dom.portalSaveBtn) {
      dom.portalSaveBtn.addEventListener('click', function () {
        savePortalSettings();
      });
    }

    if (dom.portalConnectBtn) {
      dom.portalConnectBtn.addEventListener('click', function () {
        savePortalSettings();
        connectPortal();
      });
    }

    if (dom.snapUploadBtn) {
      dom.snapUploadBtn.addEventListener('click', function () {
        savePortalSettings();
        uploadSnapshotToPortal();
      });
    }

    if (dom.snapDownloadBtn) {
      dom.snapDownloadBtn.addEventListener('click', function () {
        savePortalSettings();
        downloadSnapshotFromPortal();
      });
    }

    if (dom.portalUploadDeviceBtn && dom.portalDeviceInput) {
      dom.portalUploadDeviceBtn.addEventListener('click', function () {
        dom.portalDeviceInput.click();
      });
      dom.portalDeviceInput.addEventListener('change', function () {
        if (dom.portalDeviceInput.files && dom.portalDeviceInput.files.length) {
          uploadFilesFromDevice(dom.portalDeviceInput.files);
          dom.portalDeviceInput.value = '';
        }
      });
    }

    if (dom.portalNewFolderBtn) {
      dom.portalNewFolderBtn.addEventListener('click', function () {
        savePortalSettings();
        createPortalFolder();
      });
    }

    if (dom.portalDeleteBtn) {
      dom.portalDeleteBtn.addEventListener('click', function () {
        savePortalSettings();
        deletePortalItem();
      });
    }

    if (dom.portalRenameBtn) {
      dom.portalRenameBtn.addEventListener('click', function () {
        savePortalSettings();
        renamePortalItem();
      });
    }
  }

  // ---------------------------------------------------------------------------
  // Boot
  // ---------------------------------------------------------------------------

  function init() {
    injectStyles();
    loadState();
    ensureFoundingFile();
    buildLayout();
    renderFilesList();
    renderPortalSettings();
    attachEvents();

    if (state.activeFileId && getFiles().length) {
      openFile(state.activeFileId);
    } else if (getFiles().length) {
      openFile(getFiles()[0].id);
    }

    glitchyPing('Boot sequence: Codex deck awake in this browser. Rainbow Warriors standing by. 🌈🛡️');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})(window, document);