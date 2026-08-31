/**
 * [data-reveal] — this theme's lightweight, hand-rolled equivalent of AOS.
 * See src/css/animate.css for the CSS half of this convention.
 *
 * Three globals, all exposed on window (not just ES exports) specifically
 * so per-block view.js files can call them — those are separate, unbundled
 * scripts outside this rollup build and can't import from it:
 *
 * - window.cbReveal(el, delaySeconds) — animates one element in. Also
 *   auto-runs window.cbScribble(el) the instant its own fade-in completes,
 *   so any revealed element containing an <em> gets the RoughNotation
 *   underline for free, correctly timed, no extra wiring needed.
 * - window.cbScribble(container) — RoughNotation-underlines every <em>
 *   inside `container`. Standalone too, e.g. cb-hero calls this directly
 *   on its own h1 once the typewriter finishes, since that element isn't
 *   part of the [data-reveal] system at all.
 * - window.cbRevealSequence(container) — staggers every [data-reveal]
 *   child of `container` in DOM order via window.cbReveal.
 *
 * Default behaviour: any [data-reveal-container] auto-sequences its
 * [data-reveal] children the moment it scrolls into view. A standalone
 * [data-reveal] with no [data-reveal-container] ancestor auto-reveals
 * itself the same way.
 *
 * Opt-out: add data-reveal-manual to a [data-reveal-container] (or a
 * standalone [data-reveal]) and this file leaves it alone — some other
 * script decides when to trigger it instead, by calling
 * window.cbRevealSequence()/window.cbReveal() directly. cb-hero uses this
 * for its subtitle/CTA, gated on its typewriter finishing rather than on
 * scroll position.
 */

window.cbScribble = function ( container ) {
	if ( typeof RoughNotation === 'undefined' ) {
		/*
		 * Warn rather than return quietly. rough-notation is a global
		 * dependency of this bundle (see inc/enqueue.php), so reaching here
		 * means it failed to load — CDN blocked, offline, or the dependency
		 * dropped from the enqueue. Silent was how this went unnoticed on
		 * every page but two: a missing underline is indistinguishable from
		 * a heading that simply has no <em> in it.
		 */
		if ( window.console && console.warn ) {
			console.warn( 'cbScribble: RoughNotation is not loaded — .cb-scribble-text underlines will not render.' );
		}
		return;
	}

	container.querySelectorAll( 'em' ).forEach( function ( em ) {
		RoughNotation.annotate( em, {
			type: 'underline',
			color: '#FF4848',
			strokeWidth: 5,
			iterations: 3,
			padding: 4,
			animationDuration: 700,
			multiline: true,
		} ).show();
	} );
};

window.cbReveal = function ( el, delaySeconds ) {
	if ( typeof gsap === 'undefined' ) {
		el.style.opacity = '1';
		el.style.transform = 'none';
		window.cbScribble( el );
		return;
	}
	gsap.to( el, {
		opacity: 1,
		/*
		 * Every transform component the CSS half might start an element
		 * from, reset to its resting value — see [data-reveal-from] in
		 * src/css/animate.css. Each is a no-op for elements that weren't
		 * offset that way, so one tween serves every variant; animating y
		 * alone would leave a horizontally offset or scaled element
		 * stranded at its start state, faded in but never arriving.
		 */
		x: 0,
		y: 0,
		scale: 1,
		duration: 0.6,
		ease: 'power2.out',
		delay: delaySeconds || 0,
		onComplete: function () {
			window.cbScribble( el );
		},
	} );
};

window.cbRevealSequence = function ( container, stagger ) {
	var gap = stagger || parseFloat( container.dataset.revealStagger ) || 0.15;
	container.querySelectorAll( '[data-reveal]' ).forEach( function ( el, i ) {
		window.cbReveal( el, i * gap );
	} );
};

function cbObserveOnce( el, callback ) {
	if ( typeof IntersectionObserver === 'undefined' ) {
		callback();
		return;
	}
	var observer = new IntersectionObserver(
		function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) return;
				callback();
				obs.unobserve( entry.target );
			} );
		},
		{ threshold: 0.2 }
	);
	observer.observe( el );
}

export function initReveal() {
	if ( typeof gsap === 'undefined' || ! ( 'IntersectionObserver' in window ) ) {
		document.querySelectorAll( '[data-reveal]' ).forEach( function ( el ) {
			el.style.opacity = '1';
			el.style.transform = 'none';
		} );
		return;
	}

	document.querySelectorAll( '[data-reveal-container]:not([data-reveal-manual])' ).forEach( function ( container ) {
		cbObserveOnce( container, function () {
			window.cbRevealSequence( container );
		} );
	} );

	document.querySelectorAll( '[data-reveal]:not([data-reveal-manual])' ).forEach( function ( el ) {
		if ( el.closest( '[data-reveal-container]' ) ) return;
		cbObserveOnce( el, function () {
			window.cbReveal( el );
		} );
	} );
}
