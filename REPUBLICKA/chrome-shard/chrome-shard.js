// chrome-shard.js  (MV3 background)

chrome.action.onClicked.addListener(async (tab) => {
  try {
    if (!tab.id || !tab.url || tab.url.startsWith('chrome://')) {
      return;
    }

    // 1) Inject html2canvas into the page.
    await chrome.scripting.executeScript({
      target: { tabId: tab.id },
      files: ['html2canvas.min.js']
    });

    // 2) Inject the shard content script, which will run captureShard().
    await chrome.scripting.executeScript({
      target: { tabId: tab.id },
      files: ['shard-content.js']
    });
  } catch (e) {
    console.error('chrome-shard error:', e);
  }
});
