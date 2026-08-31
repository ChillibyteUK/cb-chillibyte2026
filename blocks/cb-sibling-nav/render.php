<?php
/**
 * Block template for CB Sibling Nav.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$current_id         = get_the_ID();
$parent_id          = wp_get_post_parent_id( $current_id );
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cb-sibling-nav' ) );

if ( ! $parent_id ) {
	return;
}

$siblings = get_pages(
	array(
		'parent'      => $parent_id,
		'sort_column' => 'menu_order,post_title',
		'post_status' => 'publish',
		'exclude'     => array( $current_id ),
	)
);

if ( empty( $siblings ) ) {
	return;
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<ul class="cb-sibling-nav__grid" data-reveal-container data-reveal-stagger="0.08">
			<?php foreach ( $siblings as $sibling ) { ?>
				<?php $description = has_excerpt( $sibling ) ? get_the_excerpt( $sibling ) : ''; ?>
				<li class="cb-sibling-nav__item" data-reveal>
					<a class="cb-sibling-nav__card" href="<?php echo esc_url( get_permalink( $sibling ) ); ?>">
						<h3 class="cb-sibling-nav__title"><?php echo esc_html( get_the_title( $sibling ) ); ?></h3>
						<?php if ( $description ) { ?>
							<p class="cb-sibling-nav__description"><?php echo esc_html( $description ); ?></p>
						<?php } ?>
						<span class="cb-sibling-nav__footer"><?php echo esc_html__( 'Explore', 'cb-chillibyte-2026' ); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
