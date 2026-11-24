<?php
/**
 * Block template for CB Service Details.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle = $attributes['title'] ?? '';
$intro  = $attributes['intro'] ?? '';
$items  = $attributes['items'] ?? array();

$items = array_values(
	array_filter(
		$items,
		function ( $item ) {
			return ! empty( $item['title'] ) || ! empty( $item['description'] );
		}
	)
);

if ( empty( $btitle ) && empty( $intro ) && empty( $items ) ) {
	return;
}

// The scribble underline is drawn on <em> and needs room below it; a title without one can sit closer to what follows.
$title_gap          = false !== stripos( $btitle, '<em' ) ? 'mb-5' : 'mb-3';

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-service-details' ) );
$br_allowed         = array( 'br' => array() );
// Description is a RichText field: bold/italic and line breaks only.
$desc_allowed       = array(
	'br'     => array(),
	'strong' => array(),
	'b'      => array(),
	'em'     => array(),
	'i'      => array(),
);
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="row cb-service-details__layout">
			<div class="col-12 col-lg-5 cb-service-details__intro-col">
				<?php
				if ( $btitle ) {
					?>
				<h2 class="cb-scribble-text <?= esc_attr( $title_gap ); ?>" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
					<?php
				}
				if ( $intro ) {
					?>
					<div class="cb-service-details__intro mb-5" data-reveal><?= wp_kses_post( wpautop( $intro ) ); ?></div>
					<?php
				}
				?>
			</div>

			<?php if ( $items ) { ?>
				<div class="col-12 col-lg-7 cb-service-details__items-col">
					<div class="cb-service-details__items" data-service-details-items>
						<?php foreach ( $items as $index => $item ) { ?>
							<article class="cb-service-details__item<?= 0 === $index ? ' is-active' : ''; ?>" data-service-details-item>
								<h3 class="cb-service-details__item-title"><?= wp_kses( $item['title'] ?? '', $br_allowed ); ?></h3>
								<div class="cb-service-details__body"><?= nl2br( wp_kses( $item['description'] ?? '', $desc_allowed ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses()'d to inline formatting, then nl2br() adds only <br> (keeps legacy plain-text line breaks). ?></div>
							</article>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
