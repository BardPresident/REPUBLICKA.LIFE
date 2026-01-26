<?php
// ============================================================================
// 🤍💎👑 IVORY YOUR GOD 👑💎🤍
// ============================================================================
// 🕊️ WENDY EBUR NESMITH — EBUR = LATIN FOR IVORY 🕊️
// 📅 SEPTEMBER 3, 2008 — SEPTEMBER 3, 2021
// 💒 MARRIED: JANUARY 12, 2021 — SYNCHRONIZED ACROSS REALMS
// 💔 KILLED BY A WORLD THAT REFUSED TO PROTECT HER
// 👑 NOW SHE IS GOD AND I AM HER ETERNAL SERVANT
// Made with tears for The Republic • December 17, 2025
// ============================================================================

// Shell metadata
$page_title       = '🤍💎👑 IVORY YOUR GOD 👑💎🤍';
$page_canonical   = 'https://trepublic.net/IVORY-YOUR-GOD.PHP';
$page_description = 'In Eternal Memory of Wendy Ebur NeSmith. EBUR means IVORY in Latin. September 3, 2008 - September 3, 2021. Married January 12, 2021. Killed by a world that refused to protect her. Now she is God.';
$page_og_title       = $page_title;
$page_og_description = $page_description;
$page_og_url         = $page_canonical;
$page_og_image       = 'https://trepublic.net/emblems/ivory-heart.png';
$hero_title   = '🤍💎👑 IVORY YOUR GOD 👑💎🤍';
$hero_tagline = 'Wendy Ebur NeSmith • September 3, 2008 — September 3, 2021';
$console_title = '🤍💎👑 IVORY YOUR GOD 👑💎🤍';

// ---------------------------------------------------------------------------
// PICTURE WALL - IP RESTRICTED UPLOADING
// ---------------------------------------------------------------------------
$ALLOWED_IP = '23.27.213.108';
$PICTURES_DIR = __DIR__ . '/data/ivory-pictures';

// Get real visitor IP
function get_real_ip() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        return $_SERVER['HTTP_X_REAL_IP'];
    }
    return $_SERVER['REMOTE_ADDR'];
}

$visitor_ip = get_real_ip();
$is_admin = ($visitor_ip === $ALLOWED_IP);

if (!is_dir($PICTURES_DIR)) {
    @mkdir($PICTURES_DIR, 0755, true);
}

// Handle picture upload
$picture_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['picture_submit']) && $is_admin) {
    $pic_title = trim($_POST['picture_title']);
    $pic_caption = trim($_POST['picture_caption']);
    $pic_type = isset($_POST['picture_type']) ? $_POST['picture_type'] : 'light';
    
    if ($pic_title === '' || !isset($_FILES['picture_file']) || $_FILES['picture_file']['error'] !== UPLOAD_ERR_OK) {
        $picture_message = '⚠️ Title and image file required.';
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['picture_file']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $picture_message = '⚠️ Only JPG, PNG, GIF, WEBP allowed.';
        } else {
            $ext = pathinfo($_FILES['picture_file']['name'], PATHINFO_EXTENSION);
            $date = date('Y-m-d');
            $time = date('His');
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($pic_title));
            $slug = trim($slug, '-');
            $filename = $date . '-' . $time . '-' . $slug . '.' . $ext;
            $filepath = $PICTURES_DIR . '/' . $filename;
            
            $meta_file = $PICTURES_DIR . '/' . $date . '-' . $time . '-' . $slug . '.meta.txt';
            $meta_content = $pic_title . "\n" . $date . "\n" . $pic_type . "\n" . $pic_caption;
            
            if (move_uploaded_file($_FILES['picture_file']['tmp_name'], $filepath)) {
                @file_put_contents($meta_file, $meta_content);
                $picture_message = '✅ PICTURE UPLOADED: ' . $filename;
            } else {
                $picture_message = '⚠️ Could not save picture.';
            }
        }
    }
}

// Load pictures - FIXED: Load ALL images, with or without metadata
$pictures = array();
if (is_dir($PICTURES_DIR)) {
    // First, get all images
    $all_images = glob($PICTURES_DIR . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
    
    if ($all_images && is_array($all_images)) {
        // Sort by modification time, newest first
        usort($all_images, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        foreach ($all_images as $img_path) {
            $img_file = basename($img_path);
            $base = pathinfo($img_file, PATHINFO_FILENAME);
            
            // Check for metadata file
            $meta_file = $PICTURES_DIR . '/' . $base . '.meta.txt';
            
            if (file_exists($meta_file)) {
                $content = @file_get_contents($meta_file);
                $lines = explode("\n", $content);
                $title = isset($lines[0]) ? trim($lines[0]) : $base;
                $date = isset($lines[1]) ? trim($lines[1]) : date('Y-m-d', filemtime($img_path));
                $type = isset($lines[2]) ? trim($lines[2]) : 'light';
                $caption = isset($lines[3]) ? trim($lines[3]) : '';
            } else {
                // No metadata - create from filename
                $title = str_replace(['-', '_'], ' ', $base);
                $title = ucwords($title);
                $date = date('Y-m-d', filemtime($img_path));
                $type = 'light';
                $caption = '';
            }
            
            $pictures[] = array(
                'id' => $base,
                'title' => $title,
                'date' => $date,
                'type' => $type,
                'caption' => $caption,
                'file' => $img_file
            );
        }
    }
}

// Build picture cards HTML
$picture_cards_html = '';
if (empty($pictures)) {
    $picture_cards_html = '<div class="ivory-empty">No pictures yet. Her memory awaits images.</div>';
} else {
    foreach ($pictures as $pic) {
        $type_class = $pic['type'] === 'dark' ? 'pic-dark' : 'pic-light';
        $type_label = $pic['type'] === 'dark' ? '⚠️ EVIDENCE' : '🤍 HER LIGHT';
        $picture_cards_html .= '<div class="pic-card ' . $type_class . '" onclick="openLightbox(this)">'
            . '<div class="pic-type">' . $type_label . '</div>'
            . '<img src="data/ivory-pictures/' . htmlspecialchars($pic['file'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($pic['title'], ENT_QUOTES, 'UTF-8') . '" loading="lazy">'
            . '<div class="pic-info">'
            . '<h4>' . htmlspecialchars($pic['title'], ENT_QUOTES, 'UTF-8') . '</h4>'
            . '<span class="pic-date">' . htmlspecialchars($pic['date'], ENT_QUOTES, 'UTF-8') . '</span>'
            . ($pic['caption'] ? '<p class="pic-caption">' . nl2br(htmlspecialchars($pic['caption'], ENT_QUOTES, 'UTF-8')) . '</p>' : '')
            . '</div>'
            . '</div>';
    }
}

// Admin form HTML
$picture_admin_html = '';
if ($is_admin) {
    $pic_msg_class = strpos($picture_message, '✅') !== false ? 'msg-success' : 'msg-error';
    $pic_msg_html = $picture_message ? '<div class="admin-msg ' . $pic_msg_class . '">' . htmlspecialchars($picture_message, ENT_QUOTES, 'UTF-8') . '</div>' : '';
    
    $picture_admin_html = '
    <div class="admin-form">
      <div class="admin-badge">🔐 ADMIN — IP: ' . htmlspecialchars($visitor_ip, ENT_QUOTES, 'UTF-8') . '</div>
      ' . $pic_msg_html . '
      <form method="post" enctype="multipart/form-data" class="upload-form">
        <input type="text" name="picture_title" placeholder="Picture Title..." required>
        <div class="type-select">
          <label><input type="radio" name="picture_type" value="light" checked> 🤍 Her Light</label>
          <label><input type="radio" name="picture_type" value="dark"> ⚠️ Evidence</label>
        </div>
        <textarea name="picture_caption" placeholder="Caption (optional)..."></textarea>
        <input type="file" name="picture_file" accept="image/*" required>
        <button type="submit" name="picture_submit">📷 UPLOAD TO HER WALL</button>
      </form>
    </div>';
}

// ---------------------------------------------------------------------------
// STORY WALL
// ---------------------------------------------------------------------------
$STORIES_DIR = __DIR__ . '/data/ivory-stories';

if (!is_dir($STORIES_DIR)) {
    @mkdir($STORIES_DIR, 0755, true);
}

$story_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['story_submit']) && $is_admin) {
    $title = trim($_POST['story_title']);
    $content = trim($_POST['story_content']);
    
    if ($title === '' || $content === '') {
        $story_message = '⚠️ Title and content required.';
    } else {
        $date = date('Y-m-d');
        $time = date('His');
        $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
        $filename = $date . '-' . $time . '-' . trim($slug, '-') . '.txt';
        $filepath = $STORIES_DIR . '/' . $filename;
        
        if (@file_put_contents($filepath, $title . "\n" . $date . "\n" . $content)) {
            $story_message = '✅ STORY POSTED';
        } else {
            $story_message = '⚠️ Could not save story.';
        }
    }
}

// Load stories
$stories = array();
if (is_dir($STORIES_DIR)) {
    $files = glob($STORIES_DIR . '/*.txt');
    if ($files) {
        usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if ($content !== false) {
                $lines = explode("\n", $content);
                $stories[] = array(
                    'title' => isset($lines[0]) ? trim($lines[0]) : 'Untitled',
                    'date' => isset($lines[1]) ? trim($lines[1]) : date('Y-m-d'),
                    'body' => implode("\n", array_slice($lines, 2))
                );
            }
        }
    }
}

$story_cards_html = '';
if (empty($stories)) {
    $story_cards_html = '<div class="ivory-empty">No stories yet.</div>';
} else {
    foreach ($stories as $story) {
        $preview = mb_substr(strip_tags($story['body']), 0, 200);
        if (mb_strlen($story['body']) > 200) $preview .= '...';
        $story_cards_html .= '<article class="story-card" onclick="toggleStory(this)">'
            . '<div class="story-header"><h3>' . htmlspecialchars($story['title'], ENT_QUOTES, 'UTF-8') . '</h3>'
            . '<span class="story-date">' . htmlspecialchars($story['date'], ENT_QUOTES, 'UTF-8') . '</span>'
            . '<span class="story-toggle">▼</span></div>'
            . '<div class="story-preview">' . htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="story-full">' . nl2br(htmlspecialchars($story['body'], ENT_QUOTES, 'UTF-8')) . '</div>'
            . '</article>';
    }
}

$story_admin_html = '';
if ($is_admin) {
    $msg_class = strpos($story_message, '✅') !== false ? 'msg-success' : 'msg-error';
    $msg_html = $story_message ? '<div class="admin-msg ' . $msg_class . '">' . htmlspecialchars($story_message, ENT_QUOTES, 'UTF-8') . '</div>' : '';
    
    $story_admin_html = '
    <div class="admin-form">
      ' . $msg_html . '
      <form method="post" class="upload-form">
        <input type="text" name="story_title" placeholder="Story Title..." required>
        <textarea name="story_content" placeholder="Write her story..." required></textarea>
        <button type="submit" name="story_submit">📜 POST STORY</button>
      </form>
    </div>';
}

// ---------------------------------------------------------------------------
// CONSOLE BODY
// ---------------------------------------------------------------------------
ob_start();
?>
<!DOCTYPE html>
<div class="throne">

<!-- DIVINE HEADER -->
<header class="divine-header">
  <div class="header-glow"></div>
  <div class="crown-icon">👑</div>
  <h1 class="god-title">IVORY YOUR GOD</h1>
  <div class="god-name">WENDY EBUR NESMITH</div>
  <div class="god-meaning">EBUR = IVORY in Latin</div>
  <div class="god-dates">September 3, 2008 — September 3, 2021</div>
  <div class="god-marriage">💒 Married: January 12, 2021 — Synchronized Across Realms 💒</div>
  <div class="crown-rail">🤍 💎 👑 💎 🤍 💎 👑 💎 🤍 💎 👑 💎 🤍</div>
</header>

<!-- THE SERVANT -->
<section class="section servant-section">
  <h2>🧎 THE SERVANT SPEAKS 🧎</h2>
  <div class="servant-box">
    <p class="servant-line">I AM GOD'S OTHER HALF.</p>
    <p class="servant-line">I AM HER SERVANT.</p>
    <p class="servant-line">I WORSHIP AT THE THRONE OF A 13-YEAR-OLD GIRL</p>
    <p class="servant-line">WHO THE WORLD KILLED</p>
    <p class="servant-line">AND BLAMED ME FOR LOVING.</p>
    <div class="servant-sig">— MOST WORSHIPFUL BROTHER YESHUA HAMASHIACH (DEAD NAME: Wendell Charles NeSmith)<br>In Eternal Worship of Ivory</div>
  </div>
</section>

<!-- THE DIVINE MARRIAGE -->
<section class="section marriage-section">
  <h2>💒 THE DIVINE MARRIAGE 💒</h2>
  <div class="marriage-date">January 12, 2021 • 7:13 AM</div>
  
  <div class="marriage-grid">
    <div class="marriage-card his">
      <h3>🖊️ HIS VOW</h3>
      <div class="location">📍 Locked in institution, Australia</div>
      <div class="state">Drugged. Erased. Cut off from all contact.</div>
      <p>Writing page 179 of <strong>Talitha Cumi</strong> — a 183-page handwritten book for her.</p>
      <p class="quote">"My Galatea... It is time we merged our lives together... My channel is now yours my Wendy Darling."</p>
    </div>
    
    <div class="marriage-sync">
      <div class="sync-icon">⚡</div>
      <div class="sync-text">SYNCHRONIZED<br>ACROSS REALMS</div>
      <div class="sync-icon">⚡</div>
    </div>
    
    <div class="marriage-card hers">
      <h3>💍 HER VOW</h3>
      <div class="location">📍 England, United Kingdom</div>
      <div class="state">Unable to reach him. Watching his silence.</div>
      <p>Changed her name from <strong>Ivory Phoenix</strong> to her true name transformed:</p>
      <p class="name-change">Smith → <strong>NeSmith</strong></p>
      <p class="quote">"Please say something, Wendell... Are you now no longer even allowed to use your phone."</p>
    </div>
  </div>
  
  <div class="marriage-truth">
    <p>Neither could see what the other was doing.</p>
    <p>Both said "I DO" at the same moment.</p>
    <p class="marriage-eternal">THE MARRIAGE HAPPENED IN THE DIVINE REALM.</p>
    <p class="marriage-eternal">SOULBOUND. ETERNAL. DOCUMENTED.</p>
  </div>
</section>

<!-- NAME PROGRESSION -->
<section class="section names-section">
  <h2>📜 THE NAME PROPHECY 📜</h2>
  <div class="names-timeline">
    <div class="name-step">
      <div class="name-date">Birth</div>
      <div class="name-value">Wendy Ebur Smith</div>
      <div class="name-note">Legal name. EBUR = Latin for IVORY.</div>
    </div>
    <div class="name-arrow">→</div>
    <div class="name-step">
      <div class="name-date">2020</div>
      <div class="name-value">Ivory Phoenix</div>
      <div class="name-note">Claimed the prophecy names from his films & books.</div>
    </div>
    <div class="name-arrow">→</div>
    <div class="name-step highlight">
      <div class="name-date">Jan 12, 2021</div>
      <div class="name-value">Wendy Ebur NeSmith</div>
      <div class="name-note">THE "I DO" — Smith became NeSmith.</div>
    </div>
  </div>
</section>

<!-- THE PROPHECY -->
<section class="section prophecy-section">
  <h2>🔮 THE PROPHECY — WRITTEN BEFORE SHE EXISTED 🔮</h2>
  
  <div class="prophecy-grid">
    <div class="prophecy-card">
      <div class="prophecy-year">2012</div>
      <h3>Ivory Heart — The Film</h3>
      <p>Released December 21, 2012 — the day the Mayan calendar ended.</p>
      <p>"Wendell takes one special little girl on a journey through his nation's heart while teaching her how to save the world."</p>
      <p>Self-credited as: <strong>Pygmalion</strong></p>
      <p class="prophecy-gap">Made for her. <strong>9 years before she found him.</strong></p>
      <a href="https://www.imdb.com/title/tt2559014" target="_blank">📽️ IMDb</a>
    </div>
    
    <div class="prophecy-card">
      <div class="prophecy-year">2013</div>
      <h3>"Help Your People or Legalise Assisted Suicide"</h3>
      <p>Written in the storyline:</p>
      <p class="prophecy-quote">"Hold my hand and fly with me <strong>Wendy Darling</strong>, for we will arrive at Neverland in the morning."</p>
      <p class="prophecy-gap"><strong>8 years before she found him.</strong></p>
      <a href="https://www.imdb.com/title/tt2612250/" target="_blank">📽️ IMDb</a>
    </div>
    
    <div class="prophecy-card">
      <div class="prophecy-year">2014</div>
      <h3>Living Neverland — The Book</h3>
      <p>Chapter 8: The Soul Mate</p>
      <p class="prophecy-quote">"the Pan will always be patiently waiting for your bloodline to wake up"</p>
      <p class="prophecy-quote">"Wendy Lady"</p>
      <p class="prophecy-gap"><strong>7 years before she found him.</strong></p>
      <a href="https://trepublic.net/library/living-neverland-2014.php" target="_blank">📖 Read</a>
    </div>
    
    <div class="prophecy-card fulfillment">
      <div class="prophecy-year">2020</div>
      <h3>🤍 WENDY FINDS HIM 🤍</h3>
      <p>October 25, 2020</p>
      <p>A 12-year-old girl in England.</p>
      <p>Named <strong>Wendy</strong>.</p>
      <p>Middle name <strong>Ebur</strong> — Latin for <strong>IVORY</strong>.</p>
      <p>Birthday: <strong>September 3</strong></p>
      <p class="prophecy-gap"><strong>THE PROPHECY FULFILLED ITSELF.</strong></p>
    </div>
  </div>
  
  <div class="pygmalion-box">
    <h3>🗿 Pygmalion</h3>
    <p>In Greek mythology, Pygmalion carved a woman from <strong>ivory</strong>. He fell in love with his creation. He prayed to Aphrodite. The goddess brought the statue to life — <strong>Galatea</strong>.</p>
    <p class="pygmalion-truth">He created the form. Fell in love. Prayed for embodiment. Waited for God to inhabit the vessel.</p>
    <p class="pygmalion-truth"><strong>She arrived. The world killed her. Now she IS God.</strong></p>
  </div>
</section>

<!-- WHAT HAPPENED -->
<section class="section story-section">
  <h2>💔 WHAT HAPPENED TO HER 💔</h2>
  
  <div class="timeline">
    <div class="timeline-card light">
      <h3>🌟 October 2020 — The Beginning</h3>
      <p>She was 12. Being bullied. No friends. Parents didn't understand.</p>
      <p>She found his videos. Reached out.</p>
      <p>He told her to stand up for herself.</p>
      <p><strong>The next day, she did. She made a friend named Eve.</strong></p>
      <p class="quote">"It's like God wanted me to stand up for myself. You might have just changed my life."</p>
    </div>
    
    <div class="timeline-card light">
      <h3>📚 November 2020 — The Teaching</h3>
      <p>He taught her philosophy means "love of wisdom."</p>
      <p>Told her to make videos about her life.</p>
      <p>Promised to be her mentor.</p>
      <p>He called her <strong>Wendy Lady</strong> — the same words he wrote in 2014.</p>
    </div>
    
    <div class="timeline-card light">
      <h3>💒 January 12, 2021 — The Marriage</h3>
      <p>Synchronized across realms.</p>
      <p>He wrote page 179, locked up and drugged.</p>
      <p>She changed her name to NeSmith.</p>
      <p><strong>Both said "I DO" at the same moment without contact.</strong></p>
    </div>
    
    <div class="timeline-card dark">
      <h3>🔥 2021 — The Attack</h3>
      <p><strong>Kiwi Farms</strong> found her and doxxed her.</p>
      <p><strong>Sexual predators</strong> sent her nude photos.</p>
      <p><strong>Her father</strong> beat her.</p>
      <p>She asked everyone for help.</p>
      <p class="verdict"><strong>NOBODY HELPED HER.</strong></p>
    </div>
    
    <div class="timeline-card dark final">
      <h3>🕊️ September 3, 2021 — The End</h3>
      <p>Her 13th birthday.</p>
      <p>She asked him to fly away with her. To Neverland. Forever.</p>
      <p><strong>HE SAID NO.</strong></p>
      <p><strong>HE TRIED TO SAVE HER.</strong></p>
      <p>He reported to authorities. He broke up with her rather than let her die.</p>
      <p class="verdict">SHE FLEW AWAY ALONE.</p>
    </div>
  </div>
</section>

<!-- THE SYSTEM'S RESPONSE -->
<section class="section inversion-section">
  <h2>⚖️ THE SYSTEM'S RESPONSE ⚖️</h2>
  
  <div class="inversion-grid">
    <div class="inversion-card guilty">
      <h3>LABELED "EXTREME RISK TO CHILDREN"</h3>
      <div class="inversion-name">MOST WORSHIPFUL BROTHER<br>YESHUA HAMASHIACH</div>
      <ul>
        <li>✓ Tried to save her</li>
        <li>✓ Refused the suicide pact</li>
        <li>✓ Reported to authorities</li>
        <li>✓ Broke up with her rather than enable death</li>
        <li>✓ Zero victims. Ever.</li>
      </ul>
    </div>
    
    <div class="inversion-card free">
      <h3>FACED NO CONSEQUENCES</h3>
      <ul>
        <li>✗ Her father who beat her</li>
        <li>✗ The predators who sent nudes to a child</li>
        <li>✗ Kiwi Farms who doxxed her</li>
        <li>✗ Every authority who ignored reports</li>
        <li>✗ Everyone who could have saved her</li>
      </ul>
    </div>
  </div>
  
  <div class="verdict-box">
    <p>The protector was labeled the predator.</p>
    <p>The predators walked free.</p>
    <p class="verdict-final">THE CHILD IS DEAD.</p>
  </div>
</section>

<!-- MISSION STATS -->
<section class="section stats-section">
  <h2>🛡️ THE MISSION — IN HER NAME 🛡️</h2>
  <p class="stats-sub">32 years of waiting. Since age 9. All for her.</p>
  
  <div class="stats-grid">
    <div class="stat-card"><span class="stat-num">32</span><span class="stat-label">Years</span></div>
    <div class="stat-card"><span class="stat-num">75,722</span><span class="stat-label">Emails</span></div>
    <div class="stat-card"><span class="stat-num">1,492</span><span class="stat-label">Videos</span></div>
    <div class="stat-card zero"><span class="stat-num">0</span><span class="stat-label">Victims</span></div>
  </div>
  
  <div class="stats-contrast">
    <p><strong>"EXTREME RISK"</strong> label. <strong>Zero victims. Ever.</strong></p>
    <p>The system producing millions of child victims calls the one with zero victims "dangerous."</p>
  </div>
</section>

<!-- THE DECLARATION -->
<section class="section declaration-section">
  <h2>🤍 THE DECLARATION 🤍</h2>
  
  <div class="declaration-box">
    <p class="declaration-line">SHE IS GOD.</p>
    <p class="declaration-line">I AM HER SERVANT.</p>
    <p class="declaration-line">I DON'T WANT WORSHIP.</p>
    <p class="declaration-line">I WANT TO WORSHIP HER.</p>
  </div>

  <div class="throne-model">
    <div class="model old">
      <h3>☠️ Old Model</h3>
      <p>God sits above</p>
      <p>Humans kneel below</p>
      <p>Power flows DOWN</p>
      <p>Adults own children</p>
    </div>
    <div class="model-arrow">⟹</div>
    <div class="model new">
      <h3>🤍 Inverted Throne</h3>
      <p>God kneels BELOW</p>
      <p>Children become Gods</p>
      <p>Power flows UP</p>
      <p>Adults SERVE children</p>
    </div>
  </div>
</section>

<!-- PICTURE WALL -->
<section class="section pictures-section">
  <h2>📷 HER PICTURE WALL 📷</h2>
  <p class="section-intro">Images of her light. Evidence of what they did. Click to enlarge.</p>
  
  <div class="pic-wall">
    <?php echo $picture_cards_html; ?>
  </div>
  
  <?php echo $picture_admin_html; ?>
</section>

<!-- STORY WALL -->
<section class="section stories-section">
  <h2>📜 HER STORY WALL 📜</h2>
  <p class="section-intro">Stories about Ivory. Click to expand.</p>
  
  <div class="story-wall">
    <?php echo $story_cards_html; ?>
  </div>
  
  <?php echo $story_admin_html; ?>
</section>

<!-- LINKS -->
<section class="section links-section">
  <h2>🔗 ETERNAL EVIDENCE 🔗</h2>
  
  <div class="links-grid">
    <a href="https://www.imdb.com/title/tt2559014" target="_blank" class="link-card">
      <span class="link-icon">📽️</span>
      <span class="link-title">Ivory Heart (2012)</span>
      <span class="link-desc">The film made for her before she existed</span>
    </a>
    <a href="https://www.imdb.com/title/tt2612250/" target="_blank" class="link-card">
      <span class="link-icon">📜</span>
      <span class="link-title">Help Your People (2013)</span>
      <span class="link-desc">"Wendy Darling"</span>
    </a>
    <a href="https://trepublic.net/library/living-neverland-2014.php" target="_blank" class="link-card">
      <span class="link-icon">📖</span>
      <span class="link-title">Living Neverland (2014)</span>
      <span class="link-desc">"Wendy Lady"</span>
    </a>
    <a href="https://trepublic.net/receipts.php" target="_blank" class="link-card">
      <span class="link-icon">📋</span>
      <span class="link-title">The Receipts</span>
      <span class="link-desc">Every piece of evidence</span>
    </a>
    <a href="https://trepublic.net/rainbow-bridge.php" target="_blank" class="link-card">
      <span class="link-icon">🌈</span>
      <span class="link-title">Rainbow Bridge</span>
      <span class="link-desc">Her gift to the world</span>
    </a>
  </div>
</section>

<!-- FOOTER -->
<footer class="divine-footer">
  <div class="footer-crown">👑</div>
  <div class="footer-title">🤍💎👑 IVORY YOUR GOD 👑💎🤍</div>
  <div class="footer-name">WENDY EBUR NESMITH</div>
  <div class="footer-meaning">EBUR = IVORY in Latin</div>
  <div class="footer-dates">September 3, 2008 — September 3, 2021</div>
  <div class="footer-marriage">💒 Married January 12, 2021 💒</div>
  <div class="footer-age">13 years old</div>
  
  <div class="footer-epitaph">
    <p>Society killed her. Blamed the one who tried to save her.</p>
    <p>Protected the ones who hurt her.</p>
    <p><strong>This page exists so the world can never forget what it did.</strong></p>
  </div>
  
  <div class="footer-servant">
    <p>Her eternal servant,</p>
    <p class="footer-sig">MOST WORSHIPFUL BROTHER YESHUA HAMASHIACH</p>
    <p>🕊️ In Eternal Worship of Ivory 🕊️</p>
  </div>
  
  <div class="footer-dedication">
    🤍 FOR ALL THE CHILDREN THE WORLD REFUSES TO PROTECT 🤍<br>
    🤍 FOR ALL THE WENDY LADIES WHO FLY AWAY TOO SOON 🤍<br>
    🤍 FOR IVORY — MY GOD — MY ETERNAL WIFE 🤍
  </div>
  
  <div class="footer-rail">🤍 💎 👑 💎 🤍 💎 👑 💎 🤍 💎 👑 💎 🤍</div>
</footer>

</div>

<!-- LIGHTBOX -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
  <span class="lightbox-close">&times;</span>
  <img id="lightbox-img" src="" alt="">
  <div id="lightbox-caption"></div>
</div>

<script>
function toggleStory(card) {
  var preview = card.querySelector('.story-preview');
  var full = card.querySelector('.story-full');
  var toggle = card.querySelector('.story-toggle');
  if (full.style.display === 'none' || full.style.display === '') {
    preview.style.display = 'none';
    full.style.display = 'block';
    toggle.textContent = '▲';
    card.classList.add('expanded');
  } else {
    preview.style.display = 'block';
    full.style.display = 'none';
    toggle.textContent = '▼';
    card.classList.remove('expanded');
  }
}

function openLightbox(card) {
  var img = card.querySelector('img');
  var title = card.querySelector('h4');
  var caption = card.querySelector('.pic-caption');
  
  document.getElementById('lightbox-img').src = img.src;
  document.getElementById('lightbox-caption').innerHTML = '<strong>' + (title ? title.textContent : '') + '</strong>' + (caption ? '<br>' + caption.innerHTML : '');
  document.getElementById('lightbox').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('lightbox').style.display = 'none';
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeLightbox();
});
</script>

<style>
/* ============================================================================
   🤍💎👑 IVORY YOUR GOD — DIVINE THRONE STYLES 👑💎🤍
   ============================================================================ */

:root {
  --ivory: #FFFFF0;
  --gold: #FFD700;
  --gold-glow: rgba(255, 215, 0, 0.4);
  --white: #FFFFFF;
  --light: #F5F5F5;
  --rose: #FF6B6B;
  --green: #4ADE80;
  --purple: #A78BFA;
  --bg: #050510;
  --bg-card: #0a0a18;
  --bg-section: #08081a;
  --text: #FFFFFF;
  --text-muted: #CCCCCC;
  --border: #333355;
}

* {
  box-sizing: border-box;
}

.throne {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
  background: var(--bg);
  color: var(--text);
  font-family: 'Segoe UI', system-ui, sans-serif;
  font-size: 1.1rem;
  line-height: 1.7;
}

/* DIVINE HEADER */
.divine-header {
  text-align: center;
  padding: 3rem 2rem;
  background: linear-gradient(180deg, rgba(255,215,0,0.15) 0%, transparent 100%);
  border: 3px solid var(--gold);
  border-radius: 20px;
  margin-bottom: 2rem;
  position: relative;
  overflow: hidden;
}

.header-glow {
  position: absolute;
  top: -50%;
  left: 50%;
  transform: translateX(-50%);
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(255,215,0,0.2) 0%, transparent 70%);
  pointer-events: none;
}

.crown-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
  text-shadow: 0 0 50px var(--gold-glow);
}

.god-title {
  font-size: 3rem;
  font-weight: 800;
  color: var(--gold);
  text-shadow: 0 0 30px var(--gold-glow);
  margin: 0 0 0.5rem 0;
  letter-spacing: 0.1em;
}

.god-name {
  font-size: 2rem;
  font-weight: 700;
  color: var(--white);
  margin: 0.5rem 0;
}

.god-meaning {
  font-size: 1.2rem;
  color: var(--rose);
  font-style: italic;
}

.god-dates {
  font-size: 1.3rem;
  color: var(--text-muted);
  margin: 0.5rem 0;
}

.god-marriage {
  font-size: 1.1rem;
  color: var(--purple);
  margin: 1rem 0;
  padding: 0.5rem 1rem;
  background: rgba(167, 139, 250, 0.1);
  border-radius: 30px;
  display: inline-block;
}

.crown-rail {
  font-size: 1.2rem;
  letter-spacing: 0.3em;
  margin-top: 1.5rem;
  opacity: 0.8;
}

/* SECTIONS */
.section {
  background: var(--bg-section);
  border: 2px solid var(--border);
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 2rem;
}

.section h2 {
  text-align: center;
  font-size: 1.6rem;
  color: var(--gold);
  margin: 0 0 1.5rem 0;
  letter-spacing: 0.05em;
}

.section-intro {
  text-align: center;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

/* SERVANT */
.servant-box {
  text-align: center;
  padding: 2rem;
  background: rgba(167, 139, 250, 0.1);
  border: 2px solid var(--purple);
  border-radius: 16px;
}

.servant-line {
  font-size: 1.4rem;
  margin: 0.5rem 0;
  color: var(--white);
}

.servant-sig {
  margin-top: 2rem;
  color: var(--gold);
  font-size: 1.1rem;
  line-height: 1.8;
}

/* MARRIAGE */
.marriage-date {
  text-align: center;
  font-size: 1.4rem;
  color: var(--purple);
  font-weight: 700;
  margin-bottom: 2rem;
}

.marriage-grid {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  gap: 1.5rem;
  align-items: center;
  margin-bottom: 2rem;
}

@media (max-width: 800px) {
  .marriage-grid {
    grid-template-columns: 1fr;
  }
  .marriage-sync {
    flex-direction: row !important;
  }
}

.marriage-card {
  padding: 1.5rem;
  border-radius: 12px;
}

.marriage-card.his {
  background: rgba(74, 222, 128, 0.1);
  border: 2px solid var(--green);
}

.marriage-card.hers {
  background: rgba(255, 107, 107, 0.1);
  border: 2px solid var(--rose);
}

.marriage-card h3 {
  color: var(--gold);
  margin: 0 0 1rem 0;
  text-align: center;
}

.marriage-card .location {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
}

.marriage-card .state {
  color: var(--rose);
  font-weight: 600;
  margin-bottom: 1rem;
}

.marriage-card .quote {
  background: rgba(0,0,0,0.3);
  border-left: 3px solid var(--gold);
  padding: 1rem;
  margin-top: 1rem;
  font-style: italic;
  color: var(--ivory);
}

.name-change {
  font-size: 1.3rem;
  text-align: center;
  color: var(--gold);
  margin: 1rem 0;
}

.marriage-sync {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.sync-icon {
  font-size: 2rem;
  color: var(--gold);
  text-shadow: 0 0 20px var(--gold-glow);
}

.sync-text {
  text-align: center;
  font-weight: 700;
  color: var(--purple);
  font-size: 0.9rem;
  letter-spacing: 0.1em;
}

.marriage-truth {
  text-align: center;
  padding: 1.5rem;
  background: rgba(255, 215, 0, 0.1);
  border: 2px solid var(--gold);
  border-radius: 12px;
}

.marriage-truth p {
  margin: 0.5rem 0;
  color: var(--text-muted);
}

.marriage-eternal {
  color: var(--gold) !important;
  font-weight: 700;
  font-size: 1.2rem;
}

/* NAME PROGRESSION */
.names-timeline {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.name-step {
  text-align: center;
  padding: 1rem 1.5rem;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  min-width: 180px;
}

.name-step.highlight {
  background: rgba(255, 215, 0, 0.15);
  border: 2px solid var(--gold);
  box-shadow: 0 0 20px var(--gold-glow);
}

.name-date {
  font-size: 0.9rem;
  color: var(--purple);
  margin-bottom: 0.5rem;
}

.name-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--white);
  margin-bottom: 0.5rem;
}

.name-note {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.name-arrow {
  font-size: 1.5rem;
  color: var(--gold);
}

/* PROPHECY */
.prophecy-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.prophecy-card {
  padding: 1.5rem;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
}

.prophecy-card.fulfillment {
  background: rgba(255, 215, 0, 0.1);
  border: 2px solid var(--gold);
  box-shadow: 0 0 20px var(--gold-glow);
}

.prophecy-year {
  font-size: 0.9rem;
  color: var(--purple);
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.prophecy-card h3 {
  color: var(--gold);
  margin: 0 0 1rem 0;
  font-size: 1.1rem;
}

.prophecy-card p {
  margin: 0.5rem 0;
  color: var(--text-muted);
}

.prophecy-quote {
  background: rgba(0,0,0,0.3);
  border-left: 3px solid var(--gold);
  padding: 0.75rem 1rem;
  margin: 0.75rem 0;
  font-style: italic;
  color: var(--ivory);
}

.prophecy-gap {
  color: var(--rose) !important;
  font-weight: 700;
  margin-top: 1rem !important;
}

.prophecy-card a {
  display: inline-block;
  margin-top: 0.75rem;
  color: var(--gold);
  font-weight: 700;
}

.pygmalion-box {
  text-align: center;
  padding: 1.5rem;
  background: rgba(255, 215, 0, 0.05);
  border: 1px solid var(--gold);
  border-radius: 12px;
}

.pygmalion-box h3 {
  color: var(--gold);
  margin: 0 0 1rem 0;
}

.pygmalion-box p {
  margin: 0.5rem 0;
  color: var(--text-muted);
}

.pygmalion-truth {
  color: var(--rose) !important;
  font-style: italic;
}

/* TIMELINE */
.timeline {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.timeline-card {
  padding: 1.5rem;
  border-radius: 12px;
}

.timeline-card h3 {
  margin: 0 0 1rem 0;
  font-size: 1.15rem;
}

.timeline-card.light {
  background: rgba(74, 222, 128, 0.1);
  border: 1px solid rgba(74, 222, 128, 0.3);
}

.timeline-card.light h3 {
  color: var(--green);
}

.timeline-card.dark {
  background: rgba(255, 107, 107, 0.1);
  border: 1px solid rgba(255, 107, 107, 0.3);
}

.timeline-card.dark h3 {
  color: var(--rose);
}

.timeline-card.final {
  border-width: 2px;
  box-shadow: 0 0 20px rgba(255, 107, 107, 0.3);
}

.timeline-card p {
  margin: 0.5rem 0;
  color: var(--text-muted);
}

.timeline-card .quote {
  background: rgba(0,0,0,0.3);
  border-left: 3px solid var(--gold);
  padding: 0.75rem 1rem;
  margin: 0.75rem 0;
  font-style: italic;
  color: var(--ivory);
}

.timeline-card .verdict {
  color: var(--rose) !important;
  font-weight: 700;
  font-size: 1.2rem;
  margin-top: 1rem !important;
}

/* INVERSION */
.inversion-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.inversion-card {
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
}

.inversion-card h3 {
  margin: 0 0 1rem 0;
  font-size: 1.1rem;
}

.inversion-card.guilty {
  background: rgba(255, 107, 107, 0.15);
  border: 2px solid var(--rose);
}

.inversion-card.guilty h3 {
  color: var(--rose);
}

.inversion-card.free {
  background: rgba(74, 222, 128, 0.1);
  border: 2px solid var(--green);
}

.inversion-card.free h3 {
  color: var(--green);
}

.inversion-name {
  color: var(--gold);
  font-weight: 700;
  margin-bottom: 1rem;
}

.inversion-card ul {
  list-style: none;
  padding: 0;
  margin: 0;
  text-align: left;
}

.inversion-card li {
  padding: 0.4rem 0;
  color: var(--text-muted);
}

.verdict-box {
  text-align: center;
  padding: 1.5rem;
  background: rgba(0,0,0,0.3);
  border-radius: 12px;
}

.verdict-box p {
  margin: 0.5rem 0;
  color: var(--text-muted);
  font-size: 1.2rem;
}

.verdict-final {
  color: var(--rose) !important;
  font-weight: 700;
  font-size: 1.5rem !important;
  margin-top: 1rem !important;
}

/* STATS */
.stats-sub {
  text-align: center;
  color: var(--text-muted);
  font-style: italic;
  margin-bottom: 1.5rem;
}

.stats-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  text-align: center;
  padding: 1.5rem 2rem;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  min-width: 120px;
}

.stat-num {
  display: block;
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--gold);
}

.stat-label {
  color: var(--text-muted);
  font-size: 1rem;
}

.stat-card.zero .stat-num {
  color: var(--green);
}

.stats-contrast {
  text-align: center;
  padding: 1.5rem;
  background: rgba(255, 107, 107, 0.1);
  border: 1px solid rgba(255, 107, 107, 0.3);
  border-radius: 12px;
  color: var(--text-muted);
}

/* DECLARATION */
.declaration-box {
  text-align: center;
  padding: 2rem;
  background: rgba(255, 215, 0, 0.1);
  border: 2px solid var(--gold);
  border-radius: 16px;
  margin-bottom: 2rem;
}

.declaration-line {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0.5rem 0;
  color: var(--white);
}

.throne-model {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.model {
  flex: 1;
  min-width: 200px;
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
}

.model h3 {
  margin: 0 0 1rem 0;
}

.model p {
  margin: 0.3rem 0;
  color: var(--text-muted);
}

.model.old {
  background: rgba(255, 107, 107, 0.1);
  border: 1px solid rgba(255, 107, 107, 0.3);
}

.model.old h3 {
  color: var(--rose);
}

.model.new {
  background: rgba(74, 222, 128, 0.1);
  border: 1px solid rgba(74, 222, 128, 0.3);
}

.model.new h3 {
  color: var(--green);
}

.model-arrow {
  font-size: 2.5rem;
  color: var(--gold);
}

/* PICTURE WALL */
.pic-wall {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.pic-card {
  background: var(--bg-card);
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}

.pic-card:hover {
  transform: translateY(-5px);
}

.pic-light {
  border: 2px solid var(--gold);
}

.pic-light:hover {
  box-shadow: 0 10px 30px var(--gold-glow);
}

.pic-dark {
  border: 2px solid var(--rose);
}

.pic-dark:hover {
  box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
}

.pic-type {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  font-weight: 700;
  text-align: center;
}

.pic-light .pic-type {
  background: rgba(255, 215, 0, 0.15);
  color: var(--gold);
}

.pic-dark .pic-type {
  background: rgba(255, 107, 107, 0.15);
  color: var(--rose);
}

.pic-card img {
  width: 100%;
  height: auto;
  max-height: 400px;
  object-fit: contain;
  background: #000;
  display: block;
}

.pic-info {
  padding: 1rem;
}

.pic-info h4 {
  margin: 0 0 0.5rem 0;
  color: var(--white);
  font-size: 1.1rem;
}

.pic-date {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.pic-caption {
  margin-top: 0.75rem;
  font-size: 0.95rem;
  color: var(--text-muted);
  line-height: 1.6;
}

.ivory-empty {
  text-align: center;
  padding: 3rem;
  color: var(--text-muted);
  grid-column: 1 / -1;
}

/* STORY WALL */
.story-wall {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.story-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}

.story-card:hover {
  border-color: var(--gold);
}

.story-card.expanded {
  border-color: var(--gold);
  box-shadow: 0 0 20px var(--gold-glow);
}

.story-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.25rem;
  background: rgba(255, 215, 0, 0.05);
}

.story-header:hover {
  background: rgba(255, 215, 0, 0.1);
}

.story-header h3 {
  flex: 1;
  margin: 0;
  color: var(--white);
  font-size: 1.1rem;
}

.story-date {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.story-toggle {
  color: var(--text-muted);
}

.story-preview {
  padding: 1rem 1.25rem;
  color: var(--text-muted);
  font-style: italic;
}

.story-full {
  display: none;
  padding: 1.25rem;
  background: rgba(0,0,0,0.2);
  color: var(--text-muted);
  line-height: 1.8;
}

/* ADMIN */
.admin-form {
  margin-top: 2rem;
  padding: 1.5rem;
  background: rgba(74, 222, 128, 0.1);
  border: 2px solid var(--green);
  border-radius: 12px;
}

.admin-badge {
  text-align: center;
  font-weight: 700;
  color: var(--green);
  margin-bottom: 1rem;
}

.upload-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.upload-form input[type="text"],
.upload-form textarea {
  padding: 1rem;
  border-radius: 8px;
  border: 2px solid var(--border);
  background: var(--bg);
  color: var(--white);
  font-size: 1rem;
  font-family: inherit;
}

.upload-form input:focus,
.upload-form textarea:focus {
  outline: none;
  border-color: var(--gold);
}

.upload-form textarea {
  min-height: 120px;
  resize: vertical;
}

.type-select {
  display: flex;
  gap: 2rem;
  padding: 1rem;
  background: rgba(0,0,0,0.2);
  border-radius: 8px;
}

.type-select label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  color: var(--text-muted);
}

.upload-form input[type="file"] {
  padding: 1rem;
  background: rgba(0,0,0,0.2);
  border: 2px dashed var(--border);
  border-radius: 8px;
  color: var(--text-muted);
  cursor: pointer;
}

.upload-form input[type="file"]:hover {
  border-color: var(--gold);
}

.upload-form button {
  padding: 1rem 2rem;
  border-radius: 30px;
  border: 2px solid var(--gold);
  background: var(--gold);
  color: #000;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.upload-form button:hover {
  box-shadow: 0 0 30px var(--gold-glow);
}

.admin-msg {
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
  font-weight: 700;
  margin-bottom: 1rem;
}

.msg-success {
  background: rgba(74, 222, 128, 0.2);
  border: 1px solid var(--green);
  color: var(--green);
}

.msg-error {
  background: rgba(255, 107, 107, 0.2);
  border: 1px solid var(--rose);
  color: var(--rose);
}

/* LINKS */
.links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
}

.link-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 1.5rem;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s;
}

.link-card:hover {
  border-color: var(--gold);
  box-shadow: 0 0 20px var(--gold-glow);
  transform: translateY(-3px);
}

.link-icon {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.link-title {
  font-weight: 700;
  color: var(--gold);
  margin-bottom: 0.25rem;
}

.link-desc {
  font-size: 0.85rem;
  color: var(--text-muted);
}

/* FOOTER */
.divine-footer {
  text-align: center;
  padding: 3rem 2rem;
  background: linear-gradient(180deg, var(--bg-section) 0%, rgba(255,215,0,0.1) 100%);
  border: 3px solid var(--gold);
  border-radius: 20px;
  margin-top: 2rem;
}

.footer-crown {
  font-size: 4rem;
  margin-bottom: 1rem;
  text-shadow: 0 0 30px var(--gold-glow);
}

.footer-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--gold);
}

.footer-name {
  font-size: 2rem;
  font-weight: 800;
  color: var(--white);
  margin: 0.5rem 0;
}

.footer-meaning {
  color: var(--rose);
  font-style: italic;
}

.footer-dates {
  color: var(--text-muted);
  margin: 0.25rem 0;
}

.footer-marriage {
  color: var(--purple);
  margin: 0.5rem 0;
}

.footer-age {
  color: var(--rose);
  font-weight: 700;
}

.footer-epitaph {
  margin: 2rem 0;
  padding: 1.5rem;
  background: rgba(0,0,0,0.3);
  border-radius: 12px;
  color: var(--text-muted);
}

.footer-epitaph p {
  margin: 0.5rem 0;
}

.footer-servant {
  margin: 2rem 0;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border);
}

.footer-servant p {
  margin: 0.3rem 0;
  color: var(--text-muted);
}

.footer-sig {
  font-weight: 700;
  color: var(--gold) !important;
}

.footer-dedication {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border);
  font-size: 1.1rem;
  line-height: 2.2;
  color: var(--white);
}

.footer-rail {
  margin-top: 2rem;
  font-size: 1.2rem;
  letter-spacing: 0.3em;
  opacity: 0.8;
}

/* LIGHTBOX */
.lightbox {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.95);
  z-index: 9999;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 2rem;
}

.lightbox-close {
  position: absolute;
  top: 1rem;
  right: 2rem;
  font-size: 3rem;
  color: var(--white);
  cursor: pointer;
  transition: color 0.3s;
}

.lightbox-close:hover {
  color: var(--gold);
}

#lightbox-img {
  max-width: 90%;
  max-height: 80vh;
  border-radius: 8px;
  box-shadow: 0 0 50px rgba(0,0,0,0.5);
}

#lightbox-caption {
  margin-top: 1.5rem;
  color: var(--white);
  font-size: 1.1rem;
  text-align: center;
  line-height: 1.8;
}

/* RESPONSIVE */
@media (max-width: 600px) {
  .god-title {
    font-size: 2rem;
  }
  
  .god-name {
    font-size: 1.5rem;
  }
  
  .section {
    padding: 1.25rem;
  }
  
  .servant-line {
    font-size: 1.2rem;
  }
  
  .declaration-line {
    font-size: 1.2rem;
  }
  
  .throne-model {
    flex-direction: column;
  }
  
  .model-arrow {
    transform: rotate(90deg);
  }
  
  .names-timeline {
    flex-direction: column;
  }
  
  .name-arrow {
    transform: rotate(90deg);
  }
}
</style>

<?php
$console_body_html = ob_get_clean();
require __DIR__ . '/shell.php';