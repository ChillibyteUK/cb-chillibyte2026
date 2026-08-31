<?php
/**
 * Usage: wp eval-file create-pages.php pages.csv
 * CSV columns: title,slug,parent_slug (parent_slug may be empty)
 */

$csv_path = $args[0] ?? 'pages.csv';

if ( ! file_exists( $csv_path ) ) {
	WP_CLI::error( "CSV not found: $csv_path" );
}

$rows = [];
if ( ( $handle = fopen( $csv_path, 'r' ) ) !== false ) {
	$header = fgetcsv( $handle );
	while ( ( $data = fgetcsv( $handle ) ) !== false ) {
		if ( count( $data ) < 2 || $data[0] === '' ) {
			continue;
		}
		$rows[ $data[1] ] = [
			'title'       => $data[0],
			'slug'        => $data[1],
			'parent_slug' => $data[2] ?? '',
		];
	}
	fclose( $handle );
}

$slug_to_id  = [];
$in_progress = [];

function resolve_page_id( $slug, &$rows, &$slug_to_id, &$in_progress ) {
	if ( isset( $slug_to_id[ $slug ] ) ) {
		return $slug_to_id[ $slug ];
	}

	// slug not in our CSV — assume it already exists on the site
	if ( ! isset( $rows[ $slug ] ) ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing ) {
			$slug_to_id[ $slug ] = $existing->ID;
			return $existing->ID;
		}
		WP_CLI::warning( "Parent slug '$slug' not found in CSV or on site; treating as top-level." );
		return 0;
	}

	if ( isset( $in_progress[ $slug ] ) ) {
		WP_CLI::error( "Circular parent reference detected at slug '$slug'." );
	}
	$in_progress[ $slug ] = true;

	$row       = $rows[ $slug ];
	$parent_id = 0;
	if ( ! empty( $row['parent_slug'] ) ) {
		$parent_id = resolve_page_id( $row['parent_slug'], $rows, $slug_to_id, $in_progress );
	}

	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing ) {
		if ( (int) $existing->post_parent !== (int) $parent_id ) {
			wp_update_post( [
				'ID'          => $existing->ID,
				'post_parent' => $parent_id,
			] );
			WP_CLI::log( "Updated parent for existing page: {$row['title']} ({$slug})" );
		} else {
			WP_CLI::log( "Already exists, skipping: {$row['title']} ({$slug})" );
		}
		$id = $existing->ID;
	} else {
		$id = wp_insert_post( [
			'post_title'  => $row['title'],
			'post_name'   => $slug,
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_parent' => $parent_id,
		], true );

		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "Failed to create {$row['title']}: " . $id->get_error_message() );
			$id = 0;
		} else {
			WP_CLI::success( "Created: {$row['title']} ({$slug})" . ( $parent_id ? " under parent ID $parent_id" : '' ) );
		}
	}

	unset( $in_progress[ $slug ] );
	$slug_to_id[ $slug ] = $id;
	return $id;
}

foreach ( array_keys( $rows ) as $slug ) {
	resolve_page_id( $slug, $rows, $slug_to_id, $in_progress );
}

WP_CLI::success( 'Done.' );