/**
 * Smooth scroll via Lenis (https://lenis.darkroom.engineering/), loaded as a
 * global from the CDN build in inc/enqueue.php — 'lenis' is a script
 * dependency of theme.min.js so window.Lenis is guaranteed to exist here.
 */
export function initLenis() {
	if (typeof Lenis === 'undefined') return;

	const lenis = new Lenis();

	function raf(time) {
		lenis.raf(time);
		requestAnimationFrame(raf);
	}

	requestAnimationFrame(raf);
}
