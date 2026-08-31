<?php
/**
 * Single post template.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();

	$post_id         = get_the_ID();
	$thumbnail_id    = get_post_thumbnail_id( $post_id );
	$thumbnail_html  = $thumbnail_id ? wp_get_attachment_image( $thumbnail_id, 'large', false, array( 'class' => 'single-post__hero-image', 'sizes' => '(min-width: 992px) 66vw, 100vw' ) ) : '';
	$raw_content     = get_the_content();
	$categories      = get_the_category( $post_id );
	$primary_category = ! empty( $categories ) ? $categories[0]->name : '';
	$breadcrumbs     = cb_chillibyte_2026_get_breadcrumbs( $post_id );
	$blocks          = parse_blocks( $raw_content );
	$content_parts   = array();

	foreach ( $blocks as $block ) {
		$content_parts[] = render_block( $block );
	}

	$rendered_content = implode( '', $content_parts );
	$quicklinks       = array();
	$used_anchor_ids  = array();

	$rendered_content = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/is',
		function ( $matches ) use ( &$quicklinks, &$used_anchor_ids ) {
			$attributes   = $matches[1];
			$heading_html = $matches[2];
			$heading_text = trim( wp_strip_all_tags( $heading_html ) );

			if ( '' === $heading_text ) {
				return $matches[0];
			}

			if ( preg_match( '/\sid=("|\')(.*?)\1/i', $attributes, $id_match ) ) {
				$heading_id = $id_match[2];
			} else {
				$heading_id      = sanitize_title( $heading_text );
				$base_heading_id = $heading_id;
				$suffix          = 2;

				while ( isset( $used_anchor_ids[ $heading_id ] ) ) {
					$heading_id = $base_heading_id . '-' . $suffix;
					++$suffix;
				}

				$attributes .= ' id="' . esc_attr( $heading_id ) . '"';
			}

			$used_anchor_ids[ $heading_id ] = true;
			$quicklinks[]                   = array(
				'label' => $heading_text,
				'id'    => $heading_id,
			);

			return '<h2' . $attributes . '>' . $heading_html . '</h2>';
		},
		$rendered_content
	);
	$category_ids     = wp_list_pluck( $categories, 'term_id' );
	$related_posts    = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( $post_id ),
			'ignore_sticky_posts' => true,
			'category__in'        => $category_ids,
		)
	);
	?>
	<main class="single-post">
		<section class="single-post__hero">
			<div class="container">
				<div class="row single-post__hero-row">
					<div class="col-12 col-lg-4 single-post__hero-copy">
						<h1 class="single-post__title"><?php the_title(); ?></h1>
						<div class="single-post__meta-wrap">
							<p class="single-post__meta">
								<?php if ( $primary_category ) { ?>
									<span class="single-post__pill"><?= esc_html( $primary_category ); ?></span>
								<?php } ?>
								<span class="single-post__meta-item single-post__meta-item--date"><?= esc_html( get_the_date( 'M j, Y' ) ); ?></span>
								<span class="single-post__meta-item single-post__meta-item--time"><?= esc_html( estimate_reading_time_in_minutes( $raw_content, 200, true, false ) ); ?> min read</span>
							</p>
						</div>
					</div>
					<div class="col-12 col-lg-8 single-post__hero-media">
						<?php if ( $thumbnail_html ) { ?>
							<div class="single-post__hero-image-wrap"><?= $thumbnail_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns escaped markup. ?></div>
						<?php } ?>
					</div>
				</div>
			</div>
		</section>

		<?php cb_chillibyte_2026_render_breadcrumbs( $breadcrumbs, 'single-post__breadcrumbs cb-breadcrumbs' ); ?>

		<section class="single-post__body">
			<div class="container pb-6">
				<div class="single-post__progress" aria-hidden="true"><div class="single-post__progress-bar" data-single-progress></div></div>
				<div class="row single-post__body-row">
					<div class="col-12 col-lg-4 single-post__sidebar-col">
						<?php if ( $quicklinks ) { ?>
							<aside class="single-post__sidebar" data-single-quicklinks>
								<p class="single-post__sidebar-title">Quick Links</p>
								<nav aria-label="Quick links">
									<ul class="single-post__quicklinks">
										<?php foreach ( $quicklinks as $quicklink ) { ?>
											<li><a href="#<?= esc_attr( $quicklink['id'] ); ?>" data-single-quicklink data-target-id="<?= esc_attr( $quicklink['id'] ); ?>"><?= esc_html( $quicklink['label'] ); ?></a></li>
										<?php } ?>
									</ul>
								</nav>
							</aside>
						<?php } ?>
					</div>
					<div class="col-12 col-lg-8 single-post__content-col">
						<article <?php post_class( 'single-post__content' ); ?>>
							<?= $rendered_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- blocks are rendered through WordPress. ?>
						</article>
					</div>
				</div>
			</div>
		</section>

		<?php
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		if ( $prev_post || $next_post ) {
			?>
		<nav class="single-post__nav" aria-label="Post navigation">
			<div class="container">
				<div class="single-post__nav-grid">
					<?php if ( $prev_post ) { ?>
						<a class="single-post__nav-card single-post__nav-card--prev" href="<?= esc_url( get_permalink( $prev_post ) ); ?>">
							<span class="single-post__nav-kicker">Previous</span>
							<span class="single-post__nav-title"><?= esc_html( get_the_title( $prev_post ) ); ?></span>
						</a>
					<?php } else { ?>
						<span></span>
					<?php } ?>
					<?php if ( $next_post ) { ?>
						<a class="single-post__nav-card single-post__nav-card--next" href="<?= esc_url( get_permalink( $next_post ) ); ?>">
							<span class="single-post__nav-kicker">Next</span>
							<span class="single-post__nav-title"><?= esc_html( get_the_title( $next_post ) ); ?></span>
						</a>
					<?php } ?>
				</div>
			</div>
		</nav>
		<?php } ?>

		<?php if ( $related_posts->have_posts() ) { ?>
			<section class="single-post__related cb-blog-cards has-beige-background-color">
				<div class="container">
					<h2 class="single-post__related-title">Related Posts</h2>
					<div class="row">
						<?php
						while ( $related_posts->have_posts() ) {
							$related_posts->the_post();

							$current_post_id = get_the_ID();
							$image_url       = get_the_post_thumbnail_url( $current_post_id, 'large' );
							$excerpt         = wp_trim_words( get_the_excerpt(), 22, '...' );
							$post_categories = get_the_category( $current_post_id );
							$category_name   = ! empty( $post_categories ) ? $post_categories[0]->name : '';
							$reading_time    = max( 1, (int) estimate_reading_time_in_minutes( get_post_field( 'post_content', $current_post_id ), 300, true, false ) );
							?>
							<div class="col-12 col-md-6 col-lg-4 mb-4 cb-blog-cards__item">
								<a class="cb-blog-cards__card" href="<?= esc_url( get_permalink() ); ?>">
									<div class="cb-blog-cards__media">
										<?php if ( $image_url ) { ?>
											<img class="cb-blog-cards__card-image" src="<?= esc_url( $image_url ); ?>" alt="<?= esc_attr( get_the_title() ); ?>">
										<?php } else { ?>
											<div class="cb-blog-cards__card-image cb-blog-cards__card-image--placeholder" aria-hidden="true"></div>
										<?php } ?>
									</div>
									<div class="cb-blog-cards__content">
										<div class="cb-blog-cards__meta">
											<?php if ( $category_name ) { ?>
												<span class="cb-blog-cards__pill"><?= esc_html( $category_name ); ?></span>
											<?php } ?>
											<div class="cb-blog-cards__meta-items">
												<p class="cb-blog-cards__eyebrow cb-blog-cards__eyebrow--date"><?= esc_html( get_the_date( 'M j, Y' ) ); ?></p>
												<p class="cb-blog-cards__eyebrow cb-blog-cards__eyebrow--time"><?= esc_html( $reading_time ); ?> min</p>
											</div>
										</div>
										<h3 class="cb-blog-cards__title"><?= esc_html( get_the_title() ); ?></h3>
										<?php if ( $excerpt ) { ?>
											<p class="cb-blog-cards__excerpt"><?= esc_html( $excerpt ); ?></p>
										<?php } ?>
										<span class="cb-blog-cards__footer">Read More</span>
									</div>
								</a>
							</div>
							<?php
						}
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php } ?>
	</main>
	<?php
}

get_footer();
