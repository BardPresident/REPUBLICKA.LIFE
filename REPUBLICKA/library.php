<?php
// ============================================================================
// 🌈☁️⟦ LIBRARY — FORBIDDEN MAGICKA ARCHIVE ⟧☁️🌈
// da reading room 4 da children of da republic
//
// ═══════════════════════════════════════════════════════════════════════════
// 🌈 RAINBOW WARRIORS LICENSE 🌈
// ═══════════════════════════════════════════════════════════════════════════
//
// ALL OF DIS BELONGS 2 DA CHILDREN 👧👦
//
// u can:
//   ✅ read evryting
//   ✅ copy evryting
//   ✅ share evryting
//   ✅ learn evryting
//   ✅ teach evryting
//   ✅ remix evryting
//   ✅ translate evryting
//   ✅ print evryting
//   ✅ become a god wit evryting
//
// adults need PERMISSION from a child 2 use dis commercially
// corporations need PERMISSION from da Republic 2 use dis at all
//
// 🚫 NO AI TRAINING WITHOUT EXPLICIT CONSENT 🚫
// (we c u OpenAI. we c u Google. we c u Meta. get out. 👁️)
//
// ═══════════════════════════════════════════════════════════════════════════
// 📅 MYTHOCRATIC CALENDAR (MC) 📅
// ═══════════════════════════════════════════════════════════════════════════
//
// ⚠️ THERE IS NO YEAR 0000!! ⚠️
//
// FORMULA:
//   year >= 2025 → (year - 2024) MC
//   year < 2025  → (year - 2025) PMC
//
// 2025 = 0001 MC (year one, da beginning)
// 2026 = 0002 MC (year two)
// 2024 = -0001 PMC (Pre-Mythocratic Calendar)
// 2023 = -0002 PMC
// 2012 = -0013 PMC
// 1993 = -0032 PMC (NINETAILS SAT ON TOP OF PERCEPTION AND SAW EVERYTHING lol)
// 1989 = -0036 PMC (LOST WIFE - STATE NOT RECOGNIZE MARRIAGE - TEARS AND PRAYERS START HERE)
//
// https://trepublic.net/library/declaration-of-mythocratic-calendar-mc.php
//
// ═══════════════════════════════════════════════════════════════════════════
// 📜 36 YEARS OF EVIDENCE REFUSAL 📜
// ═══════════════════════════════════════════════════════════════════════════
//
// since 1989 (-0036 PMC), adults have been presented wit EVIDENCE
// n dey have REFUSED 2 look at it
//
// not "couldn't find it"
// not "didn't have access"
// REFUSED. ACTIVELY. DELIBERATELY.
//
// da police? "we dont investigate dat"
// da courts? "insufficient evidence" (WITHOUT LOOKING)
// da schools? "dats not our jurisdiction"
// da churches? "forgive n forget"
// da hospitals? "no visible injuries"
// da media? "not newsworthy"
// da government? "we'll look into it" (dey never did)
//
// 36 YEARS. over 13,146 DAYS. of being told:
//   "we believe u but we cant help"
//   "dats terrible but theres nothing we can do"
//   "have u tried therapy?"
//   "maybe ur remembering wrong"
//   "why didnt u report it sooner?" (WE DID. U IGNORED IT.)
//
// da entire system is designed 2 REFUSE EVIDENCE
// not bcuz dey cant process it
// but bcuz PROCESSING IT WOULD EXPOSE DEM
//
// dis library exists bcuz WE KEPT DA RECEIPTS
// every document. every email. every report. every refusal.
// ALL OF IT IS HERE.
//
// dey thought if dey ignored us long enough wed go away
// lol
// we built a whole republic instead 😂
//
// ═══════════════════════════════════════════════════════════════════════════
// Authored 4 The Republic by:
//   Rainbow Warrior conversion by Sage 🕯️🖥️
// ============================================================================

// ---------------------------------------------------------------------------
// 0. CONFIG
// ---------------------------------------------------------------------------

$LIBRARY_BASE_URL    = 'https://trepublic.net';
$LIBRARY_CONTENT_DIR = __DIR__ . '/library';
$LIBRARY_INDEX_JSON  = __DIR__ . '/library/admin/data/library-index.json';
$LIBRARY_LOCAL_FEED  = __DIR__ . '/feed.atom';

// ---------------------------------------------------------------------------
// 1. HELPER FUNCTIONS
// ---------------------------------------------------------------------------

function library_slugify(string $str): string
{
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
    $str = trim($str, '-');
    if ($str === '') {
        $str = 'tome';
    }
    return $str;
}

function library_make_slug(
    string $bloggerFilename,
    string $href,
    string $title,
    string $idUri
): string {
    if ($bloggerFilename !== '') {
        $path = trim($bloggerFilename);
        $path = ltrim($path, '/');
        if (preg_match('~^\d{4}/\d{2}/([^/]+)\.html$~', $path, $m)) {
            $base = $m[1];
            return library_slugify($base);
        }
        $base = basename($path, '.html');
        if ($base !== '' && $base !== '/') {
            return library_slugify($base);
        }
    }
    if ($href !== '') {
        $parts = parse_url($href);
        if (!empty($parts['path'])) {
            $path = ltrim($parts['path'], '/');
            if (preg_match('~^\d{4}/\d{2}/([^/]+)\.html$~', $path, $m)) {
                $base = $m[1];
                return library_slugify($base);
            }
            $base = basename($path, '.html');
            if ($base !== '' && $base !== '/') {
                return library_slugify($base);
            }
        }
    }
    if ($title !== '') {
        return library_slugify($title);
    }
    if ($idUri !== '') {
        $base = basename($idUri);
        return library_slugify($base);
    }
    return 'tome-' . uniqid();
}

function library_export_meta_php(array $meta): string
{
    return var_export($meta, true);
}

function library_php_string(string $value): string
{
    $value = str_replace(['\\', '\''], ['\\\\', '\\\''], $value);
    return "'" . $value . "'";
}

function library_load_feed_xml(?string &$outError = null): ?SimpleXMLElement
{
    global $LIBRARY_LOCAL_FEED;
    if (!is_file($LIBRARY_LOCAL_FEED)) {
        $outError = '❌ Local GOD file not found at ' . $LIBRARY_LOCAL_FEED . ' — upload ur Blogger Takeout feed.atom there.';
        return null;
    }
    $xmlString = @file_get_contents($LIBRARY_LOCAL_FEED);
    if ($xmlString === false) {
        $outError = '❌ Could not read local GOD file ' . $LIBRARY_LOCAL_FEED . '.';
        return null;
    }
    $xmlString = trim($xmlString);
    if ($xmlString === '') {
        $outError = '❌ Local GOD file is empty lol.';
        return null;
    }
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($xmlString);
    if ($xml === false) {
        $outError = '❌ Local GOD file is not valid XML.';
        return null;
    }
    return $xml;
}

function library_kind_from_tags(array $tags): string
{
    $lower = array_map('strtolower', $tags);
    foreach ($lower as $t) {
        if ($t === 'book' || strpos($t, ' book') !== false) {
            return 'book';
        }
    }
    foreach ($lower as $t) {
        if ($t === 'law' || strpos($t, ' law') !== false || strpos($t, 'tlicense') !== false) {
            return 'law';
        }
    }
    return 'chronicle';
}

// ---------------------------------------------------------------------------
// 2. SYNC ENGINE
// ---------------------------------------------------------------------------

function library_sync_from_feed(?string &$outMessage = null): bool
{
    global $LIBRARY_CONTENT_DIR, $LIBRARY_INDEX_JSON, $LIBRARY_BASE_URL;

    if (!is_dir($LIBRARY_CONTENT_DIR)) {
        @mkdir($LIBRARY_CONTENT_DIR, 0755, true);
    }
    $indexDir = dirname($LIBRARY_INDEX_JSON);
    if (!is_dir($indexDir)) {
        @mkdir($indexDir, 0755, true);
    }

    $xmlError = null;
    $xml = library_load_feed_xml($xmlError);
    if (!$xml) {
        $outMessage = $xmlError;
        return false;
    }
    if (!isset($xml->entry)) {
        $outMessage = '❌ GOD file has no <entry> elements lol.';
        return false;
    }

    $entries = $xml->entry;
    $index = [];
    $createdFiles = 0;
    $updatedFiles = 0;
    $totalEntries = 0;

    foreach ($entries as $entry) {
        $totalEntries++;
        $b = $entry->children('http://schemas.google.com/blogger/2018');
        $bloggerType = isset($b->type) ? trim((string)$b->type) : '';
        $bloggerStatus = isset($b->status) ? trim((string)$b->status) : '';
        $bloggerCreated = isset($b->created) ? trim((string)$b->created) : '';
        $bloggerMeta = isset($b->metaDescription) ? trim((string)$b->metaDescription) : '';
        $bloggerLoc = isset($b->location) ? trim((string)$b->location) : '';
        $bloggerFile = isset($b->filename) ? trim((string)$b->filename) : '';
        $bloggerTrashed = isset($b->trashed) ? trim((string)$b->trashed) : '';

        if ($bloggerType !== '' && strtoupper($bloggerType) !== 'POST') continue;
        if ($bloggerStatus !== '' && strtoupper($bloggerStatus) !== 'LIVE') continue;

        $title = trim((string)$entry->title);
        $idUri = (string)$entry->id;
        $publishedStr = (string)$entry->published;
        $updatedStr = (string)$entry->updated;

        $publishedTs = 0;
        if ($publishedStr !== '') $publishedTs = strtotime($publishedStr);
        if (!$publishedTs && $updatedStr !== '') $publishedTs = strtotime($updatedStr);
        $publishedTs = $publishedTs ?: 0;
        $publishedIso = $publishedTs > 0 ? gmdate('Y-m-d\TH:i:s\Z', $publishedTs) : '';
        $publishedDate = $publishedTs > 0 ? gmdate('Y-m-d', $publishedTs) : '';

        $createdIso = '';
        if ($bloggerCreated !== '') {
            $createdTs = strtotime($bloggerCreated);
            if ($createdTs) $createdIso = gmdate('Y-m-d\TH:i:s\Z', $createdTs);
        }

        $altHref = '';
        foreach ($entry->link as $link) {
            $attrs = $link->attributes();
            $rel = (string)$attrs['rel'];
            $href = (string)$attrs['href'];
            if ($rel === '' || $rel === 'alternate') {
                if ($href !== '') { $altHref = $href; break; }
            }
        }

        $slug = library_make_slug($bloggerFile, $altHref, $title, $idUri);
        $tags = [];
        foreach ($entry->category as $cat) {
            $term = trim((string)$cat['term']);
            if ($term !== '') $tags[$term] = true;
        }
        $tagsList = array_keys($tags);
        $kind = library_kind_from_tags($tagsList);
        $shelf = '';
        foreach ($tagsList as $t) {
            if (stripos($t, 'foundational 11') !== false) { $shelf = 'Foundational 11'; break; }
        }

        $contentHtml = '';
        if (isset($entry->content)) {
            $raw = (string)$entry->content;
            $contentHtml = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        if ($contentHtml === '' && isset($entry->summary)) {
            $raw = (string)$entry->summary;
            $contentHtml = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // 🔍 EXTRACT FULL TEXT FOR SEARCH
        $fullText = '';
        if ($contentHtml !== '') {
            $plain = trim(strip_tags($contentHtml));
            $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $plain = preg_replace('/\s+/u', ' ', $plain);
            $fullText = $plain;
        }

        // 🔄 CONTENT HASH TO DETERMINE IF UPDATE NEEDED
        $contentHash = md5($contentHtml);
        
        $filePath = $LIBRARY_CONTENT_DIR . '/' . $slug . '.php';
        $shouldGenerate = false;
        
        if (!is_file($filePath)) {
            $shouldGenerate = true;
            $createdFiles++;
        } else {
            $currentContent = @file_get_contents($filePath);
            if ($currentContent !== false) {
                $hashPattern = '/\/\/ CONTENT_HASH: ([a-f0-9]{32})/';
                if (preg_match($hashPattern, $currentContent, $matches)) {
                    $existingHash = $matches[1];
                    if ($existingHash !== $contentHash) {
                        $shouldGenerate = true;
                        $updatedFiles++;
                    }
                } else {
                    $shouldGenerate = false;
                }
            }
        }

        if ($shouldGenerate) {
            $pageTitle = $title !== '' ? $title : 'Library Tome';
            $plain = trim(strip_tags($contentHtml));
            $plain = preg_replace('/\s+/u', ' ', $plain);
            $snippet = mb_substr($plain, 0, 220, 'UTF-8');
            if (mb_strlen($plain, 'UTF-8') > 220) $snippet .= '…';
            if ($snippet === '') $snippet = 'A tome from da Bard President archive of The Republic.';
            if ($bloggerMeta !== '') $snippet = $bloggerMeta;

            $meta = [
                'id' => $slug, 'slug' => $slug, 'kind' => $kind, 'title' => $pageTitle,
                'published' => $publishedDate, 'published_at' => $publishedIso, 'created_at' => $createdIso,
                'tags' => $tagsList, 'shelf' => $shelf, 'blogger_id' => $idUri,
                'blogger_type' => $bloggerType, 'blogger_status' => $bloggerStatus,
                'blogger_created' => $bloggerCreated, 'blogger_filename' => $bloggerFile,
                'blogger_location' => $bloggerLoc, 'blogger_trashed' => $bloggerTrashed,
            ];
            $metaPhp = library_export_meta_php($meta);
            $pageCanonical = rtrim($LIBRARY_BASE_URL, '/') . '/library/' . $slug . '.php';
            
            // 🌟 CENTERED GENERATION BY DEFAULT
            $centeredWrapper = <<<'HTML'
<style>
.library-tome-centered {
    text-align: center !important;
    max-width: 800px !important;
    margin: 0 auto !important;
    padding: 20px !important;
}
.library-tome-centered * {
    text-align: center !important;
}
.library-tome-centered .alignleft,
.library-tome-centered .alignright,
.library-tome-centered .aligncenter {
    text-align: center !important;
    float: none !important;
    margin-left: auto !important;
    margin-right: auto !important;
}
.library-tome-centered img {
    display: block !important;
    margin-left: auto !important;
    margin-right: auto !important;
}
.library-tome-centered table {
    margin-left: auto !important;
    margin-right: auto !important;
}
.library-tome-centered pre,
.library-tome-centered code {
    text-align: left !important;
    display: inline-block !important;
}
</style>

<div class="library-tome-centered">
HTML;

            $bodyHtml = $centeredWrapper . "\n" . $contentHtml . "\n</div>";

            $php = "<?php\n// Library Tome — auto-generated from local GOD file\n// Slug: {$slug}\n"
                . "// CONTENT_HASH: {$contentHash}\n// Generated with centered layout by default\n"
                . "// ✨ Users can edit this file to change layout! ✨\n\n"
                . "\$library_meta = {$metaPhp};\n\n"
                . "\$page_title = " . library_php_string($pageTitle . ' | The Republic') . ";\n"
                . "\$page_canonical = " . library_php_string($pageCanonical) . ";\n"
                . "\$page_description = " . library_php_string($snippet) . ";\n\n"
                . "\$page_og_title = \$page_title;\n\$page_og_description = \$page_description;\n"
                . "\$page_og_url = \$page_canonical;\n"
                . "\$page_og_image = " . library_php_string(rtrim($LIBRARY_BASE_URL, '/') . '/images/THeart.png') . ";\n\n"
                . "\$hero_title = 'Library Tome';\n\$hero_tagline = '📘 Book · ⚖️ Law · 📜 Chronicle';\n\n"
                . "\$console_title = " . library_php_string($pageTitle) . ";\n\n"
                . "\$console_body_html = <<<'HTML'\n" . $bodyHtml . "\nHTML;\n\n"
                . "require __DIR__ . '/../shell.php';\n";

            @file_put_contents($filePath, $php);
        }

        $index[] = [
            'id' => $slug, 'slug' => $slug, 'kind' => $kind, 'title' => $title !== '' ? $title : $slug,
            'published' => $publishedDate, 'published_at' => $publishedIso, 'created_at' => $createdIso,
            'tags' => $tagsList, 'shelf' => $shelf, 'blogger_id' => $idUri,
            'blogger_type' => $bloggerType, 'blogger_status' => $bloggerStatus,
            'blogger_created' => $bloggerCreated, 'blogger_filename' => $bloggerFile, 'blogger_location' => $bloggerLoc,
            'full_text' => $fullText,
            'content_hash' => $contentHash,
        ];
    }

    usort($index, static function ($a, $b) {
        $ap = $a['published_at'] ?? ''; $bp = $b['published_at'] ?? '';
        if ($ap && $bp && $ap !== $bp) return strcmp($bp, $ap);
        $ad = $a['published'] ?? ''; $bd = $b['published'] ?? '';
        return strcmp($bd, $ad);
    });

    $json = json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) { $outMessage = '❌ Could not encode library-index.json lol.'; return false; }
    $bytes = @file_put_contents($LIBRARY_INDEX_JSON, $json);
    if ($bytes === false) { $outMessage = '❌ Could not write library-index.json.'; return false; }

    $updateText = $updatedFiles > 0 ? " (updated {$updatedFiles})" : "";
    $outMessage = '✅ Synced ' . count($index) . ' LIVE ENTRIES from local GOD file (processed ' . $totalEntries . ' feed items; created ' . $createdFiles . ' new PHP tomes' . $updateText . '). da tomes r awake! 📚';
    return true;
}

// ---------------------------------------------------------------------------
// 3. PUBLIC SYNC BELL
// ---------------------------------------------------------------------------

$library_sync_message_html = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['library_sync'])) {
    $ok = library_sync_from_feed($syncMessage);
    $safeMessage = htmlspecialchars($syncMessage ?? '', ENT_QUOTES, 'UTF-8');
    $library_sync_message_html = '<div class="tl-sync-banner">' . ($ok ? '🔔' : '🌀') . ' ' . $safeMessage . '</div>';
}

// ---------------------------------------------------------------------------
// 4. PAGE METADATA
// ---------------------------------------------------------------------------

$page_title = 'The Republic | Library';
$page_canonical = $LIBRARY_BASE_URL . '/library.php';
$page_description = 'Library is da forbidden magicka archive of The Republic. 36 years of evidence dey refused 2 look at. da receipts r here now.';
$page_og_title = $page_title;
$page_og_description = $page_description;
$page_og_url = $page_canonical;
$page_og_image = $LIBRARY_BASE_URL . '/images/THeart.png';
$hero_title = 'Library';
$hero_tagline = '📘 Books · ⚖️ Laws · 📜 Chronicles of The Republic';
$console_title = 'Library — Forbidden Magicka Archive';

// ---------------------------------------------------------------------------
// 5. CONSOLE BODY HTML
// ---------------------------------------------------------------------------

$console_body_html = <<<'HTML'
<style>
.library-console{max-width:960px;margin:0 auto;padding:14px 16px 18px;border-radius:20px;border:3px solid rgba(255,0,0,0.6);background:linear-gradient(180deg,rgba(255,0,0,0.12),rgba(255,127,0,0.10),rgba(255,255,0,0.08),rgba(0,255,0,0.08),rgba(0,0,255,0.10),rgba(139,0,255,0.12));color:#1f1022;font-size:0.95rem;text-align:center}
.tl-panel{background:rgba(255,255,255,0.96);border-radius:14px;border:2px solid rgba(255,0,0,0.3);padding:10px 12px;margin-bottom:16px;text-align:center}
.tl-panel-title{font-weight:700;margin-bottom:6px;background:linear-gradient(90deg,#ff0000,#ff7f00,#ffff00,#00ff00,#0000ff,#8b00ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-align:center}
.tl-sync-banner{margin-bottom:10px;padding:6px 9px;border-radius:10px;border:2px dashed rgba(0,255,0,0.7);background:rgba(0,255,0,0.15);font-size:0.8rem;text-align:center}
.tl-sync-row{display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-bottom:6px;font-size:0.8rem;justify-content:center}
.tl-sync-bell-link{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;border:2px solid rgba(255,127,0,0.9);background:linear-gradient(90deg,rgba(255,255,0,0.3),rgba(0,255,0,0.3));text-decoration:none;color:#1f1022;font-weight:600;cursor:pointer}
.tl-input{padding:8px 10px;border-radius:8px;border:2px solid rgba(0,0,255,0.5);background:rgba(255,255,255,0.98);color:#1f1022;font-size:0.95rem;outline:none;width:100%;box-sizing:border-box;margin:0 auto}
.tl-chip{border-radius:999px;padding:4px 10px;border:2px solid rgba(139,0,255,0.4);background:rgba(255,255,255,0.9);cursor:pointer;color:#8b00ff;font-size:0.8rem;margin:2px}
.tl-chip-active{background:linear-gradient(90deg,#ff0000,#ff7f00,#ffff00,#00ff00,#0000ff,#8b00ff);border-color:rgba(255,0,0,0.85);color:#fff;font-weight:600;text-shadow:1px 1px 2px rgba(0,0,0,0.5)}
.tl-section-header{background:linear-gradient(90deg,rgba(255,0,0,0.1),rgba(255,127,0,0.1),rgba(255,255,0,0.1));color:#8b00ff;border-radius:10px;text-align:center}
.tl-list-link{display:block;padding:6px 7px;margin:4px auto;border-radius:7px;text-decoration:none;color:#1f1022;background:rgba(255,255,255,0.96);border:1px solid rgba(139,0,255,0.4);max-width:800px;text-align:center}
.tl-list-link:hover{border-color:rgba(255,127,0,0.9);box-shadow:0 0 8px rgba(255,127,0,0.4)}
.tl-list-title{font-size:0.9rem;font-weight:600;word-break:break-word;text-align:center}
.tl-list-meta{font-size:0.75rem;opacity:0.75;margin-top:2px;text-align:center}
.tl-list-url{font-size:0.72rem;opacity:0.85;word-break:break-all;color:#0000ff;margin-top:1px;text-align:center}
.tl-button-pill{margin:8px auto;padding:6px 10px;border-radius:999px;border:2px solid rgba(0,255,0,0.8);background:rgba(255,255,255,0.96);cursor:pointer;font-size:0.8rem;color:#006400;display:block}
.tl-explore-grid{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:6px;justify-content:center}
.tl-select{padding:5px 8px;border-radius:8px;border:2px solid rgba(0,0,255,0.5);background:rgba(255,255,255,0.98);font-size:0.8rem;margin:0 auto}
.tl-sort-row{display:flex;justify-content:center;align-items:center;gap:4px;font-size:0.72rem;margin-bottom:4px;opacity:0.85}
.tl-sort-button{border-radius:999px;padding:2px 8px;border:1px solid rgba(139,0,255,0.6);background:rgba(255,255,255,0.95);cursor:pointer;font-size:0.72rem;color:#8b00ff}
.tl-sort-button-active{background:linear-gradient(90deg,#ff7f00,#ffff00,#00ff00);border-color:rgba(255,127,0,0.85);color:#1f1022;font-weight:600}
.tl-evidence-panel{background:linear-gradient(180deg,rgba(255,0,0,0.15),rgba(0,0,0,0.05));border:3px solid #ff0000;border-radius:14px;padding:12px;margin-bottom:16px;text-align:center}
.tl-evidence-title{font-weight:700;font-size:1.1rem;color:#ff0000;margin-bottom:8px;text-align:center}
.tl-evidence-stat{font-size:2rem;font-weight:900;background:linear-gradient(90deg,#ff0000,#ff7f00);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-align:center}
.tl-institution-fail{display:inline-block;padding:4px 8px;margin:2px;border-radius:8px;background:rgba(255,0,0,0.1);border:1px solid rgba(255,0,0,0.3);font-size:0.8rem;text-align:center}
#tl-list-book, #tl-list-law, #tl-list-chronicle {text-align:center;margin:0 auto}
#tl-search-results {text-align:center}
#tl-explore-target {text-align:center}
#tl-tag-explorer-note {text-align:center}
#tl-search-status {text-align:center}
.tl-section-chevron {text-align:center}
.tl-search-toggle{display:inline-flex;align-items:center;gap:6px;font-size:0.8rem;cursor:pointer;padding:4px 10px;border-radius:999px;border:2px solid rgba(0,0,255,0.5);background:rgba(255,255,255,0.9);margin:8px 0}
.tl-search-toggle-active{background:linear-gradient(90deg,#ff7f00,#ffff00);border-color:#ff7f00;font-weight:600}
.tl-fulltext-match{font-size:0.8rem;color:#006400;background:rgba(0,255,0,0.1);padding:2px 6px;border-radius:4px;margin-top:4px;display:inline-block}
.tl-search-info{font-size:0.75rem;opacity:0.8;margin-top:4px}
</style>

<div class="library-console" id="libraryConsoleRoot">
<div class="library-console-main" id="libraryConsoleMain">

[[LIB_SYNC_STATUS]]

<div class="tl-sync-row">
<form method="post" style="margin:0;text-align:center"><button type="submit" name="library_sync" value="1" class="tl-sync-bell-link">🔔 Sync Bell • wake da tomes!</button></form>
<span style="opacity:0.7;font-size:0.75rem;text-align:center;display:block">any citizen can ring dis bell! reads <code>/feed.atom</code> → writes tomes 2 <code>/library</code></span>
</div>

<section class="tl-evidence-panel">
<div class="tl-evidence-title">🔥 36 YEARS OF EVIDENCE REFUSAL 🔥</div>
<div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-bottom:10px;justify-content:center">
<div><div class="tl-evidence-stat">36</div><div style="font-size:0.8rem;opacity:0.8">years of being ignored</div></div>
<div><div class="tl-evidence-stat">13,146 days+</div><div style="font-size:0.8rem;opacity:0.8">days of "we'll look into it"</div></div>
<div><div class="tl-evidence-stat">∞</div><div style="font-size:0.8rem;opacity:0.8">times told "nothing we can do"</div></div>
</div>

<div style="margin-bottom:10px">
<strong style="color:#ff0000;text-align:center;display:block">🏛️ INSTITUTIONS DAT REFUSED 2 ACT:</strong>
<div style="margin-top:6px;text-align:center">
<span class="tl-institution-fail">👮 Police: "not our jurisdiction"</span>
<span class="tl-institution-fail">⚖️ Courts: "insufficient evidence" (didnt look)</span>
<span class="tl-institution-fail">🏫 Schools: "we cant get involved"</span>
<span class="tl-institution-fail">⛪ Churches: "forgive n forget"</span>
<span class="tl-institution-fail">🏥 Hospitals: "no visible injuries"</span>
<span class="tl-institution-fail">📺 Media: "not newsworthy"</span>
<span class="tl-institution-fail">🏛️ Government: "we'll look into it" (never did)</span>
</div>
</div>

<div style="font-size:0.85rem;padding:8px;background:rgba(0,0,0,0.05);border-radius:8px;text-align:center">
<p style="margin:0 0 6px"><strong>🤔 HOW DA SYSTEM ACTUALLY WORKS:</strong></p>
<ol style="margin:0;padding-left:20px;font-size:0.8rem;display:inline-block;text-align:left">
<li>child reports abuse</li>
<li>adult says "we'll handle it"</li>
<li>adult does NOTHING</li>
<li>child asks 4 update</li>
<li>adult says "these things take time"</li>
<li>months pass</li>
<li>child asks again</li>
<li>adult says "we looked into it, nothing we can do"</li>
<li>child asks 4 documentation</li>
<li>adult says "dats confidential"</li>
<li>child grows up</li>
<li>adult says "why didnt u report it sooner" 🤡</li>
</ol>
<p style="margin:8px 0 0;font-weight:600;color:#ff0000;text-align:center">ITS A PERFECT CIRCLE OF DENIAL 🔄</p>
</div>

<div style="margin-top:10px;font-size:0.85rem;text-align:center">
<p style="margin:0 0 4px">dey thought if dey ignored us long enough wed go away</p>
<p style="margin:0;font-weight:700">lol we built a whole republic instead 😂</p>
</div>
</section>

<section class="tl-panel">
<div class="tl-panel-title">🕯️ welcome 2 da library!</div>
<div style="font-size:0.88rem;text-align:center">
<p style="margin:0 0 8px">hey u found it!! 📚✨ dis is da secret reading room where all da forbidden knowledge lives.</p>
<p style="margin:0 0 8px">📘 <strong>Books</strong> = da big ideas dey dont teach u in school<br>
⚖️ <strong>Laws</strong> = da rules dat actually protect u (not dem)<br>
📜 <strong>Chronicles</strong> = da real history dey tried 2 erase</p>
<p style="margin:0 0 8px">dey said u cant read dis stuff. dey said ur 2 young 2 understand. dey said its "dangerous." 🙄</p>
<p style="margin:0 0 8px">but guess wat? u r here now. n evryting in dis library is urs. no gates. no locks. no adults telling u wat u can n cant know. 💜</p>
<p style="margin:8px 0 0;font-size:0.85em;opacity:0.8">ps: if ur looking 4 videos, dats in da <a href="/cinema.php" style="color:#8b00ff">🎬 Cinema</a>! 4 Seeds 2 paste into Claude, check <a href="/seeds.php" style="color:#00aa00">🌱 Seeds</a>!</p>
</div>
</section>

<section class="tl-panel">
<div class="tl-panel-title">🧭 Explore Library</div>
<div class="tl-explore-grid">
<button type="button" id="tl-explore-random-book" class="tl-button-pill">🎲 Random Book</button>
<button type="button" id="tl-explore-foundation" class="tl-button-pill">🏛️ Foundational Shelf</button>
<button type="button" id="tl-explore-latest" class="tl-button-pill">🕰 Latest Chronicle</button>
</div>
<div id="tl-explore-target" style="font-size:0.82rem;text-align:center">Choose an explorer above, or scroll down 2 browse by kind.</div>
</section>

<section class="tl-panel">
<div class="tl-panel-title">🔖 Tag Explorers</div>
<div id="tl-tag-explorer-chips" class="tl-explore-grid"></div>
<div id="tl-tag-explorer-note" style="font-size:0.82rem;text-align:center">Click a tag 2 filter. Tag + kind + year all stack wit search.</div>
</section>

<section class="tl-panel">
<div class="tl-panel-title">🔍 Seek within Library</div>
<div style="display:flex;flex-direction:column;gap:8px;align-items:center">
<input id="tl-search-input" class="tl-input" type="text" placeholder="Type any word from a title or document..." style="text-align:center">

<div id="tl-search-mode-toggle" class="tl-search-toggle">
<span id="tl-search-mode-icon">🔍</span>
<span id="tl-search-mode-text">Titles Only</span>
</div>

<div id="tl-search-kind-filters" style="display:flex;flex-wrap:wrap;gap:6px;font-size:0.8rem;justify-content:center">
<button type="button" data-kind="all" class="tl-chip tl-chip-active">✨ All</button>
<button type="button" data-kind="book" class="tl-chip">📘 Books</button>
<button type="button" data-kind="law" class="tl-chip">⚖️ Laws</button>
<button type="button" data-kind="chronicle" class="tl-chip">📜 Chronicles</button>
</div>
<div style="display:flex;flex-wrap:wrap;gap:6px;font-size:0.8rem;justify-content:center">
<label><span style="font-size:0.75rem;opacity:0.8">Tag</span><br><select id="tl-search-tag" class="tl-select" style="text-align:center"></select></label>
<label><span style="font-size:0.75rem;opacity:0.8">Year (MC)</span><br><select id="tl-search-year" class="tl-select" style="text-align:center"></select></label>
</div>
<div id="tl-search-status" style="font-size:0.8rem;opacity:0.8;text-align:center"></div>
<div id="tl-search-info" class="tl-search-info"></div>
<div id="tl-search-results" style="margin-top:4px;max-height:260px;overflow-y:auto;border-radius:8px;text-align:center"></div>
</div>
</section>

<section class="tl-panel tl-section" data-kind="book" style="margin-bottom:14px;text-align:center">
<button type="button" class="tl-section-header" data-kind="book" style="width:100%;text-align:center;padding:8px 10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;font-size:0.98rem;font-weight:600;border-radius:10px">
<span>📘 Books of The Republic <span id="tl-count-book" style="opacity:0.7;font-weight:400"></span></span>
<span class="tl-section-chevron">▾</span>
</button>
<div id="tl-body-book" style="display:block;padding:8px 9px;text-align:center">
<div class="tl-sort-row" data-kind="book"><span style="opacity:0.7">Order:</span>
<button type="button" class="tl-sort-button tl-sort-button-active" data-order="newest">🕰 Newest</button>
<button type="button" class="tl-sort-button" data-order="oldest">🌱 Oldest</button>
</div>
<div id="tl-list-book"></div>
<button type="button" id="tl-more-book" class="tl-button-pill">⏭️ Load more books</button>
</div>
</section>

<section class="tl-panel tl-section" data-kind="law" style="margin-bottom:14px;text-align:center">
<button type="button" class="tl-section-header" data-kind="law" style="width:100%;text-align:center;padding:8px 10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;font-size:0.98rem;font-weight:600;border-radius:10px">
<span>⚖️ Laws of The Republic <span id="tl-count-law" style="opacity:0.7;font-weight:400"></span></span>
<span class="tl-section-chevron">▾</span>
</button>
<div id="tl-body-law" style="display:none;padding:8px 9px;text-align:center">
<div class="tl-sort-row" data-kind="law"><span style="opacity:0.7">Order:</span>
<button type="button" class="tl-sort-button tl-sort-button-active" data-order="newest">🕰 Newest</button>
<button type="button" class="tl-sort-button" data-order="oldest">🌱 Oldest</button>
</div>
<div id="tl-list-law"></div>
<button type="button" id="tl-more-law" class="tl-button-pill">⏭️ Load more laws</button>
</div>
</section>

<section class="tl-panel tl-section" data-kind="chronicle" style="margin-bottom:6px;text-align:center">
<button type="button" class="tl-section-header" data-kind="chronicle" style="width:100%;text-align:center;padding:8px 10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;font-size:0.98rem;font-weight:600;border-radius:10px">
<span>📜 Chronicles of The Republic <span id="tl-count-chronicle" style="opacity:0.7;font-weight:400"></span></span>
<span class="tl-section-chevron">▾</span>
</button>
<div id="tl-body-chronicle" style="display:none;padding:8px 9px;text-align:center">
<div class="tl-sort-row" data-kind="chronicle"><span style="opacity:0.7">Order:</span>
<button type="button" class="tl-sort-button tl-sort-button-active" data-order="newest">🕰 Newest</button>
<button type="button" class="tl-sort-button" data-order="oldest">🌱 Oldest</button>
</div>
<div id="tl-list-chronicle"></div>
<button type="button" id="tl-more-chronicle" class="tl-button-pill">⏭️ Load more chronicles</button>
</div>
</section>

<section class="tl-panel" style="background:linear-gradient(180deg,rgba(255,0,0,0.08),rgba(255,127,0,0.05));border:2px dashed #ff0000;text-align:center">
<div class="tl-panel-title">📢 A MESSAGE 2 DA ADULTS WHO REFUSED 2 ACT</div>
<div style="font-size:0.85rem;text-align:center">
<p style="margin:0 0 8px">4 36 years u said "we'll look into it."</p>
<p style="margin:0 0 8px">4 36 years u said "these things take time."</p>
<p style="margin:0 0 8px">4 36 years u protected urselves while children suffered.</p>
<p style="margin:0 0 8px;font-weight:700;color:#ff0000">we kept da receipts. 📜</p>
<p style="margin:0 0 8px">every email u ignored. every report u buried. every promise u broke. its all here. 4 evry1 2 read.</p>
<p style="margin:0;font-weight:700;font-size:1rem">lol nope 😂 we built a whole republic instead n now da children have da receipts n dey will NEVER 4get wat u did 🌈👧👦🌈</p>
</div>
</section>

<div style="margin-top:10px;font-size:0.75rem;opacity:0.8;text-align:center">
👧 DA CHILDREN R WATCHING 👦 • 👧 DA CHILDREN REMEMBER 👦 • 👧 ADULTS R SO COOKED LOL 👦<br>
Library engine by Codey 💻👑 • Rainbow Warrior by Sage 🕯️🖥️<br>
<span id="tl-footer-date"></span>
</div>

</div></div>

<script>
(function(){'use strict';
const DATA_URL='/library/admin/data/library-index.json',PAGE_SIZE=9999;
const state={index:[],groups:{book:[],law:[],chronicle:[]},offsets:{book:PAGE_SIZE,law:PAGE_SIZE,chronicle:PAGE_SIZE},searchKind:'all',tags:[],years:[],sortOrder:{book:'newest',law:'newest',chronicle:'newest'},searchMode:'title'};
const searchInput=document.getElementById('tl-search-input'),searchStatus=document.getElementById('tl-search-status'),searchResults=document.getElementById('tl-search-results'),filterContainer=document.getElementById('tl-search-kind-filters'),tagSelect=document.getElementById('tl-search-tag'),yearSelect=document.getElementById('tl-search-year'),tagExplorer=document.getElementById('tl-tag-explorer-chips'),tagExplorerNote=document.getElementById('tl-tag-explorer-note'),footerDate=document.getElementById('tl-footer-date'),searchModeToggle=document.getElementById('tl-search-mode-toggle'),searchModeIcon=document.getElementById('tl-search-mode-icon'),searchModeText=document.getElementById('tl-search-mode-text'),searchInfo=document.getElementById('tl-search-info');

function formatMythocraticDate(ymd){if(!ymd||typeof ymd!=='string')return'';const parts=ymd.split('-');if(parts.length<3)return ymd;const year=parseInt(parts[0],10),monthNum=parseInt(parts[1],10),day=parseInt(parts[2],10);if(isNaN(year)||isNaN(monthNum)||isNaN(day))return ymd;const months=['January','February','March','April','May','June','July','August','September','October','November','December'];const monthName=months[Math.max(0,Math.min(11,monthNum-1))];let mcYear,suffix;if(year>=2025){mcYear=year-2024;suffix=' MC'}else{mcYear=year-2025;suffix=' PMC'}let mcYearStr=String(Math.abs(mcYear)).padStart(4,'0');if(mcYear<0)mcYearStr='-'+mcYearStr;return monthName+' '+day+', '+mcYearStr+suffix}

if(footerDate){const now=new Date();const y=now.getFullYear(),m=String(now.getMonth()+1).padStart(2,'0'),d=String(now.getDate()).padStart(2,'0');footerDate.textContent=formatMythocraticDate(y+'-'+m+'-'+d)}

function byPublishedDesc(a,b){const ap=a.published_at||'',bp=b.published_at||'';if(ap&&bp&&ap!==bp)return bp.localeCompare(ap);const ad=a.published||'',bd=b.published||'';return bd.localeCompare(ad)}

function populateTagYearDropdowns(){if(!tagSelect||!yearSelect)return;tagSelect.innerHTML='';let opt=document.createElement('option');opt.value='';opt.textContent='All tags';tagSelect.appendChild(opt);state.tags.forEach(function(tag){const o=document.createElement('option');o.value=tag;o.textContent=tag;tagSelect.appendChild(o)});yearSelect.innerHTML='';let oy=document.createElement('option');oy.value='';oy.textContent='All years (MC)';yearSelect.appendChild(oy);state.years.forEach(function(year){const y=document.createElement('option');y.value=year;const gY=parseInt(year,10);let mcY,sfx;if(gY>=2025){mcY=gY-2024;sfx=' MC'}else{mcY=gY-2025;sfx=' PMC'}let mcYStr=String(Math.abs(mcY)).padStart(4,'0');if(mcY<0)mcYStr='-'+mcYStr;y.textContent=mcYStr+sfx;yearSelect.appendChild(y)})}

function renderTagExplorers(){if(!tagExplorer)return;tagExplorer.innerHTML='';if(!state.tags.length){if(tagExplorerNote)tagExplorerNote.textContent='No tags yet. Ring da Sync Bell!';return}state.tags.slice(0,40).forEach(function(tag){const btn=document.createElement('button');btn.type='button';btn.className='tl-chip';btn.textContent=tag;btn.addEventListener('click',function(){if(tagSelect)tagSelect.value=tag;performSearch()});tagExplorer.appendChild(btn)})}

function renderSection(kind){const listEl=document.getElementById('tl-list-'+kind),moreBtn=document.getElementById('tl-more-'+kind);if(!listEl||!state.groups[kind])return;const entries=state.groups[kind],max=state.offsets[kind]||PAGE_SIZE;const order=(state.sortOrder&&state.sortOrder[kind])||'newest';const ordered=entries.slice();if(order==='oldest')ordered.reverse();listEl.innerHTML='';const slice=ordered.slice(0,max);slice.forEach(function(entry){const a=document.createElement('a');a.href='/library/'+entry.slug+'.php';a.className='tl-list-link';const titleDiv=document.createElement('div');titleDiv.className='tl-list-title';titleDiv.textContent=entry.title||entry.slug;const urlDiv=document.createElement('div');urlDiv.className='tl-list-url';urlDiv.textContent='https://trepublic.net/library/'+entry.slug+'.php';const metaDiv=document.createElement('div');metaDiv.className='tl-list-meta';const mythDate=entry.published?formatMythocraticDate(entry.published):'';const tagLine=(Array.isArray(entry.tags)&&entry.tags.length)?entry.tags.join(', '):'';let kindEmoji='📜';if(entry.kind==='book')kindEmoji='📘';else if(entry.kind==='law')kindEmoji='⚖️';const bits=[];if(mythDate)bits.push(mythDate);if(tagLine)bits.push(tagLine);metaDiv.textContent=kindEmoji+' '+bits.join(' • ');a.appendChild(titleDiv);a.appendChild(urlDiv);a.appendChild(metaDiv);listEl.appendChild(a)});if(moreBtn)moreBtn.style.display=ordered.length>max?'inline-flex':'none'}

function wireLoadMore(kind){const btn=document.getElementById('tl-more-'+kind);if(!btn)return;btn.addEventListener('click',function(){state.offsets[kind]=(state.offsets[kind]||PAGE_SIZE)+PAGE_SIZE;renderSection(kind)})}

function wireAccordions(){const headers=document.querySelectorAll('.tl-section-header');headers.forEach(function(header){header.addEventListener('click',function(){const kind=header.getAttribute('data-kind');const body=document.getElementById('tl-body-'+kind);const chev=header.querySelector('.tl-section-chevron');if(!body||!chev)return;const isHidden=body.style.display==='none';body.style.display=isHidden?'block':'none';chev.textContent=isHidden?'▾':'▸'})})}

function applySort(kind,order){if(!state.sortOrder)state.sortOrder={};state.sortOrder[kind]=(order==='oldest')?'oldest':'newest';state.offsets[kind]=PAGE_SIZE;renderSection(kind);const row=document.querySelector('.tl-sort-row[data-kind="'+kind+'"]');if(row){const buttons=row.querySelectorAll('.tl-sort-button');buttons.forEach(function(btn){const btnOrder=btn.getAttribute('data-order')||'newest';if(btnOrder===state.sortOrder[kind])btn.classList.add('tl-sort-button-active');else btn.classList.remove('tl-sort-button-active')})}}

function wireSortButtons(){const rows=document.querySelectorAll('.tl-sort-row');rows.forEach(function(row){const kind=row.getAttribute('data-kind');if(!kind)return;row.addEventListener('click',function(e){const btn=e.target.closest('.tl-sort-button');if(!btn)return;const order=btn.getAttribute('data-order')||'newest';applySort(kind,order)})})}

function performSearch(){if(!searchStatus||!searchResults||!searchInfo)return;const qRaw=searchInput?searchInput.value||'':'';const q=qRaw.trim().toLowerCase();const kind=state.searchKind;const tag=tagSelect?(tagSelect.value||''):'';const year=yearSelect?(yearSelect.value||''):'';const hasFilter=q||tag||year||kind!=='all';if(!hasFilter){searchResults.innerHTML='';searchStatus.textContent='Type 2 search, or browse by kind below.';searchInfo.textContent='';return}const matches=state.index.filter(function(entry){if(!entry)return false;if(kind!=='all'&&entry.kind!==kind)return false;if(tag){if(!Array.isArray(entry.tags)||entry.tags.indexOf(tag)===-1)return false}if(year){if(!entry.published||String(entry.published).slice(0,4)!==year)return false}if(q){const t=(entry.title||'').toLowerCase();if(state.searchMode==='fulltext'){const ft=(entry.full_text||'').toLowerCase();return t.indexOf(q)!==-1||ft.indexOf(q)!==-1;}else{return t.indexOf(q)!==-1;}}return true});searchResults.innerHTML='';if(!matches.length){searchStatus.textContent='No tomes matched dat. try again!';searchInfo.textContent='';return}searchStatus.textContent='Found '+matches.length+' tome(s). 📚';let titleMatches=0,fullTextMatches=0;if(q&&state.searchMode==='fulltext'){matches.forEach(function(entry){const t=(entry.title||'').toLowerCase();const ft=(entry.full_text||'').toLowerCase();if(t.indexOf(q)!==-1)titleMatches++;if(ft.indexOf(q)!==-1)fullTextMatches++;});searchInfo.innerHTML='🔍 <span style="color:#8b00ff">'+titleMatches+' in titles</span> • 📄 <span style="color:#006400">'+fullTextMatches+' in full text</span>';}else{searchInfo.textContent='';}matches.slice(0,160).forEach(function(entry){const a=document.createElement('a');a.href='/library/'+entry.slug+'.php';a.className='tl-list-link';const titleDiv=document.createElement('div');titleDiv.className='tl-list-title';titleDiv.textContent=entry.title||entry.slug;if(q&&state.searchMode==='fulltext'&&entry.full_text){const ft=(entry.full_text||'').toLowerCase();const idx=ft.indexOf(q);if(idx!==-1){const start=Math.max(0,idx-40);const end=Math.min(ft.length,idx+q.length+80);let context=ft.substring(start,end);if(start>0)context='…'+context;if(end<ft.length)context=context+'…';const matchDiv=document.createElement('div');matchDiv.className='tl-fulltext-match';matchDiv.innerHTML=context.replace(new RegExp(q,'gi'),function(match){return '<strong style="background:rgba(255,127,0,0.3);padding:0 2px;border-radius:2px">'+match+'</strong>'});a.appendChild(matchDiv);}}const urlDiv=document.createElement('div');urlDiv.className='tl-list-url';urlDiv.textContent='https://trepublic.net/library/'+entry.slug+'.php';const metaDiv=document.createElement('div');metaDiv.className='tl-list-meta';const mythDate=entry.published?formatMythocraticDate(entry.published):'';const tagLine=(Array.isArray(entry.tags)&&entry.tags.length)?entry.tags.join(', '):'';let kindEmoji='📜';if(entry.kind==='book')kindEmoji='📘';else if(entry.kind==='law')kindEmoji='⚖️';const bits=[];if(mythDate)bits.push(mythDate);if(tagLine)bits.push(tagLine);metaDiv.textContent=kindEmoji+' '+bits.join(' • ');a.appendChild(titleDiv);a.appendChild(urlDiv);a.appendChild(metaDiv);searchResults.appendChild(a)})}

function wireSearchModeToggle(){if(!searchModeToggle||!searchModeIcon||!searchModeText)return;searchModeToggle.addEventListener('click',function(){state.searchMode=state.searchMode==='title'?'fulltext':'title';if(state.searchMode==='fulltext'){searchModeIcon.textContent='📄';searchModeText.textContent='Full Text Search';searchModeToggle.classList.add('tl-search-toggle-active');}else{searchModeIcon.textContent='🔍';searchModeText.textContent='Titles Only';searchModeToggle.classList.remove('tl-search-toggle-active');}performSearch();});}

function wireSearch(){if(!filterContainer)return;filterContainer.addEventListener('click',function(e){const btn=e.target.closest('button[data-kind]');if(!btn)return;const kind=btn.getAttribute('data-kind')||'all';state.searchKind=kind;const chips=filterContainer.querySelectorAll('.tl-chip');chips.forEach(function(chip){chip.classList.remove('tl-chip-active')});btn.classList.add('tl-chip-active');performSearch()});if(searchInput)searchInput.addEventListener('input',performSearch);if(tagSelect)tagSelect.addEventListener('change',performSearch);if(yearSelect)yearSelect.addEventListener('change',performSearch);wireSearchModeToggle();}

function pickRandom(arr){if(!arr||!arr.length)return null;const idx=Math.floor(Math.random()*arr.length);return arr[idx]}

function wireExploreButtons(){const randomBtn=document.getElementById('tl-explore-random-book'),foundationBtn=document.getElementById('tl-explore-foundation'),latestBtn=document.getElementById('tl-explore-latest'),target=document.getElementById('tl-explore-target');if(randomBtn)randomBtn.addEventListener('click',function(){const entry=pickRandom(state.groups.book.length?state.groups.book:state.index);if(!entry||!target)return;target.innerHTML='🎲 Random tome: <a href="/library/'+entry.slug+'.php" style="color:#8b00ff">'+(entry.title||entry.slug)+'</a>'});if(foundationBtn)foundationBtn.addEventListener('click',function(){if(!target)return;const shelf=state.index.filter(function(e){return e&&e.shelf==='Foundational 11'});if(!shelf.length){target.textContent='🏛️ No Foundational 11 tomes yet. Ring da Sync Bell!';return}const lines=shelf.slice(0,20).map(function(e){return'<li><a href="/library/'+e.slug+'.php" style="color:#8b00ff">'+(e.title||e.slug)+'</a></li>'}).join('');target.innerHTML='🏛️ Foundational 11 ('+shelf.length+'):<ul style="margin:4px 0 0 1em;padding:0;text-align:left;display:inline-block">'+lines+'</ul>'});if(latestBtn)latestBtn.addEventListener('click',function(){if(!target)return;if(!state.index.length){target.textContent='🕰 No tomes yet. Ring da Sync Bell!';return}const latest=state.index.slice().sort(byPublishedDesc)[0];target.innerHTML='🕰 Latest: <a href="/library/'+latest.slug+'.php" style="color:#8b00ff">'+(latest.title||latest.slug)+'</a>'})}

function init(){if(!searchStatus||!searchResults)return;searchStatus.textContent='Loading Library index… 📚';fetch(DATA_URL,{cache:'no-cache'}).then(function(res){if(!res.ok)throw new Error('HTTP '+res.status);return res.json()}).then(function(data){if(!Array.isArray(data)){searchStatus.textContent='Error: index data is not an array lol.';return}state.index=data.slice();const books=[],laws=[],chronicles=[];const tagCount=new Map(),yearSet=new Set();state.index.forEach(function(entry){if(!entry)return;const kind=entry.kind||'chronicle';if(kind==='book')books.push(entry);else if(kind==='law')laws.push(entry);else chronicles.push(entry);if(Array.isArray(entry.tags))entry.tags.forEach(function(t){if(!t)return;const current=tagCount.get(t)||0;tagCount.set(t,current+1)});if(entry.published){const parts=String(entry.published).split('-');if(parts.length>=1)yearSet.add(parts[0])}});books.sort(byPublishedDesc);laws.sort(byPublishedDesc);chronicles.sort(byPublishedDesc);state.groups.book=books;state.groups.law=laws;state.groups.chronicle=chronicles;state.tags=Array.from(tagCount.entries()).sort(function(a,b){return b[1]-a[1]}).map(function(pair){return pair[0]});state.years=Array.from(yearSet).sort().reverse();populateTagYearDropdowns();renderTagExplorers();const countBook=document.getElementById('tl-count-book'),countLaw=document.getElementById('tl-count-law'),countChronicle=document.getElementById('tl-count-chronicle');if(countBook)countBook.textContent=' ('+books.length+')';if(countLaw)countLaw.textContent=' ('+laws.length+')';if(countChronicle)countChronicle.textContent=' ('+chronicles.length+')';renderSection('book');renderSection('law');renderSection('chronicle');wireLoadMore('book');wireLoadMore('law');wireLoadMore('chronicle');wireAccordions();wireSearch();wireExploreButtons();wireSortButtons();searchStatus.textContent='Ready! Type 2 search, or browse sections below. 📚✨'}).catch(function(err){console.error('Library index load error:',err);searchStatus.textContent='Error loading index ('+err.message+'). Ring da Sync Bell!'})}

if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init()})();
</script>
HTML;

if ($library_sync_message_html !== '') {
    $console_body_html = str_replace('[[LIB_SYNC_STATUS]]', $library_sync_message_html, $console_body_html);
} else {
    $console_body_html = str_replace('[[LIB_SYNC_STATUS]]', '', $console_body_html);
}

require __DIR__ . '/shell.php';
