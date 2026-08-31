<?php
/**
 * Block template for CB FAQs.
 *
 * Migrated from cb-hts-js-2026's cb-faqs block. Two deliberate departures
 * from the source: the accordion itself uses this theme's native
 * <details>/<summary> accordion.css (no JS toggle needed, unlike the
 * source's Bootstrap-style button/collapse markup + accordion.js), and the
 * FAQPage schema is queued via this theme's own queue_faq_schema() /
 * output_faq_schema() (inc/utilities.php) instead of the source's
 * duplicate bespoke collector. The source's watermark + scroll-reveal
 * effect wasn't ported — not wanted for this project.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle = $attributes['title'] ?? '';
$intro  = $attributes['intro'] ?? '';
$faqs   = $attributes['faqs'] ?? array();

$faqs = array_filter(
	$faqs,
	function ( $faq ) {
		return ! empty( $faq['question'] ) || ! empty( $faq['answer'] );
	}
);

if ( ! $faqs ) {
	return;
}

$br_allowed = array( 'br' => array() );

$schema_items = array();
foreach ( $faqs as $faq ) {
	$schema_items[] = array(
		'question' => $faq['question'] ?? '',
		'answer'   => $faq['answer'] ?? '',
	);
}
queue_faq_schema( $schema_items );

$accordion_name     = wp_unique_id( 'cb-faqs-' );
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-faqs' ) );
?>
<section <?= $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-6">
		<?php
		if ( $btitle || $intro ) {
			?>
		<div  class="faq-header mb-4">
			<?php
			if ( $btitle ) {
				?>
			<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
				<?php
			}
			if ( $intro ) {
				?>
			<div class="faq-sub"><?= wp_kses_post( $intro ); ?></div>
				<?php
			}
			?>
		</div>
			<?php
		}
		?>
		<div class="accordion">
			<?php
			foreach ( $faqs as $faq ) {
				$question = $faq['question'] ?? '';
				$answer   = $faq['answer'] ?? '';
				?>
			<details class="accordion-item" name="<?= esc_attr( $accordion_name ); ?>">
				<summary class="accordion-header">
					<span><?= wp_kses( $question, $br_allowed ); ?></span>
					<svg class="accordion-icon" viewBox="0 0 16 16" aria-hidden="true">
						<path d="M8 0v16M0 8h16" stroke="currentColor" stroke-width="2" />
					</svg>
				</summary>
				<div class="accordion-body"><?= nl2br( wp_kses( $answer, $br_allowed ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses()'d, then nl2br() adds only <br>. ?></div>
			</details>
				<?php
			}
			?>
		</div>
	</div>
</section>
