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
 * - window.cbRevealRows(container) — observes each [data-reveal] child
 *   separately and staggers only across each visual row. See below.
 *
 * Default behaviour: any [data-reveal-container] auto-sequences its
 * [data-reveal] children the moment it scrolls into view. A standalone
 * [data-reveal] with no [data-reveal-container] ancestor auto-reveals
 * itself the same way.
 *
 * Grids taller than the viewport want [data-reveal-rows] instead.
 * [data-reveal-container] triggers on the *container*, so a long card grid
 * animates every item the instant its top edge appears — the cascade runs
 * off-screen and everything below the fold is already faded in by the time
 * you scroll to it. [data-reveal-rows] observes each item on its own, so a
 * row animates as you reach it, and staggers by the item's position within
 * its own row so the cascade still reads left-to-right. Both honour
 * data-reveal-stagger; the two are alternatives, don't put both on one
 * element. blocks/cb-team-cards is the worked example (20 cards, ~2,700px
 * tall four-up and ~11,000px stacked one-up).
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

window.cbRevealRows = function ( container, stagger ) {
	var gap = stagger || parseFloat( container.dataset.revealStagger ) || 0.08;
	var items = Array.prototype.slice.call( container.querySelectorAll( '[data-reveal]' ) );

	items.forEach( function ( el ) {
		cbObserveOnce( el, function () {
			/*
			 * Stagger by position within the item's own visual row, worked out
			 * from layout rather than from DOM index, so one implementation
			 * covers every breakpoint: whatever the grid resolves to (4-up,
			 * 3-up, 1-up) is what this reads.
			 *
			 * offsetTop/offsetLeft, not getBoundingClientRect, on purpose —
			 * these are pre-transform layout values, so an item part-way
			 * through its own zoom/slide tween still reports the position it
			 * occupies in the grid. Rect-based grouping would drift as soon as
			 * neighbours started animating.
			 */
			var column = items.filter( function ( other ) {
				return other.offsetTop === el.offsetTop && other.offsetLeft < el.offsetLeft;
			} ).length;

			window.cbReveal( el, column * gap );
		} );
	} );
};

function cbObserveOnce( el, callback ) {
	if ( typeof IntersectionObserver === 'undefined' ) {
		callback();
		return;
	}
	/*
	 * threshold is a fraction of the *target*, not of the viewport — so a
	 * ratio-based trigger is unreachable for any target taller than
	 * viewport / ratio, and the callback simply never runs. The old
	 * { threshold: 0.2 } silently did this to the About page's team grid:
	 * that grid is its own [data-reveal-container], and stacked one-up at
	 * mobile it stands 11,275px tall, so 20% of it is 2,255px — impossible to
	 * show at once in an 812px viewport (peak ratio 0.072). All 20 cards
	 * stayed at opacity 0 permanently. It only worked on desktop because
	 * four-up makes the same grid 2,665px, where 20% fits on screen.
	 *
	 * rootMargin sidesteps the whole problem: shrinking the root's bottom
	 * edge by 15% means "fire once the target's top edge has risen into the
	 * top 85% of the viewport", which is a statement about position rather
	 * than proportion and so doesn't care how tall the target is. Timing for
	 * normal-sized elements is close to what the 0.2 ratio used to give.
	 */
	var observer = new IntersectionObserver(
		function ( entries, obs ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) return;
				callback();
				obs.unobserve( entry.target );
			} );
		},
		{ threshold: 0, rootMargin: '0px 0px -15% 0px' }
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

	document.querySelectorAll( '[data-reveal-rows]:not([data-reveal-manual])' ).forEach( function ( container ) {
		window.cbRevealRows( container );
	} );

	document.querySelectorAll( '[data-reveal]:not([data-reveal-manual])' ).forEach( function ( el ) {
		if ( el.closest( '[data-reveal-container], [data-reveal-rows]' ) ) return;
		cbObserveOnce( el, function () {
			window.cbReveal( el );
		} );
	} );
}
