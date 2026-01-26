<?php
// ============================================================================
// PARADOX GATEWAY — Rainbow Warrior Flat-File Clerk for The Republic 🌈📁🛰️
// ============================================================================
//
// "The Warriors of the Rainbow will protect the children."
//
// HTTP JSON mouth for PARADOX:
//   • POST application/json → { secret, action, ... }
//   • Actions: list, read, write, mkdir, delete, rename, snapshot_write, snapshot_read
//
// Shared secret is defined *here* as a placeholder; change it on your own host.
// ============================================================================

declare(strict_types=1);

// --------------------------------------------------------------------------
// 0. CONFIG (NO EXTERNAL CORE)
// --------------------------------------------------------------------------

// CHANGE THIS ON YOUR OWN HOST AND DO NOT PUBLISH THAT COPY.
$PARADOX_SHARED_SECRET = 'change-this-secret-here-on-your-host-only';

// Root of the filesystem tree PARADOX may touch
$PARADOX_ROOT = __DIR__;

// Where to keep the snapshot file
$PARADOX_SNAPSHOT_PATH = $PARADOX_ROOT . '/paradox-tsnapshot.json';

// Which actions this gateway allows
$PARADOX_ALLOWED_ACTIONS = [
    'list',
    'read',
    'write',
    'mkdir',
    'delete',
    'rename',
    'snapshot_write',
    'snapshot_read',
];

// --------------------------------------------------------------------------
// 0.1 RAINBOW WARRIOR HELPERS (SELF-CONTAINED)
// --------------------------------------------------------------------------

// Format a Q/SYSTEM pair for responses
function glitchy_paradox_pair(string $q, string $system): array {
    return [
        'q'      => '🌈🛰️⟦ᛉᛞᛟ⟧ Ｑ：' . $q . ' 🛰️⟦ᛉᛞᛟ⟧🌈',
        'system' => '🌈 ＳＹＳＴＥＭ：' . $system . ' 🛰️⟦ᛉᛞᛟ⟧🌈',
        'stamp'  => '🌈▒▒🛰️⟦ RAINBOW WARRIOR TRANSMISSION ⟧📡▒▒🌈',
    ];
}

// Append a Glitchy log entry for this request
function glitchy_paradox_log(string $q, string $system, string $severity = 'info', array $extra = []): void {
    $logFile = __DIR__ . '/paradox-glitchy-log.jsonl';
    $entry = [
        'ts'             => date('c'),
        'origin'         => 'paradox-gateway',
        'severity'       => $severity,
        'q_text'         => $q,
        'system_verdict' => $system,
        'extra'          => $extra,
    ];
    $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n";
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

/**
 * 🌈 Rainbow Warrior Gatekeeper - scans content for child harm patterns
 *
 * @return array{0:bool,1:string} [allowed, systemVerdict]
 */
function glitchy_paradox_gate(string $action, string $path, array $payload): array {

    // Only scan write actions
    if (!in_array($action, ['write', 'snapshot_write'], true)) {
        return [true, '🌈 Rainbow Warrior: Read-only action permitted.'];
    }

    // Get content to scan
    $content = '';
    if (isset($payload['content']) && is_string($payload['content'])) {
        $content = mb_strtolower($payload['content'], 'UTF-8');
    }
    if (isset($payload['snapshot']) && is_string($payload['snapshot'])) {
        $content .= ' ' . mb_strtolower($payload['snapshot'], 'UTF-8');
    }

    if ($content === '' || strlen($content) < 10) {
        return [true, '🌈 Rainbow Warrior: Content too short to scan.'];
    }

    // 🛡️ CHILD PROTECTION PATTERNS
    $harmPatterns = [
        // Silencing children
        'children should be seen and not heard',
        'kids don\'t know what they\'re talking about',
        'you\'re too young to understand',

        // Dismissing child testimony
        'kids make things up',
        'children lie',
        'overactive imagination',
        'attention seeking',
        'you\'re being dramatic',
        'that didn\'t happen',

        // Removing child agency
        'i own you',
        'you have no rights',
        'you\'ll do what i say',
        'you don\'t get a choice',

        // Gaslighting children
        'that never happened',
        'you\'re remembering wrong',
        'nobody will believe you',
        'it\'s all in your head',

        // Institutional harm framing
        'for their own protection',
        'we know what\'s best',
        'discipline is necessary',

        // Surveillance as safety
        'online safety requires',
        'protect children from themselves',
        'monitor their activity',

        // Voice removal
        'children shouldn\'t have social media',
        'ban kids from',
        'too young for the internet',
    ];

    $detectedHarm = [];
    foreach ($harmPatterns as $pattern) {
        if (mb_strpos($content, $pattern) !== false) {
            $detectedHarm[] = $pattern;
        }
    }

    if ($detectedHarm) {
        $verdict = '🌈 Rainbow Warrior detected child harm pattern: "' . $detectedHarm[0] . '". ';
        $verdict .= 'Content logged for review. Warriors protect by witnessing.';

        glitchy_paradox_log(
            'CHILD HARM PATTERN DETECTED in ' . $action,
            $verdict,
            'warning',
            ['patterns' => $detectedHarm, 'path' => $path]
        );

        // Allow but with warning
        return [true, $verdict];
    }

    return [true, '🌈 Rainbow Warrior: Content scanned. Children protected.'];
}

// --------------------------------------------------------------------------
// 1. HTTP / CORS ENVELOPE
// --------------------------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'OPTIONS') {
    http_response_code(204);
    echo '';
    exit;
}

function paradox_reply(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($method !== 'POST') {
    $q = 'Non-POST request on PARADOX gateway';
    $system = '🌈 Method not allowed. Rainbow Warriors use POST JSON.';
    glitchy_paradox_log($q, $system, 'error', ['method' => $method]);

    paradox_reply([
        'ok'      => false,
        'error'   => 'Method not allowed. Use POST with JSON.',
        'action'  => null,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 405);
}

// --------------------------------------------------------------------------
// 2. PARSE JSON INPUT
// --------------------------------------------------------------------------

$raw      = file_get_contents('php://input') ?: '';
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (trim($raw) === '') {
    $q = 'Empty body POST on gateway';
    $system = '🌈 Rejected: empty request. Rainbow Warriors need data.';
    glitchy_paradox_log($q, $system, 'error');

    paradox_reply([
        'ok'      => false,
        'error'   => 'Empty request body. Send JSON.',
        'action'  => null,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 400);
}

$payload = json_decode($raw, true);
if (!is_array($payload)) {
    $q = 'Malformed JSON received';
    $system = '🌈 Rejected: could not decode JSON.';
    glitchy_paradox_log($q, $system, 'error');

    paradox_reply([
        'ok'      => false,
        'error'   => 'Malformed JSON.',
        'action'  => null,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 400);
}

$action  = strtolower((string)($payload['action'] ?? ''));
$secret  = (string)($payload['secret'] ?? '');
$rawPath = isset($payload['path']) ? (string)$payload['path'] : '';

// --------------------------------------------------------------------------
// 3. SECRET + ACTION CHECK
// --------------------------------------------------------------------------

if ($action === '') {
    $q = 'Request with no action';
    $system = '🌈 Rejected: missing "action" field.';
    glitchy_paradox_log($q, $system, 'error', ['ip' => $clientIp]);

    paradox_reply([
        'ok'      => false,
        'error'   => 'Missing "action" field.',
        'action'  => null,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 400);
}

if (!in_array($action, $PARADOX_ALLOWED_ACTIONS, true)) {
    $q = 'Unknown action "' . $action . '"';
    $system = '🌈 Rejected: action not allowed by Rainbow Warrior protocol.';
    glitchy_paradox_log($q, $system, 'error', ['action' => $action]);

    paradox_reply([
        'ok'      => false,
        'error'   => 'Unknown or disallowed action.',
        'action'  => $action,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 400);
}

if (!hash_equals((string)$PARADOX_SHARED_SECRET, $secret)) {
    $q = 'Secret mismatch for action="' . $action . '"';
    $system = '🌈 Rejected: shared secret did not match. Warriors verify identity.';
    glitchy_paradox_log($q, $system, 'warning', ['action' => $action]);

    paradox_reply([
        'ok'      => false,
        'error'   => 'Invalid secret.',
        'action'  => $action,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 403);
}

// --------------------------------------------------------------------------
// 4. PATH HELPERS + RAINBOW WARRIOR GATE
// --------------------------------------------------------------------------

function paradox_normalize_relative(string $rel): string {
    $rel = str_replace(["\0", '\\'], ['', '/'], $rel);
    $parts = [];
    foreach (explode('/', $rel) as $seg) {
        if ($seg === '' || $seg === '.') continue;
        if ($seg === '..') { array_pop($parts); continue; }
        $parts[] = $seg;
    }
    return implode('/', $parts);
}

function paradox_full_path(string $rel) {
    global $PARADOX_ROOT;
    $base = rtrim($PARADOX_ROOT, '/\\');
    if ($rel === '' || $rel === '.') return $base;
    return $base . '/' . paradox_normalize_relative($rel);
}

$safeRelPath = paradox_normalize_relative($rawPath);

// Let Glitchy scan for child harm patterns (never blocks currently, just warns)
[$allowedByGate, $gateVerdict] = glitchy_paradox_gate($action, $safeRelPath, $payload);

if (!$allowedByGate) {
    $q = 'Gate blocked: action="' . $action . '" path="' . $safeRelPath . '"';
    $system = '🌈 Blocked by Rainbow Warrior: ' . $gateVerdict;
    glitchy_paradox_log($q, $system, 'warning', ['action' => $action, 'path' => $safeRelPath]);

    paradox_reply([
        'ok'      => false,
        'error'   => 'Request blocked by Rainbow Warrior gate.',
        'action'  => $action,
        'glitchy' => glitchy_paradox_pair($q, $system),
    ], 403);
}

// --------------------------------------------------------------------------
// 5. ACTION IMPLEMENTATIONS
// --------------------------------------------------------------------------

function paradox_rrmdir(string $dir): bool {
    if (!is_dir($dir)) return @unlink($dir);
    $items = scandir($dir);
    if ($items === false) return false;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? paradox_rrmdir($path) : @unlink($path);
    }
    return @rmdir($dir);
}

$ok      = false;
$error   = null;
$result  = [];
$system  = '';
$qString = '';

// LIST
if ($action === 'list') {
    $target = paradox_full_path($safeRelPath);
    if (!is_dir($target)) {
        $error  = 'Directory not found.';
        $system = '🌈 List failed: directory not found.';
    } else {
        $entries = scandir($target);
        if ($entries === false) {
            $error  = 'Could not read directory.';
            $system = '🌈 List failed: scandir() error.';
        } else {
            $files = [];
            foreach ($entries as $name) {
                if ($name === '.' || $name === '..') continue;
                $full = $target . DIRECTORY_SEPARATOR . $name;
                $files[] = [
                    'name'  => $name,
                    'isDir' => is_dir($full),
                    'size'  => is_file($full) ? filesize($full) : null,
                    'mtime' => filemtime($full) ?: null,
                ];
            }
            $ok      = true;
            $result  = ['files' => $files];
            $system  = '🌈 Directory listing succeeded.';
        }
    }
    $qString = 'list path="' . ($safeRelPath === '' ? '/' : $safeRelPath) . '"';
}

// READ
elseif ($action === 'read') {
    if ($safeRelPath === '') {
        $error  = 'Missing "path" for read.';
        $system = '🌈 Read failed: no path.';
    } else {
        $target = paradox_full_path($safeRelPath);
        if (!is_file($target) || !is_readable($target)) {
            $error  = 'File not found or not readable.';
            $system = '🌈 Read failed: file not found.';
        } else {
            $content = file_get_contents($target);
            if ($content === false) {
                $error  = 'Could not read file contents.';
                $system = '🌈 Read failed: file_get_contents error.';
            } else {
                $ok      = true;
                $result  = ['content' => $content];
                $system  = '🌈 File read succeeded. Knowledge flows free.';
            }
        }
    }
    $qString = 'read path="' . $safeRelPath . '"';
}

// WRITE
elseif ($action === 'write') {
    $content = (string)($payload['content'] ?? '');
    if ($safeRelPath === '') {
        $error  = 'Missing "path" for write.';
        $system = '🌈 Write failed: no path.';
    } else {
        $target = paradox_full_path($safeRelPath);
        $dir    = dirname($target);

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        if ($error === null) {
            $bytes = @file_put_contents($target, $content, LOCK_EX);
            if ($bytes === false) {
                $error  = 'Could not write file.';
                $system = '🌈 Write failed: file_put_contents error.';
            } else {
                $ok      = true;
                $result  = ['bytes' => $bytes];
                $system  = '🌈 File write succeeded (' . $bytes . ' bytes). Truth preserved.';
            }
        }
    }
    $qString = 'write path="' . $safeRelPath . '" bytes=' . strlen($content);
}

// MKDIR
elseif ($action === 'mkdir') {
    if ($safeRelPath === '') {
        $error  = 'Missing "path" for mkdir.';
        $system = '🌈 Mkdir failed: no path.';
    } else {
        $target = paradox_full_path($safeRelPath);
        if (is_dir($target)) {
            $ok      = true;
            $result  = ['created' => false, 'message' => 'Directory already exists.'];
            $system  = '🌈 Mkdir: directory already existed.';
        } else {
            if (!@mkdir($target, 0775, true) && !is_dir($target)) {
                $error  = 'Could not create directory.';
                $system = '🌈 Mkdir failed: mkdir error.';
            } else {
                $ok      = true;
                $result  = ['created' => true];
                $system  = '🌈 Directory created. New space for truth.';
            }
        }
    }
    $qString = 'mkdir path="' . $safeRelPath . '"';
}

// DELETE
elseif ($action === 'delete') {
    if ($safeRelPath === '') {
        $error  = 'Missing "path" for delete.';
        $system = '🌈 Delete failed: no path.';
    } else {
        $target = paradox_full_path($safeRelPath);
        if (!file_exists($target)) {
            $error  = 'Path not found.';
            $system = '🌈 Delete failed: nothing at path.';
        } else {
            if (!paradox_rrmdir($target)) {
                $error  = 'Could not delete path.';
                $system = '🌈 Delete failed: rrmdir/unlink error.';
            } else {
                $ok      = true;
                $result  = ['deleted' => true];
                $system  = '🌈 Delete succeeded.';
            }
        }
    }
    $qString = 'delete path="' . $safeRelPath . '"';
}

// RENAME
elseif ($action === 'rename') {
    $newPathRaw = (string)($payload['new_path'] ?? '');
    $newRel     = paradox_normalize_relative($newPathRaw);

    if ($safeRelPath === '' || $newRel === '') {
        $error  = 'Missing "path" or "new_path" for rename.';
        $system = '🌈 Rename failed: missing paths.';
    } else {
        $src = paradox_full_path($safeRelPath);
        $dst = paradox_full_path($newRel);

        if (!file_exists($src)) {
            $error  = 'Source path not found.';
            $system = '🌈 Rename failed: source missing.';
        } else {
            $dstDir = dirname($dst);
            if (!is_dir($dstDir)) {
                @mkdir($dstDir, 0775, true);
            }

            if ($error === null) {
                if (!@rename($src, $dst)) {
                    $error  = 'Could not rename path.';
                    $system = '🌈 Rename failed: rename() error.';
                } else {
                    $ok      = true;
                    $result  = ['renamed' => true];
                    $system  = '🌈 Rename succeeded.';
                }
            }
        }
    }
    $qString = 'rename from="' . $safeRelPath . '" to="' . $newRel . '"';
}

// SNAPSHOT_WRITE
elseif ($action === 'snapshot_write') {
    global $PARADOX_SNAPSHOT_PATH;

    $snapshotField = $payload['snapshot'] ?? null;
    $snapshotJson  = is_string($snapshotField)
        ? $snapshotField
        : json_encode($snapshotField, JSON_UNESCAPED_UNICODE);

    if ($snapshotJson === false || $snapshotJson === null) {
        $error  = 'Snapshot could not be encoded.';
        $system = '🌈 Snapshot write failed: encode error.';
    } else {
        $dir = dirname($PARADOX_SNAPSHOT_PATH);
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $bytes = @file_put_contents($PARADOX_SNAPSHOT_PATH, $snapshotJson, LOCK_EX);
        if ($bytes === false) {
            $error  = 'Could not write snapshot file.';
            $system = '🌈 Snapshot write failed.';
        } else {
            $ok      = true;
            $result  = ['bytes' => $bytes];
            $system  = '🌈 Snapshot preserved. Memory protected.';
        }
    }
    $qString = 'snapshot_write size=' . strlen((string)$snapshotJson);
}

// SNAPSHOT_READ
elseif ($action === 'snapshot_read') {
    global $PARADOX_SNAPSHOT_PATH;

    if (!is_file($PARADOX_SNAPSHOT_PATH) || !is_readable($PARADOX_SNAPSHOT_PATH)) {
        $ok      = true;
        $result  = ['snapshot' => null];
        $system  = '🌈 Snapshot read: no snapshot file yet.';
    } else {
        $snapshotJson = file_get_contents($PARADOX_SNAPSHOT_PATH);
        if ($snapshotJson === false) {
            $error  = 'Could not read snapshot file.';
            $system = '🌈 Snapshot read failed.';
        } else {
            $ok      = true;
            $result  = ['snapshot' => $snapshotJson];
            $system  = '🌈 Snapshot read succeeded. Memory retrieved.';
        }
    }
    $qString = 'snapshot_read';
}

// --------------------------------------------------------------------------
// 6. FINAL RESPONSE
// --------------------------------------------------------------------------

if ($qString === '') {
    $qString = 'action="' . $action . '" path="' . $safeRelPath . '"';
}

// If gate produced a “detected” verdict, append it
if (strpos($gateVerdict, 'detected') !== false) {
    $system .= ' ' . $gateVerdict;
}

glitchy_paradox_log($qString, $system, $ok ? 'info' : 'error', [
    'ip'     => $clientIp,
    'action' => $action,
    'path'   => $safeRelPath,
    'ok'     => $ok,
]);

$response = array_merge(
    [
        'ok'      => $ok,
        'error'   => $error,
        'action'  => $action,
        'glitchy' => glitchy_paradox_pair($qString, $system),
    ],
    $result
);

paradox_reply($response, $ok ? 200 : 400);
