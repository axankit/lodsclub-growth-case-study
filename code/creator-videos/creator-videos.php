add_action('wp_footer',function(){
 if(is_admin()||!(is_front_page()||(function_exists('is_product')&&is_product())))return;
 $u=content_url('uploads/2026/11/');
 $items=array(
  array('DWD8sjxDloL','noreen__mantasha','1b','143K views'),
  array('DWOxfI5kR26','anjali_puri95',2,'112K views'),
  array('DWOeY0cgBDe','sanuuuu02',3,'11K views'),
  array('DWBOi_VADQs','srizza4real',4,'4.5K views'),
  array('DVaLRK-kYrU','anshika_dubey16',5,'3.6K views'),
 );
 $h='';
 foreach($items as $i){
  $h.='<a class="lcr-c" href="https://www.instagram.com/reel/'.esc_attr($i[0]).'/" target="_blank" rel="noopener" aria-label="Watch @'.esc_attr($i[1]).' unbox a LodsClub case on Instagram">'
   .'<div class="lcr-v"><video muted loop playsinline preload="none" width="360" height="640" poster="'.esc_url($u.'lc-reel-'.$i[2].'.webp').'" data-src="'.esc_url($u.'lc-reel-'.$i[2].'.mp4').'"></video><span class="lcr-tag">Collab</span></div>'
   .'<p>@'.esc_html($i[1]).'</p><small>'.esc_html($i[3]).'</small></a>';
 }
 ?>
<style>
.lcr{--ink:#17141c;--mute:#5e5a66;color:var(--ink);font-family:'Bricolage Grotesque',system-ui,-apple-system,sans-serif;padding:8px 0 46px}
.lcr *{box-sizing:border-box}
.lcr-w{max-width:1200px;margin:0 auto;padding:0 20px}
.lcr h2{font-family:inherit;font-size:clamp(36px,9vw,72px);font-weight:800;letter-spacing:-.045em;line-height:.95;margin:0;text-transform:none;color:var(--ink)}
.lcr-sub{margin:10px 0 18px;color:var(--mute);font-size:15px;line-height:1.45;max-width:46ch}
.lcr-row{display:flex;gap:12px;overflow-x:auto;scroll-snap-type:x mandatory;padding:2px 20px 6px;margin:0 -20px;scrollbar-width:none;-webkit-overflow-scrolling:touch}
.lcr-row::-webkit-scrollbar{display:none}
.lcr-c{min-width:0;flex:0 0 44%;max-width:230px;scroll-snap-align:start;text-decoration:none;color:var(--ink);display:block}
.lcr-v{position:relative;aspect-ratio:9/16;border-radius:18px;overflow:hidden;background:#d9d6d2}
.lcr-v video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}
.lcr-tag{position:absolute;top:8px;left:8px;background:rgba(23,20,28,.62);color:#fff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:99px;letter-spacing:.01em}
.lcr-c p{margin:8px 2px 0;font-weight:700;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.lcr-c small{display:block;margin:1px 2px 0;font-size:13px;color:var(--mute)}
.lcr-c:focus-visible{outline:3px solid #e5262d;outline-offset:3px;border-radius:20px}
@media(min-width:900px){.lcr{padding:0 0 80px}.lcr-w{padding:0 32px}.lcr-row{overflow:visible;margin:0;padding:0;display:grid;grid-template-columns:repeat(5,1fr);gap:14px}.lcr-c{max-width:none}}
/* product page: smaller, sits inside the buy column */
.lcr.lcr-pdp{padding:6px 0 4px;font-family:inherit}
.lcr.lcr-pdp .lcr-w{padding:0}
.lcr.lcr-pdp h2{font-size:17px;letter-spacing:0;line-height:1.2;font-weight:800;color:#111}
.lcr.lcr-pdp .lcr-sub{font-size:13px;margin:4px 0 10px}
.lcr.lcr-pdp .lcr-row{display:flex;overflow-x:auto;margin:0;padding:0 0 6px;gap:10px}
.lcr.lcr-pdp .lcr-c{flex:0 0 31%;max-width:150px}
.lcr.lcr-pdp .lcr-v{border-radius:14px}
.lcr.lcr-pdp .lcr-c p{font-size:12px}
.lcr.lcr-pdp .lcr-c small{display:none}
</style>
<div id="lcr-src" hidden><section class="lcr" aria-labelledby="lcr-h"><div class="lcr-w">
<h2 id="lcr-h">Unboxed on Instagram</h2>
<p class="lcr-sub">Creators we've worked with, opening their LodsClub cases. Tap a video to watch the full reel.</p>
<div class="lcr-row">
<?php echo $h; ?>
</div></div></section></div>
<script>
(function(){
 var src=document.getElementById('lcr-src');if(!src)return;var sec=src.querySelector('.lcr');
 function start(){
  var vids=[].slice.call(sec.querySelectorAll('video[data-src]'));
  if(!vids.length)return;
  var calm=window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches;
  var save=navigator.connection&&navigator.connection.saveData;
  if(calm||save||!('IntersectionObserver' in window))return; // posters only
  var io=new IntersectionObserver(function(es){es.forEach(function(e){var v=e.target;
    if(e.isIntersecting){if(!v.src){v.src=v.getAttribute('data-src');}var p=v.play();if(p&&p.catch)p.catch(function(){});}
    else if(v.src){v.pause();}
  });},{threshold:.35});
  vids.forEach(function(v){io.observe(v);});
}
 function placeHome(){
  var hs=[].slice.call(document.querySelectorAll('.lca h2'));var h=null;
  for(var k=0;k<hs.length;k++){if(/selling fast/i.test(hs[k].textContent)){h=hs[k];break;}}
  if(!h){src.remove();return;}
  var s=h.closest('section');s.parentNode.insertBefore(sec,s.nextSibling);src.remove();start();
 }
 function placePdp(n){
  var rv=document.querySelector('.lcp-rev');
  if(!rv){if(n<30){return setTimeout(function(){placePdp(n+1)},200);}src.remove();return;}
  sec.classList.add('lcr-pdp');rv.parentNode.insertBefore(sec,rv.nextSibling);src.remove();start();
 }
 var pdp=document.body.classList.contains('single-product');
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){pdp?placePdp(0):placeHome();});else{pdp?placePdp(0):placeHome();}
})();
</script>
<?php
},60);