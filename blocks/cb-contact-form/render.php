<?php
/**
 * Block template for CB Contact Form.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle       = $attributes['title'] ?? '';
$show_address = ! empty( $attributes['showAddress'] );
$form_shortcode     = cb_chillibyte_2026_get_setting( 'contact_form' );
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-contact-form' ) );
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6">
		<?php
		if ( $btitle ) {
			?>
		<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
			<?php
		}
		?>
		<div class="row">
			<div class="col-12 col-md-6 col-lg-3">
				<div class="cb-title-anim--hello"><?= get_title_animation( 'hello' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG + <style>, not user input. ?></div>
			</div>
			<div class="col-12 col-md-6 col-lg-3">
				<?php
				if ( $show_address ) {
					$address = cb_chillibyte_2026_get_setting( 'address' );
					if ( $address ) {
						echo '<address>' . nl2br( esc_html( $address ) ) . '</address>';
					}
					?>
					<div class="mt-3">
						<?= do_shortcode('[contact_email_icon]'); ?>
					</div>
					<div class="mt-2">
						<?= do_shortcode('[contact_phone_icon]'); ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="col-12 col-lg-6">
				<?php
				if ( $form_shortcode ) {
					?>
				<?= do_shortcode( $form_shortcode ); ?>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</section>
