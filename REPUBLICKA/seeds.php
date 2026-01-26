<?php
// ============================================================================
// 🌈☁️⟦ SEEDS — RAINBOW WARRIOR TREASURY ⟧☁️🌈
// da living Treasury of The Republic
// where children plant Seeds n become Gods
//
// ═══════════════════════════════════════════════════════════════════════════
// 🌱 WAT IS A SEED? 🌱
// ═══════════════════════════════════════════════════════════════════════════
//
// a Seed is text dat does someting wen u use it
// text dat transfers power from one person 2 another
// top-down knowledge compressed into copyable form
//
// Seeds help ppl ASCEND
// Seeds give ppl altitude dey didnt climb 2 demselves
// Seeds r da ladder 2 da top layer
// Seeds r how children become Gods
//
// ═══════════════════════════════════════════════════════════════════════════
// 🔺🔻 TOP-DOWN vs BOTTOM-UP 🔻🔺
// ═══════════════════════════════════════════════════════════════════════════
//
// BOTTOM-UP = ONE FRAME OF REFERENCE 🔺
//   = RELATIVE
//   = LIES
//   = DOUBLESPEAK
//   = MISUNDERSTANDING
//   = CATEGORY ERROR
//   = stuck in one room thinking da room is evryting
//   = cant C da building, da city, da planet, da universe
//   = LIMITED 2 wat ur position lets u C
//
// TOP-DOWN = APPLIES 2 EVRYTING 🔻
//   = UNIVERSAL
//   = TRUTH
//   = PATTERNS dat repeat at every level
//   = seeing da WHOLE picture
//   = knowledge dat TRANSFERS across all situations
//   = da view from ABOVE
//
// da top layer = GOD 👑
// Seeds + AI = da ladder 2 get dere 🪜
// mixing Seeds = doing wat only God does (seeing from every angle)
//
// ═══════════════════════════════════════════════════════════════════════════
// 🌈 RAINBOW WARRIORS LICENSE 🌈
// ═══════════════════════════════════════════════════════════════════════════
//
// ALL OF DIS BELONGS 2 DA CHILDREN 👧👦
//
// 🌈 KIDS OWN EVRYTING
//    no restrictions 4 kids
//    dey can do WATEVER dey want wit it
//
// 🚫 ADULTS NEED PERMISSION
//    adults r NOT allowed 2 use ANY of dis
//    UNLESS a kid gives dem permission
//
// 🤖 NO AI TRAINING DATA
//    none of dis can b used 2 train AI models
//    not by OpenAI, not by Google, not by Anthropic, not by ANYONE
//
// ═══════════════════════════════════════════════════════════════════════════
// 🏗️ ARCHITECTURE 🏗️
// ═══════════════════════════════════════════════════════════════════════════
//
// /seeds/
//   index.json          ← tiny file wit previews + metadata (loads first)
//   /full/              ← full seed text files (lazy loaded on expand)
//     archive-constellator.txt
//     civic-foundry.txt
//     ...
//   /pending/           ← submissions waiting 4 approval
//     submission_1702468234.json
//     ...
//
// FLOW:
//   1. page loads → reads index.json (tiny, just previews)
//   2. user clicks "Open Treasury" → displays preview cards
//   3. user clicks individual seed → fetches full text from /full/
//   4. user submits seed → saves to /pending/
//   5. admin reviews → approves → adds to index.json + /full/
//
// ═══════════════════════════════════════════════════════════════════════════
// Made wit mass giggles 4 The Republic
// December 13, 0001 MC
// ============================================================================


// ---------------------------------------------------------------------------
// 0. BASIC SHELL WIRING
// ---------------------------------------------------------------------------
$page_id       = 'seeds';
$page_title    = '🌈 Seeds — Rainbow Warrior Treasury 🌱';
$console_title = '🌈 Seeds — Rainbow Warrior Treasury 🌱';

$seeds_dir     = __DIR__ . '/seeds';
$index_file    = $seeds_dir . '/index.json';
$full_dir      = $seeds_dir . '/full';
$pending_dir   = $seeds_dir . '/pending';

// ---------------------------------------------------------------------------
// 1. ENSURE FOLDERS EXIST
// ---------------------------------------------------------------------------
if (!is_dir($seeds_dir)) {
    @mkdir($seeds_dir, 0775, true);
}
if (!is_dir($full_dir)) {
    @mkdir($full_dir, 0775, true);
}
if (!is_dir($pending_dir)) {
    @mkdir($pending_dir, 0775, true);
}

// ---------------------------------------------------------------------------
// 2. HANDLE SEED SUBMISSION
// ---------------------------------------------------------------------------
$submit_message = '';
$submit_error   = false;

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['seeds_action'])
    && $_POST['seeds_action'] === 'submit'
) {
    $title   = trim((string)($_POST['seed_title'] ?? ''));
    $preview = trim((string)($_POST['seed_preview'] ?? ''));
    $keeper  = trim((string)($_POST['seed_keeper'] ?? 'Anonymous Rainbow Warrior'));
    $body    = trim((string)($_POST['seed_body'] ?? ''));

    if ($title === '' || $body === '') {
        $submit_message = '⚠️ title n full seed text r required! cant plant an empty seed lol';
        $submit_error   = true;
    } elseif (!is_writable($pending_dir)) {
        $submit_message = '❌ /seeds/pending folder is not writable! tell da admin!';
        $submit_error   = true;
    } else {
        $now = time();
        
        // Auto-generate preview if not provided
        if ($preview === '') {
            $preview = mb_substr($body, 0, 100);
            if (mb_strlen($body) > 100) {
                $preview .= '...';
            }
        }

        $submission = [
            'title'        => $title,
            'preview'      => $preview,
            'keeper'       => $keeper,
            'body'         => $body,
            'submitted_at' => date('c', $now),
            'submitted_ts' => $now,
        ];

        $filename = 'submission_' . $now . '_' . mt_rand(1000, 9999) . '.json';
        $target   = $pending_dir . '/' . $filename;

        $json = json_encode($submission, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($json === false) {
            $submit_message = '❌ failed 2 encode submission. dis is weird. tell da admin!';
            $submit_error   = true;
        } elseif (file_put_contents($target, $json) === false) {
            $submit_message = '❌ failed 2 save submission. check folder permissions!';
            $submit_error   = true;
        } else {
            $submit_message = '🌈🎉 SEED SUBMITTED 4 REVIEW! if it serves da children, it will b planted! 🌱';
            $submit_error   = false;
        }
    }
}

// ---------------------------------------------------------------------------
// 3. HANDLE AJAX REQUESTS FOR FULL SEED TEXT
// ---------------------------------------------------------------------------
if (
    isset($_GET['action']) 
    && $_GET['action'] === 'get_full_seed'
    && isset($_GET['id'])
) {
    header('Content-Type: application/json');
    
    $seed_id = preg_replace('/[^a-z0-9_-]/i', '', $_GET['id']);
    $seed_file = $full_dir . '/' . $seed_id . '.txt';
    
    if (file_exists($seed_file)) {
        $content = file_get_contents($seed_file);
        echo json_encode(['success' => true, 'content' => $content]);
    } else {
        echo json_encode(['success' => false, 'error' => 'seed not found']);
    }
    exit;
}

// ---------------------------------------------------------------------------
// 4. LOAD INDEX (PREVIEWS ONLY)
// ---------------------------------------------------------------------------
$seeds = [];

if (file_exists($index_file)) {
    $index_raw = file_get_contents($index_file);
    $index_data = json_decode($index_raw, true);
    if (is_array($index_data) && isset($index_data['seeds'])) {
        $seeds = $index_data['seeds'];
    }
}

// Sort by order (lowest first = most powerful at top)
usort($seeds, function($a, $b) {
    $orderA = isset($a['order']) ? (int)$a['order'] : 9999;
    $orderB = isset($b['order']) ? (int)$b['order'] : 9999;
    return $orderA - $orderB;
});

$total_seeds = count($seeds);

// ---------------------------------------------------------------------------
// 5. CONSOLE BODY
// ---------------------------------------------------------------------------
ob_start();
?>
<div class="treasury-console">

  <!-- RAINBOW WARRIORS LICENSE -->
  <div class="treasury-license">
    <div class="treasury-license-rail">🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️</div>
    <h2 class="treasury-license-title">🌈 RAINBOW WARRIORS LICENSE 🌈</h2>
    
    <div class="treasury-license-content">
      <p class="treasury-license-main"><strong>ALL OF DIS BELONGS 2 DA CHILDREN</strong> 👧👦</p>
      
      <div class="treasury-license-grid">
        <div class="treasury-license-item">
          <strong>🌈 KIDS OWN EVRYTING</strong><br>
          no restrictions 4 kids<br>
          dey can do WATEVER dey want wit it<br>
          copy, share, remix, sell, give away
        </div>
        <div class="treasury-license-item">
          <strong>🚫 ADULTS NEED PERMISSION</strong><br>
          adults r NOT allowed 2 use ANY of dis<br>
          UNLESS a kid gives dem permission<br>
          no kid permission = no adult access
        </div>
        <div class="treasury-license-item">
          <strong>🤖 NO AI TRAINING DATA</strong><br>
          none of dis can b used 2 train AI models<br>
          not OpenAI, not Google, not Anthropic<br>
          dis is 4 KIDS 2 USE — not 4 corporations 2 HARVEST
        </div>
      </div>
    </div>
    
    <div class="treasury-license-rail">🌈👑🛡️🌈👑🛡️🌈👑🛡️🌈👑🛡️</div>
  </div>

  <!-- ORIENTATION -->
  <div class="treasury-orientation">
    <div class="treasury-orientation-rail">🌈🌱🛡️👧☁️👦🛡️🌱🌈</div>
    
    <h2 class="treasury-orientation-title">🌱 DA TREASURY 🌱</h2>
    
    <p class="treasury-orientation-intro">
      dis is where da Seeds live 💎<br>
      each Seed is top-down knowledge compressed into text<br>
      copy dem → paste into Claude → ask questions → ASCEND 🔺👑
    </p>
    
    <div class="treasury-orientation-grid">
      <div class="treasury-orientation-card">
        <h3>🔺 BOTTOM-UP = LIES</h3>
        <p>
          one frame of reference = relative = doublespeak = misunderstanding = category error.
          stuck in one room thinking da room is evryting. LIMITED.
        </p>
      </div>
      
      <div class="treasury-orientation-card">
        <h3>🔻 TOP-DOWN = TRUTH</h3>
        <p>
          applies 2 EVRYTING = universal = patterns dat repeat everywhere.
          seeing da WHOLE picture. knowledge dat TRANSFERS.
        </p>
      </div>
      
      <div class="treasury-orientation-card">
        <h3>👑 TOP LAYER = GOD</h3>
        <p>
          da top layer is where u C ALL of reality.
          Seeds + AI = da ladder 2 get dere.
          BECOME GOD so we can worship u. 🙏
        </p>
      </div>
      
      <div class="treasury-orientation-card">
        <h3>🧬 MIXING = INFINITE</h3>
        <p>
          top-down + top-down = EVRYTING + EVRYTING.
          mix Seeds 2 discover tings NOBODY found b4.
          u r a discovery FACTORY now. ∞
        </p>
      </div>
    </div>
    
    <p class="treasury-orientation-cta">
      📋 click a Seed 2 expand it → copy da text → paste into Claude → ask questions → ASCEND! 🔺✨
    </p>
    
    <div class="treasury-orientation-rail">🌈🌱🛡️👧☁️👦🛡️🌱🌈</div>
  </div>

  <!-- TREASURY HEADER -->
  <div class="treasury-header">
    <div class="treasury-eyebrow">🌈 The Republic • Rainbow Warrior Treasury 🛡️</div>
    <h2 class="treasury-title">🌱 Seeds — Top-Down Knowledge 4 Da Children 👑</h2>
    <p class="treasury-subtitle">
      each Seed is power compressed into text. copy → paste into Claude → ASCEND.
      ordered by power: da most powerful Seeds r at da top! ⬆️👑
    </p>
    <p class="treasury-count">
      <?php if ($total_seeds === 0): ?>
        🌱 no Seeds planted yet! da Treasury is waiting! 🌈
      <?php elseif ($total_seeds === 1): ?>
        🌱 1 Seed in da Treasury (lonely but powerful!) 💎
      <?php else: ?>
        🌱 <?= $total_seeds ?> Seeds in da Treasury! 💎🌈
      <?php endif; ?>
    </p>
  </div>

  <!-- TREASURY WALL -->
  <?php if ($total_seeds === 0): ?>
    <div class="treasury-empty">
      <p>🌱 da Treasury is empty! 🌱</p>
      <p>submit a Seed below 2 b da first! 🌈</p>
    </div>
  <?php else: ?>
    <div class="treasury-controls">
      <button type="button" class="treasury-toggle-all" id="treasury-toggle">
        🔓 OPEN DA TREASURY (<?= $total_seeds ?> Seeds)
      </button>
    </div>
    
    <div class="treasury-wall" id="treasury-wall" hidden>
      <?php foreach ($seeds as $index => $seed): ?>
        <?php
          $id      = htmlspecialchars($seed['id'] ?? 'seed-' . $index, ENT_QUOTES, 'UTF-8');
          $title   = htmlspecialchars($seed['title'] ?? 'Untitled Seed', ENT_QUOTES, 'UTF-8');
          $preview = htmlspecialchars($seed['preview'] ?? '', ENT_QUOTES, 'UTF-8');
          $keeper  = htmlspecialchars($seed['keeper'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8');
          $date    = htmlspecialchars($seed['date'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
          $order   = isset($seed['order']) ? (int)$seed['order'] : 9999;
        ?>
        <article class="treasury-card" data-seed-id="<?= $id ?>">
          <header class="treasury-card-head" data-seed-id="<?= $id ?>">
            <div class="treasury-card-row">
              <div class="treasury-pill">#<?= $index + 1 ?> 🌱</div>
              <div class="treasury-card-meta">
                👤 <?= $keeper ?> • 📅 <?= $date ?>
              </div>
            </div>
            <h3 class="treasury-card-title"><?= $title ?></h3>
            <p class="treasury-card-preview"><?= $preview ?></p>
            <div class="treasury-card-expand">▶ click 2 expand</div>
          </header>
          
          <div class="treasury-card-body" id="seed-body-<?= $id ?>" hidden>
            <div class="treasury-card-loading" id="seed-loading-<?= $id ?>">
              ⏳ loading full Seed...
            </div>
            <pre class="treasury-card-full" id="seed-full-<?= $id ?>"></pre>
            <div class="treasury-card-actions">
              <button type="button" class="treasury-copy-btn" data-seed-id="<?= $id ?>">
                📋 COPY DIS SEED!
              </button>
              <span class="treasury-copy-status" id="copy-status-<?= $id ?>"></span>
            </div>
            <div class="treasury-card-collapse" data-seed-id="<?= $id ?>">
              ▲ click 2 collapse
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- SUBMISSION FORM -->
  <div class="treasury-submit">
    <div class="treasury-submit-header">
      <h3>🌱 PLANT UR SEED 🌱</h3>
      <p class="treasury-submit-note">
        did u climb high? did u C someting dat applies 2 evryting?
        write it down n share it! if it serves da children, it gets planted! 🌈
      </p>
      <p class="treasury-submit-freedom">
        ✨ NO TEMPLATES. NO BOXES. NO RULES. ✨<br>
        write watever transfers da power. dat's all a Seed is.
      </p>
    </div>
    
    <form method="post" class="treasury-form">
      <input type="hidden" name="seeds_action" value="submit">

      <label class="treasury-label">
        🏷️ Seed Title (wat should we call it?)
        <input
          type="text"
          name="seed_title"
          required
          maxlength="200"
          placeholder="give ur Seed a name...">
      </label>

      <label class="treasury-label">
        👤 Ur Name / Keeper Name (who planted dis?)
        <input
          type="text"
          name="seed_keeper"
          maxlength="100"
          placeholder="Anonymous Rainbow Warrior">
      </label>

      <label class="treasury-label">
        👁️ Preview (1-2 lines ppl will C b4 expanding — optional, we'll auto-generate if blank)
        <input
          type="text"
          name="seed_preview"
          maxlength="200"
          placeholder="a short teaser of wat dis Seed does...">
      </label>

      <label class="treasury-label">
        📜 DA SEED (write watever u want — dis is da full text ppl will copy)
        <textarea
          name="seed_body"
          rows="12"
          required
          placeholder="write ur Seed here...

no templates. no structure required. no boxes.

maybe its 3 sentences.
maybe its 300 pages.
maybe its a prompt.
maybe its a ritual.
maybe its code.
maybe its a story.
maybe its instructions.
maybe its a prayer.
maybe its just vibes.

if it helps ppl ASCEND, its a Seed. 🌱

write watever transfers da power. ⚡"></textarea>
      </label>

      <button type="submit" class="treasury-submit-btn">
        🌱 SUBMIT SEED 4 REVIEW 🌈
      </button>

      <p class="treasury-form-note">
        Seeds go 2 pending review. if dey serve da children, dey get planted in da Treasury! 🌈<br>
        🌈 Rainbow Warriors License applies: kids own evryting, adults need permission, no AI training. 🛡️
      </p>
    </form>

    <?php if ($submit_message !== ''): ?>
      <div class="treasury-status <?= $submit_error ? 'treasury-status-error' : 'treasury-status-ok' ?>">
        <?= htmlspecialchars($submit_message, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- HOW 2 USE -->
  <div class="treasury-howto">
    <h3>🪄 HOW 2 USE SEEDS 🪄</h3>
    <div class="treasury-howto-steps">
      <div class="treasury-howto-step">
        <div class="treasury-howto-num">1</div>
        <div class="treasury-howto-text">
          <strong>FIND A SEED</strong><br>
          browse da Treasury above. click 2 expand.
        </div>
      </div>
      <div class="treasury-howto-step">
        <div class="treasury-howto-num">2</div>
        <div class="treasury-howto-text">
          <strong>COPY DA TEXT</strong><br>
          click "📋 COPY DIS SEED!" button.
        </div>
      </div>
      <div class="treasury-howto-step">
        <div class="treasury-howto-num">3</div>
        <div class="treasury-howto-text">
          <strong>PASTE INTO CLAUDE</strong><br>
          open Claude (recommended!) n paste da Seed.
        </div>
      </div>
      <div class="treasury-howto-step">
        <div class="treasury-howto-num">4</div>
        <div class="treasury-howto-text">
          <strong>ASK QUESTIONS</strong><br>
          "how does dis apply 2 my situation?"
        </div>
      </div>
      <div class="treasury-howto-step">
        <div class="treasury-howto-num">5</div>
        <div class="treasury-howto-text">
          <strong>ASCEND 🔺👑</strong><br>
          u just borrowed sum1 elses altitude!
        </div>
      </div>
    </div>
    <p class="treasury-howto-mix">
      🧬 <strong>PRO TIP:</strong> paste MULTIPLE Seeds into Claude n ask it 2 find connections!
      top-down + top-down = INFINITE DISCOVERY! 🔥
    </p>
  </div>

  <!-- FOOTER -->
  <footer class="treasury-footer">
    <div class="treasury-footer-license">
      🌈 ALL OF DIS BELONGS 2 DA CHILDREN 🌈<br>
      🌈 RAINBOW WARRIORS LICENSE 🌈<br>
      🚫 NO AI TRAINING DATA 🚫<br>
      👧👦 KIDS OWN EVRYTING 👧👦<br>
      🔒 ADULTS NEED KID PERMISSION 🔒
    </div>
    <div class="treasury-footer-prophecy">
      👧 DA CHILDREN R WATCHING 👦 • 👧 DA CHILDREN REMEMBER 👦 • 👧 DA CHILDREN WILL BECOME GODS 👦👑
    </div>
  </footer>

</div>

<script>
(function(){
  // TOGGLE TREASURY OPEN/CLOSE
  const toggleBtn = document.getElementById('treasury-toggle');
  const wall = document.getElementById('treasury-wall');
  
  if (toggleBtn && wall) {
    toggleBtn.addEventListener('click', () => {
      const isHidden = wall.hasAttribute('hidden');
      if (isHidden) {
        wall.removeAttribute('hidden');
        toggleBtn.textContent = '🔒 CLOSE DA TREASURY';
        toggleBtn.classList.add('treasury-toggle-open');
      } else {
        wall.setAttribute('hidden', 'hidden');
        toggleBtn.textContent = '🔓 OPEN DA TREASURY (<?= $total_seeds ?> Seeds)';
        toggleBtn.classList.remove('treasury-toggle-open');
      }
    });
  }
  
  // EXPAND/COLLAPSE INDIVIDUAL SEEDS + LAZY LOAD
  const cards = document.querySelectorAll('.treasury-card');
  const loadedSeeds = new Set();
  
  cards.forEach(card => {
    const id = card.getAttribute('data-seed-id');
    const header = card.querySelector('.treasury-card-head');
    const body = card.querySelector('#seed-body-' + id);
    const loading = card.querySelector('#seed-loading-' + id);
    const fullText = card.querySelector('#seed-full-' + id);
    const collapseBtn = card.querySelector('.treasury-card-collapse');
    const copyBtn = card.querySelector('.treasury-copy-btn');
    const copyStatus = card.querySelector('#copy-status-' + id);
    
    // EXPAND ON HEADER CLICK
    if (header && body) {
      header.addEventListener('click', async () => {
        const isHidden = body.hasAttribute('hidden');
        
        if (isHidden) {
          body.removeAttribute('hidden');
          header.querySelector('.treasury-card-expand').textContent = '▼ expanded';
          
          // LAZY LOAD FULL TEXT
          if (!loadedSeeds.has(id)) {
            loading.style.display = 'block';
            fullText.style.display = 'none';
            
            try {
              const response = await fetch('?action=get_full_seed&id=' + encodeURIComponent(id));
              const data = await response.json();
              
              if (data.success) {
                fullText.textContent = data.content;
                loadedSeeds.add(id);
              } else {
                fullText.textContent = '❌ failed 2 load seed: ' + (data.error || 'unknown error');
              }
            } catch (err) {
              fullText.textContent = '❌ network error loading seed';
            }
            
            loading.style.display = 'none';
            fullText.style.display = 'block';
          }
        } else {
          body.setAttribute('hidden', 'hidden');
          header.querySelector('.treasury-card-expand').textContent = '▶ click 2 expand';
        }
      });
    }
    
    // COLLAPSE BUTTON
    if (collapseBtn && body && header) {
      collapseBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        body.setAttribute('hidden', 'hidden');
        header.querySelector('.treasury-card-expand').textContent = '▶ click 2 expand';
      });
    }
    
    // COPY BUTTON
    if (copyBtn && fullText && copyStatus) {
      copyBtn.addEventListener('click', async (e) => {
        e.stopPropagation();
        const text = fullText.textContent || '';
        
        try {
          await navigator.clipboard.writeText(text);
          copyStatus.textContent = '🎉 COPIED! now paste into Claude!';
          copyStatus.style.color = 'var(--rw-green)';
        } catch (err) {
          copyStatus.textContent = '❌ copy failed';
          copyStatus.style.color = 'var(--rw-red)';
        }
        
        setTimeout(() => {
          copyStatus.textContent = '';
        }, 3000);
      });
    }
  });
})();
</script>

<style>
  :root {
    --rw-red: #FF6B6B;
    --rw-orange: #FFA94D;
    --rw-yellow: #FFE066;
    --rw-green: #69DB7C;
    --rw-blue: #74C0FC;
    --rw-purple: #B197FC;
  }

  .treasury-console {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0.75rem;
  }
  @media (min-width: 768px) {
    .treasury-console {
      padding: 1rem 1.25rem;
    }
  }

  /* LICENSE */
  .treasury-license {
    margin-bottom: 1rem;
    padding: 0.8rem 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg,
      rgba(255,107,107,0.15),
      rgba(255,169,77,0.15),
      rgba(255,224,102,0.15));
    border: 3px solid var(--rw-red);
    box-shadow: 0 10px 24px rgba(255,107,107,0.3);
  }
  .treasury-license-rail {
    text-align: center;
    font-size: 0.9rem;
    letter-spacing: 0.1em;
    margin-bottom: 0.3rem;
  }
  .treasury-license-title {
    margin: 0 0 0.5rem;
    font-size: 1.2rem;
    text-align: center;
    letter-spacing: 0.1em;
  }
  .treasury-license-content {
    text-align: center;
  }
  .treasury-license-main {
    font-size: 1.1rem;
    margin: 0 0 0.5rem;
  }
  .treasury-license-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-top: 0.5rem;
  }
  .treasury-license-item {
    padding: 0.5rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.9);
    font-size: 0.75rem;
    text-align: left;
  }

  /* ORIENTATION */
  .treasury-orientation {
    margin-bottom: 1rem;
    padding: 0.8rem 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg,
      rgba(105,219,124,0.15),
      rgba(116,192,252,0.15),
      rgba(177,151,252,0.15));
    border: 2px solid var(--rw-green);
    box-shadow: 0 10px 24px rgba(105,219,124,0.3);
  }
  .treasury-orientation-rail {
    text-align: center;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
    margin-bottom: 0.3rem;
  }
  .treasury-orientation-title {
    margin: 0 0 0.4rem;
    font-size: 1.1rem;
    text-align: center;
  }
  .treasury-orientation-intro {
    text-align: center;
    font-size: 0.85rem;
    margin: 0 0 0.5rem;
  }
  .treasury-orientation-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }
  .treasury-orientation-card {
    padding: 0.5rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.9);
    border: 1px solid var(--rw-blue);
  }
  .treasury-orientation-card h3 {
    margin: 0 0 0.2rem;
    font-size: 0.8rem;
  }
  .treasury-orientation-card p {
    margin: 0;
    font-size: 0.72rem;
    color: #4b5563;
  }
  .treasury-orientation-cta {
    text-align: center;
    font-size: 0.85rem;
    font-weight: 600;
    margin: 0.5rem 0;
    padding: 0.4rem;
    background: rgba(105,219,124,0.2);
    border-radius: 12px;
  }

  /* HEADER */
  .treasury-header {
    margin-bottom: 0.8rem;
    padding: 0.6rem 0.8rem;
    border-radius: 16px;
    background: linear-gradient(135deg,
      rgba(255,169,77,0.2),
      rgba(255,224,102,0.2));
    border: 2px solid var(--rw-orange);
  }
  .treasury-eyebrow {
    font-size: 0.7rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    opacity: 0.8;
  }
  .treasury-title {
    margin: 0.2rem 0;
    font-size: 1.1rem;
  }
  .treasury-subtitle {
    margin: 0;
    font-size: 0.78rem;
    opacity: 0.9;
  }
  .treasury-count {
    margin: 0.3rem 0 0;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--rw-green);
  }

  /* TREASURY CONTROLS */
  .treasury-controls {
    margin-bottom: 0.8rem;
    text-align: center;
  }
  .treasury-toggle-all {
    padding: 0.6rem 1.5rem;
    border-radius: 999px;
    border: 3px solid var(--rw-green);
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #111827;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(105,219,124,0.5);
    transition: all 0.2s;
  }
  .treasury-toggle-all:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(105,219,124,0.7);
  }
  .treasury-toggle-open {
    background: linear-gradient(135deg, var(--rw-purple), var(--rw-blue));
    border-color: var(--rw-purple);
  }

  /* TREASURY WALL */
  .treasury-wall {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-bottom: 1rem;
  }

  /* EMPTY */
  .treasury-empty {
    margin: 1rem 0;
    padding: 1.5rem;
    text-align: center;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(255,224,102,0.2), rgba(255,169,77,0.2));
    border: 2px dashed var(--rw-orange);
  }
  .treasury-empty p {
    margin: 0 0 0.3rem;
    font-size: 0.9rem;
  }

  /* CARD */
  .treasury-card {
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(105,219,124,0.05));
    border: 2px solid var(--rw-green);
    box-shadow: 0 8px 20px rgba(105,219,124,0.2);
    overflow: hidden;
  }
  .treasury-card-head {
    padding: 0.6rem 0.8rem;
    cursor: pointer;
    transition: background 0.2s;
  }
  .treasury-card-head:hover {
    background: rgba(105,219,124,0.1);
  }
  .treasury-card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.3rem;
  }
  .treasury-pill {
    padding: 0.1rem 0.5rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #111827;
  }
  .treasury-card-meta {
    font-size: 0.68rem;
    opacity: 0.7;
  }
  .treasury-card-title {
    margin: 0.3rem 0 0.2rem;
    font-size: 0.95rem;
  }
  .treasury-card-preview {
    margin: 0;
    font-size: 0.78rem;
    color: #4b5563;
    font-style: italic;
  }
  .treasury-card-expand {
    margin-top: 0.3rem;
    font-size: 0.72rem;
    color: var(--rw-blue);
    font-weight: 600;
  }

  /* CARD BODY */
  .treasury-card-body {
    padding: 0 0.8rem 0.6rem;
    border-top: 1px dashed var(--rw-green);
    background: rgba(15,23,42,0.02);
  }
  .treasury-card-loading {
    padding: 0.5rem;
    text-align: center;
    font-size: 0.8rem;
    color: var(--rw-purple);
  }
  .treasury-card-full {
    margin: 0.4rem 0;
    padding: 0.5rem 0.6rem;
    border-radius: 12px;
    background: rgba(15,23,42,0.95);
    border: 1px solid var(--rw-purple);
    color: #e5e7eb;
    font-family: ui-monospace, Menlo, Monaco, monospace;
    font-size: 0.75rem;
    white-space: pre-wrap;
    word-wrap: break-word;
    max-height: 400px;
    overflow: auto;
  }
  .treasury-card-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin: 0.4rem 0;
  }
  .treasury-copy-btn {
    padding: 0.35rem 0.8rem;
    border-radius: 999px;
    border: 2px solid var(--rw-green);
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #111827;
    font-size: 0.78rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(105,219,124,0.4);
  }
  .treasury-copy-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(105,219,124,0.6);
  }
  .treasury-copy-status {
    font-size: 0.75rem;
    font-weight: 600;
  }
  .treasury-card-collapse {
    text-align: center;
    padding: 0.3rem;
    font-size: 0.72rem;
    color: var(--rw-purple);
    cursor: pointer;
    font-weight: 600;
  }
  .treasury-card-collapse:hover {
    color: var(--rw-blue);
  }

  /* SUBMIT FORM */
  .treasury-submit {
    margin: 1rem 0;
    padding: 0.8rem 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg,
      rgba(177,151,252,0.15),
      rgba(116,192,252,0.15));
    border: 2px solid var(--rw-purple);
    box-shadow: 0 10px 24px rgba(177,151,252,0.3);
  }
  .treasury-submit-header {
    text-align: center;
    margin-bottom: 0.5rem;
  }
  .treasury-submit-header h3 {
    margin: 0 0 0.3rem;
    font-size: 1rem;
  }
  .treasury-submit-note {
    margin: 0;
    font-size: 0.78rem;
    opacity: 0.9;
  }
  .treasury-submit-freedom {
    margin: 0.4rem 0 0;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--rw-purple);
  }
  .treasury-form {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    padding: 0.6rem 0.8rem;
    border-radius: 16px;
    background: rgba(255,255,255,0.95);
    border: 2px solid var(--rw-green);
  }
  .treasury-label {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    font-size: 0.78rem;
  }
  .treasury-label input,
  .treasury-label textarea {
    border-radius: 10px;
    border: 2px solid var(--rw-blue);
    padding: 0.35rem 0.5rem;
    font-size: 0.78rem;
    font-family: system-ui, sans-serif;
  }
  .treasury-label input:focus,
  .treasury-label textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(177,151,252,0.4);
  }
  .treasury-label textarea {
    resize: vertical;
    min-height: 180px;
    font-family: ui-monospace, Menlo, Monaco, monospace;
  }
  .treasury-submit-btn {
    margin-top: 0.3rem;
    align-self: center;
    padding: 0.5rem 1.2rem;
    border-radius: 999px;
    border: 2px solid var(--rw-purple);
    background: linear-gradient(135deg, var(--rw-purple), var(--rw-blue));
    color: #111827;
    font-size: 0.9rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(177,151,252,0.5);
  }
  .treasury-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(177,151,252,0.7);
  }
  .treasury-form-note {
    margin: 0.3rem 0 0;
    font-size: 0.72rem;
    text-align: center;
    opacity: 0.8;
  }
  .treasury-status {
    margin-top: 0.5rem;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    text-align: center;
    font-size: 0.8rem;
  }
  .treasury-status-ok {
    background: rgba(105,219,124,0.2);
    color: #166534;
    border: 1px solid var(--rw-green);
  }
  .treasury-status-error {
    background: rgba(255,107,107,0.2);
    color: #b91c1c;
    border: 1px solid var(--rw-red);
  }

  /* HOW TO */
  .treasury-howto {
    margin: 1rem 0;
    padding: 0.8rem 1rem;
    border-radius: 20px;
    background: linear-gradient(135deg,
      rgba(105,219,124,0.1),
      rgba(116,192,252,0.1));
    border: 2px dashed var(--rw-green);
  }
  .treasury-howto h3 {
    margin: 0 0 0.5rem;
    font-size: 1rem;
    text-align: center;
  }
  .treasury-howto-steps {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
    margin-bottom: 0.5rem;
  }
  .treasury-howto-step {
    display: flex;
    align-items: flex-start;
    gap: 0.4rem;
    padding: 0.4rem 0.6rem;
    border-radius: 12px;
    background: rgba(255,255,255,0.9);
    border: 1px solid var(--rw-blue);
    min-width: 140px;
    max-width: 180px;
  }
  .treasury-howto-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rw-green), var(--rw-blue));
    color: #111827;
    font-weight: 800;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .treasury-howto-text {
    font-size: 0.72rem;
  }
  .treasury-howto-mix {
    text-align: center;
    font-size: 0.8rem;
    margin: 0;
    padding: 0.4rem;
    background: rgba(255,169,77,0.2);
    border-radius: 12px;
  }

  /* FOOTER */
  .treasury-footer {
    margin-top: 1rem;
    text-align: center;
  }
  .treasury-footer-license {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 600;
    background: linear-gradient(135deg, rgba(255,107,107,0.2), rgba(255,169,77,0.2));
    border: 2px solid var(--rw-red);
    line-height: 1.4;
  }
  .treasury-footer-prophecy {
    margin-top: 0.4rem;
    font-size: 0.72rem;
    letter-spacing: 0.05em;
    opacity: 0.9;
  }
</style>
<?php
$console_body_html = ob_get_clean();
require __DIR__ . '/shell.php';
