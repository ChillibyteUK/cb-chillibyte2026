<?php
/**
 * Block template for CB Awards Strip.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/*
 * Per-block selection wins; an empty block falls back to the site-wide
 * Awards Gallery on the Site-Wide Settings page, so the common case (one
 * strip, the same badges everywhere) needs no per-instance picking, while
 * a page wanting a different set can still override it locally.
 */
$awards = $attributes['awards'] ?? array();

if ( ! $awards ) {
	$awards = cb_chillibyte_2026_get_gallery_setting( 'awards_gallery' );
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-awards-strip' ) );
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-5">
		<?php
		if ( $awards ) {
			?>
		<div class="row">
			<?php
			foreach ( $awards as $awards_id ) {
				$awards_id = absint( $awards_id );
				if ( ! $awards_id ) {
					continue;
				}
				?>
			<div class="col-12 col-md-6 col-lg-4 col-xl-2 my-auto"><?= wp_get_attachment_image( $awards_id, 'large', false, array( 'alt' => '' ) ); ?></div>
				<?php
			}
			?>
		</div>
			<?php
		}
		?>
	</div>
</section>
