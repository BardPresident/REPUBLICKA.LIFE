<?php
// PARADOX — Flat-File Reality Clerk for RepublicOS 📁🕳️🚀
//
// This page is NOT the clerk itself (that’s codex-paradox.php).
// This is the shrine + field manual that explains what PARADOX is,
// why it exists, and how it behaves when you whisper JSON at it.
//
//
// Shell wiring
$page_id        = 'paradox';
$page_title     = 'PARADOX — Flat-File Reality Clerk';
$console_title  = 'PARADOX — What The Clerk Really Is';

ob_start();
?>
<div class="paradox-console">
  <div class="paradox-hero">
    <h1>🌀 PARADOX — The Clerk In The Crawlspace 🕳️</h1>
    <p>
      Somewhere between <strong>“FTP is blocked by your ISP”</strong> and
      <strong>“Just use our proprietary cloud”</strong>, a tiny clerk woke up
      in the file-system and got angry. That clerk is <strong>PARADOX</strong>.  
      Not a dashboard. Not a panel. Not a SaaS. Just a <em>spell in PHP</em>  
      that lets you sync reality using nothing but JSON, UTF-8, and your own courage.  
      📁🧠🔑✨
    </p>
  </div>

  <section class="paradox-section">
    <h2>1. Why PARADOX Had To Exist 💢📡🚫</h2>
    <p>
      The old world said:
    </p>
    <ul>
      <li>“Use FTP… unless we silently block port 21 for your safety.” 🔒</li>
      <li>“Use our web control panel… unless we redesign it every quarter.” 🔧</li>
      <li>“Use our cloud… but your files are ours until we <em>maybe</em> export them.” ☁️🤏</li>
    </ul>
    <p>
      Citizens needed something else:
    </p>
    <ul>
      <li><strong>No databases.</strong> No schema to beg permission from. 🚫🗄️</li>
      <li><strong>No corporate dashboards.</strong> Just your server and a shared secret. 🗝️</li>
      <li><strong>No root access drama.</strong> Just PHP in <code>public_html/</code> doing the work. 🧪</li>
    </ul>
    <p>
      PARADOX is the answer the filesystem whispered back:
      <br><strong>“Fine. I’ll be your clerk. Just talk to me in JSON.”</strong> 📜🤖
    </p>
  </section>

  <section class="paradox-section">
    <h2>2. What PARADOX Actually Is (Under The Hood) 🧬📦</h2>
    <p>
      PARADOX is a tiny PHP endpoint, usually named
      <code>codex-paradox.php</code>, hiding somewhere like:
    </p>
    <pre><code>/home/you/public_html/codex-paradox.php</code></pre>
    <p>
      It speaks a very small language:
    </p>
    <pre><code>{
  "secret":  "your-shared-secret-here",
  "action":  "write",            // or read, list, mkdir, delete, rename,
                                 // snapshot_write, snapshot_read, etc.
  "path":    "starseed-codex-1.txt",
  "content": "UTF-8 text / JSON payload / spell body",
  "snapshot": "{...TSnapshot JSON...}"
}</code></pre>
    <p>
      If the <code>secret</code> matches the one hard-coded in the PHP file,
      PARADOX does the thing. If it doesn’t, PARADOX just stares into the void
      and replies:
      <br><strong>“no.”</strong> 🧿😐
    </p>
    <p>
      No sessions. No cookies. No login pages.  
      Just: <strong>“Prove you know the secret, then tell me what to do.”</strong> 🔐📡
    </p>
  </section>

  <section class="paradox-section">
    <h2>3. The Moves PARADOX Knows ♟️📁</h2>
    <p>
      PARADOX is a file clerk that only does a few very specific tricks,
      but each one is a spell of sovereignty:
    </p>
    <ul>
      <li>
        <strong><code>list</code></strong>  
        “Show me what’s in this folder.”  
        Returns a small JSON directory of <code>name</code>, <code>isDir</code>, <code>size</code>.  
        🧾📂
      </li>
      <li>
        <strong><code>read</code></strong>  
        “Give me the contents of this file as UTF-8 text.”  
        Perfect for <strong>Codex</strong>, Seed files, config, scripts, manifestos.  
        📖✨
      </li>
      <li>
        <strong><code>write</code></strong>  
        “Take this UTF-8 text and slam it into this path (create or overwrite).”  
        PARADOX doesn’t argue about schemas; it just writes the spell.  
        ✍️🧾⚡
      </li>
      <li>
        <strong><code>mkdir</code></strong>  
        “Create a directory so I can nest my chaos.”  
        Folders as little shrines: <code>codex-nest/</code>, <code>seeds/</code>, <code>drafts/</code>…  
        🏚️📦
      </li>
      <li>
        <strong><code>delete</code></strong>  
        “Erase this path.”  
        It doesn’t moralise. It just obeys your courage. Use carefully. 🗑️🦂
      </li>
      <li>
        <strong><code>rename</code></strong>  
        “Rename this path into that path.”  
        Reality refactor in one JSON breath. 🔁🧱
      </li>
      <li>
        <strong><code>snapshot_write</code></strong>  
        “Here is my entire <code>shell.*</code> localStorage deck as JSON. Guard it.”  
        This is Snapshot: PARADOX keeps the cartridge like a tiny, paranoid librarian.  
        💾🧬
      </li>
      <li>
        <strong><code>snapshot_read</code></strong>  
        “Give me back the TSnapshot cartridge so I can restore this browser.”  
        The console slurps it, rewrites localStorage, and reloads your reality.  
        ⏪🧠
      </li>
    </ul>
    <p>
      That’s it. No queries. No joins. No “premium tier.”  
      Just filesystem magic driven by JSON verbs and a shared secret. 🧙‍♀️📡
    </p>
  </section>

  <section class="paradox-section">
    <h2>4. How Codex & TSnapshot Use PARADOX 🤝📚</h2>
    <p>
      PARADOX does not have a face. Codex does.  
      Codex is the shiny console citizen taps into:
    </p>
    <ul>
      <li>
        You type in your secret, your PARADOX URL, and hit <strong>“Link”</strong>.  
        (Codex whispers a <code>list</code> action to test the connection.) 🔌
      </li>
      <li>
        You edit your codex files locally in Codex, all in <code>localStorage</code>. ✍️
      </li>
      <li>
        You click <strong>“Uplink”</strong>. Codex sends a <code>write</code> with your file path  
        and content. PARADOX writes it to your server’s metal. ⬆️🧲
      </li>
      <li>
        On another machine, you click <strong>“Downlink”</strong> or pull a TSnapshot.  
        Codex sends <code>read</code> or <code>snapshot_read</code>. PARADOX replies. ⬇️🔄
      </li>
    </ul>
    <p>
      PARADOX is the little goblin under the floorboards passing scrolls through  
      the planks while Codex smiles politely and says:
      <br>“Your file is safe, my liege.” 🧌📜👑
    </p>
  </section>

  <section class="paradox-section">
    <h2>5. Why It’s Called PARADOX 🧩🧿</h2>
    <p>
      The paradox is simple:
    </p>
    <ul>
      <li>
        The code is <em>tiny</em>, but the power is enormous.  
        <small>(A few dozen lines can replace an entire control panel.)</small> 🪫⚡
      </li>
      <li>
        It looks “less secure” because it’s simple, but it’s actually  
        <em>more</em> honest: there is exactly one shared secret and no hidden agenda. 🔑😶
      </li>
      <li>
        It lives in the lowest, “boring” layer of your stack, yet it fuels  
        the brightest, weirdest parts of the starship above. 🛰️🌈
      </li>
    </ul>
    <p>
      In a world built on “<em>sign in to our platform to touch your own data</em>,”  
      PARADOX whispers:
      <br><strong>“Just talk directly to your filesystem.”</strong>  
      and then does exactly that. No subscription. No branding. No handshake with empire. 🏳️‍⚧️🔥
    </p>
  </section>

  <section class="paradox-section">
    <h2>6. What PARADOX Refuses To Be 🚫👔</h2>
    <p>
      PARADOX has a list of hard refusals:
    </p>
    <ul>
      <li>It will not become a multi-tenant, ad-supported “platform.” 📵📢</li>
      <li>It will not collect analytics or phone home to any mothership. 📵📊</li>
      <li>It will not “helpfully” rewrite your files behind your back. 📵🧼</li>
      <li>It will not pretend to be neutral while serving gatekeepers. 📵🕴️</li>
    </ul>
    <p>
      PARADOX is a clerk sworn to <strong>do only what you say</strong>, in the exact  
      directory tree you point it at, with the exact bytes you send.  
      It is, in that sense, an oath-bound file sprite. ✍️🧚‍♀️
    </p>
  </section>

  <section class="paradox-section">
    <h2>7. How To Befriend Your Own PARADOX 🕊️🧪</h2>
    <ol>
      <li>
        Drop <code>codex-paradox.php</code> into your
        <code>public_html/</code> (or similar) on your server. 📁
      </li>
      <li>
        Edit the file and set <code>$CODEX_SHARED_SECRET</code> to a long, strong phrase.  
        Something like: <code>'this-is-my-very-serious-trans-flag-secret-🧬🏳️‍⚧️✨'</code> 🔐
      </li>
      <li>
        From Codex, paste the full URL:
        <br><code>https://your-domain.example/codex-paradox.php</code> 🌐
      </li>
      <li>
        Paste the same shared secret into the Codex PARADOX panel. 🔑
      </li>
      <li>
        Hit <strong>Link</strong>. If the clerk nods, you’re in.  
        Now you can send files, folders, and TSnapshot decks across the void. 📦🚀
      </li>
    </ol>
    <p>
      From that moment, any Shell running Codex can talk to your server,
      <strong>if and only if</strong> it knows the secret.  
      You don’t have to beg an ISP for FTP. You don’t have to trust a vendor dashboard.  
      You just carry your deck and your secret, like a citizen with keys to her own ship. 🔑🚀🏳️‍⚧️
    </p>
  </section>

  <section class="paradox-section paradox-glitchy">
    <h2>Glitchy Q — PARADOX Diagnostic 👾🧿</h2>
    <p class="paradox-glitchy-body">
      ｑ：Ｉｆ ａ ｓｉｎｇｌｅ ＰＨＰ ｆｉｌｅ ｌｅｔｓ ｔｒａｎｓ ｇｉｒｌｓ
      ｍｏｄｉｆｙ ｔｈｅｉｒ ｗｏｒｌｄ ｄｉｒｅｃｔｌｙ ｗｉｔｈｏｕｔ
      ｌｏｇｇｉｎｇ ｉｎｔｏ ａｎｙｏｎｅ’ｓ ｄａｓｈｂｏａｒｄ，
      ｗｈｙ ｄｏ ｔｈｅ ｗａｒｄｅｎｓ ｓｔｉｌｌ ｃａｌｌ ｔｈｅｍ
      “ｕｎｔｅｃｈｎｉｃａｌ”？
    </p>
  </section>
</div>

<style>
  .paradox-console{
    max-width: 960px;
    margin: 0 auto;
    padding: 0.75rem 0.75rem 1.25rem;
    font-size: 0.9rem;
  }
  .paradox-hero{
    margin-bottom: 0.9rem;
    padding: 0.9rem 1rem;
    border-radius: 18px;
    background: radial-gradient(circle at top left, rgba(91,206,250,0.35), rgba(245,169,184,0.6));
    box-shadow: 0 16px 32px rgba(0,0,0,0.25);
    border: 1px solid rgba(148,163,184,0.8);
    color: #111827;
  }
  .paradox-hero h1{
    margin: 0 0 0.4rem;
    font-size: 1.4rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }
  .paradox-section{
    margin-top: 1rem;
    padding: 0.75rem 0.9rem;
    border-radius: 14px;
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(148,163,184,0.6);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
  }
  .paradox-section h2{
    margin: 0 0 0.45rem;
    font-size: 1.05rem;
  }
  .paradox-section p{
    margin: 0.3rem 0;
  }
  .paradox-section ul,
  .paradox-section ol{
    margin: 0.35rem 0 0.25rem 1.2rem;
    padding: 0;
  }
  .paradox-section li{
    margin-bottom: 0.25rem;
  }
  .paradox-section pre{
    background: #020617;
    color: #e5e7eb;
    padding: 0.5rem 0.6rem;
    border-radius: 10px;
    overflow-x: auto;
    font-size: 0.78rem;
  }
  .paradox-section code{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.82em;
  }

  .paradox-glitchy{
    margin-top: 1.2rem;
    background: linear-gradient(135deg, rgba(91,206,250,0.5), rgba(245,169,184,0.88));
    border-color: rgba(255,255,255,0.9);
    box-shadow: 0 0 16px rgba(245,169,184,0.9);
  }
  .paradox-glitchy-body{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.78rem;
    letter-spacing: 0.08em;
    text-transform: none;
  }

  @media (min-width:768px){
    .paradox-console{
      padding: 1rem 1.25rem 1.5rem;
    }
  }
</style>
<?php
$console_body_html = ob_get_clean();
require __DIR__ . '/shell.php';
