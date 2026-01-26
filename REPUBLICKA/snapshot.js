// snapshot.js
// Snapshot — TShell state cartridges
// Author: Codey, Republic Systems Programmer 💻👑

(function (window, document) {
  'use strict';

  var PREFIX = 'shell.';              // all Shell keys live under this
  var SNAP_VERSION = 'shell-os-v1';   // snapshot format version

  // --- helpers -------------------------------------------------------------

  function escapeHtml(str) {
    return String(str).replace(/[&<>\"']/g, function (ch) {
      switch (ch) {
        case '&': return '&amp;';
        case '<': return '&lt;';
        case '>': return '&gt;';
        case '\"': return '&quot;';
        case '\'': return '&#39;';
      }
      return ch;
    });
  }

  function collectStorage() {
    var out = {};
    try {
      for (var i = 0; i < window.localStorage.length; i++) {
        var k = window.localStorage.key(i);
        if (k && k.indexOf(PREFIX) === 0) {
          out[k] = window.localStorage.getItem(k);
        }
      }
    } catch (e) {
      // storage might be disabled
    }
    return out;
  }

  function wipeStorage() {
    try {
      var toRemove = [];
      for (var i = 0; i < window.localStorage.length; i++) {
        var k = window.localStorage.key(i);
        if (k && k.indexOf(PREFIX) === 0) {
          toRemove.push(k);
        }
      }
      toRemove.forEach(function (k) {
        window.localStorage.removeItem(k);
      });
    } catch (e) {
      // ignore
    }
  }

  function buildSnapshotHtml(storage) {
    var now = new Date();
    var payload = {
      version: SNAP_VERSION,
      ts: now.toISOString(),
      origin: window.location.origin || '',
      path: window.location.pathname || '/',
      ua: window.navigator.userAgent || '',
      storage: storage
    };

    var json = JSON.stringify(payload, null, 2);
    var keys = Object.keys(storage).sort();

    // Per-key collapsible entries (pretty-print JSON where possible)
    var entriesHtml;
    if (keys.length) {
      entriesHtml = keys.map(function (k) {
        var raw = storage[k] == null ? '' : String(storage[k]);

        // short one-line preview for the summary
        var preview = raw.replace(/\s+/g, ' ').slice(0, 140);
        if (raw.length > 140) preview += '…';

        // pretty value: try JSON.parse, fall back to raw string
        var pretty = raw;
        try {
          var parsed = JSON.parse(raw);
          pretty = JSON.stringify(parsed, null, 2);
        } catch (e) {
          // not JSON, leave as-is
        }

        return (
          '<details class="entry">' +
            '<summary>' +
              '<code>' + escapeHtml(k) + '</code>' +
              (preview
                ? '<span class="entry-preview"> — ' + escapeHtml(preview) + '</span>'
                : '') +
            '</summary>' +
            '<pre>' + escapeHtml(pretty) + '</pre>' +
          '</details>'
        );
      }).join('\n');
    } else {
      entriesHtml =
        '<p><em>No <code>shell.*</code> entries yet. Fresh shell.</em></p>';
    }

    var html =
      '<!doctype html>\n' +
      '<html lang="en">\n' +
      '<head>\n' +
      '  <meta charset="utf-8">\n' +
      '  <meta name="viewport" content="width=device-width,initial-scale=1">\n' +
      '  <title>Snapshot • Shell Cartridge</title>\n' +
      '  <style>\n' +
      '    :root{--bg:#0b0811;--ink:#eae2ff;--muted:#b7a9d6;--purple:#7e3af2;--purple-2:#5b2bbf;--pink:#ff5bd4;}\n' +
      '    *{box-sizing:border-box;margin:0;padding:0;}\n' +
      '    body{min-height:100vh;background:radial-gradient(circle at 0 0,#ff5bd4 0,#7e3af2 40%,#0b0811 80%);\n' +
      '         color:var(--ink);font:500 14px/1.6 system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;}\n' +
      '    .rail-bg{position:fixed;top:0;bottom:0;width:32px;background:linear-gradient(180deg,#8d57ff 0%,#7e3af2 45%,#5b2bbf 100%);\n' +
      '             box-shadow:0 0 18px rgba(0,0,0,.5);z-index:1;}\n' +
      '    .rail-bg.left{left:0;box-shadow:inset -6px 0 18px rgba(0,0,0,.6);}\n' +
      '    .rail-bg.right{right:0;box-shadow:inset 6px 0 18px rgba(0,0,0,.6);}\n' +
      '    .emoji-rail{position:fixed;top:0;bottom:0;width:32px;display:flex;flex-direction:column;align-items:center;\n' +
      '                justify-content:space-evenly;gap:6px;z-index:2;pointer-events:none;}\n' +
      '    .emoji-rail span{font-size:22px;filter:drop-shadow(0 1px 2px rgba(0,0,0,.6));}\n' +
      '    .emoji-rail.left{left:0;}\n' +
      '    .emoji-rail.right{right:0;}\n' +
      '    .page-wrap{position:relative;z-index:3;width:min(980px,100vw - 100px);margin:24px auto;}\n' +
      '    main{border-radius:16px;border:2px solid rgba(126,58,242,.65);\n' +
      '         background:rgba(11,8,17,.9);box-shadow:0 16px 40px rgba(0,0,0,.6);padding:16px 18px;}\n' +
      '    h1{font-size:22px;margin-bottom:4px;}\n' +
      '    h2{font-size:15px;margin:10px 0 4px;}\n' +
      '    p{margin:4px 0 6px;color:var(--muted);}\n' +
      '    .brand{font-size:12px;margin-bottom:6px;color:var(--muted);}\n' +
      '    .brand strong{color:#fff;}\n' +
      '    details{margin:6px 0;border-radius:10px;border:1px solid rgba(126,58,242,.55);\n' +
      '            background:#120c1a;padding:6px 9px;}\n' +
      '    summary{cursor:pointer;font-weight:700;color:#fff;}\n' +
      '    .entry-list{margin-top:4px;}\n' +
      '    .entry-list details{margin:6px 0;border-radius:8px;border:1px solid rgba(126,58,242,.45);\n' +
      '                       background:#050309;padding:5px 7px;}\n' +
      '    .entry-list summary{cursor:pointer;font-weight:600;color:#fff;}\n' +
      '    .entry-preview{font-weight:400;color:var(--muted);font-size:11px;}\n' +
      '    .entry-list pre{margin-top:5px;}\n' +
      '    ul{margin:6px 0 0 16px;padding:0;}\n' +
      '    li{margin:3px 0;}\n' +
      '    code{font-family:ui-monospace,Consolas,Menlo,monospace;font-size:12px;}\n' +
      '    small{font-size:11px;color:var(--muted);}\n' +
      '    pre{margin-top:6px;padding:8px;border-radius:8px;background:#050309;\n' +
      '        border:1px solid rgba(126,58,242,.6);color:#d1c8ff;font-size:12px;\n' +
      '        overflow-x:auto;white-space:pre-wrap;word-break:break-word;}\n' +
      '    .chip{display:inline-flex;align-items:center;gap:4px;font-size:11px;\n' +
      '          padding:3px 7px;border-radius:999px;border:1px solid rgba(255,255,255,.25);\n' +
      '          background:rgba(20,12,40,.9);margin-right:6px;}\n' +
      '    @media (max-width:720px){\n' +
      '      .rail-bg,.emoji-rail{width:26px;}\n' +
      '      .page-wrap{width:calc(100vw - 70px);margin:12px auto;}\n' +
      '      main{padding:12px;}\n' +
      '    }\n' +
      '  </style>\n' +
      '</head>\n' +
      '<body>\n' +
      '<div class="rail-bg left"></div>\n' +
      '<div class="rail-bg right"></div>\n' +
      '<div class="emoji-rail left" id="snap-rail-left"></div>\n' +
      '<div class="emoji-rail right" id="snap-rail-right"></div>\n' +
      '<div class="page-wrap">\n' +
      '<main>\n' +
      '  <h1>Snapshot • Shell Cartridge</h1>\n' +
      '  <div class="brand">Generated by <strong>Codey</strong> (Republic Systems Programmer) for\n' +
      '    <strong>The Republic</strong> 💻👑🏳️‍⚧️</div>\n' +
      '  <details open>\n' +
      '    <summary>Overview</summary>\n' +
      '    <p>This file is a portable Shell state cartridge. Keep it safe; you can import it back into any TShell-aware TStarship.</p>\n' +
      '    <p>\n' +
      '      <span class="chip">Version: ' + escapeHtml(SNAP_VERSION) + '</span>\n' +
      '      <span class="chip">Saved: ' + escapeHtml(now.toISOString()) + '</span>\n' +
      '    </p>\n' +
      '    <p><span class="chip">Origin: ' + escapeHtml(payload.origin + payload.path) + '</span></p>\n' +
      '  </details>\n' +
      '  <details open>\n' +
      '    <summary>Shell storage (<code>tshell.*</code> keys)</summary>\n' +
      '    <p>Everything Codey is preserving from this browser for The Republic.</p>\n' +
      '    <div class="entry-list">\n' +
              entriesHtml +
      '    </div>\n' +
      '  </details>\n' +
      '  <details>\n' +
      '    <summary>Raw payload (for Codey to import)</summary>\n' +
      '    <p>Do not edit this block. TShell + Codey will read it when you import this cartridge.</p>\n' +
      '    <pre id="tshell-json" data-tshell-version="' + escapeHtml(SNAP_VERSION) + '">' +
              escapeHtml(json) +
      '    </pre>\n' +
      '  </details>\n' +
      '</main>\n' +
      '</div>\n' +
      '<script>\n' +
      '(function(){\n' +
      '  // System emoji rail for Snapshot: Codey\'s core trio 👑💜🏳️‍⚧️\n' +
      '  var seq = ["👑","💜","🏳️‍⚧️"];\n' +
      '  function fillRail(id){\n' +
      '    var node = document.getElementById(id);\n' +
      '    if(!node) return;\n' +
      '    node.innerHTML = "";\n' +
      '    var h = window.innerHeight || document.documentElement.clientHeight || 600;\n' +
      '    var step = h / 14;\n' +
      '    if(step < 28) step = 28;\n' +
      '    if(step > 52) step = 52;\n' +
      '    var n = Math.ceil(h / step) + 2;\n' +
      '    for(var i = 0; i < n; i++){\n' +
      '      var s = document.createElement("span");\n' +
      '      s.textContent = seq[i % seq.length];\n' +
      '      node.appendChild(s);\n' +
      '    }\n' +
      '  }\n' +
      '  function refresh(){ fillRail("snap-rail-left"); fillRail("snap-rail-right"); }\n' +
      '  refresh();\n' +
      '  window.addEventListener("resize", refresh);\n' +
      '})();\n' +
      '</script>\n' +
      '</body>\n' +
      '</html>\n';

    return html;
  }

  function doExport() {
    var storage = collectStorage();
    var html = buildSnapshotHtml(storage);

    var stamp = new Date().toISOString().replace(/[:.]/g, '-');
    var name = 'snapshot-' + stamp + '.html';

    try {
      var blob = new Blob([html], { type: 'text/html;charset=utf-8' });
      var a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = name;
      a.click();
      setTimeout(function () {
        URL.revokeObjectURL(a.href);
      }, 1500);
    } catch (e) {
      window.alert('Snapshot export failed. (Maybe this browser blocks file downloads?)');
    }
  }

  function importFromText(txt) {
    var payload = null;

    // 1) Try plain JSON
    try {
      var maybe = JSON.parse(txt);
      if (maybe && typeof maybe === 'object' && maybe.storage) {
        payload = maybe;
      }
    } catch (e) {}

    // 2) Try HTML snapshot
    if (!payload) {
      try {
        var parser = new DOMParser();
        var doc = parser.parseFromString(txt, 'text/html');
        var pre = doc && doc.getElementById('tshell-json');
        if (pre) {
          var inner = pre.textContent || pre.innerText || '';
          var maybe2 = JSON.parse(inner);
          if (maybe2 && typeof maybe2 === 'object' && maybe2.storage) {
            payload = maybe2;
          }
        }
      } catch (e2) {}
    }

    if (!payload || !payload.storage || typeof payload.storage !== 'object') {
      window.alert('This file does not look like a Snapshot cartridge.');
      return;
    }

    if (!window.confirm(
      'Import TShell cartridge from ' +
      (payload.origin || 'unknown origin') +
      '?\n\nThis will overwrite existing tshell.* data in this browser.'
    )) {
      return;
    }

    wipeStorage();

    try {
      Object.keys(payload.storage).forEach(function (k) {
        window.localStorage.setItem(k, payload.storage[k]);
      });
    } catch (e3) {
      window.alert('Import failed while writing to localStorage.');
      return;
    }

    window.alert('Snapshot imported. Reload this page to use the restored TShell state.');
  }

  function doReset() {
    if (!window.confirm(
      'Reset TShell data in this browser? This clears all tshell.* keys, but does not touch posts/pages.'
    )) {
      return;
    }
    wipeStorage();
    window.alert('TShell storage cleared for this browser. The page will now reload.');
    window.location.reload();
  }

  function hookUI() {
    var btnExport = document.getElementById('snap-export');
    var btnImport = document.getElementById('snap-import');
    var btnReset  = document.getElementById('snap-reset');
    var fileInput = document.getElementById('snap-file');

    if (btnExport) {
      btnExport.addEventListener('click', function () {
        doExport();
      });
    }

    if (btnImport && fileInput) {
      btnImport.addEventListener('click', function () {
        fileInput.value = '';
        fileInput.click();
      });

      fileInput.addEventListener('change', function () {
        var f = fileInput.files && fileInput.files[0];
        if (!f) return;
        var reader = new FileReader();
        reader.onload = function () {
          importFromText(String(reader.result || ''));
        };
        reader.readAsText(f);
      });
    }

    if (btnReset) {
      btnReset.addEventListener('click', function () {
        doReset();
      });
    }

    // Expose for console debugging / future IDE hooks
    window.Snapshot = {
      export: doExport,
      reset: doReset,
      _collect: collectStorage
    };
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', hookUI);
  } else {
    hookUI();
  }

})(window, document);
