<?php
/**
 * Project-specific helpers for this site — the counterpart to inc/utilities.php,
 * which holds only project-agnostic functions safe to lift into any project on
 * this skeleton. Anything Chillibyte-specific belongs here instead.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Replace Gravity Forms' submit <input> with a <button> carrying the theme's
 * own button classes. Ported from the cb-hts-js-2026 sibling theme.
 *
 * Two reasons this has to be an element swap rather than just CSS:
 *
 * 1. `.btn-arrow` draws its arrow with a `::after` pseudo-element, and
 *    `<input>` is a replaced element — it cannot have generated content at
 *    all, so the arrow is unreachable without a real <button>.
 * 2. Gravity Forms' framework CSS scopes its button styling to
 *    `input[type=submit].button.gform_button`, so a <button> drops out of
 *    those rules entirely and `.btn` applies without a specificity fight.
 *
 * The onclick/data-submission-type attributes mirror what GF 2.9 emits for
 * its own button — they drive gform.submission's handling, so they're kept
 * rather than reconstructed.
 *
 * @param string $button Default button HTML (discarded).
 * @param array  $form   Form object.
 * @return string
 */
function cb_chillibyte_2026_gform_submit_button( $button, $form ) {
	return sprintf(
		'<button type="submit" id="gform_submit_button_%1$s" class="gform_button btn btn-green btn-arrow" onclick="gform.submission.handleButtonClick(this);" data-submission-type="submit">%2$s</button>',
		esc_attr( $form['id'] ),
		esc_html( $form['button']['text'] )
	);
}
add_filter( 'gform_submit_button', 'cb_chillibyte_2026_gform_submit_button', 10, 2 );
