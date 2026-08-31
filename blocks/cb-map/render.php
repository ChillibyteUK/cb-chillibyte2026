<?php
/**
 * Block template for CB Map.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-map' ) );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6">
		<?php
		if ( $btitle ) {
			?>
		<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
			<?php
		}
		$map_url = cb_chillibyte_2026_get_setting( 'map_url' );
		if ( $map_url ) {
			?>
		<iframe src="<?= esc_url( $map_url ); ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			<?php
		}
		?>
	</div>
</section>
