<?php
/**
 * Block template for CB Text Quote.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$content            = $attributes['content'] ?? '';
$cta_text           = $attributes['ctaText'] ?? '';
$cta_url            = $attributes['ctaUrl'] ?? '';
$cta_target         = ! empty( $attributes['ctaTarget'] );
$quote_body         = $attributes['quoteBody'] ?? '';
$quote_attribution  = $attributes['quoteAttribution'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-text-quote' ) );
$has_content        = '' !== trim( wp_strip_all_tags( $content ) );
$text_col_class     = $has_content ? 'col-12 col-lg-7' : 'col-12 col-lg-5';
$quote_col_class    = $has_content ? 'col-12 col-lg-5' : 'col-12 col-lg-7';
$quote_class        = $has_content ? 'cb-text-quote__quote' : 'cb-text-quote__quote cb-text-quote__quote--plain';
$quote_wrap_class   = $has_content ? 'cb-text-quote__quote-wrapper' : 'cb-text-quote__quote-wrapper cb-text-quote__quote-wrapper--plain';
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6" data-reveal-container>
		<div class="row gap-5">
			<div class="<?= esc_attr( $text_col_class ); ?>">
				<?php
				if ( $btitle ) {
					?>
				<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
					<?php
				}
				if ( $has_content ) {
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
			<div class="<?= esc_attr( $quote_col_class ); ?>">
				<?php
				/*
				 * Reveals as one unit: the panel and its accent bar slide in
				 * together from the right. The body and attribution inside
				 * deliberately carry no data-reveal of their own — staggering
				 * them as well would animate content within a card that is
				 * itself still moving.
				 */
				?>
				<div class="<?= esc_attr( $quote_wrap_class ); ?>" data-reveal data-reveal-from="right">
					<div class="<?= esc_attr( $quote_class ); ?>">
						<svg class="cb-text-quote__quote-icon" viewBox="0 0 125 85" aria-hidden="true">
							<path class="cb-text-quote__quote-icon-left" d="M0,0 L11,0 L26,28 L56,28 L56,85 L0,85 Z" />
							<path class="cb-text-quote__quote-icon-right" d="M69,0 L80,0 L95,28 L125,28 L125,85 L69,85 Z" />
						</svg>
						<?php
						if ( $quote_body ) {
							?>
						<div class="cb-text-quote__quote-body"><?= wp_kses_post( wpautop( $quote_body ) ); ?></div>
							<?php
						}
						if ( $quote_attribution ) {
							?>
						<div class="cb-text-quote__quote-attribution"><?= esc_html( $quote_attribution ); ?></div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
