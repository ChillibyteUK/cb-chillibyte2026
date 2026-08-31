<?php
/**
 * Block template for CB Review Slider.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-review-slider' ) );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6">
		<?php
		if ( $btitle ) {
			?>
		<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
			<?php
		}
		?>
		INSERT BRB SLIDER HERE
	</div>
</section>
