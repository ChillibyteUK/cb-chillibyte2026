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
 * Enqueue theme.min.js.
 *
 * @return void
 */
function cb_chillibyte_2026_enqueue_scripts() {

	// wp_enqueue_script( 'gsap-scrolltrigger', 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/ScrollTrigger.min.js', array( 'gsap' ), '3.12.7', true );

	wp_enqueue_style( 'lenis-style', 'https://unpkg.com/lenis@1.3.11/dist/lenis.css', array() );
	wp_enqueue_script( 'lenis', 'https://unpkg.com/lenis@1.3.11/dist/lenis.min.js', array(), '1.3.11', true );

	// Global, not per-block — used across enough blocks (typewriter/reveal
	// animations) that it belongs in the main bundle's dependency chain
	// rather than each block re-registering it individually.
	wp_enqueue_script( 'gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js', array(), '3.12.7', true );

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
	wp_enqueue_script( 'rough-notation', 'https://cdn.jsdelivr.net/npm/rough-notation@0.5.1/lib/rough-notation.iife.js', array(), '0.5.1', true );

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
