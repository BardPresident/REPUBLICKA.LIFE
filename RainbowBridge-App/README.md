# RainbowBridge-App
Local-only Rainbow Bridge wrapper: kids can encode their own seeds and decode them offline using a simple HTML+BAT toolkit.


## License – Rainbow Warrior Lollipop

This project is released under the **Rainbow Warrior Lollipop License v1.0**.

Short version:
- Kids own their words and decide who can use this app with them.  
- Adults must ask permission and pay a lollipop (or agreed treat) per use.  
- No commercial use, no profit from harm, no bullying.

See `LICENSE.txt` for full terms.


# Rainbow Bridge Wrapper (Local)

This is a tiny, local-only Rainbow Bridge app.

It lets anyone – especially kids – turn their own words into an encoded file
and then unlock and read it later using a simple HTML page. No servers, no
accounts, no cloud. Just files on disk and a browser.

## What’s in this folder

- `index.html`  
  Local Rainbow Bridge wrapper. Open this file in a browser (you should see
  a `file:///` URL). It never talks to the internet.

- `bridge.txt`  
  Plain text pad where you can write your own story, seed, receipt, or prayer.
  This file is **editable** and easy to change.

- `ENCODE-BRIDGE.bat`  
  One-click encoder. Reads `bridge.txt`, converts it to Base64, and writes
  the result into `bridge.b64.txt`.

- `bridge.b64.txt`  
  The encoded version of your text. This is the file you can copy, back up,
  sync, or share if you want. It looks like gibberish without the wrapper.

- `CHILDREN.txt`  
  Rainbow Warrior children’s licence and lollipop protocol. Kids own the
  bridge; adults bring the lollipops.

## How to use it

1. Open `bridge.txt` in a text editor and write whatever you want. Save it.  
2. Double-click `ENCODE-BRIDGE.bat`. It will update `bridge.b64.txt`.  
3. Open `index.html` in a browser.  
4. Choose `bridge.b64.txt` with the file picker.  
5. Enter the secret unlock trick (not documented here) and press **Unlock**.  
6. Your original text appears on screen, decoded, still running only on your
   own device.

## What this is (and isn’t)

- It **is** a small, inspectable toolkit for carrying private seeds and stories
  in encoded form.  
- It **is** designed so kids can understand the pieces and control when their
  words become visible.  
- It is **not** a network service, cloud backup, or surveillance tool. Nothing
  is sent anywhere unless you choose to move the files yourself.

If you fork or remix this project, please keep a clear children-first licence
and an equally simple, local workflow.


