<?php
/**
 * Block template for CB Numbered Grid.
 *
 * Numbered peers, not sequential steps — the numerals order the set for
 * reference, they don't imply that one item follows from another. That's why
 * this is a grid of equal cards rather than a timeline or a connected rail.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$intro              = $attributes['intro'] ?? '';
$items              = $attributes['items'] ?? array();
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-numbered-grid' ) );

if ( empty( $btitle ) && empty( $items ) ) {
	return;
}
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php
		if ( $btitle ) {
			?>
		<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
			<?php
		}

		if ( $intro ) {
			?>
			<div class="mb-5" data-reveal><?= wp_kses_post( $intro ); ?></div>
			<?php
		}

		if ( $items ) {
			/*
			 * data-reveal-rows, not data-reveal-container: stacked one-up on
			 * mobile this grid is several viewports tall, and a container
			 * reveal would run the whole cascade the moment the top edge
			 * appeared, leaving everything below already faded in.
			 */
			?>
			<div class="grid cb-numbered-grid__grid" data-reveal-rows data-reveal-stagger="0.08">
				<?php
				$number = 0;
				foreach ( $items as $item ) {
					$item_title = $item['title'] ?? '';
					$body       = $item['body'] ?? '';

					if ( '' === $item_title && '' === $body ) {
						continue;
					}

					++$number;
					?>
					<article class="cb-numbered-grid__card" data-reveal data-reveal-from="zoom">
						<div class="cb-numbered-grid__head">
							<?php
							/*
							 * aria-hidden: the numeral is a visual ordering
							 * device, and screen readers already announce
							 * these as a list of articles. Reading "zero one"
							 * before every title is noise, not information.
							 */
							?>
							<span class="cb-numbered-grid__number" aria-hidden="true"><?= esc_html( str_pad( $number, 2, '0', STR_PAD_LEFT ) ); ?></span>
							<?php if ( $item_title ) { ?>
								<h3 class="cb-numbered-grid__title"><?= esc_html( $item_title ); ?></h3>
							<?php } ?>
						</div>
						<?php if ( $body ) { ?>
							<p class="cb-numbered-grid__body"><?= nl2br( esc_html( $body ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></p>
						<?php } ?>
					</article>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</section>
