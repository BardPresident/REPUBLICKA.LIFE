<?php
declare(strict_types=1);

// ============================================================================
// Codex — Local Codex Deck (Notes • Mini-IDE • PARADOX) for The Republic
//
// One banner:
//   • Notes: private, local-only notes (Snapshot-ready).
//   • Files: local project files (mini-IDE).
//   • Paradox: optional link to your own codex-paradox.php.
//
// UTF-8 only: Codex assumes the entire page and all content are UTF-8.
//
// ============================================================================

header('Content-Type: text/html; charset=utf-8');

$page_title       = 'Codex — Local Codex Deck | The Republic';
$page_canonical   = 'https://trepublic.net/codex.php';
$page_description = 'Codex: a local-first codex deck for notes and code, with PARADOX to your own server.';

// Shell will render this as the console body.
$console_body_html = <<<HTML
<div id="codex-root" class="codex-root">
  <div class="codex-loading">
    🕮 Codex is booting in your browser (UTF-8, local-first). 💻👑
  </div>
</div>
<script src="codex-engine.js"></script>
HTML;

require __DIR__ . '/shell.php';
