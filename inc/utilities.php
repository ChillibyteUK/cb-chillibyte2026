<?php
/**
 * Reusable, project-agnostic utility functions — safe to lift verbatim into
 * other projects built on this skeleton. Project-specific helpers (coupled
 * to a project's own field schema or content structure) belong in
 * inc/helpers.php instead — don't create that file until something actually
 * needs it.
 *
 * @package cb-chillibyte-2026
 */

defined( 'ABSPATH' ) || exit;

/**
 * Strip formatting from a UK phone number for use in tel: links.
 *
 * @param string $phone Phone number as entered (spaces, brackets, dashes allowed).
 * @return string
 */
function parse_phone( $phone ) {
	$phone = preg_replace( '/\s+/', '', $phone );
	$phone = preg_replace( '/\(0\)/', '', $phone );
	$phone = preg_replace( '/[\(\)\.]/', '', $phone );
	$phone = preg_replace( '/-/', '', $phone );
	$phone = preg_replace( '/^0/', '+44', $phone );
	return $phone;
}

/**
 * Pluralise a word based on quantity.
 *
 * @param int         $quantity Quantity to check.
 * @param string      $singular Singular form.
 * @param string|null $plural   Explicit plural form, if the default suffix rules don't apply.
 * @return string
 */
function pluralise( $quantity, $singular, $plural = null ) {
	if ( 1 === $quantity || ! strlen( $singular ) ) {
		return $singular;
	}
	if ( null !== $plural ) {
		return $plural;
	}

	$last_letter = strtolower( $singular[ strlen( $singular ) - 1 ] );
	switch ( $last_letter ) {
		case 'y':
			return substr( $singular, 0, -1 ) . 'ies';
		case 's':
			return $singular . 'es';
		default:
			return $singular . 's';
	}
}

/**
 * List available icons from img/icons/ as slug => label pairs.
 *
 * Drop an .svg file into img/icons/ and it appears automatically — no
 * registration step. Useful as the options list for a generated block's
 * "select" field (see add_block.sh) when a block needs an icon picker.
 *
 * @return array Slug => human-readable label pairs.
 */
function get_icon_choices() {
	$choices = array();
	$files   = glob( get_template_directory() . '/img/icons/*.svg' );

	if ( ! $files ) {
		return $choices;
	}

	foreach ( $files as $file ) {
		$slug             = basename( $file, '.svg' );
		$choices[ $slug ] = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
	}

	return $choices;
}

/**
 * Inline an SVG icon from img/icons/ by slug.
 *
 * @param string $name Icon slug — matches a filename in img/icons/ without the extension.
 * @return string SVG markup, or an empty string if the icon doesn't exist.
 */
function get_icon( $name ) {
	if ( ! $name ) {
		return '';
	}

	$path = get_template_directory() . '/img/icons/' . basename( $name ) . '.svg';

	if ( ! file_exists( $path ) ) {
		return '';
	}

	return file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}

/**
 * Inline a hover-triggered animated SVG from animation/hover/{slug}/ by
 * slug, plus its accompanying CSS (inlined in a <style> tag right next to
 * the SVG — these are small, per-instance, hover-only rulesets, so scoping
 * cost/collision risk isn't worth a global enqueue). Add a new animation by
 * dropping {slug}.svg + {slug}.css into a new animation/hover/{slug}/
 * folder — no registration step beyond adding a matching option to
 * whichever block's "Animation" SelectControl offers it (see
 * cb-title-anim-two-cols/src/edit.js for the pattern).
 *
 * @param string $slug Animation slug — matches a folder name in animation/hover/.
 * @return string SVG + <style> markup, or an empty string if the slug doesn't exist.
 */
function get_title_animation( $slug ) {
	if ( ! $slug ) {
		return '';
	}

	$slug = basename( $slug );
	$dir  = get_template_directory() . "/animation/hover/{$slug}";
	$svg  = "{$dir}/{$slug}.svg";
	$css  = "{$dir}/{$slug}.css";

	if ( ! file_exists( $svg ) ) {
		return '';
	}

	$markup = file_get_contents( $svg ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	if ( file_exists( $css ) ) {
		$markup .= '<style>' . file_get_contents( $css ) . '</style>'; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	return $markup;
}

/**
 * Read the generated hero grid-reveal animation config.
 *
 * The list is baked at build time by src/build/generate-hero-animations.js
 * (npm run generate-hero-animations), which reads whatever
 * animation/json/grid-*.json bitmaps exist and writes both this PHP file and
 * the editor's matching JS module. Deriving both ends from one build step is
 * what stops the dropdown and the bitmaps on disk drifting apart — that
 * mismatch is what silently 404'd the reveal fetch and left the hero blank.
 *
 * Deliberately not a runtime glob(): animation/json/ only ever changes on a
 * dev machine, so scanning the directory on every editor load and every hero
 * render would be paying at runtime for something already known at build
 * time.
 *
 * @return array{available: string[], default: string}
 */
function cb_chillibyte_2026_get_hero_animation_config() {
	static $config = null;

	if ( null === $config ) {
		$file   = get_template_directory() . '/inc/hero-animations.php';
		$config = file_exists( $file ) ? require $file : array();

		$config = array(
			'available' => isset( $config['available'] ) && is_array( $config['available'] ) ? $config['available'] : array(),
			'default'   => isset( $config['default'] ) ? $config['default'] : '',
		);
	}

	return $config;
}

/**
 * Resolve a hero's saved animation slug to one that actually has a bitmap.
 *
 * Falls back to the generated default when the attribute is blank or names a
 * file that no longer exists. Returns '' only when even the default is
 * missing, which the caller should treat as "render no grid at all" rather
 * than emitting a URL guaranteed to 404.
 *
 * @param string $slug Saved animation attribute.
 * @return string Resolved slug, or '' if nothing usable exists.
 */
function cb_chillibyte_2026_resolve_hero_animation( $slug ) {
	$config = cb_chillibyte_2026_get_hero_animation_config();
	$slug   = strtolower( trim( (string) $slug ) );

	if ( in_array( $slug, $config['available'], true ) ) {
		return $slug;
	}

	return in_array( $config['default'], $config['available'], true ) ? $config['default'] : '';
}

/**
 * Queue Q&A pairs for the aggregated FAQPage JSON-LD schema, output once in
 * the footer by output_faq_schema(). Safe to call from multiple
 * FAQ-style blocks on the same page — everything queued is combined into a
 * single FAQPage block rather than one per block instance, matching
 * Google's own guidance of one FAQPage schema per page.
 *
 * @param array $items Array of ['question' => string, 'answer' => string] pairs.
 * @return void
 */
function queue_faq_schema( array $items ) {
	global $faq_schema_items;

	if ( ! isset( $faq_schema_items ) ) {
		$faq_schema_items = array();
	}

	foreach ( $items as $item ) {
		if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
			continue;
		}

		$faq_schema_items[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $item['answer'] ),
			),
		);
	}
}

/**
 * Output the aggregated FAQPage JSON-LD schema, if anything was queued.
 *
 * @return void
 */
function output_faq_schema() {
	global $faq_schema_items;

	if ( empty( $faq_schema_items ) ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $faq_schema_items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'output_faq_schema' );

/**
 * Estimate reading time for a piece of content.
 *
 * @param string $content          Content to estimate.
 * @param int    $words_per_minute Reading speed assumption.
 * @param bool   $with_gutenberg   Parse content as Gutenberg blocks before stripping tags.
 * @param bool   $formatted        Return a formatted sentence instead of a bare number.
 * @return int|string
 */
function estimate_reading_time_in_minutes( $content = '', $words_per_minute = 300, $with_gutenberg = false, $formatted = false ) {
	if ( $with_gutenberg ) {
		$blocks       = parse_blocks( $content );
		$content_html = '';

		foreach ( $blocks as $block ) {
			$content_html .= render_block( $block );
		}

		$content = $content_html;
	}

	$content = wp_strip_all_tags( $content );

	if ( ! $content ) {
		return 0;
	}

	$words_count = str_word_count( $content );
	$minutes     = ceil( $words_count / $words_per_minute );

	if ( $formatted ) {
		$minutes = '<p class="reading">Estimated reading time ' . $minutes . ' ' . pluralise( $minutes, 'minute' ) . '</p>';
	}

	return $minutes;
}

/**
 * Build breadcrumb items for the current singular view.
 *
 * @param int $post_id Current post ID.
 * @return array<int, array{label: string, url: string}>
 */
function cb_chillibyte_2026_get_breadcrumbs( $post_id = 0 ) {
	$post_id     = $post_id ? (int) $post_id : get_the_ID();
	$breadcrumbs = array(
		array(
			'label' => __( 'Home', 'cb-chillibyte-2026' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( ! $post_id ) {
		return $breadcrumbs;
	}

	if ( 'post' === get_post_type( $post_id ) ) {
		$blog_page_id = (int) get_option( 'page_for_posts' );

		if ( ! $blog_page_id ) {
			$blog_page = get_page_by_path( 'blog' );
			$blog_page_id = $blog_page ? (int) $blog_page->ID : 0;
		}

		if ( $blog_page_id ) {
			$breadcrumbs[] = array(
				'label' => get_the_title( $blog_page_id ),
				'url'   => get_permalink( $blog_page_id ),
			);
		}
	} elseif ( is_page( $post_id ) || 'page' === get_post_type( $post_id ) ) {
		$ancestor_ids = array_reverse( get_post_ancestors( $post_id ) );

		foreach ( $ancestor_ids as $ancestor_id ) {
			$breadcrumbs[] = array(
				'label' => get_the_title( $ancestor_id ),
				'url'   => get_permalink( $ancestor_id ),
			);
		}
	}

	$breadcrumbs[] = array(
		'label' => get_the_title( $post_id ),
		'url'   => '',
	);

	return $breadcrumbs;
}

/**
 * Render breadcrumb markup with schema metadata.
 *
 * @param array  $breadcrumbs Breadcrumb items.
 * @param string $class_name  Wrapper class name.
 * @return void
 */
function cb_chillibyte_2026_render_breadcrumbs( $breadcrumbs, $class_name = 'cb-breadcrumbs' ) {
	if ( empty( $breadcrumbs ) || ! is_array( $breadcrumbs ) || is_front_page() ) {
		return;
	}
	?>
	<nav class="<?php echo esc_attr( $class_name ); ?>" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
		<div class="container">
			<ol class="cb-breadcrumbs__list">
				<?php foreach ( $breadcrumbs as $index => $breadcrumb ) { ?>
					<li class="cb-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
						<?php if ( ! empty( $breadcrumb['url'] ) ) { ?>
							<a href="<?php echo esc_url( $breadcrumb['url'] ); ?>" itemprop="item"><span itemprop="name"><?php echo esc_html( $breadcrumb['label'] ); ?></span></a>
						<?php } else { ?>
							<span itemprop="name" aria-current="page"><?php echo esc_html( $breadcrumb['label'] ); ?></span>
						<?php } ?>
						<meta itemprop="position" content="<?php echo esc_attr( $index + 1 ); ?>">
					</li>
				<?php } ?>
			</ol>
		</div>
	</nav>
	<?php
}
