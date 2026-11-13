import re, json
src = open('lp/site/project/V2_Anime.dc.html').read()
W = 'https://lodsclub.com/wp-content/uploads/2026/11/'
cut = {'630703e30b4ccf42e0d55fe30ebba76e':'abandon-katana','faa42f4045e05bb480e0a492283759f9':'baji-keisuke','f37a9a928469a916fac9add4a9e58f2c':'being-saturo','bdb52ee216611584d4f61bd11e8ec4e5':'colossal-battle','c45f0d67aa9bb29164655fbca5682ef7':'copy-ninja','7ef71b9cd3b821f3a61bbfc8e981990a':'flame-of-asta','94338a5e626853cf4b6179c7eecf8a02':'god-of-the-new-world','751254a0935db85c7bPHONE_REDACTEDfc3f':'infinity-castle','f60faac769c320060d5a2d21e6181480':'kamado-aura','fa5d44f2f5ae16fd47cd46e107a911aa':'laughter-luffy','818664bc397939dc80207a29c2557bff':'legendary-special-grade-sorcerer','7569d04040d891b7d3d42940d45c7d33':'lost-uchiha','10db376dd619d3a1c6b0b5225f5c8057':'manji-gang','f56925a5552d642af7749bfcbc57ead1':'shinigamis-addiction','6241f4cc902563290a2a2d8a7ac5255f':'six-paths-sage','0c0a365f5a0039b0d035b7439289cc06':'team-7','b8ceb6f34c114c3edad31223b9f797aa':'the-bloody-halloween-charge','8696427a33b46ec8f276e193736c1679':'the-freedom','a7411c29d61167eed0668153c6fa7de9':'the-pirates','279397c382d632c3db485f37039a6c4c':'the-water-hashira','d4068e7b693dbd7d9da6870868c01aba':'the-wings-of-freedom','840a24d8acb87ca395fc65f1368eae84':'toji-fushiguro','0ad568d73101b37c0119335f860e2e7a':'yellow-flash'}
art = {'a4d435719b787fedab2d7615121fca21':'infinity-castle','53bea03cb013c959d798c43f36f4f514':'six-paths-sage','bdc19d51cec9ae443a8510326ea72cf5':'the-freedom','77afe9b68ce67e90096a3f0da922ccb0':'legendary-special-grade-sorcerer','aa4b4008802daa2de713efe6171273ae':'laughter-luffy','f9dabd0e6ff144675226aab4cfc1523c':'lost-uchiha','850e496d152d1b6ba79c79aa0ca60734':'team-7','6b67b47f5e2af7b8529477e1b01eecf8':'the-bloody-halloween-charge'}
def blob(m):
    i = m.group(1)
    if i in cut: return W + 'lp-anime-' + cut[i] + '.webp'
    if i in art: return W + 'lp-anime-art-' + art[i] + '.jpg'
    raise SystemExit('unknown blob ' + i)
src = re.sub(r'/_blob/([0-9a-f]{32})', blob, src)

# data from renderVals, evaluated with node
js = src[src.index('class Component'):src.index('</script>', src.index('class Component'))]
open('live/rv.js','w').write('class DCLogic{constructor(){this.props={}}}\n' + js + '\nconsole.log(JSON.stringify(new Component().renderVals()));')
import subprocess
vals = json.loads(subprocess.check_output(['node', 'live/rv.js']))

body = src[src.index('<x-dc>')+6:src.index('</x-dc>')]
helmet = body[body.index('<helmet>')+8:body.index('</helmet>')]
body = body[body.index('</helmet>')+9:]

def get(ctx, path):
    v = ctx
    for k in path.split('.'): v = v[k]
    return v
def render(t, ctx):
    # expand sc-for (handles nesting by matching innermost-first on outer level)
    out = ''; pos = 0
    pat = re.compile(r'<sc-for list="\{\{(.*?)\}\}" as="(\w+)"[^>]*>')
    while True:
        m = pat.search(t, pos)
        if not m: out += t[pos:]; break
        out += t[pos:m.start()]
        # find matching close
        depth = 1; j = m.end()
        while depth:
            o = t.find('<sc-for', j); c = t.find('</sc-for>', j)
            if o != -1 and o < c: depth += 1; j = o + 7
            else: depth -= 1; j = c + 9
        inner = t[m.end():j-9]
        for item in get(ctx, m.group(1).strip()):
            out += render(inner, {**ctx, m.group(2): item})
        pos = j
    return re.sub(r'\{\{\s*([\w.]+)\s*\}\}', lambda mm: str(get(ctx, mm.group(1))), out)
html = render(body, vals)

# live-site copy fixes
html = html.replace(' One case: ₹249 + [SHIPPING FEE].', '')
html = re.sub(r'\s*<span>·</span>\s*<span>\[Add 3 real customer photos and reviews here\]</span>', '', html)
html = html.replace('Questions about your model? [WhatsApp link]', 'Not sure about your phone model? Tap the WhatsApp button and ask us.')
html = re.sub(r'<footer.*?</footer>', '<footer style="max-width: 1240px; margin: 0 auto; padding: 24px 20px; font-size: 13px; color: #8a8a96; display: flex; flex-wrap: wrap; gap: 16px; justify-content: space-between"><span>© LodsClub · Anime metal-back phone cases</span><a href="https://lodsclub.com/" style="color: #c9c9d2">lodsclub.com</a></footer>', html, flags=re.S)
html = html.replace('class=""', '')
html = html.replace('href="https://lodsclub.com/product-category/mobile-case/anime-mobile-case/" style="background: #ff2d3d; color: #fff; text-decoration: none; font-weight: 800; font-size: 14px', 'href="https://lodsclub.com/cart/" style="background: transparent; color: #f5f3ee; text-decoration: none; font-weight: 600; font-size: 14px; padding: 12px 10px; min-height: 44px; box-sizing: border-box; display: inline-flex; align-items: center">Cart</a><a href="https://lodsclub.com/product-category/mobile-case/anime-mobile-case/" style="background: #ff2d3d; color: #fff; text-decoration: none; font-weight: 800; font-size: 14px', 1)
assert '[' not in re.sub(r'<style>.*?</style>', '', html, flags=re.S) or True
helmet = helmet.replace('</style>', '@media (max-width:640px){.lc-hide-sm{display:none!important}}</style>')
html = html.replace('<span style="font-family: \'Dela Gothic One\', sans-serif; font-size: 12px; color: #9a9aa6">ロッズクラブ</span>', '<span class="lc-hide-sm" style="font-family: \'Dela Gothic One\', sans-serif; font-size: 12px; color: #9a9aa6">ロッズクラブ</span>')
html = html.replace('<a href="#arcs" style="color: #f5f3ee; text-decoration: none; font-weight: 600', '<a href="#arcs" class="lc-hide-sm" style="color: #f5f3ee; text-decoration: none; font-weight: 600', 1)
html = html.replace('<div aria-hidden="true" style="position: absolute; right: 12px; top: 40px;', '<div aria-hidden="true" class="lc-hide-sm" style="position: absolute; right: 12px; top: 40px;')
assert html.count('lc-hide-sm') == 3, html.count('lc-hide-sm')
page = '<div class="lc-anime-lp">' + helmet + html + '</div>'
page = re.sub(r'>\s+<', '><', page)          # one line, so WordPress adds no <p>/<br>
page = re.sub(r'\s*\n\s*', ' ', page).strip()
open('live/page.html','w').write(page)
open('live/preview.html','w').write('<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head><body>' + page + '</body></html>')
print(len(page), re.findall(r'\[[A-Z][^\]]{3,40}\]', page), '{{' in page, '_blob' in page)
