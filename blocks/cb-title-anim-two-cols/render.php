<?php
/**
 * Block template for CB Title, Anim, Two Cols.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$left_text          = $attributes['leftText'] ?? '';
$right_text         = $attributes['rightText'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-title-anim-two-cols' ) );

$animation = $attributes['animation'] ?? '';
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6">
		<div class="cb-title-anim-two-cols__title-container">
			<?php
			if ( $btitle ) {
				?>
			<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
				<?php
			}
			if ( $animation ) {
				?>
			<div class="cb-title-anim--<?= esc_attr( $animation ); ?>"><?= get_title_animation( $animation ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG + <style>, not user input. ?></div>
				<?php
			}
			?>
		</div>
		<div class="row pretty-text">
			<div class="col-12 col-md-6 has-xl-font-size">
				<?php
				if ( $left_text ) {
					echo wp_kses_post( $left_text );
				}
				?>
			</div>
			<div class="col-12 col-md-6">
				<?php
				if ( $right_text ) {
					echo wp_kses_post( $right_text );
				}
				?>
			</div>
		</div>
	</div>
</section>
