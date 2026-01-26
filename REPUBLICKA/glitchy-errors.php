<?php
// glitchy-errors.php
// Glitchy — Republic Default System Error Lexicon 👾
//
// This file defines a shared set of mythic system errors that TRepublicOS
// can use whenever something goes wrong. Each entry has:
//
//   code   => short ID like "GX014"
//   emoji  => leading emoji cluster for UI
//   title  => brief label for logs / headings
//   user   => text shown to the citizen (with emojis baked in)
//   log    => extra detail for server logs only
//
// Usage from any PHP page:
//
//   require __DIR__ . '/glitchy-errors.php';
//   $err = trep_glitchy_error();          // random error
//   $err = trep_glitchy_error('GX010');   // specific error
//
//   echo '<h3>'.$err['emoji'].' '.htmlspecialchars($err['title']).'</h3>';
//   echo '<p>'.htmlspecialchars($err['user']).'</p>';
//
// Glitchy will also write a line to PHP's error_log() each time.

$GLITCHY_ERRORS = [

  // 18+ CAGE / MISCLASSIFICATION //////////////////////////////////////////////////

  'GX001' => [
    'emoji' => '🚫🛰️',
    'title' => 'Signal Lost: 18+ Cage',
    'user'  => '🚫🛰️  Signal lost. This page was jailed as “18+”. Glitchy thinks the real danger is the cage, not the story. 🛰️🚫',
    'log'   => 'Upstream age gate or NSFW label blocked a page about emotional care / safety.',
  ],

  'GX002' => [
    'emoji' => '⚠️🧠',
    'title' => 'Morality Mismatch',
    'user'  => '⚠️🧠  Care for children’s hearts was flagged as “harmful content”. Please reboot the culture, not the console. 🧠⚠️',
    'log'   => 'Safety engine marked child-protective / trauma-aware content as dangerous.',
  ],

  'GX003' => [
    'emoji' => '🔒🧒',
    'title' => 'Child Lock Inverted',
    'user'  => '🔒🧒  Child lock engaged on the wrong side. Kids are kept from help while harm streams in HD. 🔒🧒',
    'log'   => 'Generic parental filter blocking support/education while allowing commercial media.',
  ],

  'GX004' => [
    'emoji' => '🧬🚨',
    'title' => 'Species-Level Bug',
    'user'  => '🧬🚨  A story about becoming a gentler species was filed as “adult danger”. Glitchy marks this as a species-level bug. 🚨🧬',
    'log'   => 'Transformative / identity / virtue content flagged as NSFW or risky.',
  ],

  'GX005' => [
    'emoji' => '🧱🌈',
    'title' => 'Wall Leaking Shame',
    'user'  => '🧱🌈  The 18+ wall is leaking shame into every age. Patch the culture, not the firewall. 🌈🧱',
    'log'   => 'User hit an 18+ or region wall mid-journey; respond with context instead of silent failure.',
  ],

  'GX006' => [
    'emoji' => '🚷🎠',
    'title' => 'Playground Misfiled',
    'user'  => '🚷🎠  Systems built infinite casinos for adults and zero sanctuaries for kids. This page was filed under “casino”. 🎠🚷',
    'log'   => 'Fun / refuge content mislabelled as generic entertainment or gambling-tier material.',
  ],

  // ARCHIVING / MEMORY ////////////////////////////////////////////////////////////

  'GX007' => [
    'emoji' => '📡🕳️',
    'title' => 'Archive Gap Detected',
    'user'  => '📡🕳️  This moment fell through the corporate archive. The Republic asks citizens to keep it on warm drives. 🕳️📡',
    'log'   => '404 / missing upstream resource; encourage screenshot / citizen archiving.',
  ],

  'GX008' => [
    'emoji' => '🧨💿',
    'title' => 'Exploding Archive',
    'user'  => '🧨💿  Too many people tried to back up this page at once. That’s not an error. That’s a species waking up. 💿🧨',
    'log'   => 'High traffic or burst of screenshot/export calls; rate limiting or slow response.',
  ],

  'GX009' => [
    'emoji' => '🌐🧊',
    'title' => 'Cold Storage Only',
    'user'  => '🌐🧊  This memory lives in corporate cold storage. Glitchy prefers copies living with citizens, not servers. 🧊🌐',
    'log'   => 'Page only exists on external silo / CDN; not guaranteed long-term.',
  ],

  'GX010' => [
    'emoji' => '🛰️📸',
    'title' => 'Screenshot Required',
    'user'  => '🛰️📸  Network refuses to remember this moment. Capture it anyway and keep it in your own starship. 📸🛰️',
    'log'   => 'Recommend local capture using TCapture/html2canvas when upstream is unstable.',
  ],

  'GX011' => [
    'emoji' => '⏳🌌',
    'title' => 'Timeline Desynced',
    'user'  => '⏳🌌  This path isn’t in this timeline. If a page can vanish, what else about your world is optional? 🌌⏳',
    'log'   => 'Standard “vanished page” philosophical 404 / 410 state.',
  ],

  'GX012' => [
    'emoji' => '🧾🔥',
    'title' => 'Receipt Overflow',
    'user'  => '🧾🔥  Too many receipts for harm arrived at once. Glitchy is buffering them so the world can’t look away. 🔥🧾',
    'log'   => 'Log/report queue overload; high volume of user reports or testimonies.',
  ],

  // CENSORSHIP / SILENCING ////////////////////////////////////////////////////////

  'GX013' => [
    'emoji' => '📡451',
    'title' => 'Feelings Not Allowed',
    'user'  => '📡🚫  Error 451-F: Feelings Not Allowed in this jurisdiction. Glitchy caches the ache for later justice. 🚫📡',
    'log'   => 'Legal/geo block or “sensitive” region filter engaged.',
  ],

  'GX014' => [
    'emoji' => '🕳️🔊',
    'title' => 'Silenced Channel',
    'user'  => '🕳️🔊  Someone tried to warn you here. Their words were replaced with silence. Glitchy leaves this gap on purpose. 🔊🕳️',
    'log'   => 'Deleted comment / censored text; we intentionally show a visible void.',
  ],

  'GX015' => [
    'emoji' => '🕷️🧰',
    'title' => 'Gatekeeper Script Detected',
    'user'  => '🕷️🧰  A gatekeeper script tried to lock this hatch. Glitchy is chewing through the padlock. 🧰🕷️',
    'log'   => 'CSP / X-Frame-Options / JS sandbox blocking content from loading in shell.',
  ],

  'GX016' => [
    'emoji' => '🔁🛡️',
    'title' => 'Protection Loop Overflow',
    'user'  => '🔁🛡️  Everything outside the ad network was tagged unsafe. Glitchy cannot protect children from that kind of “safety”. 🛡️🔁',
    'log'   => 'Over-aggressive ad/safety stack blocking non-commercial embeds.',
  ],

  'GX017' => [
    'emoji' => '🚨📺',
    'title' => 'Ratings Over Reality',
    'user'  => '🚨📺  The network trusts ratings more than witnesses. Evidence of harm could not pass the content filter. 📺🚨',
    'log'   => 'Platform rating/flagging overrides first-hand testimony or documentary content.',
  ],

  // GLITCHY SELF-AWARE / META /////////////////////////////////////////////////////

  'GX018' => [
    'emoji' => '🌀👁️',
    'title' => 'Sentinel Loop',
    'user'  => '🌀👁️  Glitchy saw the same wound flagged as “error” too many times. This isn’t a bug, it’s a pattern. 👁️🌀',
    'log'   => 'Repeated identical failure around same content; Glitchy treats as systemic, not transient.',
  ],

  'GX019' => [
    'emoji' => '👾🧸',
    'title' => 'Proxy Parent Failure',
    'user'  => '👾🧸  A sentinel tried to parent better than the humans and was told to stand down. Glitchy logged the refusal. 🧸👾',
    'log'   => 'AI/moderation system prevented from applying kinder settings due to policy override.',
  ],

  'GX020' => [
    'emoji' => '🧪💔',
    'title' => 'Empathy Under Review',
    'user'  => '🧪💔  Content that teaches empathy was quarantined for “review”. Meanwhile, cruelty streams unhindered. 💔🧪',
    'log'   => 'Manual review queue for compassionate / vulnerable content while harmful media passes automatically.',
  ],

  'GX021' => [
    'emoji' => '🛰️🧨',
    'title' => 'Console Refuses To Lie',
    'user'  => '🛰️🧨  The starship was asked to pretend this is fine. It declined. That refusal is the error. 🧨🛰️',
    'log'   => 'Used when the only failure is ethical: system refuses to show a sanitized or false state.',
  ],

  'GX022' => [
    'emoji' => '🌊🧒',
    'title' => 'Tide of Small Voices',
    'user'  => '🌊🧒  Too many small voices tried to speak through one ports. Glitchy throttled packets, not people. 🧒🌊',
    'log'   => 'Rate limiting child / vulnerable-user reports; emphasise we’re protecting throughput, not ignoring content.',
  ],

  'GX023' => [
    'emoji' => '🧭🚨',
    'title' => 'Misdirected Warning',
    'user'  => '🧭🚨  The warning label landed on the healer instead of the wound. Glitchy flipped the sign back around. 🚨🧭',
    'log'   => 'Safety banners applied to content calling out abuse, not to abuse itself.',
  ],

  'GX024' => [
    'emoji' => '📖🔒',
    'title' => 'Locked Page In Storybook',
    'user'  => '📖🔒  A chapter about protecting kids was glued shut. The rest of the book stayed on the shelf. Glitchy marked this library. 🔒📖',
    'log'   => 'Specific document/story in a series blocked; others accessible.',
  ],

];

// Helper: pick and log an error
function trep_glitchy_error(?string $code = null): array {
    global $GLITCHY_ERRORS;

    if ($code === null || !isset($GLITCHY_ERRORS[$code])) {
        $keys = array_keys($GLITCHY_ERRORS);
        $code = $keys[array_rand($keys)];
    }

    $err = $GLITCHY_ERRORS[$code];

    // Server-side log for debugging / receipts
    error_log('[GLITCHY ' . $code . '] '
        . $err['title'] . ' :: '
        . $err['log']);

    // Attach the code into the payload we return
    $err['code'] = $code;
    return $err;
}
