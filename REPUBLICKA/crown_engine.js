// snapshot.js must already be loaded by Shell for global behaviour.
// This file is only Crown"s own engine.

// ============================================================================
// Crown Engine — shared crown renderer for The Republic 👑
//
// • Renders decorative Crown layout
// • Inline edit per field (Edit / Save / Cancel)
// • Saves to localStorage key: shell.crown
// • Reset -> clears shell.crown and returns to page default
// • Export -> downloads crown.php with SAME structure as crown.php,
//             only JSON crown data changed.
//
// ============================================================================

(function (window, document) {
  "use strict";

  var STORAGE_KEY = "shell.crown";

  function loadCrown() {
    try {
      var raw = window.localStorage.getItem(STORAGE_KEY);
      if (!raw) return null;
      var parsed = JSON.parse(raw);
      if (!parsed || typeof parsed !== "object") return null;
      return parsed;
    } catch (e) {
      return null;
    }
  }

  function saveCrown(c) {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(c));
      toast("Crown saved to this browser (shell.crown).");
    } catch (e) {
      toast("Could not save crown (storage disabled?)");
    }
  }

  function clearCrown() {
    try {
      window.localStorage.removeItem(STORAGE_KEY);
    } catch (e) {}
  }

  function toast(msg) {
    var node = document.createElement("div");
    node.className = "crown-toast";
    node.textContent = msg;
    document.body.appendChild(node);
    setTimeout(function () {
      node.classList.add("hide");
      setTimeout(function () {
        if (node.parentNode) node.parentNode.removeChild(node);
      }, 400);
    }, 1400);
  }

  function clone(obj) {
    return JSON.parse(JSON.stringify(obj || {}));
  }

  // Build crown.php that matches your canonical crown.php,
  // but with JSON crown swapped.
  function buildExportPhp(crownObj) {
    var json = JSON.stringify(crownObj || {}, null, 2).replace(
      /<\/script/gi,
      "<\\/script"
    );

    var php =
      "<?php\n" +
      "// ============================================================================\n" +
      "// Crown — Elite Crown Profile for The Republic 👑\n" +
      "//\n" +
      "// This file is DATA + Shell wiring.\n" +
      "// All rendering, editing, saving & exporting lives in crown_engine.js.\n" +
      "//\n" +
      "// Crown engine & page crafted for The Republic by\n" +
      "//   Codey, Republic Systems Programmer 💻👑\n" +
      "// ============================================================================\n\n" +
      "$page_title       = 'Crown — Elite Crown Profile';\n" +
      "$page_canonical   = 'https://trepublic.net/crown.php';\n" +
      "$page_description = 'Crown is your personal elite crown profile inside The Republic.';\n\n" +
      "$page_og_title       = $page_title;\n" +
      "$page_og_description = $page_description;\n" +
      "$page_og_url         = $page_canonical;\n" +
      "$page_og_image       = 'https://trepublic.net/Crown.png';\n\n" +
      "$hero_title   = 'Crown — Elite Crown Profile';\n" +
      "$hero_tagline = \"Your crown is not a reward.\\nIt is the shape of the world you are willing to carry.\";\n\n" +
      "$console_title = 'Crown — Elite Crown Profile';\n\n" +
      "// Build $console_body_html via output buffering\n" +
      "ob_start();\n" +
      "?>\n" +
      "<div id=\"crown-root\"></div>\n" +
      "<script src=\"/crown_engine.js\"></script>\n" +
      "<script>\n" +
      "  window.CROWN_BOOT && window.CROWN_BOOT(" +
      json +
      ");\n" +
      "</script>\n" +
      "<?php\n" +
      "$console_body_html = ob_get_clean();\n\n" +
      "// Hand off to Shell layout\n" +
      "require __DIR__ . '/shell.php';\n" +
      "?>\n";

    return php;
  }

  function downloadPhp(phpSource) {
    try {
      var blob = new Blob([phpSource], {
        type: "application/x-httpd-php;charset=utf-8",
      });
      var a = document.createElement("a");
      a.href = URL.createObjectURL(blob);
      a.download = "crown.php";
      a.click();
      setTimeout(function () {
        URL.revokeObjectURL(a.href);
      }, 1500);
      toast("crown.php exported with your crown as default.");
    } catch (e) {
      toast("Export as PHP failed in this browser.");
    }
  }

  function escapeHtml(str) {
    return String(str).replace(/[&<>"]/g, function (ch) {
      switch (ch) {
        case "&":
          return "&amp;";
        case "<":
          return "&lt;";
        case ">":
          return "&gt;";
        case '"':
          return "&quot;";
      }
      return ch;
    });
  }

  function nl2br(str) {
    return escapeHtml(str).replace(/\n/g, "<br>");
  }

  // Main boot function, called from crown.php
  function CROWN_BOOT(initialData) {
    var root = document.getElementById("crown-root");
    if (!root) return;

    var defaultCrown = clone(initialData || {});
    var stored = loadCrown();
    var crown = stored || clone(defaultCrown);

    root.innerHTML =
      '<style>' +
      '.crown-page{width:100%;display:flex;justify-content:center;margin-top:4px;}' +
      '.crown-inner{width:100%;max-width:880px;text-align:center;}' +
      '.crown-label-main{font-weight:800;font-size:15px;letter-spacing:.04em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;}' +
      '.crown-header{margin-bottom:14px;}' +
      '.crown-avatar-wrap{display:flex;justify-content:center;margin:6px 0 8px;}' +
      '.crown-avatar{width:110px;height:110px;border-radius:50%;border:3px solid #ff5bd4;box-shadow:0 0 0 4px rgba(255,91,212,.35),0 14px 32px rgba(0,0,0,.6);background:#120618;object-fit:cover;}' +
      '.crown-card{border-radius:18px;border:2px solid rgba(126,58,242,.95);background:radial-gradient(circle at 0 0,rgba(255,91,212,.18),transparent 45%),radial-gradient(circle at 100% 0,rgba(126,58,242,.35),transparent 55%),linear-gradient(180deg,#0b0814,#130a1f);box-shadow:0 12px 28px rgba(0,0,0,.65);padding:10px 12px 10px;margin:10px 0 12px;text-align:center;}' +
      '.crown-card-label{font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--muted);margin-bottom:4px;}' +
      '.crown-name-main{font-weight:900;font-size:22px;margin-top:4px;}' +
      '.crown-line-meta{color:var(--muted);font-weight:600;font-size:13px;margin-top:2px;}' +
      '.crown-bio-chip{display:inline-flex;align-items:center;justify-content:center;padding:2px 8px;border-radius:999px;border:1px solid rgba(255,255,255,.45);font-size:11px;color:var(--muted);margin-bottom:4px;}' +
      '.crown-bio-body{font-size:14px;line-height:1.7;margin-top:4px;}' +
      '.crown-editarea{width:100%;max-width:780px;margin:3px auto 4px;}' +
      '.crown-editarea textarea{width:100%;border-radius:12px;border:1px solid rgba(255,255,255,.55);background:#050309;color:#f7f2ff;font-size:13px;line-height:1.5;padding:6px 8px;resize:vertical;box-sizing:border-box;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;}' +
      '.crown-controls{display:flex;justify-content:center;gap:6px;margin-top:4px;flex-wrap:wrap;}' +
      '.crown-btn{display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:4px 11px;border-radius:999px;border:1px solid rgba(255,255,255,.55);background:rgba(10,6,24,.96);color:#ffffff;font-size:11px;font-weight:600;cursor:pointer;}' +
      '.crown-btn.ghost{background:transparent;border-style:dashed;opacity:.9;}' +
      '.crown-btn.danger{border-color:#ff5b7c;color:#ffd7e1;}' +
      '.crown-btn:hover{background:rgba(32,16,72,.98);}' +
      '.crown-toast{position:fixed;left:50%;bottom:18px;transform:translateX(-50%);padding:6px 12px;border-radius:999px;background:rgba(12,6,30,.98);border:1px solid rgba(255,255,255,.6);color:#fff;font-size:11px;z-index:9999;opacity:1;transition:opacity .35s ease, transform .35s ease;}' +
      '.crown-toast.hide{opacity:0;transform:translateX(-50%) translateY(6px);}' +
      '.crown-export{margin-top:14px;padding-top:8px;border-top:1px dashed rgba(255,255,255,.25);}' +
      '.crown-export p{font-size:11px;color:var(--muted);margin:3px 0 5px;}' +
      '.crown-credit{margin-top:8px;font-size:11px;color:var(--muted);}' +
      '.crown-credit strong{color:#ffffff;}' +
      "</style>" +
      '<div class="crown-page">' +
      '  <div class="crown-inner">' +
      '    <header class="crown-header">' +
      '      <div class="crown-label-main">Crown — Elite Crown Profile</div>' +
      '      <div class="crown-avatar-wrap">' +
      '        <img src="/Crown.png" alt="Crown emblem" class="crown-avatar">' +
      "      </div>" +
      '      <div class="crown-card">' +
      '        <div class="crown-card-label">Crown Identity</div>' +
      '        <div class="crown-name-main" id="crown-display-name"></div>' +
      '        <div class="crown-editarea" id="crown-edit-wrap-name" hidden>' +
      '          <textarea id="crown-edit-name" rows="1"></textarea>' +
      "        </div>" +
      '        <div class="crown-controls" data-field="name">' +
      '          <button type="button" class="crown-btn crown-edit-btn">✏️ Edit name</button>' +
      '          <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '          <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "        </div>" +
      '        <div class="crown-line-meta" id="crown-display-pronouns"></div>' +
      '        <div class="crown-editarea" id="crown-edit-wrap-pronouns" hidden>' +
      '          <textarea id="crown-edit-pronouns" rows="1"></textarea>' +
      "        </div>" +
      '        <div class="crown-controls" data-field="pronouns">' +
      '          <button type="button" class="crown-btn crown-edit-btn">✏️ Edit pronouns / title</button>' +
      '          <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '          <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "        </div>" +
      '        <div class="crown-line-meta" id="crown-display-location"></div>' +
      '        <div class="crown-editarea" id="crown-edit-wrap-location" hidden>' +
      '          <textarea id="crown-edit-location" rows="1"></textarea>' +
      "        </div>" +
      '        <div class="crown-controls" data-field="location">' +
      '          <button type="button" class="crown-btn crown-edit-btn">✏️ Edit location</button>' +
      '          <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '          <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "        </div>" +
      '        <div class="crown-line-meta" id="crown-display-contact"></div>' +
      '        <div class="crown-editarea" id="crown-edit-wrap-contact" hidden>' +
      '          <textarea id="crown-edit-contact" rows="2"></textarea>' +
      "        </div>" +
      '        <div class="crown-controls" data-field="contact">' +
      '          <button type="button" class="crown-btn crown-edit-btn">✏️ Edit contact line</button>' +
      '          <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '          <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "        </div>" +
      "      </div>" +
      "    </header>" +
      '    <section class="crown-card">' +
      '      <div class="crown-bio-chip">✨ Crown Tagline ✨</div>' +
      '      <div class="crown-bio-body" id="crown-display-tagline"></div>' +
      '      <div class="crown-editarea" id="crown-edit-wrap-tagline" hidden>' +
      '        <textarea id="crown-edit-tagline" rows="2"></textarea>' +
      "      </div>" +
      '      <div class="crown-controls" data-field="tagline">' +
      '        <button type="button" class="crown-btn crown-edit-btn">✏️ Edit tagline</button>' +
      '        <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '        <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "      </div>" +
      "    </section>" +
      '    <section class="crown-card">' +
      '      <div class="crown-bio-chip">💜 Short Bio 💜</div>' +
      '      <div class="crown-bio-body" id="crown-display-bio-short"></div>' +
      '      <div class="crown-editarea" id="crown-edit-wrap-bio-short" hidden>' +
      '        <textarea id="crown-edit-bio-short" rows="4"></textarea>' +
      "      </div>" +
      '      <div class="crown-controls" data-field="bio_short">' +
      '        <button type="button" class="crown-btn crown-edit-btn">✏️ Edit short bio</button>' +
      '        <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '        <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "      </div>" +
      "    </section>" +
      '    <section class="crown-card">' +
      '      <div class="crown-bio-chip">📜 Medium Bio 📜</div>' +
      '      <div class="crown-bio-body" id="crown-display-bio-medium"></div>' +
      '      <div class="crown-editarea" id="crown-edit-wrap-bio-medium" hidden>' +
      '        <textarea id="crown-edit-bio-medium" rows="5"></textarea>' +
      "      </div>" +
      '      <div class="crown-controls" data-field="bio_medium">' +
      '        <button type="button" class="crown-btn crown-edit-btn">✏️ Edit medium bio</button>' +
      '        <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '        <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "      </div>" +
      "    </section>" +
      '    <section class="crown-card">' +
      '      <div class="crown-bio-chip">🕯️ Long Bio 🕯️</div>' +
      '      <div class="crown-bio-body" id="crown-display-bio-long"></div>' +
      '      <div class="crown-editarea" id="crown-edit-wrap-bio-long" hidden>' +
      '        <textarea id="crown-edit-bio-long" rows="8"></textarea>' +
      "      </div>" +
      '      <div class="crown-controls" data-field="bio_long">' +
      '        <button type="button" class="crown-btn crown-edit-btn">✏️ Edit long bio</button>' +
      '        <button type="button" class="crown-btn crown-save-btn" hidden>💾 Save</button>' +
      '        <button type="button" class="crown-btn ghost crown-cancel-btn" hidden>✖ Cancel</button>' +
      "      </div>" +
      "    </section>" +
      '    <section class="crown-export">' +
      '      <button type="button" class="crown-btn" id="crown-export-php">' +
      "        📦 Export this crown as crown.php" +
      "      </button>" +
      "      <p>" +
      "        This button downloads a crown.php file using your current crown text. " +
      "        You can upload it to any Source starship (with shell.php and crown_engine.js)." +
      "      </p>" +
      '      <button type="button" class="crown-btn ghost danger" id="crown-reset">' +
      "        🗑️ Reset crown in this browser (back to page default)" +
      "      </button>" +
      "      <p>" +
      "        Reset only affects this browser’s shell.crown entry. " +
      "        Your Snapshot cartridges stay untouched." +
      "      </p>" +
      "    </section>" +
      '    <div class="crown-credit">' +
      "    </div>" +
      "  </div>" +
      "</div>";

    var fieldMap = {
      name: {
        displayId: "crown-display-name",
        editWrapId: "crown-edit-wrap-name",
        editId: "crown-edit-name",
        multiline: false,
      },
      pronouns: {
        displayId: "crown-display-pronouns",
        editWrapId: "crown-edit-wrap-pronouns",
        editId: "crown-edit-pronouns",
        multiline: false,
      },
      location: {
        displayId: "crown-display-location",
        editWrapId: "crown-edit-wrap-location",
        editId: "crown-edit-location",
        multiline: false,
      },
      contact: {
        displayId: "crown-display-contact",
        editWrapId: "crown-edit-wrap-contact",
        editId: "crown-edit-contact",
        multiline: true,
      },
      tagline: {
        displayId: "crown-display-tagline",
        editWrapId: "crown-edit-wrap-tagline",
        editId: "crown-edit-tagline",
        multiline: true,
      },
      bio_short: {
        displayId: "crown-display-bio-short",
        editWrapId: "crown-edit-wrap-bio-short",
        editId: "crown-edit-bio-short",
        multiline: true,
      },
      bio_medium: {
        displayId: "crown-display-bio-medium",
        editWrapId: "crown-edit-wrap-bio-medium",
        editId: "crown-edit-bio-medium",
        multiline: true,
      },
      bio_long: {
        displayId: "crown-display-bio-long",
        editWrapId: "crown-edit-wrap-bio-long",
        editId: "crown-edit-bio-long",
        multiline: true,
      },
    };

    function renderField(key) {
      var cfg = fieldMap[key];
      if (!cfg) return;
      var value = crown[key] != null ? String(crown[key]) : "";
      var display = document.getElementById(cfg.displayId);
      var editWrap = document.getElementById(cfg.editWrapId);
      var edit = document.getElementById(cfg.editId);
      if (!display || !edit) return;

      if (cfg.multiline) {
        display.innerHTML = nl2br(value);
      } else {
        display.textContent = value;
      }
      edit.value = value;
      if (editWrap) {
        editWrap.setAttribute("hidden", "hidden");
      }
    }

    function findControlsForField(field) {
      var nodes = root.querySelectorAll(".crown-controls[data-field]");
      for (var i = 0; i < nodes.length; i++) {
        var n = nodes[i];
        if (n.getAttribute("data-field") === field) return n;
      }
      return null;
    }

    function setEditingState(field, editing) {
      var cfg = fieldMap[field];
      if (!cfg) return;
      var editWrap = document.getElementById(cfg.editWrapId);
      var display = document.getElementById(cfg.displayId);
      var controls = findControlsForField(field);
      if (!display || !editWrap || !controls) return;

      var editBtn = controls.querySelector(".crown-edit-btn");
      var saveBtn = controls.querySelector(".crown-save-btn");
      var cancelBtn = controls.querySelector(".crown-cancel-btn");

      if (editing) {
        display.style.display = "none";
        editWrap.removeAttribute("hidden");
        if (editBtn) editBtn.hidden = true;
        if (saveBtn) saveBtn.hidden = false;
        if (cancelBtn) cancelBtn.hidden = false;
      } else {
        display.style.display = "";
        editWrap.setAttribute("hidden", "hidden");
        if (editBtn) editBtn.hidden = false;
        if (saveBtn) saveBtn.hidden = true;
        if (cancelBtn) cancelBtn.hidden = true;
      }
    }

    function hookFieldControls(field) {
      var cfg = fieldMap[field];
      if (!cfg) return;
      var controls = findControlsForField(field);
      if (!controls) return;
      var editBtn = controls.querySelector(".crown-edit-btn");
      var saveBtn = controls.querySelector(".crown-save-btn");
      var cancelBtn = controls.querySelector(".crown-cancel-btn");
      var edit = document.getElementById(cfg.editId);

      if (editBtn) {
        editBtn.addEventListener("click", function () {
          setEditingState(field, true);
          if (edit) edit.focus();
        });
      }

      if (cancelBtn) {
        cancelBtn.addEventListener("click", function () {
          if (edit) {
            edit.value = crown[field] != null ? String(crown[field]) : "";
          }
          setEditingState(field, false);
        });
      }

      if (saveBtn) {
        saveBtn.addEventListener("click", function () {
          if (!edit) return;
          var val = String(edit.value || "");
          crown[field] = val;
          saveCrown(crown);
          renderField(field);
          setEditingState(field, false);
        });
      }
    }

    function renderAll() {
      for (var key in fieldMap) {
        if (Object.prototype.hasOwnProperty.call(fieldMap, key)) {
          renderField(key);
          hookFieldControls(key);
        }
      }
    }

    function doReset() {
      if (
        !window.confirm(
          "Reset this browser's crown back to this page's default?"
        )
      )
        return;
      clearCrown();
      crown = clone(defaulCrown);
      renderAll();
      toast("Crown reset to page default.");
    }

    function doExport() {
      var php = buildExportPhp(crown);
      downloadPhp(php);
    }

    renderAll();

    var resetBtn = document.getElementById("crown-reset");
    if (resetBtn) resetBtn.addEventListener("click", doReset);

    var exportBtn = document.getElementById("crown-export-php");
    if (exportBtn) exportBtn.addEventListener("click", doExport);
  }

  window.CROWN_BOOT = CROWN_BOOT;
})(window, document);
})(window, document);
