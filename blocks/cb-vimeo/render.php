<?php
/**
 * Block template for CB Vimeo.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle   = $attributes['title'] ?? '';
$intro    = $attributes['intro'] ?? '';
$vimeo_id = $attributes['vimeoId'] ?? '';
$size     = $attributes['size'] ?? '';

// True whether the editor's background colour came from the theme palette
// (backgroundColor, a slug) or the custom colour picker (style.color.background,
// a hex value) — supports.color.background can produce either.
$has_background_color = ! empty( $attributes['backgroundColor'] ) || ! empty( $attributes['style']['color']['background'] );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => trim( 'cb-vimeo' . ( $has_background_color ? ' py-6' : '' ) ) ) );

if ( ! $vimeo_id ) {
	return;
}

if ( 'Container' === $size ) {
	$container = 'container';
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php
		if ( $btitle ) {
			?>
		<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
			<?php
		}
		if ( $intro ) {
			?>
			<div class="mb-5" data-reveal><?= wp_kses_post( $intro ); ?></div>
			<?php
		}
		?>
	</div>
	<?php
	/*
	 * Zooms in on entry. Applied to the player wrapper rather than the
	 * iframe itself: this element already carries the aspect-ratio box, so
	 * its height is reserved before the embed loads, and a transform is
	 * compositor-only and never reflows — between them nothing shifts at
	 * any point. Scaling the iframe directly would look identical but makes
	 * the embedded player re-rasterise mid-animation.
	 *
	 * There's no data-reveal-container in this block, so reveal.js treats
	 * this as a standalone element and reveals it on its own intersection.
	 */
	?>
	<div class="cb-vimeo__player <?php echo esc_attr( $container ?? '' ); ?>" data-reveal data-reveal-from="zoom">
		<?php
		if ( $vimeo_id ) {
			?>
		<iframe src="https://player.vimeo.com/video/<?= esc_attr( $vimeo_id ); ?>" frameborder="0" allowfullscreen="" loading="lazy"></iframe>
			<?php
		}
		?>
	</div>
</section>
