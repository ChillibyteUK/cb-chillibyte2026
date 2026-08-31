<?php
/**
 * Block template for CB Client Grid.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$logos              = $attributes['logos'] ?? array();
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-client-grid' ) );
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
	<?php
	if ( $logos ) {
		?>
		<ul class="cb-client-grid__grid" data-reveal-container data-reveal-stagger="0.05">
			<?php
			foreach ( $logos as $logos_id ) {
				$logos_id = absint( $logos_id );
				if ( ! $logos_id ) {
					continue;
				}
				?>
				<li class="cb-client-grid__item" data-reveal><?= wp_get_attachment_image( $logos_id, 'large', false, array( 'alt' => '' ) ); ?></li>
				<?php
			}
			?>
		</ul>
		<?php
	}
	?>
	</div>
</section>
