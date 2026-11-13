/**
 * LodsClub – Fast /anime/ landing page.
 * Only runs on the /anime/ page. Deactivate this snippet to undo everything.
 */
if ( ! function_exists( 'lc_anime_lp_is' ) ) {
	function lc_anime_lp_is() {
		return ! is_admin() && is_page( 'anime' );
	}
	function lc_anime_lp_strip() {
		if ( ! lc_anime_lp_is() ) {
			return;
		}
		$keep = '/^(jquery|jquery-core|jquery-migrate|google_gtagjs|googlesitekit-events-provider-content-events|joinchat|jquery-bind-first|js-cookie-pys|js-sha256|pys)$/';
		foreach ( (array) wp_styles()->queue as $h ) {
			if ( ! preg_match( $keep, $h ) ) {
				wp_dequeue_style( $h );
			}
		}
		foreach ( (array) wp_scripts()->queue as $h ) {
			if ( ! preg_match( $keep, $h ) ) {
				wp_dequeue_script( $h );
			}
		}
	}
	add_action( 'wp_enqueue_scripts', 'lc_anime_lp_strip', 9999 );
	add_action( 'wp_print_styles', 'lc_anime_lp_strip', 9999 );
	add_action( 'wp_print_scripts', 'lc_anime_lp_strip', 9999 );
	add_action( 'wp_print_footer_scripts', 'lc_anime_lp_strip', 1 );

	add_action( 'wp', function () {
		if ( ! lc_anime_lp_is() ) {
			return;
		}
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
	} );

	add_action( 'wp_head', function () {
		if ( ! lc_anime_lp_is() ) {
			return;
		}
		echo '<link rel="preload" as="image" fetchpriority="high" href="https://lodsclub.com/wp-content/uploads/2026/11/lp-anime-art-infinity-castle.jpg">' . "\n";
	}, 1 );

	add_filter( 'the_content', function ( $html ) {
		if ( ! lc_anime_lp_is() ) {
			return $html;
		}
		$i = 0;
		return preg_replace_callback( '/<img\b(?![^>]*\bloading=)/i', function ( $m ) use ( &$i ) {
			$i++;
			return $i <= 4 ? '<img fetchpriority="high"' : '<img loading="lazy"';
		}, $html );
	}, 99 );
}
