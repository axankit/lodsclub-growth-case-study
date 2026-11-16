add_action('wp_head',function(){ if(is_admin()||is_user_logged_in())return; ?>
<style id="lch">
.haru-account--style-2 .haru-account__content.logged-out .haru-account__buttons{display:none!important}
.haru-account--style-2 .haru-account__content.logged-out{display:flex;align-items:center;justify-content:flex-end}
@media(max-width:767px){
 .haru-account--style-2 .haru-account__content.logged-out::after{content:"Free delivery\A Cases from \20B9 249";white-space:pre;text-align:right;font-size:12px;line-height:1.25;font-weight:700;color:#17141c}
}
.lch-acct{display:none}
@media(min-width:1025px){
 .lch-acct{display:inline-flex;align-items:center;gap:7px;color:#17141c;font-size:14px;font-weight:600;text-decoration:none;padding:6px 4px}
 .lch-acct svg{width:20px;height:20px}
 .lch-acct:hover{color:#e5262d}
}
</style>
<?php },99);
add_action('wp_footer',function(){ if(is_admin()||is_user_logged_in())return; ?>
<script>
(function(){var c=document.querySelectorAll('.haru-account--style-2 .haru-account__content.logged-out');
for(var i=0;i<c.length;i++){if(c[i].querySelector('.lch-acct'))continue;var a=document.createElement('a');a.className='lch-acct';a.href='/my-account/';a.setAttribute('aria-label','My account');
a.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>Account';c[i].appendChild(a);}})();
</script>
<?php },5);
