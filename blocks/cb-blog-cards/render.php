<?php
/**
 * Block template for CB Blog Cards.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$show_filters       = ! empty( $attributes['showFilters'] );
$post_count         = $attributes['postCount'] ?? 3;
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-blog-cards' ) );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container py-5">
		<?php
		if ( $btitle ) {
			?>
		<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
			<?php
		}

		$q = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => $post_count,
			)
		);

		if ( $q->have_posts() ) {
			$cards   = array();
			$filters = array();

			while ( $q->have_posts() ) {
				$q->the_post();

				$current_post_id = get_the_ID();
				$thumbnail_id    = get_post_thumbnail_id( $current_post_id );
				$excerpt         = wp_trim_words( get_the_excerpt(), 22, '...' );
				$categories      = get_the_category();
				$category_name   = ! empty( $categories ) ? $categories[0]->name : '';
				$category_data   = array();
				$reading_time    = max( 1, (int) estimate_reading_time_in_minutes( get_post_field( 'post_content', $current_post_id ), 300, true, false ) );

				foreach ( $categories as $category ) {
					if ( empty( $category->slug ) || empty( $category->name ) ) {
						continue;
					}

					$category_data[] = $category->slug;

					if ( ! isset( $filters[ $category->slug ] ) ) {
						$filters[ $category->slug ] = $category->name;
					}
				}

				$cards[] = array(
					'permalink'      => get_permalink(),
					'title'          => get_the_title(),
					'thumbnail_id'   => $thumbnail_id,
					'excerpt'        => $excerpt,
					'date'           => get_the_date( 'M j, Y' ),
					'reading_time'   => $reading_time,
					'category_name'  => $category_name,
					'category_slugs' => implode( ' ', $category_data ),
				);
			}
			wp_reset_postdata();

			if ( $show_filters && ! empty( $filters ) ) {
				?>
			<div class="cb-blog-cards__filters" data-blog-filter-group>
				<button class="cb-blog-cards__filter is-active" type="button" data-blog-filter="all" aria-pressed="true">All</button>
				<?php foreach ( $filters as $filter_slug => $filter_name ) { ?>
					<button class="cb-blog-cards__filter" type="button" data-blog-filter="<?= esc_attr( $filter_slug ); ?>" aria-pressed="false"><?= esc_html( $filter_name ); ?></button>
				<?php } ?>
			</div>
				<?php
			}
			?>
		<div class="row">
			<?php
			foreach ( $cards as $card ) {
				?>
			<div class="col-12 col-md-6 col-lg-4 mb-4 cb-blog-cards__item" data-blog-card data-categories="<?= esc_attr( $card['category_slugs'] ); ?>">
				<a class="cb-blog-cards__card" href="<?= esc_url( $card['permalink'] ); ?>">
					<div class="cb-blog-cards__media">
						<?php if ( $card['thumbnail_id'] ) { ?>
							<?= wp_get_attachment_image( $card['thumbnail_id'], 'large', false, array( 'class' => 'cb-blog-cards__card-image', 'alt' => $card['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() already escapes its output. ?>
						<?php } else { ?>
							<div class="cb-blog-cards__card-image cb-blog-cards__card-image--placeholder" aria-hidden="true"></div>
						<?php } ?>
					</div>
					<div class="cb-blog-cards__content">
						<div class="cb-blog-cards__meta">
							<?php if ( $card['category_name'] ) { ?>
								<span class="cb-blog-cards__pill"><?= esc_html( $card['category_name'] ); ?></span>
							<?php } ?>
							<div class="cb-blog-cards__meta-items">
								<p class="cb-blog-cards__eyebrow cb-blog-cards__eyebrow--date"><?= esc_html( $card['date'] ); ?></p>
								<p class="cb-blog-cards__eyebrow cb-blog-cards__eyebrow--time"><?= esc_html( $card['reading_time'] ); ?> min</p>
							</div>
						</div>
						<h3 class="cb-blog-cards__title"><?= esc_html( $card['title'] ); ?></h3>
						<?php if ( $card['excerpt'] ) { ?>
							<p class="cb-blog-cards__excerpt"><?= esc_html( $card['excerpt'] ); ?></p>
						<?php } ?>
						<span class="cb-blog-cards__footer">Read More</span>
					</div>
				</a>
			</div>
				<?php
			}
			?>
		</div>
			<?php
		} else {
			?>
		<p><?php echo esc_html( $post_count ); ?></p>
			<?php
		}
		?>
	</div>
</section>
