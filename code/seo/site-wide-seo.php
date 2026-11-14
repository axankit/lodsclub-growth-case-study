/**
 * LodsClub – site-wide SEO (titles, descriptions, share tags, noindex, sitemap, H1, alt, llms.txt)
 * Deactivate this snippet to undo everything it does.
 * The /anime/ page and the anime category keep their own SEO snippets (skipped here).
 */
if ( ! defined( 'ABSPATH' ) ) { return; }

function lcseo_cfg() {
	static $c = null;
	if ( $c ) { return $c; }
	$c = array(
		'anime_page'  => 202793,
		'anime_cat'   => 1637,
		'default_img' => 'https://lodsclub.com/wp-content/uploads/2026/11/lodsclub-share-default.jpg',
		'case_cats'   => array( 1636, 1637, 1638, 1641 ),
		'tee_cats'    => array( 1525, 1526 ),
		// Thin, demo or utility pages: noindex + out of sitemap (pages stay live).
		'noindex_pages' => array( 202612, 179385, 29611, 17376, 15651, 15627, 15135, 9371, 9016, 5105, 12, 13, 14 ),
		'noindex_cats'  => array( 15 ),
		'theme_types'   => array( 'haru_header', 'haru_footer', 'haru_megamenu', 'haru_content' ),
		'pages' => array(
			14274  => array( 'LodsClub – Anime Phone Cases at ₹249 & Graphic T-Shirts', 'Metal-back anime and aesthetic phone cases at ₹249 for iPhone, Samsung, Vivo, Oppo, Redmi and more, plus graphic T-shirts. Delivered across India.' ),
			11     => array( 'Shop Phone Cases & T-Shirts – LodsClub', 'Shop every LodsClub design: metal-back phone cases at ₹249 in anime, aesthetic and energy styles, plus graphic T-shirts. Delivered across India.' ),
			202622 => array( 'Phone Cases by Style: Anime, Aesthetic & Energy – LodsClub', 'Pick your vibe: anime, aesthetic or energy. Metal-back phone cases for 14 phone brands, every design ₹249, delivered across India.' ),
			179412 => array( 'T-Shirt Styles: Regular Fit & Oversized – LodsClub', 'Choose your T-shirt style: classic regular fit or oversized graphic tees by LodsClub. Statement designs, delivered across India.' ),
			9462   => array( 'About LodsClub – Designs You Wear', 'LodsClub is an Indian brand making metal-back phone cases and graphic T-shirts. Learn who we are and how we make every design.' ),
			10885  => array( 'Contact LodsClub – Orders, Delivery & Support', 'Questions about an order, delivery or a replacement? Contact LodsClub on WhatsApp, phone or email at support@REDACTED.' ),
			17494  => array( 'Refund & Returns Policy – LodsClub', 'How replacements, returns and refunds work for LodsClub phone cases and T-shirts, including timelines and how to raise a request.' ),
			17809  => array( 'Privacy Policy – LodsClub', 'How LodsClub collects, uses and protects your personal information when you shop with us.' ),
			17815  => array( 'Terms & Conditions – LodsClub', 'The terms that apply when you browse and buy from LodsClub, including orders, payments, delivery and use of the website.' ),
			17823  => array( 'Printing Terms & Cancellation Policy – LodsClub', 'Printing terms for custom LodsClub products and how order cancellations work.' ),
		),
		'cats' => array(
			1636 => array( 'Metal-Back Phone Cases at ₹249 – LodsClub', 'Metal-back phone cases with glossy prints at ₹249: anime, aesthetic and energy designs for iPhone, Samsung, Vivo, Oppo, Redmi and more.' ),
			1637 => array( 'Anime Phone Cases – Every Design ₹249 | LodsClub', '' ),
			1641 => array( 'Aesthetic Phone Cases at ₹249 – LodsClub', 'Aesthetic metal-back phone cases at ₹249: butterflies, bows, scrapbook collages and moody prints for 14 phone brands. Delivered across India.' ),
			1638 => array( 'Energy Phone Cases – LodsClub', 'Bold energy-themed metal-back phone cases at ₹249 for iPhone, Samsung, Vivo, Oppo, Redmi and more.' ),
			1525 => array( 'Graphic T-Shirts – LodsClub', 'Graphic T-shirts by LodsClub: statement prints in regular and oversized fits. Delivered across India.' ),
			1526 => array( 'Regular Fit Graphic T-Shirts – LodsClub', 'Regular fit graphic T-shirts by LodsClub: classic cut, bold prints. Delivered across India.' ),
		),
	);
	return $c;
}

function lcseo_is_skip() { // pages that already have dedicated SEO snippets
	$c = lcseo_cfg();
	return ( is_page( $c['anime_page'] ) || is_tax( 'product_cat', $c['anime_cat'] ) );
}

function lcseo_inr( $v ) { return '₹' . rtrim( rtrim( number_format( (float) $v, 2, '.', '' ), '0' ), '.' ); }

function lcseo_product_info( $p ) {
	$c    = lcseo_cfg();
	$id   = $p->get_id();
	$name = trim( wp_strip_all_tags( html_entity_decode( $p->get_name(), ENT_QUOTES, 'UTF-8' ) ) );
	$cats = wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'ids' ) );
	$is_case = (bool) array_intersect( (array) $cats, $c['case_cats'] );
	$is_tee  = (bool) array_intersect( (array) $cats, $c['tee_cats'] );
	$price = $p->get_price();
	$reg   = $p->get_regular_price();
	if ( $is_case ) {
		$base = trim( preg_replace( '/\s*metal\s*tpu\s*case\s*$/i', '', $name ) );
		if ( '' === $base ) { $base = $name; }
		$title = $base . ' Metal-Back Phone Case' . ( $price ? ' – ' . lcseo_inr( $price ) : '' ) . ' | LodsClub';
		$desc  = $base . ' metal-back phone case with a glossy print' . ( $price ? ', ' . lcseo_inr( $price ) : '' ) . ( ( $reg && $price && (float) $reg > (float) $price ) ? ' (MRP ' . lcseo_inr( $reg ) . ')' : '' ) . '. Made for iPhone, Samsung, Vivo, Oppo, Redmi and more. Delivered across India.';
	} elseif ( $is_tee ) {
		$t     = preg_match( '/shirt|tee/i', $name ) ? $name : $name . ' Graphic T-Shirt';
		$title = $t . ' – LodsClub';
		$desc  = $name . ' graphic T-shirt by LodsClub' . ( $price ? ', ' . lcseo_inr( $price ) : '' ) . '. Statement print, delivered across India.';
	} else {
		$title = $name . ' – LodsClub';
		$short = trim( wp_strip_all_tags( $p->get_short_description() ) );
		$desc  = $short ? $short : $name . ' by LodsClub' . ( $price ? ', ' . lcseo_inr( $price ) : '' ) . '. Delivered across India.';
	}
	$desc = preg_replace( '/\s+/', ' ', $desc );
	if ( mb_strlen( $desc ) > 160 ) { $desc = rtrim( mb_substr( $desc, 0, 157 ), " ,.;" ) . '…'; }
	return array( $title, $desc, $price );
}

function lcseo_current() { // [title, desc, image, url, type, extra]
	$c = lcseo_cfg();
	if ( is_front_page() ) {
		$id = (int) get_option( 'page_on_front' );
		$d  = isset( $c['pages'][ $id ] ) ? $c['pages'][ $id ] : $c['pages'][14274];
		return array( $d[0], $d[1], $c['default_img'], home_url( '/' ), 'website', array() );
	}
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$d = $c['pages'][11];
		return array( $d[0], $d[1], $c['default_img'], get_permalink( 11 ), 'website', array() );
	}
	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$p = wc_get_product( get_queried_object_id() );
		if ( ! $p ) { return null; }
		list( $t, $d, $price ) = lcseo_product_info( $p );
		$img = get_the_post_thumbnail_url( $p->get_id(), 'large' );
		return array( $t, $d, $img ? $img : $c['default_img'], get_permalink( $p->get_id() ), 'product', array( 'price' => $price ) );
	}
	if ( is_page() ) {
		$id = get_queried_object_id();
		if ( isset( $c['pages'][ $id ] ) ) {
			$img = get_the_post_thumbnail_url( $id, 'large' );
			return array( $c['pages'][ $id ][0], $c['pages'][ $id ][1], $img ? $img : $c['default_img'], get_permalink( $id ), 'website', array() );
		}
		return null;
	}
	if ( is_tax( 'product_cat' ) ) {
		$t = get_queried_object();
		if ( ! $t || ! isset( $c['cats'][ $t->term_id ] ) ) { return null; }
		$url   = get_term_link( $t );
		$paged = (int) get_query_var( 'paged' );
		if ( $paged > 1 ) { $url = trailingslashit( $url ) . 'page/' . $paged . '/'; }
		$thumb = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
		$img   = $thumb ? wp_get_attachment_image_url( $thumb, 'large' ) : '';
		return array( $c['cats'][ $t->term_id ][0], $c['cats'][ $t->term_id ][1], $img ? $img : $c['default_img'], $url, 'website', array( 'term' => 1 ) );
	}
	return null;
}

/* 1. Titles */
add_filter( 'pre_get_document_title', function ( $title ) {
	if ( is_admin() || is_page( lcseo_cfg()['anime_page'] ) ) { return $title; }
	$cur = lcseo_current();
	return ( $cur && $cur[0] ) ? $cur[0] : $title;
}, 99 );

/* 2. Meta description, canonical (archives), Open Graph, Twitter, homepage schema */
add_action( 'wp_head', function () {
	if ( is_admin() || lcseo_is_skip() ) { return; }
	$cur = lcseo_current();
	if ( ! $cur ) { return; }
	list( $title, $desc, $img, $url, $type, $extra ) = $cur;
	$e = function ( $v ) { return esc_attr( $v ); };
	echo "\n<!-- LodsClub SEO -->\n";
	if ( $desc ) { echo '<meta name="description" content="' . $e( $desc ) . '">' . "\n"; }
	if ( ! empty( $extra['term'] ) || ( function_exists( 'is_shop' ) && is_shop() ) ) { echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n"; }
	echo '<meta property="og:site_name" content="LodsClub">' . "\n";
	echo '<meta property="og:locale" content="en_IN">' . "\n";
	echo '<meta property="og:type" content="' . $e( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . $e( $title ) . '">' . "\n";
	if ( $desc ) { echo '<meta property="og:description" content="' . $e( $desc ) . '">' . "\n"; }
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $img ) { echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n"; }
	if ( ! empty( $extra['price'] ) ) {
		echo '<meta property="product:price:amount" content="' . $e( $extra['price'] ) . '">' . "\n";
		echo '<meta property="product:price:currency" content="INR">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . $e( $title ) . '">' . "\n";
	if ( $desc ) { echo '<meta name="twitter:description" content="' . $e( $desc ) . '">' . "\n"; }
	if ( $img ) { echo '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n"; }
	if ( is_front_page() ) {
		$ld = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type' => 'Organization', '@id' => home_url( '/#org' ), 'name' => 'LodsClub', 'url' => home_url( '/' ),
					'logo' => 'https://lodsclub.com/wp-content/uploads/2022/06/lodsclub_light.png',
					'email' => 'support@REDACTED', 'telephone' => '+91 XXXXXXXXXX',
					'contactPoint' => array( '@type' => 'ContactPoint', 'contactType' => 'customer support', 'email' => 'support@REDACTED', 'telephone' => '+91 XXXXXXXXXX', 'areaServed' => 'IN' ),
				),
				array(
					'@type' => 'WebSite', '@id' => home_url( '/#website' ), 'name' => 'LodsClub', 'url' => home_url( '/' ), 'publisher' => array( '@id' => home_url( '/#org' ) ),
					'potentialAction' => array( '@type' => 'SearchAction', 'target' => home_url( '/?s={search_term_string}&post_type=product' ), 'query-input' => 'required name=search_term_string' ),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
	echo "<!-- /LodsClub SEO -->\n";
}, 2 );

/* 3. Noindex thin, demo, utility and theme-template URLs */
add_filter( 'wp_robots', function ( $robots ) {
	$c  = lcseo_cfg();
	$no = false;
	if ( is_page( $c['noindex_pages'] ) || is_home() || is_singular( $c['theme_types'] ) || is_author() || is_search() || is_tax( 'product_cat', $c['noindex_cats'] ) ) { $no = true; }
	if ( $no ) { $robots['noindex'] = true; $robots['follow'] = true; unset( $robots['max-image-preview'] ); }
	return $robots;
} );

/* 4. Clean sitemap */
add_filter( 'wp_sitemaps_post_types', function ( $types ) {
	foreach ( lcseo_cfg()['theme_types'] as $t ) { unset( $types[ $t ] ); }
	return $types;
} );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) { return 'users' === $name ? false : $provider; }, 10, 2 );
add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $type ) {
	if ( 'page' === $type ) { $args['post__not_in'] = array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array(), lcseo_cfg()['noindex_pages'] ); }
	return $args;
}, 10, 2 );
add_filter( 'wp_sitemaps_taxonomies_query_args', function ( $args, $tax ) {
	if ( 'product_cat' === $tax ) { $args['exclude'] = array_merge( isset( $args['exclude'] ) ? (array) $args['exclude'] : array(), lcseo_cfg()['noindex_cats'] ); }
	return $args;
}, 10, 2 );

/* 5. One H1 per page: turn the theme's page-title H2 into H1 when the page has none */
add_action( 'template_redirect', function () {
	if ( is_admin() || wp_doing_ajax() || is_feed() || is_front_page() ) { return; }
	if ( ! ( is_page() || is_tax( 'product_cat' ) || ( function_exists( 'is_shop' ) && is_shop() ) ) ) { return; }
	ob_start( function ( $html ) {
		if ( stripos( $html, '<h1' ) !== false ) { return $html; }
		return preg_replace( '~<h2(\s+class="haru-page-title__heading--main"[^>]*)>(.*?)</h2>~s', '<h1$1>$2</h1>', $html, 1 );
	} );
}, 1 );

/* 5b. Visible H1 + short intro on the shop and category pages (the theme shows no title there) */
add_action( 'woocommerce_before_shop_loop', function () {
	$c = lcseo_cfg();
	if ( is_tax( 'product_cat', $c['anime_cat'] ) ) { return; }
	$h = array( 1636 => 'Phone cases', 1641 => 'Aesthetic phone cases', 1638 => 'Energy phone cases', 1525 => 'Graphic T-shirts', 1526 => 'Regular fit T-shirts' );
	$title = ''; $intro = '';
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		$title = 'Shop all designs';
		$intro = 'Metal-back phone cases at ₹249 and graphic T-shirts, delivered across India.';
	} elseif ( is_tax( 'product_cat' ) ) {
		$t = get_queried_object();
		if ( ! $t ) { return; }
		$title = isset( $h[ $t->term_id ] ) ? $h[ $t->term_id ] : $t->name;
		$intro = isset( $c['cats'][ $t->term_id ] ) ? $c['cats'][ $t->term_id ][1] : '';
	} else { return; }
	echo '<div class="lc-archive-head"><h1>' . esc_html( $title ) . '</h1>' . ( $intro ? '<p>' . esc_html( $intro ) . '</p>' : '' ) . '</div>';
	echo '<script>(function(){var h=document.querySelector(".lc-archive-head"),f=h&&h.closest(".shop-filter");if(f&&f.parentNode){f.parentNode.insertBefore(h,f);}})();</script>';
	echo '<style>:has(> .lc-archive-head){flex-wrap:wrap!important}.lc-archive-head{width:100%;flex:0 0 100%;max-width:100%;order:-1;margin:6px 0 18px}.lc-archive-head h1{font-size:clamp(26px,4vw,36px);font-weight:600;line-height:1.2;margin:0 0 6px;color:#141414}.lc-archive-head p{margin:0;font-size:15px;line-height:1.5;color:#666;max-width:70ch}</style>';
}, 5 );

/* 5c. Header-level noindex too (covers pages with custom templates that skip wp_head, e.g. the design editor) */
add_action( 'template_redirect', function () {
	$c = lcseo_cfg();
	if ( is_page( $c['noindex_pages'] ) || is_home() || is_singular( $c['theme_types'] ) || is_author() ) {
		header( 'X-Robots-Tag: noindex, follow', true );
	}
}, 0 );

/* 6. Alt text for product and media images that have none */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $att ) {
	if ( isset( $attr['alt'] ) && '' !== trim( $attr['alt'] ) ) { return $attr; }
	$parent = $att->post_parent ? get_post( $att->post_parent ) : null;
	if ( $parent && 'product' === $parent->post_type ) {
		$attr['alt'] = wp_strip_all_tags( get_the_title( $parent ) ) . ' – LodsClub';
	} elseif ( is_singular( 'product' ) ) {
		$attr['alt'] = wp_strip_all_tags( get_the_title( get_queried_object_id() ) ) . ' – LodsClub';
	} else {
		$t = trim( preg_replace( '/[-_]+/', ' ', get_the_title( $att ) ) );
		if ( $t ) { $attr['alt'] = $t; }
	}
	return $attr;
}, 20, 2 );

/* 7. llms.txt – plain facts for AI assistants (GEO) */
add_action( 'init', function () {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? strtok( $_SERVER['REQUEST_URI'], '?' ) : '';
	if ( '/llms.txt' !== $path ) { return; }
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'Cache-Control: public, max-age=86400' );
	echo "# LodsClub\n\n";
	echo "> LodsClub is an Indian online store selling metal-back phone cases with glossy printed designs at ₹249 (MRP ₹499), in anime, aesthetic and energy styles, for 14 phone brands including iPhone, Samsung, Vivo, Oppo, Realme, Redmi, OnePlus, iQOO, Poco and Motorola. It also sells graphic T-shirts. Orders are prepaid by UPI or card and delivered across India.\n\n";
	echo "## Phone cases\n";
	echo "- [Anime phone cases](https://lodsclub.com/anime/): anime designs from Naruto, Jujutsu Kaisen, Demon Slayer, One Piece, Attack on Titan, Tokyo Revengers and more\n";
	echo "- [All anime cases](https://lodsclub.com/product-category/mobile-case/anime-mobile-case/)\n";
	echo "- [Aesthetic cases](https://lodsclub.com/product-category/mobile-case/asthetic/)\n";
	echo "- [Choose a style](https://lodsclub.com/choose-your-style/)\n";
	echo "- [All phone cases](https://lodsclub.com/product-category/mobile-case/)\n\n";
	echo "## T-shirts\n";
	echo "- [Graphic T-shirts](https://lodsclub.com/product-category/apparel/)\n\n";
	echo "## Help\n";
	echo "- [Refund and returns policy](https://lodsclub.com/refund-returns/)\n";
	echo "- [Terms and conditions](https://lodsclub.com/terms-and-conditions/)\n";
	echo "- [Contact](https://lodsclub.com/contact-us/): support@REDACTED, +91 XXXXXXXXXX\n";
	exit;
}, 0 );
