<?php
/**
 * Card title/description meta fields for pages, used by the services-nav
 * block to render linked cards for each child of /service/. Plain classic
 * meta box (renders below the content editor) — no ACF, no custom sidebar
 * panel/build step.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register card_title / card_description as page meta.
 *
 * @return void
 */
function cb_chillibyte_2026_register_service_card_meta() {
	register_post_meta(
		'page',
		'card_title',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'page',
		'card_description',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'cb_chillibyte_2026_register_service_card_meta' );

/**
 * Add the "Service Card" meta box below the content editor — only on pages
 * that are children of /service/, not every page. A brand-new, unsaved
 * child page won't show it until after its first save (post_parent isn't
 * set on the in-memory post object before then) — an accepted limitation
 * rather than something worth extra plumbing for.
 *
 * @param string  $post_type Current screen's post type.
 * @param WP_Post $post      Current post.
 * @return void
 */
function cb_chillibyte_2026_add_service_card_meta_box( $post_type, $post ) {
	if ( 'page' !== $post_type ) {
		return;
	}

	$service_page = get_page_by_path( 'service' );

	if ( ! $service_page || (int) $post->post_parent !== $service_page->ID ) {
		return;
	}

	add_meta_box(
		'cb-service-card',
		__( 'Service Card', 'cb-chillibyte-2026' ),
		'cb_chillibyte_2026_render_service_card_meta_box',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'cb_chillibyte_2026_add_service_card_meta_box', 10, 2 );

/**
 * Render the Service Card meta box fields.
 *
 * @param WP_Post $post Current page.
 * @return void
 */
function cb_chillibyte_2026_render_service_card_meta_box( $post ) {
	wp_nonce_field( 'cb_chillibyte_2026_service_card', 'cb_chillibyte_2026_service_card_nonce' );

	$card_title       = get_post_meta( $post->ID, 'card_title', true );
	$card_description = get_post_meta( $post->ID, 'card_description', true );
	?>
	<p>
		<label for="cb-service-card-title"><strong><?php esc_html_e( 'Card Title', 'cb-chillibyte-2026' ); ?></strong></label><br>
		<input type="text" id="cb-service-card-title" name="cb_service_card_title" value="<?php echo esc_attr( $card_title ); ?>" class="widefat" />
		<span class="description"><?php esc_html_e( 'Falls back to the page title if left blank.', 'cb-chillibyte-2026' ); ?></span>
	</p>
	<p>
		<label for="cb-service-card-description"><strong><?php esc_html_e( 'Card Description', 'cb-chillibyte-2026' ); ?></strong></label><br>
		<textarea id="cb-service-card-description" name="cb_service_card_description" class="widefat" rows="3"><?php echo esc_textarea( $card_description ); ?></textarea>
	</p>
	<?php
}

/**
 * Save the Service Card meta box fields.
 *
 * @param int $post_id Page being saved.
 * @return void
 */
function cb_chillibyte_2026_save_service_card_meta( $post_id ) {
	if ( ! isset( $_POST['cb_chillibyte_2026_service_card_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['cb_chillibyte_2026_service_card_nonce'] ), 'cb_chillibyte_2026_service_card' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['cb_service_card_title'] ) ) {
		update_post_meta( $post_id, 'card_title', sanitize_text_field( wp_unslash( $_POST['cb_service_card_title'] ) ) );
	}

	if ( isset( $_POST['cb_service_card_description'] ) ) {
		update_post_meta( $post_id, 'card_description', sanitize_textarea_field( wp_unslash( $_POST['cb_service_card_description'] ) ) );
	}
}
add_action( 'save_post_page', 'cb_chillibyte_2026_save_service_card_meta' );
