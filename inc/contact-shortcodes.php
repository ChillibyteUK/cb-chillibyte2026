<?php
/**
 * Contact shortcodes reading from Site-Wide Settings.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the contact phone link.
 *
 * @param bool $with_icon Whether to prepend a phone icon.
 * @return string
 */
function cb_chillibyte_2026_get_contact_phone_link( $with_icon = false ) {
	$phone = cb_chillibyte_2026_get_setting( 'phone' );

	if ( ! $phone ) {
		return '';
	}

	$icon = '';

	if ( $with_icon ) {
		$icon = '<svg class="contact-link__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.86 19.86 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.86 19.86 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.77.63 2.61a2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6.09 6.09l1.47-1.29a2 2 0 0 1 2.11-.45c.84.3 1.71.51 2.61.63A2 2 0 0 1 22 16.92Z" fill="currentColor"/></svg>';
	}

	return '<a href="' . esc_url( 'tel:' . parse_phone( $phone ) ) . '">' . $icon . '<span>' . esc_html( $phone ) . '</span></a>';
}

/**
 * Render the contact email link.
 *
 * @param bool $with_icon Whether to prepend an email icon.
 * @return string
 */
function cb_chillibyte_2026_get_contact_email_link( $with_icon = false ) {
	$email = cb_chillibyte_2026_get_setting( 'email' );

	if ( ! $email ) {
		return '';
	}

	$bot_safe_email = antispambot( $email );
	$icon           = '';

	if ( $with_icon ) {
		$icon = '<svg class="contact-link__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 2v.01L12 13l8-6.99V6H4Zm16 12V8.52l-7.4 6.47a1 1 0 0 1-1.2 0L4 8.52V18h16Z" fill="currentColor"/></svg>';
	}

	return '<a href="' . esc_url( 'mailto:' . $bot_safe_email ) . '">' . $icon . '<span>' . esc_html( $bot_safe_email ) . '</span></a>';
}

add_shortcode(
	'contact_phone',
	function () {
		return cb_chillibyte_2026_get_contact_phone_link();
	}
);

add_shortcode(
	'contact_phone_icon',
	function () {
		return cb_chillibyte_2026_get_contact_phone_link( true );
	}
);

add_shortcode(
	'contact_email',
	function () {
		return cb_chillibyte_2026_get_contact_email_link();
	}
);

add_shortcode(
	'contact_email_icon',
	function () {
		return cb_chillibyte_2026_get_contact_email_link( true );
	}
);
