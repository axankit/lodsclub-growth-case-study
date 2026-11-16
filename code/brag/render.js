// usage: node render.js page.html W H outdir [stills t1,t2,...]
// Page must define window.SCENES=[{name,start,end}] and window.render(t) (pure function of time).
const { chromium } = require('playwright');
const { spawn } = require('child_process');
const fs = require('fs'), path = require('path');
const [page_, W, H, outdir, stills] = process.argv.slice(2);
const FPS = 30;
(async () => {
  fs.mkdirSync(outdir, { recursive: true });
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: +W, height: +H }, deviceScaleFactor: 1 });
  await p.goto('file://' + path.resolve(page_));
  await p.evaluate(async () => { await document.fonts.ready; await Promise.all([...document.images].map(i => i.complete ? 0 : new Promise(r => { i.onload = i.onerror = r; }))); });
  const shot = async (t) => { await p.evaluate(t => window.render(t), t); return p.screenshot({ type: 'jpeg', quality: 92 }); };
  if (stills) {
    for (const t of stills.split(',').map(Number)) fs.writeFileSync(path.join(outdir, `still_${t.toFixed(2)}.jpg`), await shot(t));
    await b.close(); return;
  }
  const scenes = await p.evaluate(() => window.SCENES);
  for (const s of scenes) {
    const tmp = path.join(outdir, `tmp_${s.name}.mp4`), fin = path.join(outdir, `${s.name}.mp4`);
    const ff = spawn('ffmpeg', ['-nostdin', '-v', 'error', '-y', '-f', 'image2pipe', '-framerate', String(FPS), '-c:v', 'mjpeg', '-i', '-',
      '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '18', '-pix_fmt', 'yuv420p', '-r', String(FPS), tmp]);
    const f0 = Math.round(s.start * FPS), f1 = Math.round(s.end * FPS);
    for (let f = f0; f < f1; f++) {
      const buf = await shot(f / FPS);
      if (!ff.stdin.write(buf)) await new Promise(r => ff.stdin.once('drain', r));
    }
    ff.stdin.end();
    const code = await new Promise(r => ff.on('close', r));
    if (code === 0) fs.renameSync(tmp, fin); else { console.error('ffmpeg failed', s.name); process.exit(1); }
    console.log('scene', s.name, f1 - f0, 'frames');
  }
  await b.close();
})();
