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

if ( empty( $title ) && empty( $intro ) && empty( $items ) ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-service-details' ) );
$br_allowed         = array( 'br' => array() );
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="row cb-service-details__layout">
			<div class="col-12 col-lg-5 cb-service-details__intro-col">
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
				?>
			</div>

			<?php if ( $items ) { ?>
				<div class="col-12 col-lg-7 cb-service-details__items-col">
					<div class="cb-service-details__items" data-service-details-items>
						<?php foreach ( $items as $index => $item ) { ?>
							<article class="cb-service-details__item<?= 0 === $index ? ' is-active' : ''; ?>" data-service-details-item>
								<h3 class="cb-service-details__item-title"><?= wp_kses( $item['title'] ?? '', $br_allowed ); ?></h3>
								<div class="cb-service-details__body"><?= nl2br( wp_kses( $item['description'] ?? '', $br_allowed ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses()'d then nl2br() adds only <br>. ?></div>
							</article>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
