<?php
/**
 * Role meta field for people, following the same plain meta-box pattern as
 * service-cards.php.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the person role meta.
 *
 * @return void
 */
function cb_chillibyte_2026_register_person_meta() {
	register_post_meta(
		'person',
		'role',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'cb_chillibyte_2026_register_person_meta' );

/**
 * Add the Person Details meta box.
 *
 * @return void
 */
function cb_chillibyte_2026_add_person_meta_box( $post_type ) {
	if ( 'person' !== $post_type ) {
		return;
	}

	add_meta_box(
		'cb-person-details',
		__( 'Person Details', 'cb-chillibyte-2026' ),
		'cb_chillibyte_2026_render_person_meta_box',
		'person',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'cb_chillibyte_2026_add_person_meta_box' );

/**
 * Render the Person Details meta box.
 *
 * @param WP_Post $post Current person post.
 * @return void
 */
function cb_chillibyte_2026_render_person_meta_box( $post ) {
	wp_nonce_field( 'cb_chillibyte_2026_person_meta', 'cb_chillibyte_2026_person_meta_nonce' );

	$role = get_post_meta( $post->ID, 'role', true );
	?>
	<p>
		<label for="cb-person-role"><strong><?php esc_html_e( 'Role', 'cb-chillibyte-2026' ); ?></strong></label><br>
		<input type="text" id="cb-person-role" name="cb_person_role" value="<?php echo esc_attr( $role ); ?>" class="widefat" />
	</p>
	<?php
}

/**
 * Save the Person Details meta box fields.
 *
 * @param int $post_id Person post being saved.
 * @return void
 */
function cb_chillibyte_2026_save_person_meta( $post_id ) {
	if ( ! isset( $_POST['cb_chillibyte_2026_person_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['cb_chillibyte_2026_person_meta_nonce'] ), 'cb_chillibyte_2026_person_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['cb_person_role'] ) ) {
		update_post_meta( $post_id, 'role', sanitize_text_field( wp_unslash( $_POST['cb_person_role'] ) ) );
	}
}
add_action( 'save_post_person', 'cb_chillibyte_2026_save_person_meta' );
