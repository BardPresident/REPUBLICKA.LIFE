<?php
// PARADOX — flat-file JSON clerk for TRepublicOS 🏳️‍⚧️📂
//
// Speaks JSON over HTTPS, no SQL, no sessions.
// Actions (all POST JSON):
//   list            → list files + folders at a path
//   read            → read UTF-8 text file
//   write           → write UTF-8 text file
//   mkdir           → create folder
//   delete          → delete file or empty folder
//   rename          → rename/move path
//   snapshot_write  → store TSnapshot JSON
//   snapshot_read   → fetch TSnapshot JSON
//
// Payload:
//   {
//     "secret":  "SHARED_SECRET",
//     "action":  "write",
//     "path":    "posts/hello.txt",
//     "content": "UTF-8 text here",
//     "snapshot": "{...}"
//   }
//
// Auth is just a strong shared secret. Use HTTPS.

const PARADOX_SECRET = 'CHANGE_ME_TO_A_LONG_RANDOM_STRING';

// All paths are relative to this directory:
const PARADOX_ROOT    = __DIR__;          // public_html or a subfolder
const SNAPSHOT_FILE   = PARADOX_ROOT . '/tshell-snapshot.json';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');

function px_fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function px_ok(array $data = []) {
    $data['ok'] = true;
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Make sure we never escape out of PARADOX_ROOT
function px_safe_path($relPath) {
    $relPath = (string)$relPath;
    $relPath = str_replace(["\\", "\0"], ['/', ''], $relPath);
    $relPath = trim($relPath, "/");

    if ($relPath === '') {
        return PARADOX_ROOT;
    }

    $full = PARADOX_ROOT . '/' . $relPath;
    $realRoot = realpath(PARADOX_ROOT);
    $realFull = realpath($full) ?: $full;

    if (strpos($realFull, $realRoot) !== 0) {
        px_fail('Path escapes root.');
    }
    return $realFull;
}

// Small FS helper to list immediate children in a directory.
function px_list_dir($path) {
    $path = rtrim($path, '/');
    if (!is_dir($path)) {
        px_fail('Not a directory.');
    }

    $items = [];
    foreach (scandir($path) as $name) {
        if ($name === '.' || $name === '..') continue;
        $full = $path . '/' . $name;
        $items[] = [
            'name'  => $name,
            'isDir' => is_dir($full),
            'size'  => is_file($full) ? filesize($full) : null,
        ];
    }

    return $items;
}

// ---------------------------------------------------------------------------
// Read & validate request
// ---------------------------------------------------------------------------

$raw = file_get_contents('php://input');
$req = json_decode($raw, true);

if (!is_array($req)) {
    px_fail('Invalid JSON payload.');
}

$secret = $req['secret'] ?? '';
if (!hash_equals(PARADOX_SECRET, $secret)) {
    px_fail('Forbidden: bad secret.', 403);
}

$action = $req['action'] ?? '';
if (!$action) {
    px_fail('Missing action.');
}

// ---------------------------------------------------------------------------
// Dispatch
// ---------------------------------------------------------------------------

switch ($action) {

    case 'list': {
        $rel  = $req['path'] ?? '';
        $dir  = px_safe_path($rel);
        if (!is_dir($dir)) {
            px_fail('Directory does not exist.');
        }
        $items = px_list_dir($dir);
        px_ok(['items' => $items]);
    }

    case 'read': {
        $rel = $req['path'] ?? '';
        if ($rel === '') px_fail('Missing path.');
        $full = px_safe_path($rel);
        if (!is_file($full)) {
            px_fail('File not found.');
        }
        $content = file_get_contents($full);
        // content is treated as UTF-8 text
        px_ok(['content' => $content]);
    }

    case 'write': {
        $rel = $req['path'] ?? '';
        if ($rel === '') px_fail('Missing path.');
        $content = $req['content'] ?? '';
        if (!is_string($content)) {
            px_fail('Content must be a string (UTF-8).');
        }

        $full = px_safe_path($rel);
        $dir  = dirname($full);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
                px_fail('Could not create directory.');
            }
        }

        $ok = file_put_contents($full, $content, LOCK_EX);
        if ($ok === false) {
            px_fail('Failed to write file.');
        }
        px_ok(['bytes' => $ok]);
    }

    case 'mkdir': {
        $rel = $req['path'] ?? '';
        if ($rel === '') px_fail('Missing path.');
        $full = px_safe_path($rel);
        if (is_dir($full)) {
            px_ok(['message' => 'Folder already exists.']);
        }
        if (!mkdir($full, 0775, true)) {
            px_fail('Failed to create folder.');
        }
        px_ok(['message' => 'Folder created.']);
    }

    case 'delete': {
        $rel = $req['path'] ?? '';
        if ($rel === '') px_fail('Missing path.');
        $full = px_safe_path($rel);

        if (is_dir($full)) {
            // Only allow delete of empty folders for safety
            if (count(array_diff(scandir($full), ['.','..'])) > 0) {
                px_fail('Folder not empty.');
            }
            if (!rmdir($full)) {
                px_fail('Failed to remove folder.');
            }
            px_ok(['message' => 'Folder removed.']);
        }

        if (is_file($full)) {
            if (!unlink($full)) {
                px_fail('Failed to delete file.');
            }
            px_ok(['message' => 'File deleted.']);
        }

        px_fail('Path not found.');
    }

    case 'rename': {
        $from = $req['path'] ?? '';
        $to   = $req['content'] ?? ''; // TCodex sends new name in "content"
        if ($from === '' || $to === '') {
            px_fail('Missing from/to.');
        }
        $fullFrom = px_safe_path($from);
        $fullTo   = px_safe_path($to);

        $dirTo = dirname($fullTo);
        if (!is_dir($dirTo)) {
            if (!mkdir($dirTo, 0775, true) && !is_dir($dirTo)) {
                px_fail('Could not create target directory.');
            }
        }

        if (!rename($fullFrom, $fullTo)) {
            px_fail('Rename failed.');
        }
        px_ok(['from' => $from, 'to' => $to]);
    }

    case 'snapshot_write': {
        // Snapshot is already JSON text; treat as UTF-8
        $snapshot = $req['snapshot'] ?? '';
        if (!is_string($snapshot) || $snapshot === '') {
            px_fail('Missing snapshot.');
        }
        $ok = file_put_contents(SNAPSHOT_FILE, $snapshot, LOCK_EX);
        if ($ok === false) {
            px_fail('Failed to write snapshot.');
        }
        px_ok(['bytes' => $ok]);
    }

    case 'snapshot_read': {
        if (!is_file(SNAPSHOT_FILE)) {
            px_ok(['snapshot' => null]);
        }
        $snapshot = file_get_contents(SNAPSHOT_FILE);
        px_ok(['snapshot' => $snapshot]);
    }

    default:
        px_fail('Unknown action: ' . $action);
}
