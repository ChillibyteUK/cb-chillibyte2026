<?php
/**
 * Block template for CB Team Cards.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$intro              = $attributes['intro'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-team-cards' ) );
$people             = new WP_Query(
	array(
		'post_type'      => 'person',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

if ( empty( $btitle ) && empty( $intro ) && ! $people->have_posts() ) {
	return;
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
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

		if ( $people->have_posts() ) {
			?>
			<div class="row cb-team-cards__grid" data-reveal-container data-reveal-stagger="0.08">
			<?php
			while ( $people->have_posts() ) {
				$people->the_post();

				$person_id      = get_the_ID();
				$role           = get_post_meta( $person_id, 'role', true );
				$thumbnail_url  = get_the_post_thumbnail_url( $person_id, 'large' );
				?>
				<div class="col-12 col-md-6 col-lg-4 col-xl-3 cb-team-cards__item">
					<article class="cb-team-cards__card" data-reveal data-reveal-from="zoom">
						<div class="cb-team-cards__media">
							<?php if ( $thumbnail_url ) { ?>
								<img class="cb-team-cards__image" src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
							<?php } else { ?>
								<div class="cb-team-cards__image cb-team-cards__image--placeholder" aria-hidden="true"></div>
							<?php } ?>
						</div>
						<div class="cb-team-cards__body">
							<h3 class="cb-team-cards__name"><?php the_title(); ?></h3>
							<?php if ( $role ) { ?>
								<p class="cb-team-cards__role"><?php echo esc_html( $role ); ?></p>
							<?php } ?>
						</div>
					</article>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
			</div>
			<?php
		}
		?>
	</div>
</section>
