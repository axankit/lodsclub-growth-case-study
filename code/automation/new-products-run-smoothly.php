/**
 * LodsClub – New products run smoothly (deactivate to undo)
 * 1. Auto cut-out of case photos (white background removed) for the shop grid and product page.
 * 2. Cases saved with MRP 500 are stored as 499.
 * 3. "73 designs" style counts on anime pages and "Pick your vibe" become live counts.
 * 4. Series labels for new designs on the anime shop grid.
 * 5. /anime-shop/ (old private preview) redirects to the anime shop.
 */
if ( ! defined( 'ABSPATH' ) ) { return; }

function lcnp_case_cats() { return array( 1636, 1637, 1638, 1641 ); }
function lcnp_is_case( $pid ) {
	$t = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'ids' ) );
	return ! is_wp_error( $t ) && (bool) array_intersect( (array) $t, lcnp_case_cats() );
}

/* ---------- 2. MRP 500 -> 499 on save ---------- */
add_action( 'woocommerce_before_product_object_save', function ( $product ) {
	if ( ! $product || $product->is_type( 'variation' ) ) { return; }
	if ( ! array_intersect( (array) $product->get_category_ids(), lcnp_case_cats() ) ) { return; }
	$rp = $product->get_regular_price();
	if ( '' !== (string) $rp && abs( (float) $rp - 500 ) < 0.001 ) { $product->set_regular_price( '499' ); }
}, 10, 1 );

/* ---------- 1. Cut-outs ---------- */
function lcnp_make_cutout( $pid ) {
	$pid   = (int) $pid;
	$thumb = (int) get_post_thumbnail_id( $pid );
	if ( ! $thumb || ! lcnp_is_case( $pid ) ) { return 'skip'; }
	if ( (int) get_post_meta( $pid, '_lc_cut_src', true ) === $thumb ) { return 'done-before'; }
	$file = get_attached_file( $thumb );
	if ( ! $file || ! file_exists( $file ) || ! function_exists( 'imagecreatefromstring' ) ) { return 'no-file'; }
	$src = @imagecreatefromstring( file_get_contents( $file ) );
	if ( ! $src ) { return 'bad-image'; }
	$w = imagesx( $src ); $h = imagesy( $src );
	$scale = min( 1, 760 / max( $w, $h ) );
	$W = max( 1, (int) round( $w * $scale ) ); $H = max( 1, (int) round( $h * $scale ) );
	$im = imagecreatetruecolor( $W, $H );
	imagealphablending( $im, false ); imagesavealpha( $im, true );
	$tr = imagecolorallocatealpha( $im, 0, 0, 0, 127 );
	imagefilledrectangle( $im, 0, 0, $W, $H, $tr );
	imagecopyresampled( $im, $src, 0, 0, 0, 0, $W, $H, $w, $h );
	imagedestroy( $src );

	$corners = array( array( 1, 1 ), array( $W - 2, 1 ), array( 1, $H - 2 ), array( $W - 2, $H - 2 ) );
	$ref = array( 0, 0, 0 ); $alphaC = 0;
	foreach ( $corners as $p ) {
		$v = imagecolorat( $im, $p[0], $p[1] );
		if ( ( ( $v >> 24 ) & 127 ) > 100 ) { $alphaC++; }
		$ref[0] += ( $v >> 16 ) & 255; $ref[1] += ( $v >> 8 ) & 255; $ref[2] += $v & 255;
	}
	$ref = array( $ref[0] / 4, $ref[1] / 4, $ref[2] / 4 );
	$N   = $W * $H;
	$bg  = str_repeat( "\0", $N );

	if ( $alphaC < 3 ) {
		if ( ( $ref[0] + $ref[1] + $ref[2] ) / 3 < 205 ) { // not a light background: keep the original photo
			update_post_meta( $pid, '_lc_cut_src', $thumb ); delete_post_meta( $pid, '_lc_cut_url' );
			imagedestroy( $im ); delete_transient( 'lcnp_cuts' ); return 'dark-bg';
		}
		$tol = 34; $stack = array();
		for ( $x = 0; $x < $W; $x++ ) { $stack[] = $x; $stack[] = ( $H - 1 ) * $W + $x; }
		for ( $y = 0; $y < $H; $y++ ) { $stack[] = $y * $W; $stack[] = $y * $W + $W - 1; }
		while ( $stack ) {
			$i = array_pop( $stack );
			if ( "\0" !== $bg[ $i ] ) { continue; }
			$x = $i % $W; $y = ( $i - $x ) / $W;
			$v = imagecolorat( $im, $x, $y );
			if ( abs( ( ( $v >> 16 ) & 255 ) - $ref[0] ) > $tol || abs( ( ( $v >> 8 ) & 255 ) - $ref[1] ) > $tol || abs( ( $v & 255 ) - $ref[2] ) > $tol ) { $bg[ $i ] = "\2"; continue; }
			$bg[ $i ] = "\1";
			if ( $x > 0 && "\0" === $bg[ $i - 1 ] ) { $stack[] = $i - 1; }
			if ( $x < $W - 1 && "\0" === $bg[ $i + 1 ] ) { $stack[] = $i + 1; }
			if ( $y > 0 && "\0" === $bg[ $i - $W ] ) { $stack[] = $i - $W; }
			if ( $y < $H - 1 && "\0" === $bg[ $i + $W ] ) { $stack[] = $i + $W; }
		}
		// Clear background, soften a 1px edge ring, find bounds.
		$minx = $W; $miny = $H; $maxx = 0; $maxy = 0;
		for ( $y = 0; $y < $H; $y++ ) {
			for ( $x = 0; $x < $W; $x++ ) {
				$i = $y * $W + $x;
				if ( "\1" === $bg[ $i ] ) { imagesetpixel( $im, $x, $y, $tr ); continue; }
				$edge = ( $x > 0 && "\1" === $bg[ $i - 1 ] ) || ( $x < $W - 1 && "\1" === $bg[ $i + 1 ] ) || ( $y > 0 && "\1" === $bg[ $i - $W ] ) || ( $y < $H - 1 && "\1" === $bg[ $i + $W ] );
				if ( $edge ) {
					$v = imagecolorat( $im, $x, $y );
					imagesetpixel( $im, $x, $y, imagecolorallocatealpha( $im, ( $v >> 16 ) & 255, ( $v >> 8 ) & 255, $v & 255, 70 ) );
				}
				if ( $x < $minx ) { $minx = $x; } if ( $x > $maxx ) { $maxx = $x; }
				if ( $y < $miny ) { $miny = $y; } if ( $y > $maxy ) { $maxy = $y; }
			}
		}
	} else { // already transparent: just find bounds
		$minx = $W; $miny = $H; $maxx = 0; $maxy = 0;
		for ( $y = 0; $y < $H; $y++ ) { for ( $x = 0; $x < $W; $x++ ) {
			if ( ( ( imagecolorat( $im, $x, $y ) >> 24 ) & 127 ) < 110 ) {
				if ( $x < $minx ) { $minx = $x; } if ( $x > $maxx ) { $maxx = $x; }
				if ( $y < $miny ) { $miny = $y; } if ( $y > $maxy ) { $maxy = $y; }
			}
		} }
	}
	$cw = $maxx - $minx + 1; $ch = $maxy - $miny + 1;
	if ( $cw < $W * 0.15 || $ch < $H * 0.3 ) { // cut-out failed (too small): keep original
		update_post_meta( $pid, '_lc_cut_src', $thumb ); delete_post_meta( $pid, '_lc_cut_url' );
		imagedestroy( $im ); delete_transient( 'lcnp_cuts' ); return 'too-small';
	}
	$out = imagecreatetruecolor( $cw, $ch );
	imagealphablending( $out, false ); imagesavealpha( $out, true );
	imagefilledrectangle( $out, 0, 0, $cw, $ch, imagecolorallocatealpha( $out, 0, 0, 0, 127 ) );
	imagecopy( $out, $im, 0, 0, $minx, $miny, $cw, $ch );
	imagedestroy( $im );
	$up  = wp_upload_dir();
	$dir = trailingslashit( $up['basedir'] ) . 'lc-cuts';
	if ( ! wp_mkdir_p( $dir ) ) { imagedestroy( $out ); return 'no-dir'; }
	$webp = function_exists( 'imagewebp' );
	$name = 'lc-cut-' . $pid . '-' . $thumb . ( $webp ? '.webp' : '.png' );
	$ok   = $webp ? imagewebp( $out, $dir . '/' . $name, 86 ) : imagepng( $out, $dir . '/' . $name, 7 );
	imagedestroy( $out );
	if ( ! $ok ) { return 'save-failed'; }
	update_post_meta( $pid, '_lc_cut_url', trailingslashit( $up['baseurl'] ) . 'lc-cuts/' . $name );
	update_post_meta( $pid, '_lc_cut_src', $thumb );
	delete_transient( 'lcnp_cuts' );
	return 'made';
}

function lcnp_queue( $pid ) {
	if ( ! lcnp_is_case( $pid ) ) { return; }
	delete_transient( 'lcnp_cuts' ); lcnp_flush_counts();
	if ( ! wp_next_scheduled( 'lcnp_cut_event', array( (int) $pid ) ) ) { wp_schedule_single_event( time() + 5, 'lcnp_cut_event', array( (int) $pid ) ); }
}
add_action( 'woocommerce_new_product', 'lcnp_queue' );
add_action( 'woocommerce_update_product', 'lcnp_queue' );
add_action( 'lcnp_cut_event', 'lcnp_make_cutout' );

function lcnp_backfill( $limit = 6 ) {
	$q = new WP_Query( array(
		'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'orderby' => 'date', 'order' => 'DESC',
		'tax_query' => array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => lcnp_case_cats() ) ),
	) );
	$done = array();
	foreach ( $q->posts as $pid ) {
		if ( count( $done ) >= $limit ) { break; }
		$thumb = (int) get_post_thumbnail_id( $pid );
		if ( $thumb && (int) get_post_meta( $pid, '_lc_cut_src', true ) !== $thumb ) { $done[ $pid ] = lcnp_make_cutout( $pid ); }
	}
	return $done;
}
add_action( 'lcnp_backfill_event', function () { lcnp_backfill( 6 ); } );
add_action( 'init', function () { if ( ! wp_next_scheduled( 'lcnp_backfill_event' ) ) { wp_schedule_event( time() + 60, 'hourly', 'lcnp_backfill_event' ); } } );

// Admin-only manual run: /wp-admin/?lcnp_run=1
add_action( 'admin_init', function () {
	if ( empty( $_GET['lcnp_run'] ) || ! current_user_can( 'manage_woocommerce' ) ) { return; }
	@set_time_limit( 120 );
	$r = lcnp_backfill( 12 );
	header( 'Content-Type: application/json' );
	echo wp_json_encode( array( 'processed' => $r, 'cuts' => count( lcnp_cut_map() ) ) );
	exit;
} );

function lcnp_cut_map() {
	$m = get_transient( 'lcnp_cuts' );
	if ( is_array( $m ) ) { return $m; }
	global $wpdb;
	$rows = $wpdb->get_results( "SELECT p.post_name, m.meta_value FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE m.meta_key = '_lc_cut_url' AND p.post_status = 'publish'" );
	$m = array();
	foreach ( $rows as $r ) { $m[ $r->post_name ] = $r->meta_value; }
	set_transient( 'lcnp_cuts', $m, DAY_IN_SECONDS );
	return $m;
}

/* ---------- 3. Live counts ---------- */
function lcnp_count( $term_id ) {
	$k = 'lcnp_count_' . (int) $term_id;
	$c = get_transient( $k );
	if ( false !== $c ) { return (int) $c; }
	$args = array( 'status' => 'publish', 'limit' => -1, 'return' => 'ids', 'visibility' => 'catalog', 'tax_query' => array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => array( (int) $term_id ) ) ) );
	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) { $args['stock_status'] = 'instock'; }
	$c = count( wc_get_products( $args ) );
	set_transient( $k, $c, HOUR_IN_SECONDS );
	return $c;
}
function lcnp_flush_counts() { foreach ( array( 1637, 1641, 1638, 1636 ) as $t ) { delete_transient( 'lcnp_count_' . $t ); } }
add_action( 'woocommerce_delete_product', 'lcnp_flush_counts' );
add_action( 'woocommerce_trash_product', 'lcnp_flush_counts' );

add_action( 'template_redirect', function () {
	if ( is_admin() || is_feed() || wp_doing_ajax() ) { return; }
	// 5. Old preview URL
	$path = trim( (string) parse_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH ), '/' );
	if ( 'anime-shop' === $path && ! is_user_logged_in() ) {
		$l = get_term_link( 1637, 'product_cat' );
		if ( ! is_wp_error( $l ) ) { wp_redirect( $l, 301 ); exit; }
	}
	$on_style = is_page( 202622 );
	if ( ! ( is_page( 202793 ) || $on_style || is_tax( 'product_cat', 1637 ) ) ) { return; }
	ob_start( function ( $html ) use ( $on_style ) {
		$a = lcnp_count( 1637 );
		if ( $a < 10 ) { return $html; } // safety: keep the written numbers if the count looks wrong
		$html = preg_replace( '/\b73(?=(?:\s|&nbsp;|&#160;)+(?:anime|Anime|ANIME|designs|metal-back|mobile covers))/u', (string) $a, $html );
		$html = preg_replace( '/\b(all|All|ALL|are|Shop|of) 73\b/u', '$1 ' . $a, $html );
		if ( $on_style ) {
			$map = array( 'r-aes' => 1641, 'r-en' => 1638 );
			foreach ( $map as $cls => $tid ) {
				$n = lcnp_count( $tid );
				if ( $n < 1 ) { continue; }
				$html = preg_replace( '/(class="lcs-row ' . $cls . '".*?<span class="ct">)\d+ designs?(<\/span>)/s', '${1}' . $n . ( 1 === $n ? ' design' : ' designs' ) . '$2', $html, 1 );
			}
		}
		return $html;
	} );
}, 2 );

/* ---------- 1b. Cut-out map for the shop grid and product page scripts ---------- */
add_action( 'wp_head', function () {
	if ( is_admin() ) { return; }
	if ( ! ( is_product() || is_tax( 'product_cat' ) || is_page( array( 202793, 202622 ) ) ) ) { return; }
	echo '<script>window.LC_CUTS=' . wp_json_encode( (object) lcnp_cut_map() ) . ';</script>' . "\n";
}, 1 );
