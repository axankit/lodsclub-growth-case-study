const { chromium } = require('playwright'); const fs = require('fs'); const path = require('path');
const F = f => 'data:font/woff2;base64,' + fs.readFileSync(path.join(__dirname, 'node_modules/@fontsource', f)).toString('base64');
const I = s => 'data:image/webp;base64,' + fs.readFileSync(path.join(__dirname, '../v2/cut', s + '-metal-tpu-case.webp')).toString('base64');
const jp = fs.readdirSync(path.join(__dirname, 'node_modules/@fontsource/dela-gothic-one/files')).filter(f => /japanese.*400.*woff2$/.test(f));
const jpFaces = jp.map(f => `@font-face{font-family:DG;src:url(${F('dela-gothic-one/files/' + f)})}`).join('');
const css = `@font-face{font-family:DG;src:url(${F('dela-gothic-one/files/dela-gothic-one-latin-400-normal.woff2')})}${jpFaces}
@font-face{font-family:IN;font-weight:600;src:url(${F('inter/files/inter-latin-600-normal.woff2')})}@font-face{font-family:IN;font-weight:800;src:url(${F('inter/files/inter-latin-800-normal.woff2')})}
*{margin:0;box-sizing:border-box}body{width:1200px;height:630px;overflow:hidden;background:#0b0b0f;color:#f5f3ee;font-family:IN}
.c{position:relative;width:1200px;height:630px;overflow:hidden}
.burst{position:absolute;left:830px;top:340px;width:1800px;height:1800px;transform:translate(-50%,-50%);border-radius:50%;background:repeating-conic-gradient(from 0deg,rgba(255,255,255,.07) 0 1.5deg,transparent 1.5deg 7deg)}
.glow{position:absolute;left:830px;top:360px;width:640px;height:640px;transform:translate(-50%,-50%);border-radius:50%;background:radial-gradient(circle,rgba(255,45,61,.45),rgba(255,45,61,0) 65%)}
.tick{position:absolute;left:0;right:0;top:0;height:46px;background:#ffd400;color:#0b0b0f;font:18px DG;letter-spacing:1px;display:flex;align-items:center;gap:26px;white-space:nowrap;padding-left:30px;border-bottom:3px solid #0b0b0f}
.brand{position:absolute;left:60px;top:82px;font:30px DG;letter-spacing:1px}.brand b{color:#ff2d3d;font-weight:400}
h1{position:absolute;left:58px;top:136px;font:400 88px/0.95 DG}h1 span{color:#ff2d3d}
.sub{position:absolute;left:62px;top:342px;width:540px;font-weight:600;font-size:23px;line-height:1.4;color:#d6d6de}
.price{position:absolute;left:60px;bottom:34px;display:flex;align-items:center;gap:18px}
.star{width:136px;height:136px;background:#ffd400;color:#0b0b0f;clip-path:polygon(50% 0%,58.9% 11%,71.7% 5%,74.9% 18.7%,89.1% 18.8%,86% 32.6%,98.7% 38.9%,90% 50%,98.7% 61.1%,86% 67.4%,89.1% 81.2%,74.9% 81.3%,71.7% 95%,58.9% 89%,50% 100%,41.1% 89%,28.3% 95%,25.1% 81.3%,10.9% 81.2%,14% 67.4%,1.3% 61.1%,10% 50%,1.3% 38.9%,14% 32.6%,10.9% 18.8%,25.1% 18.7%,28.3% 5%,41.1% 11%);display:flex;flex-direction:column;align-items:center;justify-content:center;transform:rotate(-10deg)}
.star .a{font:38px DG}.star .b{font-weight:800;font-size:13px}
.pills{display:flex;flex-direction:column;gap:8px;font-weight:800;font-size:20px}.pills s{color:#8a8a96;font-weight:600}
.jp{position:absolute;right:26px;top:66px;font:140px/0.9 DG;writing-mode:vertical-rl;color:transparent;-webkit-text-stroke:2px rgba(255,45,61,.5)}
.ph{position:absolute;filter:drop-shadow(0 26px 26px rgba(0,0,0,.65))}`;
const page = (title, sub, phones, jpText) => `<!doctype html><html><head><meta charset="utf-8"><style>${css}</style></head><body><div class="c"><div class="burst"></div><div class="glow"></div>
<div class="tick"><span>₹249 ONLY</span><span>✦</span><span>50% OFF</span><span>✦</span><span>FREE DELIVERY</span><span>✦</span><span>FREE KEYRING</span><span>✦</span><span>73 ANIME DESIGNS</span><span>✦</span><span>14 PHONE BRANDS</span></div>
<div class="brand">LODS<b>CLUB</b></div><h1 style="${title.split('<br>').length > 2 ? 'font-size:70px' : ''}">${title}</h1><div class="sub" style="${title.split('<br>').length > 2 ? 'top:380px' : ''}">${sub}</div>
<div class="price"><div class="star"><div class="a">₹249</div><div class="b">50% OFF</div></div><div class="pills"><span>MRP <s>₹499</s></span><span>Free delivery · Free keyring</span></div></div>
${phones}</div></body></html>`;
const fan = (a, b, c, d, e) => `<img class="ph" src="${I(a)}" style="height:330px;left:670px;top:240px;transform:rotate(-14deg)"><img class="ph" src="${I(b)}" style="height:380px;left:755px;top:190px;transform:rotate(-7deg)"><img class="ph" src="${I(e)}" style="height:330px;left:1015px;top:240px;transform:rotate(14deg)"><img class="ph" src="${I(d)}" style="height:380px;left:930px;top:190px;transform:rotate(7deg)"><img class="ph" src="${I(c)}" style="height:450px;left:828px;top:130px;z-index:3">`;
(async () => {
  const b = await chromium.launch(); const p = await b.newPage({ viewport: { width: 1200, height: 630 } });
  const jobs = [
    ['og-anime-landing.jpg', 'WEAR<br>YOUR<br><span>ANIME.</span>', 'Anime metal-back phone covers for 14 brands. Naruto, JJK, Demon Slayer, One Piece & more.', fan('six-paths-sage', 'laughter-luffy', 'legendary-special-grade-sorcerer', 'infinity-castle', 'the-freedom'), 'アニメ'],
    ['og-anime-shop.jpg', 'ANIME<br><span>CASES</span>', 'All 73 anime mobile covers in one place. Free delivery across India.', fan('baji-keisuke', 'flame-of-asta', 'the-freedom', 'infinity-castle', 'kamado-aura'), 'アニメ']];
  for (const [f, t, s, ph, jpT] of jobs) { await p.setContent(page(t, s, ph, jpT), { waitUntil: 'load' }); await p.evaluate(() => document.fonts.ready); await p.waitForTimeout(300); await p.screenshot({ path: f, type: 'jpeg', quality: 86 }); console.log(f, fs.statSync(f).size); }
  await b.close();
})();
