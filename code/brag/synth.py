"""Small beat-grid synth: every hit lands on one grid counted from t=0.
usage: python3 synth.py <style> <seconds> <out.wav> [impact times comma-sep]
styles: trap (dark, 120 bpm), drop (fast, 140 bpm), calm (polished, 100 bpm)
"""
import sys, numpy as np, wave

SR = 44100
style, dur, out = sys.argv[1], float(sys.argv[2]), sys.argv[3]
impacts = [float(x) for x in sys.argv[4].split(',')] if len(sys.argv) > 4 and sys.argv[4] else []
BPM = {'trap': 120, 'drop': 140, 'calm': 100}[style]
beat = 60 / BPM
N = int(SR * dur)
L = np.zeros(N); R = np.zeros(N)
rng = np.random.default_rng(7)

def add(sig, t, gain=1.0, pan=0.0):
    i = int(t * SR)
    if i >= N: return
    s = sig[: N - i] * gain
    L[i:i + len(s)] += s * (1 - max(pan, 0))
    R[i:i + len(s)] += s * (1 + min(pan, 0))

def env(n, a=0.002, d=0.2):
    t = np.arange(n) / SR
    e = np.minimum(1, t / max(a, 1e-4)) * np.exp(-t / d)
    return e

def kick(dec=0.35, f0=150, f1=45):
    n = int(SR * 0.6); t = np.arange(n) / SR
    f = f1 + (f0 - f1) * np.exp(-t * 30)
    ph = 2 * np.pi * np.cumsum(f) / SR
    return np.sin(ph) * env(n, 0.001, dec) * 1.0

def clap():
    n = int(SR * 0.3); s = rng.standard_normal(n)
    s = np.convolve(s, np.ones(6) / 6, 'same') - np.convolve(s, np.ones(40) / 40, 'same')
    e = env(n, 0.001, 0.09)
    for k in (0.008, 0.016):  # clap smear
        e += np.roll(env(n, 0.001, 0.02), int(k * SR)) * 0.6
    return s * e * 0.55

def hat(open_=False):
    n = int(SR * (0.25 if open_ else 0.06)); s = rng.standard_normal(n)
    s = s - np.convolve(s, np.ones(8) / 8, 'same')
    return s * env(n, 0.0005, 0.08 if open_ else 0.018) * 0.22

def tone(freq, length, kind='saw', a=0.01, d=1.0, detune=0.0):
    n = int(SR * length); t = np.arange(n) / SR
    out = np.zeros(n)
    for dt in ([0] if not detune else [-detune, 0, detune]):
        f = freq * (2 ** (dt / 1200))
        ph = (t * f) % 1.0
        if kind == 'saw': w = 2 * ph - 1
        elif kind == 'sq': w = np.sign(np.sin(2 * np.pi * f * t))
        else: w = np.sin(2 * np.pi * f * t)
        out += w
    out /= (1 if not detune else 3)
    return out * env(n, a, d)

def lowpass(x, k):
    k = max(1, int(k)); return np.convolve(x, np.ones(k) / k, 'same')

def sub(freq, length):
    n = int(SR * length); t = np.arange(n) / SR
    f = freq * (1 + 0.5 * np.exp(-t * 40))
    s = np.tanh(1.6 * np.sin(2 * np.pi * np.cumsum(f) / SR))
    e = np.minimum(1, t / 0.005) * np.minimum(1, (length - t) / 0.03)
    return s * e * 0.55

def riser(length):
    n = int(SR * length); t = np.arange(n) / SR
    s = rng.standard_normal(n); s = s - lowpass(s, 30)
    return s * (t / length) ** 2 * 0.25

def boom():
    k = kick(1.2, 90, 32) * 1.2
    n = int(SR * 1.5); s = rng.standard_normal(n); s = lowpass(s, 60) * env(n, 0.002, 0.5) * 0.5
    m = np.zeros(max(len(k), n)); m[:len(k)] += k; m[:n] += s
    return m

A, C, D, E, F, G = 55.0, 65.41, 73.42, 82.41, 87.31, 98.0
nb = int(dur / beat) + 1

if style in ('trap', 'drop'):
    prog = [A, A, F, F, C * 1, C, G, G] if style == 'trap' else [A, F, C, G]
    chord = {A: [220, 261.6, 329.6], F: [174.6, 220, 261.6], C: [130.8 * 2, 164.8 * 2, 196 * 2], G: [196, 246.9, 293.7]}
    for b in range(nb):
        t = b * beat; bar_beat = b % 4
        if bar_beat == 0 or (bar_beat == 2 and style == 'drop') or (b % 8 == 3 and style == 'trap'):
            add(kick(), t, 0.95)
        if bar_beat in (1, 3): add(clap(), t, 0.8)
        for h in range(2 if style == 'trap' else 4):
            add(hat(), t + h * beat / (2 if style == 'trap' else 4), 0.8 if h == 0 else 0.5, pan=0.25)
        if style == 'trap' and b % 8 == 7:  # roll
            for h in range(6): add(hat(), t + h * beat / 6, 0.45, pan=-0.2)
        root = prog[(b // 2) % len(prog)] if style == 'trap' else prog[(b // 4) % 4]
        if bar_beat in (0, 2) or style == 'drop':
            add(sub(root, beat * (1.9 if style == 'trap' else 0.95)), t, 0.9)
        if bar_beat == 0:
            for fq in chord[root]:
                pad = lowpass(tone(fq, beat * 4.2, 'saw', 0.25, 3.0, detune=9), 14)
                add(pad, t, 0.06, pan=0.15 if fq % 2 else -0.15)
            if style == 'drop':
                for i, fq in enumerate(chord[root] * 2):
                    add(lowpass(tone(fq * 2, beat / 2, 'sq', 0.002, 0.12), 4), t + i * beat / 2, 0.05)
else:  # calm
    prog = [(C * 2, [261.6, 329.6, 392.0, 493.9]), (A * 2, [220, 261.6, 329.6, 392]), (F * 2, [174.6, 220, 261.6, 329.6]), (G * 2, [196, 246.9, 293.7, 349.2])]
    for b in range(nb):
        t = b * beat; bar = (b // 4) % 4; bb = b % 4
        root, ch = prog[bar]
        if bb == 0:
            for fq in ch: add(lowpass(tone(fq, beat * 4.3, 'saw', 0.4, 3.5, detune=6), 18), t, 0.055)
            add(sub(root / 2, beat * 3.6), t, 0.5)
        if bb in (0, 2): add(kick(0.25, 120, 50), t, 0.55)
        if bb in (1, 3): add(clap(), t, 0.35)
        add(hat(), t + beat / 2, 0.35, pan=0.3)
        arp = ch[(b * 3) % 4] * 2
        add(lowpass(tone(arp, beat * 0.9, 'sine', 0.005, 0.35), 2), t, 0.07, pan=-0.2)

for it in impacts:
    add(riser(min(1.5, it)), max(0, it - min(1.5, it)), 0.8)
    add(boom(), it, 0.9)

# fade in/out a touch, glue, normalise to -1 dBFS
fade = np.ones(N); fi = int(0.01 * SR); fo = int(0.6 * SR)
fade[:fi] = np.linspace(0, 1, fi); fade[-fo:] = np.linspace(1, 0, fo)
mix = np.stack([L, R], 1) * fade[:, None]
mix = np.tanh(mix * 1.3) / np.tanh(1.3)
mix = mix / (np.abs(mix).max() + 1e-9) * 0.89
pcm = (mix * 32767).astype('<i2')
with wave.open(out, 'wb') as w:
    w.setnchannels(2); w.setsampwidth(2); w.setframerate(SR); w.writeframes(pcm.tobytes())
print(out, dur, 's', BPM, 'bpm')
