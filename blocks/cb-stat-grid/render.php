<?php
/**
 * Block template for CB Stat Grid.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$intro              = $attributes['intro'] ?? '';
$stats              = $attributes['stats'] ?? array();
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-stat-grid' ) );

if ( empty( $btitle ) && empty( $stats ) ) {
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

		if ( $stats ) {
			?>
			<div class="row cb-stat-grid__grid">
				<?php
				foreach ( $stats as $item ) {
					$stat        = isset( $item['stat'] ) ? (int) $item['stat'] : 0;
					$stat_suffix = $item['statSuffix'] ?? '';
					$description = $item['description'] ?? '';

					if ( 0 === $stat && '' === $stat_suffix && '' === $description ) {
						continue;
					}
					?>
					<div class="col-12 col-md-6 col-lg-4 cb-stat-grid__item" data-reveal data-reveal-from="zoom">
					<article class="cb-stat-grid__card">
						<div class="cb-stat-grid__stat-wrap">
							<span class="cb-stat-grid__stat" aria-label="<?= esc_attr( $stat ); ?>">
								<span class="cb-stat-grid__stat-value" data-stat-target="<?= esc_attr( $stat ); ?>">0</span>
								<?php if ( '' !== $stat_suffix ) { ?>
									<span class="cb-stat-grid__stat-suffix"><?= esc_html( $stat_suffix ); ?></span>
								<?php } ?>
							</span>
						</div>
						<?php if ( $description ) { ?>
							<p class="cb-stat-grid__description"><?= esc_html( $description ); ?></p>
						<?php } ?>
					</article>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</section>
