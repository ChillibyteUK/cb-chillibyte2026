<?php
/**
 * Block template for CB Text Image.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$content            = $attributes['content'] ?? '';
$cta_text           = $attributes['ctaText'] ?? '';
$cta_url            = $attributes['ctaUrl'] ?? '';
$cta_target         = ! empty( $attributes['ctaTarget'] );
$image_id           = $attributes['imageId'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-text-image' ) );
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6" data-reveal-container>
		<div class="row gap-5">
			<div class="col-12 col-md-7">
				<?php
				if ( $btitle ) {
					?>
				<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
					<?php
				}
				if ( $content ) {
					?>
					<div class="mb-5" data-reveal><?= wp_kses_post( $content ); ?></div>
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
					class="btn btn-green-outline btn-arrow" data-reveal><?= esc_html( $cta_text ? $cta_text : $cta_url ); ?></a>
					<?php
				}
				?>
			</div>
			<div class="col-12 col-md-5 my-auto mx-auto">
				<?php
				if ( $image_id ) {
					echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'img-fluid', 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() already escapes its output.
				}
				?>
			</div>
		</div>
	</div>
</section>
