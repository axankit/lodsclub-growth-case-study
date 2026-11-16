// shared helpers for brag scenes (all pure functions of t)
const clamp = (x, a = 0, b = 1) => Math.max(a, Math.min(b, x));
const prog = (t, t0, d) => clamp((t - t0) / d);
const eOut = x => 1 - Math.pow(1 - x, 3);
const eIn = x => x * x * x;
const eInOut = x => x < .5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2;
const eBack = x => { const c1 = 1.7, c3 = c1 + 1; return 1 + c3 * Math.pow(x - 1, 3) + c1 * Math.pow(x - 1, 2); };
const lerp = (a, b, x) => a + (b - a) * x;
function el(tag, cls, parent, html) { const e = document.createElement(tag); if (cls) e.className = cls; if (html != null) e.innerHTML = html; (parent || document.getElementById('stage')).appendChild(e); return e; }
function img(src, cls, parent) { const e = el('img', cls, parent); e.src = src; return e; }
function set(e, o) { // o: {x,y,s,r,o,show,blur}
  if (o.show === false) { e.style.display = 'none'; return; }
  e.style.display = '';
  e.style.transform = `translate(${o.x || 0}px,${o.y || 0}px) scale(${o.s == null ? 1 : o.s}) rotate(${o.r || 0}deg)`;
  e.style.opacity = o.o == null ? 1 : o.o;
  e.style.filter = o.blur ? `blur(${o.blur}px)` : (o.f || '');
}
// a text line that rises in, settles, then leaves
function line(e, t, tin, tout, dy = 60) {
  if (t < tin || t >= tout) return set(e, { show: false });
  const a = eOut(prog(t, tin, .35)), z = eIn(prog(t, tout - .2, .2));
  set(e, { y: (1 - a) * dy - z * 30, o: a * (1 - z) });
}
