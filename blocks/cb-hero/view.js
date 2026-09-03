/**
 * Frontend JS for the cb-hero block. Registered via block.json's
 * "viewScript" — WordPress auto-enqueues this only on pages that actually
 * render the block, no manual wp_enqueue_script needed. Plain vanilla,
 * unbundled (not part of webpack.config.js's src/index.js entry glob), so
 * no import/export syntax — write it the same way src/js/nav-toggle.js is
 * written.
 */

/**
 * Wraps every character of text nodes under `node` in its own
 * .cb-hero-char span, recursing into element children (so <strong>/<em>
 * wrappers survive intact — only their text gets split) but leaving <br>
 * alone since it has no text to split.
 */
function cbHeroWrapChars( node ) {
	Array.from( node.childNodes ).forEach( function ( child ) {
		if ( child.nodeType === Node.TEXT_NODE ) {
			var frag = document.createDocumentFragment();
			child.textContent.split( '' ).forEach( function ( char ) {
				var span = document.createElement( 'span' );
				span.className = 'cb-hero-char';
				span.textContent = char;
				frag.appendChild( span );
			} );
			node.replaceChild( frag, child );
		} else if ( child.nodeType === Node.ELEMENT_NODE && child.tagName !== 'BR' ) {
			cbHeroWrapChars( child );
		}
	} );
}

/**
 * Reveals the subtitle/CTA once the title has finished animating. Both
 * carry data-reveal, and their wrapping div carries data-reveal-container
 * data-reveal-manual (see render.php) — the "manual" flag opts the whole
 * group out of reveal.js's default scroll-triggered auto-sequencing, so
 * this function controls the timing instead. window.cbRevealSequence /
 * window.cbScribble are the same shared helpers reveal.js uses for its own
 * auto-sequenced reveals elsewhere on the site — see src/js/reveal.js.
 *
 * This block's viewScript only declares gsap as a dependency (see
 * block.json), not the full theme bundle that defines these two globals,
 * so the typewriter can start as soon as gsap is ready instead of queuing
 * behind lenis/rough-notation/theme.min.js. Guarded rather than assumed
 * present for that reason — in the extremely unlikely case the bundle
 * hasn't finished executing yet, skip rather than throw.
 */
function cbHeroRevealContent( hero ) {
	var container = hero.querySelector( '[data-reveal-container]' );
	if ( container && typeof window.cbRevealSequence === 'function' ) window.cbRevealSequence( container );
}

function cbHeroScribble( h1 ) {
	if ( typeof window.cbScribble === 'function' ) window.cbScribble( h1 );
}

function initCbHero() {
	document.querySelectorAll( '.wp-block-cb-chillibyte-2026-cb-hero' ).forEach( function ( hero ) {
		var h1 = hero.querySelector( '.cb-hero-title' );
		if ( ! h1 ) return;

		// No gsap? Skip the typewriter and just show everything as-is.
		if ( typeof gsap === 'undefined' ) {
			h1.style.opacity = '1';
			cbHeroScribble( h1 );
			cbHeroRevealContent( hero );
			return;
		}

		var originalHTML = h1.innerHTML;
		cbHeroWrapChars( h1 );
		var chars = h1.querySelectorAll( '.cb-hero-char' );

		// Chars start hidden via CSS (.cb-hero-char { opacity: 0 }), so
		// revealing the title itself here shows nothing yet — no flash of
		// the full unsplit heading before the typewriter stagger begins.
		h1.style.opacity = '1';

		gsap.timeline()
			.to( chars, {
				opacity: 1,
				duration: 0.01,
				stagger: 0.035,
			} )
			.call( function () {
				// Undo the per-character span wrapping — RoughNotation's
				// multiline mode (see window.cbScribble) uses getClientRects()
				// to detect line wraps, and reads one rect per inline child
				// element. Left wrapped, that's one rect per character
				// (mistaken for one per line) instead of one per actual
				// visual line, producing a jagged per-character underline.
				h1.innerHTML = originalHTML;
				cbHeroScribble( h1 );
				cbHeroRevealContent( hero );
			} );
	} );
}

// This is a footer-placed script, so DOMContentLoaded has often already
// fired by the time it runs — attaching the listener unconditionally would
// silently never call initCbHero() in that case.
if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initCbHero );
} else {
	initCbHero();
}
