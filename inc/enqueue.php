<?php
/**
 * Enqueue theme CSS/JS. filemtime versioning, no dependencies (no jQuery,
 * no Bootstrap JS) — plain vanilla output, loads immediately.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue theme.min.css.
 *
 * @return void
 */
function cb_chillibyte_2026_enqueue_styles() {
	$rel = '/css/theme.min.css';
	$abs = get_stylesheet_directory() . $rel;
	if ( file_exists( $abs ) ) {
		wp_enqueue_style( 'cb-chillibyte-2026-theme', get_stylesheet_directory_uri() . $rel, array(), filemtime( $abs ) );
	}
}
add_action( 'wp_enqueue_scripts', 'cb_chillibyte_2026_enqueue_styles' );

/**
 * Enqueue a file from js/vendor/, filemtime-versioned like everything else.
 *
 * Third-party libraries are vendored into js/vendor/ and committed, the same
 * convention the compiled css/ and js/ output already follows. They used to
 * be loaded from jsdelivr/unpkg, which cost three extra DNS/TLS handshakes
 * and put lenis.css in the render-blocking critical path on a third-party
 * origin; serving them from this origin removes both.
 *
 * Vendored by hand rather than tracked in package.json, so the upstream
 * version of each is recorded here — check this before replacing a file:
 *   gsap.min.js             3.12.7  cdn.jsdelivr.net/npm/gsap
 *   lenis.min.js, lenis.css 1.3.11  unpkg.com/lenis
 *   rough-notation.iife.js  0.5.1   cdn.jsdelivr.net/npm/rough-notation
 *
 * @param string $handle Handle to register under.
 * @param string $file   Filename within js/vendor/.
 * @param bool   $is_css True to enqueue as a stylesheet rather than a script.
 * @return void
 */
function cb_chillibyte_2026_enqueue_vendor( $handle, $file, $is_css = false ) {
	$rel = '/js/vendor/' . $file;
	$abs = get_stylesheet_directory() . $rel;
	if ( ! file_exists( $abs ) ) {
		return;
	}
	$url = get_stylesheet_directory_uri() . $rel;
	if ( $is_css ) {
		wp_enqueue_style( $handle, $url, array(), filemtime( $abs ) );
	} else {
		wp_enqueue_script( $handle, $url, array(), filemtime( $abs ), true );
	}
}

/**
 * Enqueue theme.min.js.
 *
 * @return void
 */
function cb_chillibyte_2026_enqueue_scripts() {

	cb_chillibyte_2026_enqueue_vendor( 'lenis-style', 'lenis.css', true );
	cb_chillibyte_2026_enqueue_vendor( 'lenis', 'lenis.min.js' );

	// Global, not per-block — used across enough blocks (typewriter/reveal
	// animations) that it belongs in the main bundle's dependency chain
	// rather than each block re-registering it individually.
	cb_chillibyte_2026_enqueue_vendor( 'gsap', 'gsap.min.js' );

	/*
	 * Global for the same reason as gsap, and for one more: window.cbScribble
	 * lives in the main bundle and cbReveal() calls it automatically for any
	 * revealed element containing an <em>. The helper is therefore always
	 * present and always tries to run, so a per-block dependency guaranteed
	 * they would drift — and they did. This was previously registered and
	 * pulled in via block.json's viewScript, but only 2 of the 9 blocks
	 * rendering .cb-scribble-text actually listed it, so the underline
	 * silently never drew on most pages (cbScribble bails when the library
	 * is missing). Making it a dependency here means presence is guaranteed
	 * wherever the helper can be called from.
	 */
	cb_chillibyte_2026_enqueue_vendor( 'rough-notation', 'rough-notation.iife.js' );

	$rel = '/js/theme.min.js';
	$abs = get_stylesheet_directory() . $rel;
	if ( file_exists( $abs ) ) {
		wp_enqueue_script( 'cb-chillibyte-2026-theme', get_stylesheet_directory_uri() . $rel, array( 'lenis', 'gsap', 'rough-notation' ), filemtime( $abs ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'cb_chillibyte_2026_enqueue_scripts' );

/*
 * A cb_chillibyte_2026_register_script_handles() used to sit here, registering
 * rough-notation so block.json viewScript arrays could pull it in by handle.
 * It existed solely for that one library, which is now enqueued globally
 * above, so the function had nothing left to register. The pattern itself is
 * still valid for a genuinely block-specific library — re-add a registrar if
 * one comes along; block.json resolves any handle registered before render.
 */

/**
 * Enqueue prop-for-that (https://github.com/argyleink/prop-for-that) as a
 * native ES module. It's ESM-only (`import 'prop-for-that/auto'`), so it
 * goes through the Script Modules API rather than wp_enqueue_script — this
 * theme's rollup pipeline has no node-resolve/commonjs plugin (see
 * src/build/rollup.config.js), so it's loaded from esm.sh rather than
 * bundled, same CDN-first approach as the commented gsap/lenis examples
 * above.
 *
 * @return void
 */
function cb_chillibyte_2026_enqueue_script_modules() {
	wp_enqueue_script_module( 'prop-for-that', 'https://esm.sh/prop-for-that@0.7.12/auto', array(), '0.7.12' );
}
add_action( 'wp_enqueue_scripts', 'cb_chillibyte_2026_enqueue_script_modules' );

/**
 * Dequeue Gravity Forms (and its Turnstile add-on) CSS/JS on any page that
 * doesn't actually render the contact form block.
 *
 * Gravity Forms' own conditional-loading logic looks for the
 * [gravityform] shortcode (or its own block) sitting directly in
 * post_content. That never matches here: cb-contact-form pulls its
 * shortcode from a Site-Wide Settings option and runs it through
 * do_shortcode() at render time (see blocks/cb-contact-form/render.php),
 * so GF has no way to tell which pages need its assets and — per a live
 * trace of the homepage — enqueues them everywhere regardless, several
 * render-blocking stylesheets included.
 *
 * This checks for our own wrapper block instead (has_block() reads
 * post_content directly, so it works whether or not the block itself
 * bothers to call GF's detection) and strips anything already enqueued
 * whose src lives under the plugin's own directory when that block is
 * absent. Matched by plugin path rather than specific handle names so a
 * Gravity Forms/Turnstile update that renames a handle doesn't silently
 * stop this working. Priority 100 so it runs after GF's own
 * wp_enqueue_scripts registration (default priority 10).
 *
 * @return void
 */
function cb_chillibyte_2026_dequeue_unused_gravityforms_assets() {
	if ( is_admin() || has_block( 'cb-chillibyte-2026/cb-contact-form' ) ) {
		return;
	}

	$plugin_slugs = array( 'gravityforms', 'gravityformsturnstile' );

	foreach ( wp_styles()->queue as $handle ) {
		$style = wp_styles()->registered[ $handle ] ?? null;
		foreach ( $plugin_slugs as $plugin_slug ) {
			if ( $style && is_string( $style->src ) && false !== strpos( $style->src, "/plugins/{$plugin_slug}/" ) ) {
				wp_dequeue_style( $handle );
				break;
			}
		}
	}

	foreach ( wp_scripts()->queue as $handle ) {
		$script = wp_scripts()->registered[ $handle ] ?? null;
		foreach ( $plugin_slugs as $plugin_slug ) {
			if ( $script && is_string( $script->src ) && false !== strpos( $script->src, "/plugins/{$plugin_slug}/" ) ) {
				wp_dequeue_script( $handle );
				break;
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'cb_chillibyte_2026_dequeue_unused_gravityforms_assets', 100 );
