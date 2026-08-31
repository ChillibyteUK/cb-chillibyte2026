<?php
/**
 * Block template for CB Hero.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$is_homepage        = ! empty( $attributes['isHomepage'] );
$hero_title         = $attributes['heroTitle'] ?? '';
$subtitle           = $attributes['subtitle'] ?? '';
$cta_text           = $attributes['ctaText'] ?? '';
$cta_url            = $attributes['ctaUrl'] ?? '';
$cta_target         = ! empty( $attributes['ctaTarget'] );
/*
 * Resolve against the generated list of bitmaps that actually exist: blank
 * falls back to the default, and so does a slug whose grid-*.json has been
 * renamed or removed. Emitting the raw attribute meant a stale value asked
 * for a nonexistent JSON, and reveal.js has no .catch() — the fetch 404'd,
 * .json() threw on WordPress's HTML error page, and the hero rendered blank
 * with nothing user-visible to explain it.
 */
$animation          = cb_chillibyte_2026_resolve_hero_animation( $attributes['animation'] ?? '' );
/*
 * Only the homepage hero runs full height; everything else gets the short
 * variant. Driven by a modifier class rather than an inline style so the
 * height stays in CSS with the rest of the hero's layout —
 * --hero-max-height is declared on :root in animation/css/reveal.css and
 * read by .grid-container, so overriding it anywhere up the tree cascades
 * down without needing to restate the height calc itself.
 */
$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => 'cb-hero' . ( $is_homepage ? '' : ' cb-hero--short' ) )
);
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<?php if ( $animation ) { ?>
	<div class="grid-container">
		<svg class="grid" viewBox="0 0 560 460" preserveAspectRatio="xMaxYMid meet"
			data-grid-reveal="<?= esc_url( get_template_directory_uri() . '/animation/json/grid-' . $animation . '.json' ); ?>">
		</svg>
	</div>
	<?php } ?>
	<div class="hero-content container">
		<?php
		if ( $hero_title ) {
			?>
		<h1 class="cb-hero-title cb-scribble-text">
			<?= nl2br( wp_kses_post( $hero_title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?>
		</h1>
		<noscript><style>.cb-hero-title { opacity: 1 !important; }</style></noscript>
			<?php
		}
		?>
		<div data-reveal-container data-reveal-manual>
			<?php
			if ( $subtitle ) {
				?>
			<p class="mb-4" data-reveal><?= esc_html( $subtitle ); ?></p>
				<?php
			}
			if ( $cta_url ) {
				?>
			<a href="<?= esc_url( $cta_url ); ?>"
				<?php
				if ( $cta_target ) {
					?>
				target="_blank" rel="noopener"
					<?php
				}
				?>
				class="btn btn-green btn-arrow" data-reveal><?= esc_html( $cta_text ? $cta_text : $cta_url ); ?></a>
				<?php
			}
			?>
		</div>
	</div>
</section>

<?php if ( ! $is_homepage && is_singular() ) { ?>
	<?php cb_chillibyte_2026_render_breadcrumbs( cb_chillibyte_2026_get_breadcrumbs( get_the_ID() ), 'cb-breadcrumbs' ); ?>
<?php } ?>
