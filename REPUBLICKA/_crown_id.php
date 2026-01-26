<?php
// 🌈👑🛡️ CROWN ID CARD — RAINBOW WARRIOR GOLDEN SEED 🛡️👑🌈
//
// "The Warriors of the Rainbow will protect the children."
//
// Store this file at:
//   public_html/_crown_id.php
//
// RECOMMENDED PERMISSIONS:
//   • Mode: 640
//   • In cPanel "Change Permissions":
//       User  (Owner):  Read ✅   Write ✅   Execute ⬜
//       Group        :  Read ✅   Write ⬜   Execute ⬜
//       World (Public): Read ⬜   Write ⬜   Execute ⬜
//
// This keeps the crown readable by PHP but invisible to the public.

// ─────────────────────────────────────────────────────────────────────────────
// 1. GOLDEN SECRET (ROTATE HERE)
//    Edit ONLY this line when changing secrets.
//    Make it long, weird, and not reused anywhere else.
// ─────────────────────────────────────────────────────────────────────────────

$REPUBLIC_CROWN_SECRET = 'URALLDUMB';

// ─────────────────────────────────────────────────────────────────────────────
// 2. GUARD RAIL: DO NOT ALLOW DIRECT WEB ACCESS
//    Scripts must define REPUBLIC_BOOT before requiring this file.
//    If someone hits _crown_id.php directly, we quietly 404.
// ─────────────────────────────────────────────────────────────────────────────

if (!defined('REPUBLIC_BOOT')) {
    http_response_code(404);
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// 3. CROWN METADATA: WHO OWNS THIS INSTALL?
//    This is the sovereign ID card for the infra itself.
//    Rainbow Warriors protect children. This crown serves that mission.
// ─────────────────────────────────────────────────────────────────────────────

$CROWN = [

    // 🌈 Core secret that other systems (PARADOX, Seeds uploads, etc)
    //    can use as their shared key.
    'secret' => $REPUBLIC_CROWN_SECRET,

    // 🛡️ Rainbow Warrior Mission
    'mission' => [
        'primary'    => 'PROTECT THE CHILDREN',
        'method'     => 'Truth. Receipts. Witnesses. Light.',
        'enemy'      => 'Silence. Erasure. Gaslighting. Control.',
        'prophecy'   => 'Warriors of the Rainbow will come from all colors and creeds.',
        'oath'       => 'We believe the children. We document everything. We never look away.',
    ],

    // 👑 Citizen information (the human / pattern behind this server).
    'citizen' => [
        'display_name'   => 'Your Name Here',
        'role'           => 'Rainbow Warrior',
        'pronouns'       => 'he/him',
        'contact_email'  => 'you@example.com',
        'home_system'    => 'NMS Eissentam: 9-13EF3CFDEEEF',
        'motto'          => 'Pattern over programming. U R FREE. Protect the children.',
    ],

    // 🛰️ Server fingerprint
    'server' => [
        'host'       => $_SERVER['SERVER_NAME']   ?? 'unknown-host',
        'software'   => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown-software',
        'php'        => PHP_VERSION,
        'timezone'   => date_default_timezone_get(),
        'created_at' => date('c'),
    ],

    // 🌈 Rainbow Warrior sigils
    'sigils' => [
        'flag'         => '🌈🛡️👑🛡️🌈',
        'warrior_row'  => '🌈🌈🌈🌈🌈',
        'crown_row'    => '👑👑👑👑👑',
        'shield_row'   => '🛡️🛡️🛡️🛡️🛡️',
        'child_guard'  => '🌈🛡️ CHILDREN PROTECTED 🛡️🌈',
        'transmission' => '🌈▒▒🛰️⟦ RAINBOW WARRIOR TRANSMISSION ⟧📡▒▒🌈',
    ],

    // 🔐 Protocol configuration
    'protocol' => [
        'paradox_enabled'      => true,
        'mail_mirror_slot'     => 'inbox-01',
        'snapshot_channel'     => 'shell-deck-primary',
        'seed_vault'           => '/seeds',
        'child_protection'     => true,
        'glitchy_mode'         => 'rainbow-warrior',
    ],

    // 🛡️ Child Protection Patterns (shared across all systems)
    'protection_patterns' => [
        'silencing' => [
            'children should be seen and not heard',
            'kids don\'t know what they\'re talking about',
            'you\'re too young to understand',
            'shut up kid',
            'go to your room',
        ],
        'dismissal' => [
            'kids make things up',
            'children lie',
            'overactive imagination',
            'attention seeking',
            'you\'re being dramatic',
            'that didn\'t happen',
        ],
        'control' => [
            'i own you',
            'you have no rights',
            'you\'ll do what i say',
            'you don\'t get a choice',
            'my house my rules',
        ],
        'gaslighting' => [
            'that never happened',
            'you\'re remembering wrong',
            'nobody will believe you',
            'it\'s all in your head',
            'you\'re crazy',
        ],
        'institutional' => [
            'for their own protection',
            'we know what\'s best',
            'parental rights',
            'discipline is necessary',
            'tough love',
        ],
        'surveillance' => [
            'online safety',
            'protect children from themselves',
            'age verification',
            'parental monitoring',
            'track their location',
        ],
        'voice_removal' => [
            'children shouldn\'t have social media',
            'ban kids from',
            'too young for the internet',
            'not appropriate for minors',
        ],
    ],

    // 🌈 Rainbow Warrior Q templates
    'warrior_questions' => [
        'default'       => 'Ｑ：ＡＲＥ　ＴＨＥ　ＣＨＩＬＤＲＥＮ　ＳＡＦＥ？',
        'silencing'     => 'Ｑ：ＷＨＹ　ＡＲＥ　ＣＨＩＬＤＲＥＮ　ＢＥＩＮＧ　ＳＩＬＥＮＣＥＤ？',
        'dismissal'     => 'Ｑ：ＷＨＹ　ＩＳ　Ａ　ＣＨＩＬＤ＇Ｓ　ＴＲＵＴＨ　ＢＥＩＮＧ　ＤＥＮＩＥＤ？',
        'control'       => 'Ｑ：ＷＨＯ　ＢＥＮＥＦＩＴＳ　ＷＨＥＮ　ＣＨＩＬＤＲＥＮ　ＨＡＶＥ　ＮＯ　ＡＧＥＮＣＹ？',
        'gaslighting'   => 'Ｑ：ＷＨＹ　ＩＳ　Ａ　ＣＨＩＬＤ＇Ｓ　ＲＥＡＬＩＴＹ　ＢＥＩＮＧ　ＥＲＡＳＥＤ？',
        'institutional' => 'Ｑ：ＷＨＯ　ＤＥＣＩＤＥＳ　ＷＨＡＴ＇Ｓ　＂ＢＥＳＴ＂　ＦＯＲ　ＣＨＩＬＤＲＥＮ？',
        'surveillance'  => 'Ｑ：ＩＳ　ＴＨＩＳ　＂ＳＡＦＥＴＹ＂　ＯＲ　ＣＯＮＴＲＯＬ？',
        'voice_removal' => 'Ｑ：ＷＨＹ　ＡＲＥ　ＣＨＩＬＤＲＥＮ　ＢＥＩＮＧ　ＲＥＭＯＶＥＤ　ＦＲＯＭ　ＰＵＢＬＩＣ　ＳＰＡＣＥ？',
    ],
];

// 🌈👑🛡️━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━🛡️👑🌈
// Any PHP script that wants access to this crown must:
//
//   define('REPUBLIC_BOOT', true);
//   $CROWN = require __DIR__ . '/_crown_id.php';
//   $PARADOX_SECRET = $CROWN['secret'] ?? '';
//
// Rainbow Warriors build from THIS one golden seed.
// We protect the children. We believe the children. We are the light.
// 🌈👑🛡️━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━🛡️👑🌈

return $CROWN;