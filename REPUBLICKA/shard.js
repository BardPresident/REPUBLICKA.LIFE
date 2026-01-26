// Crownkind Shard Protocol v3.2 🧿
// External shard capture engine for TShell GOLD 3.1+
// Depends on:
//   • html2canvas (loaded globally)
//   • #snap-capture button
//   • #rep-frame root frame
//   • window.REPUBLIC_META (from TShell)

(function(){
  // Hard caps: larger than v3.1, but still under typical browser limits.
  var MAX_CANVAS_DIM      = 15000;    // max width/height for html2canvas
  var MAX_COL_HEIGHT      = 15000;    // max height per column in final shard
  var MAX_FINAL_WIDTH     = 16000;    // max total width of final shard mosaic
  var SHARD_JPEG_QUALITY  = 0.985;    // extra crisp text

  function pad2(n){ return n < 10 ? '0' + n : '' + n; }

  // Build the lore stamp: Sol date, shiptime, TR-year, system, glyph, URL, etc.
  function buildRepublicStamp(){
    var meta  = window.REPUBLIC_META || {};
    var shard = meta.shard || {};

    var now   = new Date();
    var y     = now.getUTCFullYear();
    var m     = pad2(now.getUTCMonth() + 1);
    var d     = pad2(now.getUTCDate());
    var hh    = pad2(now.getUTCHours());
    var mm    = pad2(now.getUTCMinutes());
    var ss    = pad2(now.getUTCSeconds());

    var trYear = y - 2024;
    if (trYear < 1) trYear = 1;
    var trLabel = 'TR-Year ' + (trYear < 10 ? '0' + trYear : trYear);

    var line = 'Sol ' + y + '-' + m + '-' + d + ' • Shiptime ' + hh + ':' + mm + ' UTC • ' + trLabel;

    var file = y + m + d + '-' + hh + mm + ss;

    var mythic = {
      title:   meta.title   || document.title || 'The Republic',
      url:     meta.url     || (window.location.href || 'https://trepublic.net/'),
      console: meta.console || 'Starship Console',
      system:  shard.system || 'NMS Eissentam',
      glyph:   shard.glyph  || '9-13EF3CFDEEEF',
      version: shard.version || '3.2',
      deck:    shard.deck   || 'TShell GOLD 3.1'
    };

    return { line: line, file: file, mythic: mythic };
  }

  function downloadDataURL(dataURL, filename){
    var link = document.createElement('a');
    link.href = dataURL;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  // Fit a single header line to width by shrinking font until it fits.
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

  // Take a full canvas (header + console) and, if needed, wrap it into columns.
  function buildShardMosaic(fullCanvas, stamp){
    var mythic = stamp.mythic || {};
    var sw = fullCanvas.width;
    var sh = fullCanvas.height;

    // How many vertical columns do we need so each one stays under MAX_COL_HEIGHT?
    var cols = Math.max(1, Math.ceil(sh / MAX_COL_HEIGHT));
    var finalWUnscaled = sw * cols;

    // If the total width would be too wide, scale everything down evenly first.
    var scale = 1;
    if (finalWUnscaled > MAX_FINAL_WIDTH){
      scale = MAX_FINAL_WIDTH / finalWUnscaled;
    }

    // Optional pre-scale (only once) to avoid repeated resampling.
    if (scale !== 1){
      var scaled = document.createElement('canvas');
      scaled.width  = Math.max(1, Math.round(sw * scale));
      scaled.height = Math.max(1, Math.round(sh * scale));
      scaled.getContext('2d').drawImage(fullCanvas, 0, 0, scaled.width, scaled.height);
      fullCanvas = scaled;
      sw = scaled.width;
      sh = scaled.height;
    }

    // If everything fits nicely as one tall tower, just ship it.
    if (cols === 1){
      var dataURLSingle = fullCanvas.toDataURL('image/jpeg', SHARD_JPEG_QUALITY);
      downloadDataURL(dataURLSingle, 'trepublic-' + stamp.file + '-shard.jpg');
      return;
    }

    // Otherwise, slice into cols vertical segments and lay them left→right.
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

      mctx.drawImage(
        fullCanvas,
        0, srcY, sw, srcH,
        c * sw, 0, sw, srcH
      );
    }

    // Roman numerals along the top to mark reading order
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

  // Higher base scale: aim for ~2.5–3x CSS resolution, then clamp to caps.
  function computeScale(cssW, cssH){
    var dpr = window.devicePixelRatio || 1;

    // Push harder than v3.1, but keep an upper bound.
    var target = dpr * 2.5;
    if (target > 3.0) target = 3.0;

    var scale  = target;

    if (cssW * scale > MAX_CANVAS_DIM){
      scale = MAX_CANVAS_DIM / cssW;
    }
    if (cssH * scale > MAX_CANVAS_DIM){
      scale = Math.min(scale, MAX_CANVAS_DIM / cssH);
    }

    // For extremely tall pages we allow going below 1,
    // but keep a slightly higher floor than before for readability.
    if (scale < 0.7) scale = 0.7;
    return scale;
  }

  function buildHeaderCanvas(canvas, stamp){
    var mythic = stamp.mythic || {};
    var w = canvas.width;
    var h = canvas.height;
    var headerH = 190;

    var full = document.createElement('canvas');
    full.width  = w;
    full.height = h + headerH;

    var ctx = full.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, full.width, full.height);

    var centerX = w / 2;
    var maxLineWidth = w - 80;

    // Line 1: Protocol name
    fitAndDrawCentered(
      ctx,
      'The Republic • Crownkind Shard Protocol v3.2',
      maxLineWidth,
      centerX,
      40,
      Math.max(24, Math.min(40, w / 22)),
      20,
      '#7e3af2',
      'bold'
    );

    // Line 2: Sol / Shiptime / TR-year
    var y2 = 72;
    fitAndDrawCentered(
      ctx,
      stamp.line,
      maxLineWidth,
      centerX,
      y2,
      Math.max(14, Math.min(20, w / 40)),
      12,
      '#333333',
      'normal'
    );

    // Line 3: Title + URL
    var titleLine = (mythic.title || 'The Republic') + ' • ' + (mythic.url || '');
    var y3 = y2 + 24;
    fitAndDrawCentered(
      ctx,
      titleLine,
      maxLineWidth,
      centerX,
      y3,
      Math.max(12, Math.min(18, w / 45)),
      10,
      '#555555',
      'normal'
    );

    // Line 4: system + glyph + console
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
      Math.max(12, Math.min(16, w / 50)),
      10,
      '#555555',
      'normal'
    );

    // Line 5: shard id
    var shardLine = 'Shard ' + stamp.file + ' v' + (mythic.version || '3.2');
    var y5 = y4 + 18;
    fitAndDrawCentered(
      ctx,
      shardLine,
      maxLineWidth,
      centerX,
      y5,
      Math.max(10, Math.min(14, w / 60)),
      9,
      '#777777',
      'normal'
    );

    // Divider
    ctx.strokeStyle = 'rgba(120,120,120,0.35)';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(40, headerH - 20);
    ctx.lineTo(w - 40, headerH - 20);
    ctx.stroke();

    // Finally, draw the captured console below the header.
    ctx.drawImage(canvas, 0, headerH);

    return full;
  }

  function initShardProtocol(){
    var btn   = document.getElementById('snap-capture');
    var frame = document.getElementById('rep-frame');

    if (!btn || !frame){
      // If TShell hasn't rendered yet, do nothing.
      return;
    }

    if (typeof html2canvas !== 'function'){
      console.warn('[Shard] html2canvas not available; shard capture disabled.');
      return;
    }

    function capture(){
      btn.disabled = true;

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
        console.error('Shard capture failed', err);
        alert('Shard capture failed on this page. Try lowering Zoom or collapsing some sections, then capture again.');
      }).finally(function(){
        btn.disabled = false;
      });
    }

    btn.addEventListener('click', capture);
  }

  // Make sure we attach after DOM is ready, even if shard.js loads in <head>.
  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', initShardProtocol);
  } else {
    initShardProtocol();
  }
})();
