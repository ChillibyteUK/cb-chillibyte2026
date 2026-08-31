<?php
/**
 * Block template for CB Services Nav.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

$btitle             = $attributes['title'] ?? '';
$intro              = $attributes['intro'] ?? '';
$image_id           = $attributes['imageId'] ?? 0;
$image_url          = $attributes['imageUrl'] ?? '';
$image_alt          = $attributes['imageAlt'] ?? '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'container' ) );

// Children of /service/, minus whichever one is currently being viewed —
// dropping the current page from its own sibling list is what makes this
// block reusable as inter-service navigation, not just a top-level menu.
$service_page = get_page_by_path( 'service' );
$current_id   = get_the_ID();
$services     = array();

if ( $service_page ) {
	$services = get_pages(
		array(
			'parent'      => $service_page->ID,
			'sort_column' => 'menu_order',
			'post_status' => 'publish',
			'exclude'     => $current_id ? array( $current_id ) : array(),
		)
	);
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<?php
	if ( $btitle ) {
		?>
	<h2 class="cb-scribble-text mb-5" data-reveal><?= nl2br( wp_kses_post( $btitle ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nl2br() output of an already-escaped string. ?></h2>
		<?php
	}
	if ( $intro ) {
		?>
		<div class="cb-services-nav__intro" data-reveal><?php echo wp_kses_post( $intro ); ?></div>
		<?php
	}
	if ( $services ) {
		?>
	<ul class="cb-services-nav__grid" data-reveal-container data-reveal-stagger="0.08">
		<?php if ( $image_url ) { ?>
		<?php /* On the <li>, not the .cb-services-nav__image-card inside it: the item is already what the grid's stagger sequence reveals, so putting a second data-reveal on the card would animate it twice over. */ ?>
		<li class="cb-services-nav__item cb-services-nav__item--image" data-reveal data-reveal-from="left">
			<div class="cb-services-nav__image-card">
				<div class="cb-services-nav__image-panel">
					<?php
					if ( $image_id ) {
						echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'cb-services-nav__image', 'alt' => $image_alt ) );
					} else {
						?>
						<img class="cb-services-nav__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
						<?php
					}
					?>
				</div>
			</div>
		</li>
		<?php } ?>
		<?php
		foreach ( $services as $service ) {
			$card_title       = get_post_meta( $service->ID, 'card_title', true );
			$card_title       = $card_title ? $card_title : $service->post_title;
			$card_description = get_post_meta( $service->ID, 'card_description', true );
			?>
		<li class="cb-services-nav__item" data-reveal>
			<a class="cb-services-nav__card" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
				<h3 class="cb-services-nav__title"><?php echo esc_html( $card_title ); ?></h3>
				<?php if ( $card_description ) { ?>
				<p class="cb-services-nav__description"><?php echo esc_html( $card_description ); ?></p>
				<?php } ?>
				<span class="cb-services-nav__footer"><?php echo esc_html__( 'Explore service', 'cb-chillibyte-2026' ); ?></span>
			</a>
		</li>
			<?php
		}
		?>
	</ul>
		<?php
	}
	?>
</section>
