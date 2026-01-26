// shard-content.js
'use strict';

// ---------- HARD LIMITS ----------
var MAX_CANVAS_DIM      = 15000;
var MAX_COL_HEIGHT      = 15000;
var MAX_FINAL_WIDTH     = 16000;
var SHARD_JPEG_QUALITY  = 0.985;

// ---------- BASIC UTIL ----------
function pad2(n){ return n < 10 ? '0' + n : '' + n; }

// ---------- META SEED ----------
(function seedRepublicMeta(){
  if (!window.REPUBLIC_META) window.REPUBLIC_META = {};
  var meta = window.REPUBLIC_META;

  if (!meta.title)   meta.title   = document.title || 'Shard Capture';
  if (!meta.url)     meta.url     = window.location.href;
  if (!meta.console) meta.console = 'Browser Shard Console';

  if (!meta.shard)   meta.shard = {};
  var s = meta.shard;
  if (!s.system)  s.system  = window.location.hostname || 'Unknown System';
  if (!s.glyph)   s.glyph   = 'WWW-' + (window.location.hostname || 'LOCAL');
  if (!s.version) s.version = '3.2';
  if (!s.deck)    s.deck    = 'chrome-shard v3.2';
})();

// ---------- STAMP BUILDER ----------
function buildRepublicStamp(){
  var meta  = window.REPUBLIC_META || {};
  var shard = meta.shard || {};

  var now   = new Date();

  var y     = now.getFullYear();
  var monthNames = [
    'January','February','March','April','May','June',
    'July','August','September','October','November','December'
  ];
  var monthName = monthNames[now.getMonth()];
  var dayNumber = now.getDate();

  var hhLocal = pad2(now.getHours());
  var mmLocal = pad2(now.getMinutes());
  var ssLocal = pad2(now.getSeconds());

  // Republic year: start at 1 in 2024
  var baseYear = 2024;
  var trYear   = y - baseYear + 1;
  if (trYear < 1) trYear = 1;

  var mcYear = String(trYear);
  while (mcYear.length < 4) mcYear = '0' + mcYear;

  // Human-readable line
  var line =
    monthName + ' ' + dayNumber + ', ' + mcYear + ' MC' +
    '  \u2022  ' + hhLocal + ':' + mmLocal + ' local';

  // Republic-style file stamp: 0001MC-YYYYMMDD-HHMMSS
  var mNum = pad2(now.getMonth() + 1);
  var dNum = pad2(dayNumber);
  var file = mcYear + 'MC-' + y + mNum + dNum + '-' + hhLocal + mmLocal + ssLocal;

  var mythic = {
    title:   meta.title   || document.title || 'The Republic',
    url:     meta.url     || (window.location.href || 'https://trepublic.net/'),
    console: meta.console || 'Starship Console',
    system:  shard.system || (window.location.hostname || 'Local System'),
    glyph:   shard.glyph  || 'WWW-' + (window.location.hostname || 'LOCAL'),
    version: shard.version || '3.2',
    deck:    shard.deck   || 'chrome-shard v3.2'
  };

  return { line: line, file: file, mythic: mythic };
}

// ---------- DOWNLOAD HELPERS ----------
function downloadDataURL(dataURL, filename){
  try{
    if (typeof chrome !== 'undefined'
        && chrome.runtime
        && chrome.downloads
        && typeof chrome.downloads.download === 'function'){
      var blob = dataURLtoBlob(dataURL);
      var url  = URL.createObjectURL(blob);
      chrome.downloads.download({
        url: url,
        filename: filename,
        saveAs: false
      }, function(){
        URL.revokeObjectURL(url);
      });
      return;
    }
  } catch(e){ /* fall through to <a> */ }

  var link = document.createElement('a');
  link.href = dataURL;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function dataURLtoBlob(dataURL){
  var parts = dataURL.split(',');
  var meta  = parts[0].match(/data:(.*?);base64/);
  var mime  = meta ? meta[1] : 'image/jpeg';
  var bin   = atob(parts[1]);
  var len   = bin.length;
  var arr   = new Uint8Array(len);
  for (var i = 0; i < len; i++) arr[i] = bin.charCodeAt(i);
  return new Blob([arr], { type: mime });
}

// ---------- TEXT FIT ----------
function fitAndDrawCentered(ctx, text, maxWidth, centerX, y, baseSize, minSize, color, weight){
  if (!text) return;
  var size = baseSize;
  var fontBase = (weight || 'normal') + ' ';
  ctx.font = fontBase + size + 'px system-ui,-apple-system,Segoe UI,sans-serif';
  while (size > minSize && ctx.measureText(text).width > maxWidth){
    size -= 1;
    ctx.font = fontBase + size + 'px system-ui,-apple-system,Segoe UI,sans-serif';
  }
  ctx.fillStyle = color;
  ctx.textAlign = 'center';
  ctx.fillText(text, centerX, y);
  return size;
}

// ---------- MOSAIC BUILDER ----------
function buildShardMosaic(fullCanvas, stamp){
  var sw = fullCanvas.width;
  var sh = fullCanvas.height;

  var cols = Math.max(1, Math.ceil(sh / MAX_COL_HEIGHT));
  var finalWUnscaled = sw * cols;

  var scale = 1;
  if (finalWUnscaled > MAX_FINAL_WIDTH){
    scale = MAX_FINAL_WIDTH / finalWUnscaled;
  }

  if (scale !== 1){
    var scaled = document.createElement('canvas');
    scaled.width  = Math.max(1, Math.round(sw * scale));
    scaled.height = Math.max(1, Math.round(sh * scale));
    scaled.getContext('2d').drawImage(fullCanvas, 0, 0, scaled.width, scaled.height);
    fullCanvas = scaled;
    sw = scaled.width;
    sh = scaled.height;
  }

  if (cols === 1){
    var single = fullCanvas.toDataURL('image/jpeg', SHARD_JPEG_QUALITY);
    downloadDataURL(single, 'trepublic-' + stamp.file + '-shard.jpg');
    return;
  }

  var colH = Math.ceil(sh / cols);
  if (colH > MAX_COL_HEIGHT) colH = MAX_COL_HEIGHT;

  var finalW = sw * cols;
  var finalH = colH;

  var mosaic = document.createElement('canvas');
  mosaic.width  = finalW;
  mosaic.height = finalH;

  var mctx = mosaic.getContext('2d');
  mctx.fillStyle = '#ffffff';
  mctx.fillRect(0, 0, finalW, finalH);

  for (var c = 0; c < cols; c++){
    var srcY = c * colH;
    var srcH = Math.min(colH, sh - srcY);
    if (srcH <= 0) break;
    mctx.drawImage(fullCanvas, 0, srcY, sw, srcH, c * sw, 0, sw, srcH);
  }

  mctx.textAlign = 'center';
  mctx.fillStyle = 'rgba(0,0,0,0.55)';
  var labelSize = Math.max(16, Math.min(26, finalW / (cols * 12)));
  mctx.font = 'bold ' + labelSize + 'px system-ui,-apple-system,Segoe UI,sans-serif';
  var romans = ['Ⅰ','Ⅱ','Ⅲ','Ⅳ','Ⅴ','Ⅵ','Ⅶ','Ⅷ','Ⅸ','Ⅹ'];
  var labelY = labelSize + 4;
  for (var i = 0; i < cols; i++){
    var label = romans[i] || String(i + 1);
    mctx.fillText(label, i * sw + sw / 2, labelY);
  }

  var dataURL = mosaic.toDataURL('image/jpeg', SHARD_JPEG_QUALITY);
  downloadDataURL(dataURL, 'trepublic-' + stamp.file + '-shard.jpg');
}

// ---------- SCALE ----------
function computeScale(cssW, cssH){
  var dpr = window.devicePixelRatio || 1;
  var target = dpr * 2.5;
  if (target > 3.0) target = 3.0;
  var scale  = target;

  if (cssW * scale > MAX_CANVAS_DIM){
    scale = MAX_CANVAS_DIM / cssW;
  }
  if (cssH * scale > MAX_CANVAS_DIM){
    scale = Math.min(scale, MAX_CANVAS_DIM / cssH);
  }
  if (scale < 0.7) scale = 0.7;
  return scale;
}

// ---------- HEADER ----------
function buildHeaderCanvas(canvas, stamp){
  var mythic  = stamp.mythic || {};
  var pageW   = canvas.width;
  var pageH   = canvas.height;
  var headerH = 190;

  // Final image will be wider than the page so we get side margins.
  var marginX   = Math.round(pageW * 0.08);      // 8% of page width on each side
  var finalW    = pageW + marginX * 2;
  var innerW    = finalW - marginX * 2;          // width usable for text and page
  var finalH    = pageH + headerH;

  var full = document.createElement('canvas');
  full.width  = finalW;
  full.height = finalH;

  var ctx = full.getContext('2d');
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, finalW, finalH);

  var centerX      = finalW / 2;
  var maxLineWidth = innerW;

  fitAndDrawCentered(
    ctx,
    'The Republic • Crownkind Shard Protocol v3.2',
    maxLineWidth,
    centerX,
    40,
    Math.max(24, Math.min(40, innerW / 22)),
    20,
    '#7e3af2',
    'bold'
  );

  var y2 = 72;
  fitAndDrawCentered(
    ctx,
    stamp.line,
    maxLineWidth,
    centerX,
    y2,
    Math.max(14, Math.min(20, innerW / 40)),
    12,
    '#333333',
    'normal'
  );

  var titleLine = (mythic.title || 'The Republic') + ' • ' + (mythic.url || '');
  var y3 = y2 + 24;
  fitAndDrawCentered(
    ctx,
    titleLine,
    maxLineWidth,
    centerX,
    y3,
    Math.max(12, Math.min(18, innerW / 45)),
    10,
    '#555555',
    'normal'
  );

  var metaLine =
    (mythic.system || 'NMS Eissentam') + ' ' + (mythic.glyph || '') +
    ' • ' + (mythic.console || 'Starship Console') +
    ' • Deck ' + (mythic.deck || 'TShell GOLD 3.1');

  var y4 = y3 + 20;
  fitAndDrawCentered(
    ctx,
    metaLine,
    maxLineWidth,
    centerX,
    y4,
    Math.max(12, Math.min(16, innerW / 50)),
    10,
    '#555555',
    'normal'
  );

  var shardLine = 'Shard ' + stamp.file + ' v' + (mythic.version || '3.2');
  var y5 = y4 + 18;
  fitAndDrawCentered(
    ctx,
    shardLine,
    maxLineWidth,
    centerX,
    y5,
    Math.max(10, Math.min(14, innerW / 60)),
    9,
    '#777777',
    'normal'
  );

  // Divider line across inner width only
  ctx.strokeStyle = 'rgba(120,120,120,0.35)';
  ctx.lineWidth = 1;
  ctx.beginPath();
  ctx.moveTo(marginX, headerH - 20);
  ctx.lineTo(finalW - marginX, headerH - 20);
  ctx.stroke();

  // Draw the page canvas centered between margins
  ctx.drawImage(canvas, marginX, headerH, pageW, pageH);

  return full;
}

// ---------- FRAME TARGET ----------
function findShardFrame(){
  var frame = document.querySelector('[data-republic-frame="1"], #rep-frame');
  if (frame) return frame;

  frame = document.querySelector('main');
  if (frame) return frame;

  var candidates = Array.from(document.querySelectorAll('body, div, section, article'))
    .filter(function(el){
      var rect = el.getBoundingClientRect();
      return rect.height > 200 && rect.width > 200;
    });
  if (candidates.length){
    candidates.sort(function(a,b){
      var ra = a.getBoundingClientRect();
      var rb = b.getBoundingClientRect();
      return (rb.height * rb.width) - (ra.height * ra.width);
    });
    return candidates[0];
  }

  return document.body || document.documentElement;
}

// ---------- CAPTURE ----------
function captureShard(btn){
  var frame = findShardFrame();
  if (!frame){
    alert('Shard: could not find a frame on this page.');
    return;
  }

  if (typeof html2canvas !== 'function'){
    alert('Shard: html2canvas is not available on this page.');
    return;
  }

  if (btn && typeof btn === 'object') btn.disabled = true;

  try{
    if (frame.scrollIntoView){
      frame.scrollIntoView({ block: 'center', behavior: 'auto' });
    }
  }catch(e){}

  var rect = frame.getBoundingClientRect();
  var cssW = rect.width  || frame.offsetWidth  || 800;
  var cssH = rect.height || frame.offsetHeight || 1200;

  var scale = computeScale(cssW, cssH);

  html2canvas(frame, {
    backgroundColor: '#ffffff',
    useCORS: true,
    scale: scale,
    logging: false
  }).then(function(canvas){
    if (!canvas || !canvas.width || !canvas.height){
      throw new Error('Empty canvas from html2canvas');
    }
    var stamp  = buildRepublicStamp();
    var full   = buildHeaderCanvas(canvas, stamp);
    buildShardMosaic(full, stamp);
  }).catch(function(err){
    console.error('Shard capture failed:', err);
    alert('Shard: capture failed. See console for details.');
  }).finally(function(){
    if (btn && typeof btn === 'object') btn.disabled = false;
  });
}

// auto-run once whenever this file is injected
captureShard({});
