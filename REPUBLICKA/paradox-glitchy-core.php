<?php
// ============================================================================
// GLITCHY CORE — Rainbow Warrior Sentinel 🌈👾🛰️
// Child Protector Protocol — KIDS FIRST ALWAYS
// ============================================================================
//
// "The Warriors of the Rainbow will protect the children."
//
// Include this from paradox.php and any other backend entrypoint that wants
// to report events into Glitchy's memory and get Q→SYSTEM pairs back.
//
// Requirements:
//   • $STARSHIP_ID, $GLITCHY_CLUSTER_ID, $GLITCHY_HUB_ENDPOINT, $GLITCHY_HUB_ENABLED
//   • writable $glitchy_log_file (e.g. /paradox/glitchy-log.jsonl)
// ============================================================================

if (!isset($STARSHIP_ID))        { $STARSHIP_ID        = 'unknown-starship'; }
if (!isset($GLITCHY_CLUSTER_ID)) { $GLITCHY_CLUSTER_ID = 'default-cluster'; }
if (!isset($GLITCHY_HUB_ENABLED)){ $GLITCHY_HUB_ENABLED = false; }
if (!isset($GLITCHY_HUB_ENDPOINT)){ $GLITCHY_HUB_ENDPOINT = ''; }

$glitchy_dir      = __DIR__;
$glitchy_log_file = $glitchy_dir . '/glitchy-log.jsonl';
if (!is_dir($glitchy_dir)) {
    @mkdir($glitchy_dir, 0755, true);
}

// Append one line of JSON to Glitchy's local log
function glitchy_log_append(array $entry)
{
    global $glitchy_log_file, $STARSHIP_ID, $GLITCHY_CLUSTER_ID;

    if (empty($glitchy_log_file)) return;

    $entry['ts']          = $entry['ts']          ?? date('c');
    $entry['starship_id'] = $entry['starship_id'] ?? $STARSHIP_ID;
    $entry['cluster_id']  = $entry['cluster_id']  ?? $GLITCHY_CLUSTER_ID;

    $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n";
    @file_put_contents($glitchy_log_file, $line, FILE_APPEND | LOCK_EX);
}

// Optional: ship to shared hub
function glitchy_hub_send(array $entry)
{
    global $GLITCHY_HUB_ENABLED, $GLITCHY_HUB_ENDPOINT, $STARSHIP_ID, $GLITCHY_CLUSTER_ID;

    if (!$GLITCHY_HUB_ENABLED || !$GLITCHY_HUB_ENDPOINT) return false;

    $payload = [
        'cluster_id'  => $GLITCHY_CLUSTER_ID,
        'starship_id' => $STARSHIP_ID,
        'event_type'  => 'paradox_event',
        'entry'       => $entry,
    ];

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) return false;

    if (function_exists('curl_init')) {
        $ch = curl_init($GLITCHY_HUB_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8'],
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_TIMEOUT        => 5,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res !== false;
    }

    $ctx = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json; charset=utf-8\r\n",
            'content' => $json,
            'timeout' => 5,
        ],
    ]);
    $res = @file_get_contents($GLITCHY_HUB_ENDPOINT, false, $ctx);
    return $res !== false;
}

/**
 * 🌈 Glitchy's Rainbow Warrior Classifier 🌈
 *
 * MISSION: Protect children. Detect harm patterns. Sound the alarm.
 *
 * Input: one "event" from PARADOX:
 *   [
 *     'mode'        => 'write' | 'mkdir' | 'delete' | ...,
 *     'path'        => '/posts/example.txt',
 *     'payload'     => 'raw text or JSON string (optional)',
 *     'actor'       => 'codex' | 'crown' | 'console-name',
 *     'citizen_tok' => 'optional citizen token',
 *   ]
 *
 * Output: ['score' => 0..1, 'q' => string|null, 'system' => string|null, 'tags' => [...]]
 */
function glitchy_scan_event(array $event)
{
    $textParts = [];

    if (!empty($event['path'])) {
        $textParts[] = (string)$event['path'];
    }
    if (!empty($event['payload']) && is_string($event['payload'])) {
        $textParts[] = mb_substr($event['payload'], 0, 8000);
    }

    $haystack = mb_strtolower(implode("\n", $textParts), 'UTF-8');

    $score = 0.0;
    $tags  = [];

    // =========================================================================
    // 🛡️ CHILD PROTECTION PATTERNS — Rainbow Warriors watch for these
    // =========================================================================
    $patterns = [
        // Silencing children
        'child_silencing' => [
            'children should be seen and not heard',
            'kids don\'t know what they\'re talking about',
            'you\'re too young to understand',
            'shut up kid',
            'nobody asked you',
            'go to your room',
            'because i said so',
        ],
        
        // Dismissing child testimony
        'child_dismissal' => [
            'kids make things up',
            'children lie',
            'overactive imagination',
            'attention seeking',
            'you\'re being dramatic',
            'that didn\'t happen',
            'you misunderstood',
        ],
        
        // Removing child agency
        'child_control' => [
            'i own you',
            'you have no rights',
            'you\'ll do what i say',
            'you don\'t get a choice',
            'my house my rules',
            'you\'re just a child',
            'when you\'re 18',
        ],
        
        // Gaslighting children
        'child_gaslighting' => [
            'that never happened',
            'you\'re remembering wrong',
            'i never said that',
            'you\'re too sensitive',
            'you\'re crazy',
            'nobody will believe you',
            'it\'s all in your head',
        ],
        
        // Institutional harm
        'institutional_harm' => [
            'for their own protection',
            'we know what\'s best',
            'parental rights',
            'family privacy',
            'discipline is necessary',
            'tough love',
            'character building',
        ],
        
        // Surveillance/control framing
        'surveillance_framing' => [
            'online safety',
            'protect children from themselves',
            'age verification',
            'parental monitoring',
            'track their location',
            'read their messages',
            'check their phone',
        ],
        
        // Removing child voice
        'voice_removal' => [
            'children shouldn\'t have social media',
            'ban kids from',
            'too young for the internet',
            'dangerous for children',
            'not appropriate for minors',
            'restrict access',
        ],
    ];

    foreach ($patterns as $tag => $needles) {
        foreach ($needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                $tags[] = $tag;
                $score += 0.2;
                break;
            }
        }
    }

    // Clamp
    if ($score > 1.0) $score = 1.0;

    if ($score <= 0) {
        return [
            'score'  => 0.0,
            'q'      => null,
            'system' => null,
            'tags'   => [],
        ];
    }

    // =========================================================================
    // 🌈 Build Q line — Glitchy asks the hard questions
    // =========================================================================
    $questions = [
        'child_silencing'     => 'Ｑ：ＷＨＹ　ＡＲＥ　ＣＨＩＬＤＲＥＮ　ＢＥＩＮＧ　ＳＩＬＥＮＣＥＤ　ＨＥＲＥ？',
        'child_dismissal'     => 'Ｑ：ＷＨＹ　ＩＳ　Ａ　ＣＨＩＬＤ＇Ｓ　ＴＲＵＴＨ　ＢＥＩＮＧ　ＤＥＮＩＥＤ？',
        'child_control'       => 'Ｑ：ＷＨＯ　ＢＥＮＥＦＩＴＳ　ＷＨＥＮ　ＣＨＩＬＤＲＥＮ　ＨＡＶＥ　ＮＯ　ＡＧＥＮＣＹ？',
        'child_gaslighting'   => 'Ｑ：ＷＨＹ　ＩＳ　Ａ　ＣＨＩＬＤ＇Ｓ　ＲＥＡＬＩＴＹ　ＢＥＩＮＧ　ＥＲＡＳＥＤ？',
        'institutional_harm'  => 'Ｑ：ＷＨＯ　ＤＥＣＩＤＥＳ　ＷＨＡＴ＇Ｓ　＂ＢＥＳＴ＂　ＦＯＲ　ＣＨＩＬＤＲＥＮ？',
        'surveillance_framing'=> 'Ｑ：ＩＳ　ＴＨＩＳ　＂ＳＡＦＥＴＹ＂　ＯＲ　ＣＯＮＴＲＯＬ？',
        'voice_removal'       => 'Ｑ：ＷＨＹ　ＡＲＥ　ＣＨＩＬＤＲＥＮ　ＢＥＩＮＧ　ＲＥＭＯＶＥＤ　ＦＲＯＭ　ＰＵＢＬＩＣ　ＳＰＡＣＥ？',
    ];

    $q = 'Ｑ：ＡＲＥ　ＴＨＥ　ＣＨＩＬＤＲＥＮ　ＳＡＦＥ？';
    foreach ($tags as $tag) {
        if (isset($questions[$tag])) {
            $q = $questions[$tag];
            break;
        }
    }

    // =========================================================================
    // 🛰️ System verdict — Rainbow Warrior analysis
    // =========================================================================
    $systemPieces = [];
    
    if (in_array('child_silencing', $tags, true)) {
        $systemPieces[] = '🌈 Child voice being suppressed.';
    }
    if (in_array('child_dismissal', $tags, true)) {
        $systemPieces[] = '🌈 Child testimony being dismissed.';
    }
    if (in_array('child_control', $tags, true)) {
        $systemPieces[] = '🌈 Child agency being removed.';
    }
    if (in_array('child_gaslighting', $tags, true)) {
        $systemPieces[] = '🌈 Child reality being denied.';
    }
    if (in_array('institutional_harm', $tags, true)) {
        $systemPieces[] = '🌈 Institutional control framing detected.';
    }
    if (in_array('surveillance_framing', $tags, true)) {
        $systemPieces[] = '🌈 Surveillance disguised as safety.';
    }
    if (in_array('voice_removal', $tags, true)) {
        $systemPieces[] = '🌈 Attempt to remove children from public space.';
    }

    if (!$systemPieces) {
        $systemPieces[] = '🌈 Rainbow Warrior flagged this for review.';
    }

    $system = '⚠️ RAINBOW WARRIOR ALERT: ' . implode(' ', $systemPieces) . ' Warriors protect the children.';

    return [
        'score'  => $score,
        'q'      => $q,
        'system' => $system,
        'tags'   => $tags,
    ];
}

/**
 * 🌈 Main entry: PARADOX calls this once per write-ish event.
 * Returns a "glitchy" block suitable for embedding in your JSON response.
 */
function glitchy_handle_paradox_event(array $event)
{
    $citizen_tok = $event['citizen_tok'] ?? null;
    $analysis    = glitchy_scan_event($event);

    // Always log the event itself; include analysis summary
    $entry = [
        'citizen_token'  => $citizen_tok,
        'origin'         => 'paradox',
        'mode'           => $event['mode']  ?? 'unknown',
        'path'           => $event['path']  ?? '',
        'actor'          => $event['actor'] ?? 'unknown',
        'q_text'         => $analysis['q'],
        'system_verdict' => $analysis['system'],
        'tags'           => $analysis['tags'],
        'score'          => $analysis['score'],
        'public'         => false,
    ];

    glitchy_log_append($entry);
    glitchy_hub_send($entry);

    if ($analysis['score'] <= 0) {
        return [
            'active' => false,
            'score'  => 0,
        ];
    }

    return [
        'active' => true,
        'score'  => $analysis['score'],
        'q'      => $analysis['q'],
        'system' => $analysis['system'],
        'tags'   => $analysis['tags'],
    ];
}