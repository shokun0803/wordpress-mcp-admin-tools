<?php

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 新規投稿を作成します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_create_post( array $input = array() ) {
	$title         = isset( $input['title'] ) ? sanitize_text_field( wp_unslash( (string) $input['title'] ) ) : '';
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'title', 'status', 'type' ) );

	if ( '' === $title ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_title',
			__( 'A title is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-post', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post_type = isset( $input['type'] ) ? sanitize_key( (string) $input['type'] ) : 'post';
	$status    = isset( $input['status'] ) ? sanitize_key( (string) $input['status'] ) : 'draft';

	if ( ! post_type_exists( $post_type ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_type',
			__( 'The specified post type does not exist.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-post', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => isset( $input['content'] ) ? wp_kses_post( wp_unslash( (string) $input['content'] ) ) : '',
			'post_excerpt' => isset( $input['excerpt'] ) ? sanitize_textarea_field( wp_unslash( (string) $input['excerpt'] ) ) : '',
			'post_status'  => $status,
			'post_type'    => $post_type,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-post', false, 'post', 0, $input_summary, $post_id->get_error_code(), $post_id->get_error_message() );

		return $post_id;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-post', true, 'post', (int) $post_id, $input_summary );

	return array(
		'post_id'   => (int) $post_id,
		'status'    => (string) get_post_status( $post_id ),
		'edit_link' => (string) get_edit_post_link( $post_id, 'raw' ),
	);
}

/**
 * 新規固定ページを作成します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_create_page( array $input = array() ) {
	$title         = isset( $input['title'] ) ? sanitize_text_field( wp_unslash( (string) $input['title'] ) ) : '';
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'title', 'status' ) );

	if ( '' === $title ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_title',
			__( 'A title is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-page', false, 'page', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$status = isset( $input['status'] ) ? sanitize_key( (string) $input['status'] ) : 'draft';

	$page_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => isset( $input['content'] ) ? wp_kses_post( wp_unslash( (string) $input['content'] ) ) : '',
			'post_excerpt' => isset( $input['excerpt'] ) ? sanitize_textarea_field( wp_unslash( (string) $input['excerpt'] ) ) : '',
			'post_status'  => $status,
			'post_type'    => 'page',
		),
		true
	);

	if ( is_wp_error( $page_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-page', false, 'page', 0, $input_summary, $page_id->get_error_code(), $page_id->get_error_message() );

		return $page_id;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-page', true, 'page', (int) $page_id, $input_summary );

	return array(
		'page_id'   => (int) $page_id,
		'status'    => (string) get_post_status( $page_id ),
		'edit_link' => (string) get_edit_post_link( $page_id, 'raw' ),
	);
}

/**
 * 既存投稿を更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_post( array $input = array() ) {
	$post_id       = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'post_id', 'title', 'status' ) );

	if ( $post_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_id',
			__( 'A valid post_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found',
			__( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post_data = array(
		'ID' => $post_id,
	);

	if ( array_key_exists( 'title', $input ) ) {
		$post_data['post_title'] = sanitize_text_field( wp_unslash( (string) $input['title'] ) );
	}

	if ( array_key_exists( 'content', $input ) ) {
		$post_data['post_content'] = wp_kses_post( wp_unslash( (string) $input['content'] ) );
	}

	if ( array_key_exists( 'excerpt', $input ) ) {
		$post_data['post_excerpt'] = sanitize_textarea_field( wp_unslash( (string) $input['excerpt'] ) );
	}

	if ( array_key_exists( 'status', $input ) ) {
		$post_data['post_status'] = sanitize_key( (string) $input['status'] );
	}

	$updated_post_id = wp_update_post( $post_data, true );

	if ( is_wp_error( $updated_post_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post', false, 'post', $post_id, $input_summary, $updated_post_id->get_error_code(), $updated_post_id->get_error_message() );

		return $updated_post_id;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post', true, 'post', (int) $updated_post_id, $input_summary );

	return array(
		'post_id'   => (int) $updated_post_id,
		'status'    => (string) get_post_status( $updated_post_id ),
		'edit_link' => (string) get_edit_post_link( $updated_post_id, 'raw' ),
	);
}

/**
 * 既存投稿を削除します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_delete_post( array $input = array() ) {
	$post_id       = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'post_id', 'force' ) );

	if ( $post_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_id',
			__( 'A valid post_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-post', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found',
			__( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-post', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$previous_status = (string) $post->post_status;
	$force_delete    = ! empty( $input['force'] );
	$deleted_post    = wp_delete_post( $post_id, $force_delete );

	if ( ! $deleted_post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_delete_failed',
			__( 'Failed to delete the post.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-post', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-post', true, 'post', $post_id, $input_summary );

	return array(
		'post_id'         => $post_id,
		'deleted'         => true,
		'previous_status' => $previous_status,
	);
}

/**
 * 既存固定ページを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_page( array $input = array() ) {
	$page_id       = isset( $input['page_id'] ) ? (int) $input['page_id'] : 0;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'page_id', 'title', 'status' ) );

	if ( $page_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_page_id',
			__( 'A valid page_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-page', false, 'page', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$page = get_post( $page_id );

	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_page_not_found',
			__( 'The specified page could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-page', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$page_data = array(
		'ID'        => $page_id,
		'post_type' => 'page',
	);

	if ( array_key_exists( 'title', $input ) ) {
		$page_data['post_title'] = sanitize_text_field( wp_unslash( (string) $input['title'] ) );
	}

	if ( array_key_exists( 'content', $input ) ) {
		$page_data['post_content'] = wp_kses_post( wp_unslash( (string) $input['content'] ) );
	}

	if ( array_key_exists( 'excerpt', $input ) ) {
		$page_data['post_excerpt'] = sanitize_textarea_field( wp_unslash( (string) $input['excerpt'] ) );
	}

	if ( array_key_exists( 'status', $input ) ) {
		$page_data['post_status'] = sanitize_key( (string) $input['status'] );
	}

	$updated_page_id = wp_update_post( $page_data, true );

	if ( is_wp_error( $updated_page_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-page', false, 'page', $page_id, $input_summary, $updated_page_id->get_error_code(), $updated_page_id->get_error_message() );

		return $updated_page_id;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-page', true, 'page', (int) $updated_page_id, $input_summary );

	return array(
		'page_id'   => (int) $updated_page_id,
		'status'    => (string) get_post_status( $updated_page_id ),
		'edit_link' => (string) get_edit_post_link( $updated_page_id, 'raw' ),
	);
}

/**
 * 固定ページ本文のブロックを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_edit_page_blocks( array $input = array() ) {
	$page_id = isset( $input['page_id'] ) ? (int) $input['page_id'] : 0;
	$page    = get_post( $page_id );

	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_page_not_found',
			__( 'The specified page could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-blocks', false, 'page', $page_id, wordpress_mcp_admin_build_input_summary( array( 'page_id' => $page_id ), array( 'page_id' ) ), $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$result = wordpress_mcp_admin_update_post_blocks(
		'wordpress-mcp-admin/edit-page-blocks',
		$page_id,
		array_key_exists( 'content', $input ) ? wp_unslash( (string) $input['content'] ) : ''
	);

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return array(
		'page_id'   => isset( $result['post_id'] ) ? (int) $result['post_id'] : $page_id,
		'status'    => isset( $result['status'] ) ? (string) $result['status'] : '',
		'edit_link' => isset( $result['edit_link'] ) ? (string) $result['edit_link'] : '',
	);
}

/**
 * 投稿またはカスタム投稿タイプ本文のブロックを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_edit_post_blocks( array $input = array() ) {
	$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;

	return wordpress_mcp_admin_update_post_blocks(
		'wordpress-mcp-admin/edit-post-blocks',
		$post_id,
		array_key_exists( 'content', $input ) ? wp_unslash( (string) $input['content'] ) : ''
	);
}

/**
 * 固定ページ本文のブロックを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_page_blocks( array $input = array() ) {
	$page_id = isset( $input['page_id'] ) ? (int) $input['page_id'] : 0;
	$page    = get_post( $page_id );

	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_page_not_found',
			__( 'The specified page could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-page-blocks', false, 'page', $page_id, wordpress_mcp_admin_build_input_summary( array( 'page_id' => $page_id ), array( 'page_id' ) ), $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$result = wordpress_mcp_admin_get_post_blocks_response( 'wordpress-mcp-admin/get-page-blocks', $page_id, 'page_id' );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	unset( $result['post_type'] );

	return $result;
}

/**
 * 投稿またはカスタム投稿タイプ本文のブロックを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_post_blocks( array $input = array() ) {
	$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;

	return wordpress_mcp_admin_get_post_blocks_response( 'wordpress-mcp-admin/get-post-blocks', $post_id );
}

/**
 * アクティブな block theme の固定ページデザインを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_edit_page_design( array $input = array() ) {
	$page_id       = isset( $input['page_id'] ) ? (int) $input['page_id'] : 0;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'page_id' ) );

	if ( $page_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_page_id',
			__( 'A valid page_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$page = get_post( $page_id );

	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_page_not_found',
			__( 'The specified page could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$theme = wp_get_theme();

	if ( ! $theme->exists() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_active_theme_not_found',
			__( 'The active theme could not be loaded.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$block_theme_active = function_exists( 'wp_is_block_theme' ) ? wp_is_block_theme() : current_theme_supports( 'block-templates' );

	if ( ! $block_theme_active ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_active_theme_not_block_theme',
			__( 'The active theme must be a block theme to edit page design files.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$relative_path = wordpress_mcp_admin_get_page_design_relative_path( $page );
	$target_path   = trailingslashit( $theme->get_stylesheet_directory() ) . $relative_path;
	$target_dir    = dirname( $target_path );
	$content       = isset( $input['content'] ) ? wp_unslash( (string) $input['content'] ) : '';

	if ( '' === trim( $content ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_empty_page_design_content',
			__( 'Page design content cannot be empty.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! is_dir( $target_dir ) && ! wp_mkdir_p( $target_dir ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_page_design_directory_create_failed',
			__( 'Failed to create the target directory for the page design file.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$bytes = file_put_contents( $target_path, $content );

	if ( false === $bytes ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_page_design_write_failed',
			__( 'Failed to write the page design template file.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', false, 'page', $page_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-page-design', true, 'page', $page_id, $input_summary );

	return array(
		'page_id'       => $page_id,
		'theme'         => (string) $theme->get_stylesheet(),
		'relative_path' => $relative_path,
		'bytes_written' => (int) $bytes,
		'edit_link'     => wordpress_mcp_admin_get_theme_editor_link( (string) $theme->get_stylesheet(), $relative_path ),
	);
}

/**
 * 一般設定を更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, int|string>|WP_Error
 */
function wordpress_mcp_admin_execute_update_general_settings( array $input = array() ) {
	$updated       = false;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'blogname', 'blogdescription', 'show_on_front', 'page_on_front', 'page_for_posts', 'posts_per_page' ) );

	if ( array_key_exists( 'blogname', $input ) ) {
		update_option( 'blogname', sanitize_text_field( wp_unslash( (string) $input['blogname'] ) ) );
		$updated = true;
	}

	if ( array_key_exists( 'blogdescription', $input ) ) {
		update_option( 'blogdescription', sanitize_text_field( wp_unslash( (string) $input['blogdescription'] ) ) );
		$updated = true;
	}

	$current_show_on_front = (string) get_option( 'show_on_front', 'posts' );
	$show_on_front         = array_key_exists( 'show_on_front', $input ) ? sanitize_key( (string) $input['show_on_front'] ) : $current_show_on_front;
	$page_on_front         = array_key_exists( 'page_on_front', $input ) ? max( 0, (int) $input['page_on_front'] ) : (int) get_option( 'page_on_front', 0 );
	$page_for_posts        = array_key_exists( 'page_for_posts', $input ) ? max( 0, (int) $input['page_for_posts'] ) : (int) get_option( 'page_for_posts', 0 );

	if ( array_key_exists( 'show_on_front', $input ) ) {
		if ( ! in_array( $show_on_front, array( 'posts', 'page' ), true ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_show_on_front',
				__( 'show_on_front must be either posts or page.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$updated = true;
	}

	if ( array_key_exists( 'page_on_front', $input ) && $page_on_front > 0 ) {
		$page = get_post( $page_on_front );

		if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_page_on_front',
				__( 'page_on_front must reference an existing page.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$updated = true;
	}

	if ( array_key_exists( 'page_for_posts', $input ) && $page_for_posts > 0 ) {
		$page = get_post( $page_for_posts );

		if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_page_for_posts',
				__( 'page_for_posts must reference an existing page.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$updated = true;
	}

	if ( $page_on_front > 0 && $page_on_front === $page_for_posts ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_duplicate_reading_pages',
			__( 'page_on_front and page_for_posts must be different pages.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( 'page' === $show_on_front && $page_on_front <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_page_on_front',
			__( 'page_on_front is required when show_on_front is page.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( array_key_exists( 'posts_per_page', $input ) ) {
		update_option( 'posts_per_page', max( 1, (int) $input['posts_per_page'] ) );
		$updated = true;
	}

	if ( array_key_exists( 'show_on_front', $input ) ) {
		update_option( 'show_on_front', $show_on_front );
	}

	if ( array_key_exists( 'page_on_front', $input ) ) {
		update_option( 'page_on_front', $page_on_front );
	}

	if ( array_key_exists( 'page_for_posts', $input ) ) {
		update_option( 'page_for_posts', $page_for_posts );
	}

	if ( ! $updated ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_no_settings',
			__( 'No settings values were provided for update.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-general-settings', true, 'option', 0, $input_summary );

	return array(
		'blogname'        => (string) get_option( 'blogname', '' ),
		'blogdescription' => (string) get_option( 'blogdescription', '' ),
		'show_on_front'   => (string) get_option( 'show_on_front', 'posts' ),
		'page_on_front'   => (int) get_option( 'page_on_front', 0 ),
		'page_for_posts'  => (int) get_option( 'page_for_posts', 0 ),
		'posts_per_page'  => (int) get_option( 'posts_per_page', 10 ),
	);
}

/**
 * 任意のオプションを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_options( array $input = array() ) {
	$raw_names      = $input['names'] ?? null;
	$input_summary  = is_array( $raw_names ) ? 'names_count=' . count( $raw_names ) : '';
	$option_names   = array();
	$option_records = array();

	if ( ! is_array( $raw_names ) || empty( $raw_names ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_option_names',
			__( 'names must be a non-empty array of option names.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-options', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	foreach ( $raw_names as $raw_name ) {
		$option_name = wordpress_mcp_admin_normalize_option_name( $raw_name );

		if ( is_wp_error( $option_name ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-options', false, 'option', 0, $input_summary, $option_name->get_error_code(), $option_name->get_error_message() );

			return $option_name;
		}

		$option_names[] = $option_name;
	}

	$option_names  = array_values( array_unique( $option_names ) );
	$names_summary = wordpress_mcp_admin_summarize_string_list( 'names', $option_names );
	$input_summary = '' !== $names_summary ? $names_summary : $input_summary;

	foreach ( $option_names as $option_name ) {
		$option_records[] = wordpress_mcp_admin_format_option_record( $option_name, get_option( $option_name, null ) );
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-options', true, 'option', 0, $input_summary );

	return array(
		'items' => $option_records,
	);
}

/**
 * 任意のオプションを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_options( array $input = array() ) {
	$raw_options    = $input['options'] ?? null;
	$input_summary  = is_array( $raw_options ) ? 'options_count=' . count( $raw_options ) : '';
	$updated_names  = array();
	$option_records = array();

	if ( ! is_array( $raw_options ) || empty( $raw_options ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_option_values',
			__( 'options must be a non-empty array of option updates.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-options', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	foreach ( $raw_options as $option_update ) {
		if ( ! is_array( $option_update ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_option_update',
				__( 'Each option update must be an object with name and value.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-options', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$option_name = wordpress_mcp_admin_normalize_option_name( $option_update['name'] ?? '' );

		if ( is_wp_error( $option_name ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-options', false, 'option', 0, $input_summary, $option_name->get_error_code(), $option_name->get_error_message() );

			return $option_name;
		}

		if ( ! array_key_exists( 'value', $option_update ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_missing_option_value',
				__( 'Each option update must include a value.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-options', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		update_option( $option_name, wordpress_mcp_admin_normalize_serializable_value( $option_update['value'] ) );
		$updated_names[]  = $option_name;
		$option_records[] = wordpress_mcp_admin_format_option_record( $option_name, get_option( $option_name, null ) );
	}

	$names_summary = wordpress_mcp_admin_summarize_string_list( 'names', $updated_names );
	$input_summary = '' !== $names_summary ? $names_summary : $input_summary;

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-options', true, 'option', 0, $input_summary );

	return array(
		'items' => $option_records,
	);
}

/**
 * 任意の投稿タイプ項目を一覧取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_post_type_entries( array $input = array() ) {
	$post_type      = isset( $input['post_type'] ) ? sanitize_key( (string) $input['post_type'] ) : '';
	$search         = isset( $input['search'] ) ? sanitize_text_field( wp_unslash( (string) $input['search'] ) ) : '';
	$per_page       = isset( $input['per_page'] ) ? max( 1, min( 50, (int) $input['per_page'] ) ) : 20;
	$page           = isset( $input['page'] ) ? max( 1, (int) $input['page'] ) : 1;
	$include_content = ! empty( $input['include_content'] );
	$input_summary  = wordpress_mcp_admin_build_input_summary( $input, array( 'post_type', 'search', 'per_page', 'page' ) );

	if ( '' === $post_type || ! post_type_exists( $post_type ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_type',
			__( 'The specified post_type does not exist.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-post-type-entries', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$statuses = array( 'publish', 'draft', 'pending', 'private' );

	if ( isset( $input['statuses'] ) ) {
		if ( ! is_array( $input['statuses'] ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_post_statuses',
				__( 'statuses must be an array of post status strings.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-post-type-entries', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$statuses = array_values(
			array_filter(
				array_map(
					'sanitize_key',
					array_map( 'strval', $input['statuses'] )
				)
			)
		);
	}

	$query = new WP_Query(
		array(
			'post_type'      => $post_type,
			'post_status'    => ! empty( $statuses ) ? $statuses : array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'date',
			'order'          => 'DESC',
			's'              => $search,
		)
	);

	$records = array();

	foreach ( $query->posts as $post ) {
		if ( $post instanceof WP_Post ) {
			$records[] = wordpress_mcp_admin_format_post_type_entry_record( $post, $include_content );
		}
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-post-type-entries', true, 'post', 0, $input_summary );

	return array(
		'entries'       => $records,
		'post_type'     => $post_type,
		'page'          => $page,
		'per_page'      => $per_page,
		'found_posts'   => (int) $query->found_posts,
		'max_num_pages' => (int) $query->max_num_pages,
	);
}

/**
 * 任意の投稿タイプ項目を更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_post_type_entry( array $input = array() ) {
	$post_id       = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;
	$expected_type = isset( $input['post_type'] ) ? sanitize_key( (string) $input['post_type'] ) : '';
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'post_id', 'post_type', 'title', 'status', 'slug', 'menu_order' ) );

	if ( $post_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_id',
			__( 'A valid post_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post-type-entry', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found',
			__( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post-type-entry', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( '' !== $expected_type && $expected_type !== $post->post_type ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_type_mismatch',
			__( 'post_type does not match the stored post type of the target entry.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post-type-entry', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post_data = array(
		'ID'        => $post_id,
		'post_type' => $post->post_type,
	);

	if ( array_key_exists( 'title', $input ) ) {
		$post_data['post_title'] = sanitize_text_field( wp_unslash( (string) $input['title'] ) );
	}

	if ( array_key_exists( 'content', $input ) ) {
		$post_data['post_content'] = current_user_can( 'unfiltered_html' )
			? wp_unslash( (string) $input['content'] )
			: wp_kses_post( wp_unslash( (string) $input['content'] ) );
	}

	if ( array_key_exists( 'excerpt', $input ) ) {
		$post_data['post_excerpt'] = sanitize_textarea_field( wp_unslash( (string) $input['excerpt'] ) );
	}

	if ( array_key_exists( 'status', $input ) ) {
		$post_data['post_status'] = sanitize_key( (string) $input['status'] );
	}

	if ( array_key_exists( 'slug', $input ) ) {
		$post_data['post_name'] = sanitize_title( wp_unslash( (string) $input['slug'] ) );
	}

	if ( array_key_exists( 'menu_order', $input ) ) {
		$post_data['menu_order'] = (int) $input['menu_order'];
	}

	$updated_post_id = wp_update_post( $post_data, true );

	if ( is_wp_error( $updated_post_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post-type-entry', false, 'post', $post_id, $input_summary, $updated_post_id->get_error_code(), $updated_post_id->get_error_message() );

		return $updated_post_id;
	}

	$updated_post = get_post( (int) $updated_post_id );

	if ( ! $updated_post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found_after_update',
			__( 'The post was updated but could not be loaded afterwards.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post-type-entry', false, 'post', (int) $updated_post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-post-type-entry', true, 'post', (int) $updated_post_id, $input_summary );

	return wordpress_mcp_admin_format_post_type_entry_record( $updated_post, array_key_exists( 'content', $input ) );
}

/**
 * 投稿またはタームのメタデータを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_object_meta( array $input = array() ) {
	$object_type   = isset( $input['object_type'] ) ? sanitize_key( (string) $input['object_type'] ) : '';
	$object_id     = isset( $input['object_id'] ) ? (int) $input['object_id'] : 0;
	$raw_keys      = $input['keys'] ?? null;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'object_type', 'object_id' ) );
	$meta_keys     = array();

	$target = wordpress_mcp_admin_validate_object_meta_target( $object_type, $object_id );

	if ( is_wp_error( $target ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-object-meta', false, sanitize_key( $object_type ), $object_id, $input_summary, $target->get_error_code(), $target->get_error_message() );

		return $target;
	}

	if ( null !== $raw_keys ) {
		if ( ! is_array( $raw_keys ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_meta_keys',
				__( 'keys must be an array of meta key strings.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-object-meta', false, (string) $target['target_type'], (int) $target['object_id'], $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		foreach ( $raw_keys as $raw_key ) {
			$meta_key = wordpress_mcp_admin_normalize_meta_key( $raw_key );

			if ( is_wp_error( $meta_key ) ) {
				wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-object-meta', false, (string) $target['target_type'], (int) $target['object_id'], $input_summary, $meta_key->get_error_code(), $meta_key->get_error_message() );

				return $meta_key;
			}

			$meta_keys[] = $meta_key;
		}

		$meta_keys = array_values( array_unique( $meta_keys ) );
	}

	$records = array();

	if ( ! empty( $meta_keys ) ) {
		$input_summary = implode( '; ', array_filter( array( $input_summary, wordpress_mcp_admin_summarize_string_list( 'keys', $meta_keys ) ) ) );

		foreach ( $meta_keys as $meta_key ) {
			$values    = get_metadata( (string) $target['object_type'], (int) $target['object_id'], $meta_key, false );
			$records[] = wordpress_mcp_admin_format_object_meta_record( $meta_key, is_array( $values ) ? $values : array() );
		}
	} else {
		$all_meta = get_metadata( (string) $target['object_type'], (int) $target['object_id'] );

		foreach ( $all_meta as $meta_key => $values ) {
			$records[] = wordpress_mcp_admin_format_object_meta_record( (string) $meta_key, is_array( $values ) ? array_values( $values ) : array() );
		}

		usort(
			$records,
			static function ( array $left, array $right ): int {
				return strcmp( (string) $left['key'], (string) $right['key'] );
			}
		);
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-object-meta', true, (string) $target['target_type'], (int) $target['object_id'], $input_summary );

	return array(
		'object_type' => (string) $target['object_type'],
		'object_id'   => (int) $target['object_id'],
		'items'       => $records,
	);
}

/**
 * 投稿またはタームのメタデータを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_object_meta( array $input = array() ) {
	$object_type   = isset( $input['object_type'] ) ? sanitize_key( (string) $input['object_type'] ) : '';
	$object_id     = isset( $input['object_id'] ) ? (int) $input['object_id'] : 0;
	$raw_entries   = $input['entries'] ?? null;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'object_type', 'object_id' ) );
	$updated_keys  = array();
	$records       = array();

	$target = wordpress_mcp_admin_validate_object_meta_target( $object_type, $object_id );

	if ( is_wp_error( $target ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-object-meta', false, sanitize_key( $object_type ), $object_id, $input_summary, $target->get_error_code(), $target->get_error_message() );

		return $target;
	}

	if ( ! is_array( $raw_entries ) || empty( $raw_entries ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_meta_entries',
			__( 'entries must be a non-empty array of meta updates.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-object-meta', false, (string) $target['target_type'], (int) $target['object_id'], $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	foreach ( $raw_entries as $entry ) {
		if ( ! is_array( $entry ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_meta_entry',
				__( 'Each meta update must be an object with key and value.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-object-meta', false, (string) $target['target_type'], (int) $target['object_id'], $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$meta_key = wordpress_mcp_admin_normalize_meta_key( $entry['key'] ?? '' );

		if ( is_wp_error( $meta_key ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-object-meta', false, (string) $target['target_type'], (int) $target['object_id'], $input_summary, $meta_key->get_error_code(), $meta_key->get_error_message() );

			return $meta_key;
		}

		if ( ! array_key_exists( 'value', $entry ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_missing_meta_value',
				__( 'Each meta update must include a value.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-object-meta', false, (string) $target['target_type'], (int) $target['object_id'], $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		update_metadata( (string) $target['object_type'], (int) $target['object_id'], $meta_key, wordpress_mcp_admin_normalize_serializable_value( $entry['value'] ) );
		$updated_keys[] = $meta_key;
	}

	$updated_keys = array_values( array_unique( $updated_keys ) );
	$input_summary = implode( '; ', array_filter( array( $input_summary, wordpress_mcp_admin_summarize_string_list( 'keys', $updated_keys ) ) ) );

	foreach ( $updated_keys as $meta_key ) {
		$values    = get_metadata( (string) $target['object_type'], (int) $target['object_id'], $meta_key, false );
		$records[] = wordpress_mcp_admin_format_object_meta_record( $meta_key, is_array( $values ) ? $values : array() );
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-object-meta', true, (string) $target['target_type'], (int) $target['object_id'], $input_summary );

	return array(
		'object_type' => (string) $target['object_type'],
		'object_id'   => (int) $target['object_id'],
		'items'       => $records,
	);
}

/**
 * テーマをインストールします。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_install_theme( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'slug', 'activate' ) );
	$slug          = wordpress_mcp_admin_validate_theme_slug(
		$input['slug'] ?? '',
		'wordpress_mcp_admin_invalid_theme_slug',
		__( 'A valid theme slug is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-theme', false, 'theme', 0, $input_summary, $slug->get_error_code(), $slug->get_error_message() );

		return $slug;
	}

	$activate = ! empty( $input['activate'] );

	if ( ! function_exists( 'themes_api' ) ) {
		require_once ABSPATH . 'wp-admin/includes/theme-install.php';
	}

	if ( ! class_exists( 'Theme_Upgrader' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';
	}

	$theme_information = themes_api(
		'theme_information',
		array(
			'slug'   => $slug,
			'fields' => array(
				'sections' => false,
			),
		)
	);

	if ( is_wp_error( $theme_information ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-theme', false, 'theme', 0, $input_summary, $theme_information->get_error_code(), $theme_information->get_error_message() );

		return $theme_information;
	}

	$upgrader = new Theme_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( (string) $theme_information->download_link );

	if ( is_wp_error( $result ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-theme', false, 'theme', 0, $input_summary, $result->get_error_code(), $result->get_error_message() );

		return $result;
	}

	if ( false === $result ) {
		$skin_error = $upgrader->skin->get_errors();
		$error      = $skin_error instanceof WP_Error && $skin_error->has_errors()
			? $skin_error
			: new WP_Error( 'wordpress_mcp_admin_theme_install_failed', __( 'Failed to install the theme.', 'wordpress-mcp-admin-tools' ) );

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$installed_theme = $upgrader->theme_info();

	if ( ! $installed_theme instanceof WP_Theme || ! $installed_theme->exists() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_not_found_after_install',
			__( 'The theme was installed but could not be loaded afterwards.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$stylesheet = (string) $installed_theme->get_stylesheet();

	if ( $activate ) {
		switch_theme( $stylesheet );
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-theme', true, 'theme', 0, $input_summary );

	return array(
		'stylesheet' => $stylesheet,
		'name'       => (string) $installed_theme->get( 'Name' ),
		'version'    => (string) $installed_theme->get( 'Version' ),
		'activated'  => $stylesheet === get_stylesheet(),
	);
}

/**
 * 新規テーマを作成します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_create_theme( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'slug', 'name', 'type', 'template', 'activate' ) );
	$slug          = wordpress_mcp_admin_validate_theme_slug(
		$input['slug'] ?? '',
		'wordpress_mcp_admin_invalid_theme_slug',
		__( 'A valid theme slug is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $slug->get_error_code(), $slug->get_error_message() );

		return $slug;
	}

	$name = isset( $input['name'] ) ? sanitize_text_field( wp_unslash( (string) $input['name'] ) ) : '';

	if ( '' === $name ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_theme_name',
			__( 'A theme name is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$type = isset( $input['type'] ) && 'classic' === sanitize_key( (string) $input['type'] ) ? 'classic' : 'block';

	$theme_data = array(
		'slug'        => $slug,
		'name'        => $name,
		'description' => isset( $input['description'] ) ? sanitize_text_field( wp_unslash( (string) $input['description'] ) ) : '',
		'author'      => isset( $input['author'] ) ? sanitize_text_field( wp_unslash( (string) $input['author'] ) ) : '',
		'author_uri'  => isset( $input['author_uri'] ) ? esc_url_raw( wp_unslash( (string) $input['author_uri'] ) ) : '',
		'theme_uri'   => isset( $input['theme_uri'] ) ? esc_url_raw( wp_unslash( (string) $input['theme_uri'] ) ) : '',
		'version'     => isset( $input['version'] ) ? sanitize_text_field( wp_unslash( (string) $input['version'] ) ) : '1.0.0',
		'type'        => $type,
		'template'    => isset( $input['template'] ) ? sanitize_key( (string) $input['template'] ) : '',
	);

	$theme_directory = trailingslashit( get_theme_root() ) . $slug;

	if ( file_exists( $theme_directory ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_exists',
			__( 'A theme with that slug already exists.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$scaffold_files = wordpress_mcp_admin_get_theme_scaffold_files( $theme_data );

	if ( ! wp_mkdir_p( $theme_directory ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_directory_create_failed',
			__( 'Failed to create the theme directory.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	foreach ( $scaffold_files as $relative_path => $content ) {
		$target_path = trailingslashit( $theme_directory ) . $relative_path;
		$target_dir  = dirname( $target_path );

		if ( ! is_dir( $target_dir ) && ! wp_mkdir_p( $target_dir ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_theme_file_directory_create_failed',
				__( 'Failed to create a directory for the theme scaffold.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		if ( false === file_put_contents( $target_path, $content ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_theme_file_write_failed',
				__( 'Failed to write a file in the theme scaffold.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}
	}

	wp_clean_themes_cache( true );
	$created_theme = wp_get_theme( $slug );

	if ( ! $created_theme->exists() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_not_found_after_create',
			__( 'The theme was created but could not be loaded afterwards.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! empty( $input['activate'] ) ) {
		switch_theme( $slug );
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-theme', true, 'theme', 0, $input_summary );

	return array(
		'stylesheet' => (string) $created_theme->get_stylesheet(),
		'name'       => (string) $created_theme->get( 'Name' ),
		'type'       => $type,
		'activated'  => $slug === get_stylesheet(),
	);
}

/**
 * テーマファイルを編集します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_edit_theme( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'theme', 'relative_path', 'create_missing' ) );
	$theme_slug    = wordpress_mcp_admin_validate_theme_slug(
		$input['theme'] ?? '',
		'wordpress_mcp_admin_invalid_theme',
		__( 'A valid theme stylesheet is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $theme_slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', false, 'theme', 0, $input_summary, $theme_slug->get_error_code(), $theme_slug->get_error_message() );

		return $theme_slug;
	}

	$relative_path = wordpress_mcp_admin_normalize_theme_relative_path( isset( $input['relative_path'] ) ? (string) $input['relative_path'] : '' );

	if ( is_wp_error( $relative_path ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', false, 'theme', 0, $input_summary, $relative_path->get_error_code(), $relative_path->get_error_message() );

		return $relative_path;
	}

	$theme = wp_get_theme( $theme_slug );

	if ( ! $theme->exists() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_not_found',
			__( 'The specified theme could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$theme_directory = $theme->get_stylesheet_directory();
	$target_path     = trailingslashit( $theme_directory ) . $relative_path;
	$target_dir      = dirname( $target_path );

	if ( ! file_exists( $target_path ) && empty( $input['create_missing'] ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_file_not_found',
			__( 'The specified theme file does not exist. Set create_missing to true to create it.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! is_dir( $target_dir ) && ! wp_mkdir_p( $target_dir ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_edit_directory_create_failed',
			__( 'Failed to create the target directory inside the theme.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$content = isset( $input['content'] ) ? wp_unslash( (string) $input['content'] ) : '';
	$bytes   = file_put_contents( $target_path, $content );

	if ( false === $bytes ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_edit_write_failed',
			__( 'Failed to write the theme file.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/edit-theme', true, 'theme', 0, $input_summary );

	return array(
		'theme'         => $theme_slug,
		'relative_path' => $relative_path,
		'bytes_written' => (int) $bytes,
		'edit_link'     => wordpress_mcp_admin_get_theme_editor_link( $theme_slug, $relative_path ),
	);
}

/**
 * テーマファイルを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_theme_file( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'theme', 'relative_path' ) );
	$theme_slug    = wordpress_mcp_admin_validate_theme_slug(
		$input['theme'] ?? '',
		'wordpress_mcp_admin_invalid_theme',
		__( 'A valid theme stylesheet is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $theme_slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-theme-file', false, 'theme', 0, $input_summary, $theme_slug->get_error_code(), $theme_slug->get_error_message() );

		return $theme_slug;
	}

	$relative_path = wordpress_mcp_admin_normalize_theme_relative_path( isset( $input['relative_path'] ) ? (string) $input['relative_path'] : '' );

	if ( is_wp_error( $relative_path ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-theme-file', false, 'theme', 0, $input_summary, $relative_path->get_error_code(), $relative_path->get_error_message() );

		return $relative_path;
	}

	$theme = wp_get_theme( $theme_slug );

	if ( ! $theme->exists() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_not_found',
			__( 'The specified theme could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-theme-file', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$target_path = trailingslashit( $theme->get_stylesheet_directory() ) . $relative_path;

	if ( ! file_exists( $target_path ) || ! is_file( $target_path ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_file_not_found',
			__( 'The specified theme file does not exist.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-theme-file', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$content = file_get_contents( $target_path );

	if ( false === $content ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_file_read_failed',
			__( 'Failed to read the theme file.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-theme-file', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-theme-file', true, 'theme', 0, $input_summary );

	return array(
		'theme'         => $theme_slug,
		'relative_path' => $relative_path,
		'content'       => $content,
		'bytes'         => strlen( $content ),
		'edit_link'     => wordpress_mcp_admin_get_theme_editor_link( $theme_slug, $relative_path ),
	);
}

/**
 * インストール済みテーマを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, array<int, array<string, string>>>
 */
function wordpress_mcp_admin_execute_get_themes( array $input = array() ): array {
	$include_inactive = ! isset( $input['include_inactive'] ) || ! empty( $input['include_inactive'] );
	$themes           = wp_get_themes();
	$records          = array();

	foreach ( $themes as $theme ) {
		if ( ! $theme instanceof WP_Theme || ! $theme->exists() ) {
			continue;
		}

		$is_active = $theme->get_stylesheet() === get_stylesheet();

		if ( ! $include_inactive && ! $is_active ) {
			continue;
		}

		$records[] = wordpress_mcp_admin_format_theme_record( $theme );
	}

	usort(
		$records,
		static function ( array $left, array $right ): int {
			return strcmp( $left['name'], $right['name'] );
		}
	);

	return array(
		'themes' => $records,
	);
}

/**
 * インストール済みテーマを削除します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_delete_theme( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'theme' ) );
	$theme_slug    = wordpress_mcp_admin_validate_theme_slug(
		$input['theme'] ?? '',
		'wordpress_mcp_admin_invalid_theme',
		__( 'A valid theme stylesheet is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $theme_slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-theme', false, 'theme', 0, $input_summary, $theme_slug->get_error_code(), $theme_slug->get_error_message() );

		return $theme_slug;
	}

	$theme = wp_get_theme( $theme_slug );

	if ( ! $theme->exists() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_not_found',
			__( 'The specified theme could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( $theme_slug === get_stylesheet() || $theme_slug === get_template() ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_active_theme_delete_blocked',
			__( 'The active theme cannot be deleted.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! function_exists( 'delete_theme' ) ) {
		require_once ABSPATH . 'wp-admin/includes/theme.php';
	}

	$deleted = delete_theme( $theme_slug );

	if ( is_wp_error( $deleted ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-theme', false, 'theme', 0, $input_summary, $deleted->get_error_code(), $deleted->get_error_message() );

		return $deleted;
	}

	if ( ! $deleted ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_theme_delete_failed',
			__( 'Failed to delete the theme.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-theme', false, 'theme', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-theme', true, 'theme', 0, $input_summary );

	return array(
		'theme'   => $theme_slug,
		'deleted' => true,
	);
}

/**
 * プラグインをインストールします。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_install_plugin( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'slug', 'activate' ) );
	$slug          = wordpress_mcp_admin_validate_plugin_identifier(
		$input['slug'] ?? '',
		'wordpress_mcp_admin_invalid_plugin_slug',
		__( 'A valid plugin slug is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $slug->get_error_code(), $slug->get_error_message() );

		return $slug;
	}

	$activate = ! empty( $input['activate'] );

	if ( ! function_exists( 'plugins_api' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	}

	if ( ! class_exists( 'Plugin_Upgrader' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
	}

	if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'activate_plugin' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugin_information = plugins_api(
		'plugin_information',
		array(
			'slug'   => sanitize_key( basename( $slug, '.php' ) ),
			'fields' => array(
				'sections' => false,
			),
		)
	);

	if ( is_wp_error( $plugin_information ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $plugin_information->get_error_code(), $plugin_information->get_error_message() );

		return $plugin_information;
	}

	$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
	$result   = $upgrader->install( (string) $plugin_information->download_link );

	if ( is_wp_error( $result ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $result->get_error_code(), $result->get_error_message() );

		return $result;
	}

	if ( false === $result ) {
		$skin_error = $upgrader->skin->get_errors();
		$error      = $skin_error instanceof WP_Error && $skin_error->has_errors()
			? $skin_error
			: new WP_Error( 'wordpress_mcp_admin_plugin_install_failed', __( 'Failed to install the plugin.', 'wordpress-mcp-admin-tools' ) );

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$plugin_basename = (string) $upgrader->plugin_info();

	if ( '' === $plugin_basename ) {
		$resolved_plugin = wordpress_mcp_admin_resolve_plugin_entry( sanitize_key( basename( $slug, '.php' ) ) );

		if ( is_wp_error( $resolved_plugin ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $resolved_plugin->get_error_code(), $resolved_plugin->get_error_message() );

			return $resolved_plugin;
		}

		$plugin_basename = $resolved_plugin['plugin'];
	}

	$all_plugins = get_plugins();

	if ( ! isset( $all_plugins[ $plugin_basename ] ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_not_found_after_install',
			__( 'The plugin was installed but could not be loaded afterwards.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( $activate ) {
		$activation_result = activate_plugin( $plugin_basename );

		if ( is_wp_error( $activation_result ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', false, 'plugin', 0, $input_summary, $activation_result->get_error_code(), $activation_result->get_error_message() );

			return $activation_result;
		}
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/install-plugin', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'    => $plugin_basename,
		'name'      => isset( $all_plugins[ $plugin_basename ]['Name'] ) ? (string) $all_plugins[ $plugin_basename ]['Name'] : '',
		'version'   => isset( $all_plugins[ $plugin_basename ]['Version'] ) ? (string) $all_plugins[ $plugin_basename ]['Version'] : '',
		'activated' => is_plugin_active( $plugin_basename ),
	);
}

/**
 * 新規プラグインを作成します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_create_plugin( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'slug', 'name', 'activate' ) );
	$slug          = wordpress_mcp_admin_validate_plugin_identifier(
		$input['slug'] ?? '',
		'wordpress_mcp_admin_invalid_plugin_slug',
		__( 'A valid plugin slug is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $slug ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $slug->get_error_code(), $slug->get_error_message() );

		return $slug;
	}

	$plugin_slug = sanitize_key( basename( $slug, '.php' ) );
	$name        = isset( $input['name'] ) ? sanitize_text_field( wp_unslash( (string) $input['name'] ) ) : '';

	if ( '' === $name ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_plugin_name',
			__( 'A plugin name is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$plugin_directory = trailingslashit( WP_PLUGIN_DIR ) . $plugin_slug;
	$plugin_basename  = $plugin_slug . '/' . $plugin_slug . '.php';

	if ( file_exists( $plugin_directory ) || file_exists( trailingslashit( WP_PLUGIN_DIR ) . $plugin_slug . '.php' ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_exists',
			__( 'A plugin with that slug already exists.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$plugin_data = array(
		'slug'        => $plugin_slug,
		'name'        => $name,
		'description' => isset( $input['description'] ) ? sanitize_text_field( wp_unslash( (string) $input['description'] ) ) : __( 'Created via MCP.', 'wordpress-mcp-admin-tools' ),
		'author'      => isset( $input['author'] ) ? sanitize_text_field( wp_unslash( (string) $input['author'] ) ) : '',
		'author_uri'  => isset( $input['author_uri'] ) ? esc_url_raw( wp_unslash( (string) $input['author_uri'] ) ) : '',
		'plugin_uri'  => isset( $input['plugin_uri'] ) ? esc_url_raw( wp_unslash( (string) $input['plugin_uri'] ) ) : '',
		'version'     => isset( $input['version'] ) ? sanitize_text_field( wp_unslash( (string) $input['version'] ) ) : '1.0.0',
	);

	$scaffold_files = wordpress_mcp_admin_get_plugin_scaffold_files( $plugin_data );

	if ( ! wp_mkdir_p( $plugin_directory ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_directory_create_failed',
			__( 'Failed to create the plugin directory.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	foreach ( $scaffold_files as $relative_path => $content ) {
		$target_path = trailingslashit( $plugin_directory ) . $relative_path;
		$target_dir  = dirname( $target_path );

		if ( ! is_dir( $target_dir ) && ! wp_mkdir_p( $target_dir ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_plugin_file_directory_create_failed',
				__( 'Failed to create a directory for the plugin scaffold.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		if ( false === file_put_contents( $target_path, $content ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_plugin_file_write_failed',
				__( 'Failed to write a file in the plugin scaffold.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}
	}

	if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'activate_plugin' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	wp_clean_plugins_cache( true );
	$plugins = get_plugins();

	if ( ! isset( $plugins[ $plugin_basename ] ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_not_found_after_create',
			__( 'The plugin was created but could not be loaded afterwards.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! empty( $input['activate'] ) ) {
		$activation_result = activate_plugin( $plugin_basename );

		if ( is_wp_error( $activation_result ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', false, 'plugin', 0, $input_summary, $activation_result->get_error_code(), $activation_result->get_error_message() );

			return $activation_result;
		}
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/create-plugin', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'    => $plugin_basename,
		'name'      => isset( $plugins[ $plugin_basename ]['Name'] ) ? (string) $plugins[ $plugin_basename ]['Name'] : '',
		'activated' => is_plugin_active( $plugin_basename ),
	);
}

/**
 * 既存プラグインを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_plugin( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'plugin', 'relative_path', 'create_missing' ) );
	$plugin_id     = wordpress_mcp_admin_validate_plugin_identifier(
		$input['plugin'] ?? '',
		'wordpress_mcp_admin_invalid_plugin',
		__( 'A valid plugin identifier is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $plugin_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $plugin_id->get_error_code(), $plugin_id->get_error_message() );

		return $plugin_id;
	}

	$relative_path = wordpress_mcp_admin_normalize_plugin_relative_path( isset( $input['relative_path'] ) ? (string) $input['relative_path'] : '' );

	if ( is_wp_error( $relative_path ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $relative_path->get_error_code(), $relative_path->get_error_message() );

		return $relative_path;
	}

	$plugin = wordpress_mcp_admin_resolve_plugin_entry( $plugin_id );

	if ( is_wp_error( $plugin ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $plugin->get_error_code(), $plugin->get_error_message() );

		return $plugin;
	}

	if ( '' === $plugin['directory'] ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_update_requires_directory',
			__( 'Only plugins stored in their own directory can be updated with relative_path.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$plugin_directory = trailingslashit( WP_PLUGIN_DIR ) . $plugin['directory'];
	$target_path      = trailingslashit( $plugin_directory ) . $relative_path;
	$target_dir       = dirname( $target_path );

	if ( ! file_exists( $target_path ) && empty( $input['create_missing'] ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_file_not_found',
			__( 'The specified plugin file does not exist. Set create_missing to true to create it.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! is_dir( $target_dir ) && ! wp_mkdir_p( $target_dir ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_edit_directory_create_failed',
			__( 'Failed to create the target directory inside the plugin.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$content = isset( $input['content'] ) ? wp_unslash( (string) $input['content'] ) : '';
	$bytes   = file_put_contents( $target_path, $content );

	if ( false === $bytes ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_edit_write_failed',
			__( 'Failed to write the plugin file.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-plugin', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'        => $plugin['plugin'],
		'relative_path' => $relative_path,
		'bytes_written' => (int) $bytes,
		'edit_link'     => wordpress_mcp_admin_get_plugin_editor_link( $plugin['plugin'] ),
	);
}

/**
 * プラグインファイルを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_get_plugin_file( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'plugin', 'relative_path' ) );
	$plugin_id     = wordpress_mcp_admin_validate_plugin_identifier(
		$input['plugin'] ?? '',
		'wordpress_mcp_admin_invalid_plugin',
		__( 'A valid plugin identifier is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $plugin_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', false, 'plugin', 0, $input_summary, $plugin_id->get_error_code(), $plugin_id->get_error_message() );

		return $plugin_id;
	}

	$relative_path = wordpress_mcp_admin_normalize_plugin_relative_path( isset( $input['relative_path'] ) ? (string) $input['relative_path'] : '' );

	if ( is_wp_error( $relative_path ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', false, 'plugin', 0, $input_summary, $relative_path->get_error_code(), $relative_path->get_error_message() );

		return $relative_path;
	}

	$plugin = wordpress_mcp_admin_resolve_plugin_entry( $plugin_id );

	if ( is_wp_error( $plugin ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', false, 'plugin', 0, $input_summary, $plugin->get_error_code(), $plugin->get_error_message() );

		return $plugin;
	}

	if ( '' === $plugin['directory'] ) {
		if ( $relative_path !== $plugin['file'] ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_plugin_read_requires_main_file',
				__( 'For single-file plugins, relative_path must point to the main plugin file.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$target_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin['file'];
	} else {
		$target_path = trailingslashit( trailingslashit( WP_PLUGIN_DIR ) . $plugin['directory'] ) . $relative_path;
	}

	if ( ! file_exists( $target_path ) || ! is_file( $target_path ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_file_not_found',
			__( 'The specified plugin file does not exist.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$content = file_get_contents( $target_path );

	if ( false === $content ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_file_read_failed',
			__( 'Failed to read the plugin file.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-plugin-file', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'        => $plugin['plugin'],
		'relative_path' => $relative_path,
		'content'       => $content,
		'bytes'         => strlen( $content ),
		'edit_link'     => wordpress_mcp_admin_get_plugin_editor_link( $plugin['plugin'] ),
	);
}

/**
 * インストール済みプラグインを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, array<int, array<string, string>>>
 */
function wordpress_mcp_admin_execute_get_plugins( array $input = array() ): array {
	if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$include_inactive = ! isset( $input['include_inactive'] ) || ! empty( $input['include_inactive'] );
	$plugins          = get_plugins();
	$records          = array();

	foreach ( $plugins as $plugin_basename => $plugin_data ) {
		$record = wordpress_mcp_admin_format_plugin_record( $plugin_basename, $plugin_data );

		if ( ! $include_inactive && 'inactive' === $record['status'] ) {
			continue;
		}

		$records[] = $record;
	}

	usort(
		$records,
		static function ( array $left, array $right ): int {
			return strcmp( $left['name'], $right['name'] );
		}
	);

	return array(
		'plugins' => $records,
	);
}

/**
 * インストール済みプラグインを削除します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_delete_plugin( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'plugin' ) );
	$plugin_id     = wordpress_mcp_admin_validate_plugin_identifier(
		$input['plugin'] ?? '',
		'wordpress_mcp_admin_invalid_plugin',
		__( 'A valid plugin identifier is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $plugin_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-plugin', false, 'plugin', 0, $input_summary, $plugin_id->get_error_code(), $plugin_id->get_error_message() );

		return $plugin_id;
	}

	if ( ! function_exists( 'delete_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	$plugin = wordpress_mcp_admin_resolve_plugin_entry( $plugin_id );

	if ( is_wp_error( $plugin ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-plugin', false, 'plugin', 0, $input_summary, $plugin->get_error_code(), $plugin->get_error_message() );

		return $plugin;
	}

	if ( is_plugin_active( $plugin['plugin'] ) || ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( $plugin['plugin'] ) ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_active_plugin_delete_blocked',
			__( 'The active plugin cannot be deleted.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$deleted = delete_plugins( array( $plugin['plugin'] ) );

	if ( is_wp_error( $deleted ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-plugin', false, 'plugin', 0, $input_summary, $deleted->get_error_code(), $deleted->get_error_message() );

		return $deleted;
	}

	if ( ! $deleted ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_delete_failed',
			__( 'Failed to delete the plugin.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/delete-plugin', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'  => $plugin['plugin'],
		'deleted' => true,
	);
}

/**
 * プラグインを有効化します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_activate_plugin( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'plugin' ) );
	$plugin_id     = wordpress_mcp_admin_validate_plugin_identifier(
		$input['plugin'] ?? '',
		'wordpress_mcp_admin_invalid_plugin',
		__( 'A valid plugin identifier is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $plugin_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/activate-plugin', false, 'plugin', 0, $input_summary, $plugin_id->get_error_code(), $plugin_id->get_error_message() );

		return $plugin_id;
	}

	if ( ! function_exists( 'activate_plugin' ) || ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugin = wordpress_mcp_admin_resolve_plugin_entry( $plugin_id );

	if ( is_wp_error( $plugin ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/activate-plugin', false, 'plugin', 0, $input_summary, $plugin->get_error_code(), $plugin->get_error_message() );

		return $plugin;
	}

	if ( is_plugin_active( $plugin['plugin'] ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/activate-plugin', true, 'plugin', 0, $input_summary );

		return array(
			'plugin'    => $plugin['plugin'],
			'activated' => true,
		);
	}

	$activation_result = activate_plugin( $plugin['plugin'] );

	if ( is_wp_error( $activation_result ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/activate-plugin', false, 'plugin', 0, $input_summary, $activation_result->get_error_code(), $activation_result->get_error_message() );

		return $activation_result;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/activate-plugin', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'    => $plugin['plugin'],
		'activated' => is_plugin_active( $plugin['plugin'] ),
	);
}

/**
 * プラグインを無効化します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_deactivate_plugin( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'plugin' ) );
	$plugin_id     = wordpress_mcp_admin_validate_plugin_identifier(
		$input['plugin'] ?? '',
		'wordpress_mcp_admin_invalid_plugin',
		__( 'A valid plugin identifier is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $plugin_id ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/deactivate-plugin', false, 'plugin', 0, $input_summary, $plugin_id->get_error_code(), $plugin_id->get_error_message() );

		return $plugin_id;
	}

	if ( ! function_exists( 'deactivate_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugin = wordpress_mcp_admin_resolve_plugin_entry( $plugin_id );

	if ( is_wp_error( $plugin ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/deactivate-plugin', false, 'plugin', 0, $input_summary, $plugin->get_error_code(), $plugin->get_error_message() );

		return $plugin;
	}

	if ( defined( 'WORDPRESS_MCP_ADMIN_TOOLS_BASENAME' ) && WORDPRESS_MCP_ADMIN_TOOLS_BASENAME === $plugin['plugin'] ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_self_deactivation_blocked',
			__( 'This plugin cannot deactivate itself.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/deactivate-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( ! is_plugin_active( $plugin['plugin'] ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/deactivate-plugin', true, 'plugin', 0, $input_summary );

		return array(
			'plugin'      => $plugin['plugin'],
			'deactivated' => true,
		);
	}

	deactivate_plugins( $plugin['plugin'], false, false );

	if ( is_plugin_active( $plugin['plugin'] ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_deactivation_failed',
			__( 'Failed to deactivate the plugin.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/deactivate-plugin', false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/deactivate-plugin', true, 'plugin', 0, $input_summary );

	return array(
		'plugin'      => $plugin['plugin'],
		'deactivated' => true,
	);
}

/**
 * プラグイン自動更新の設定を変更します。
 *
 * @param string $ability_name Ability 名。
 * @param string $plugin_input プラグイン識別子。
 * @param bool   $enable       有効化する場合は true。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_set_plugin_auto_update_state( string $ability_name, string $plugin_input, bool $enable ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( array( 'plugin' => $plugin_input ), array( 'plugin' ) );
	$plugin_id     = wordpress_mcp_admin_validate_plugin_identifier(
		$plugin_input,
		'wordpress_mcp_admin_invalid_plugin',
		__( 'A valid plugin identifier is required.', 'wordpress-mcp-admin-tools' )
	);

	if ( is_wp_error( $plugin_id ) ) {
		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'plugin', 0, $input_summary, $plugin_id->get_error_code(), $plugin_id->get_error_message() );

		return $plugin_id;
	}

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugin = wordpress_mcp_admin_resolve_plugin_entry( $plugin_id );

	if ( is_wp_error( $plugin ) ) {
		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'plugin', 0, $input_summary, $plugin->get_error_code(), $plugin->get_error_message() );

		return $plugin;
	}

	$all_plugins  = apply_filters( 'all_plugins', get_plugins() );
	$plugin_file  = $plugin['plugin'];
	$auto_updates = (array) get_site_option( 'auto_update_plugins', array() );

	if ( ! array_key_exists( $plugin_file, $all_plugins ) ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_not_found',
			__( 'The specified plugin could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( $enable ) {
		$auto_updates[] = $plugin_file;
		$auto_updates   = array_unique( $auto_updates );
	} else {
		$auto_updates = array_diff( $auto_updates, array( $plugin_file ) );
	}

	$auto_updates = array_values( array_intersect( $auto_updates, array_keys( $all_plugins ) ) );
	update_site_option( 'auto_update_plugins', $auto_updates );

	$auto_update_enabled = in_array( $plugin_file, (array) get_site_option( 'auto_update_plugins', array() ), true );

	if ( $enable !== $auto_update_enabled ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_plugin_auto_update_toggle_failed',
			$enable
				? __( 'Failed to enable plugin auto-updates.', 'wordpress-mcp-admin-tools' )
				: __( 'Failed to disable plugin auto-updates.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'plugin', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( $ability_name, true, 'plugin', 0, $input_summary );

	return array(
		'plugin'              => $plugin_file,
		'auto_update_enabled' => $auto_update_enabled,
	);
}

/**
 * プラグイン自動更新を有効化します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_enable_plugin_auto_update( array $input = array() ) {
	return wordpress_mcp_admin_set_plugin_auto_update_state(
		'wordpress-mcp-admin/enable-plugin-auto-update',
		(string) ( $input['plugin'] ?? '' ),
		true
	);
}

/**
 * プラグイン自動更新を無効化します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_disable_plugin_auto_update( array $input = array() ) {
	return wordpress_mcp_admin_set_plugin_auto_update_state(
		'wordpress-mcp-admin/disable-plugin-auto-update',
		(string) ( $input['plugin'] ?? '' ),
		false
	);
}

/**
 * リモート URL からメディアを取り込みます。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_import_media_from_url( array $input = array() ) {
	$input_summary  = wordpress_mcp_admin_build_input_summary( $input, array( 'source_url', 'parent_post_id' ) );
	$source_url     = isset( $input['source_url'] ) ? esc_url_raw( wp_unslash( (string) $input['source_url'] ) ) : '';
	$parent_post_id = isset( $input['parent_post_id'] ) ? max( 0, (int) $input['parent_post_id'] ) : 0;

	if ( '' === $source_url ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_media_source_url',
			__( 'A valid source_url is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/import-media-from-url', false, 'attachment', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( $parent_post_id > 0 && ! get_post( $parent_post_id ) instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_media_parent',
			__( 'parent_post_id must reference an existing post.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/import-media-from-url', false, 'attachment', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$temporary_file = download_url( $source_url, 60 );

	if ( is_wp_error( $temporary_file ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/import-media-from-url', false, 'attachment', 0, $input_summary, $temporary_file->get_error_code(), $temporary_file->get_error_message() );

		return $temporary_file;
	}

	$source_path = wp_parse_url( $source_url, PHP_URL_PATH );
	$file_name   = is_string( $source_path ) ? basename( $source_path ) : '';
	$file_name   = '' !== $file_name ? rawurldecode( $file_name ) : 'remote-file';
	$file_array  = array(
		'name'     => sanitize_file_name( $file_name ),
		'tmp_name' => $temporary_file,
	);

	$attachment_id = media_handle_sideload( $file_array, $parent_post_id );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $temporary_file );
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/import-media-from-url', false, 'attachment', 0, $input_summary, $attachment_id->get_error_code(), $attachment_id->get_error_message() );

		return $attachment_id;
	}

	$attachment_update = array(
		'ID' => (int) $attachment_id,
	);

	if ( array_key_exists( 'title', $input ) ) {
		$attachment_update['post_title'] = sanitize_text_field( wp_unslash( (string) $input['title'] ) );
	}

	if ( array_key_exists( 'caption', $input ) ) {
		$attachment_update['post_excerpt'] = sanitize_textarea_field( wp_unslash( (string) $input['caption'] ) );
	}

	if ( array_key_exists( 'description', $input ) ) {
		$attachment_update['post_content'] = sanitize_textarea_field( wp_unslash( (string) $input['description'] ) );
	}

	if ( count( $attachment_update ) > 1 ) {
		wp_update_post( $attachment_update );
	}

	if ( array_key_exists( 'alt_text', $input ) ) {
		update_post_meta( (int) $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( wp_unslash( (string) $input['alt_text'] ) ) );
	}

	$attachment = get_post( (int) $attachment_id );

	if ( ! $attachment instanceof WP_Post || 'attachment' !== $attachment->post_type ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_media_not_found_after_import',
			__( 'The media item was imported but could not be loaded afterwards.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/import-media-from-url', false, 'attachment', (int) $attachment_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/import-media-from-url', true, 'attachment', (int) $attachment_id, $input_summary );

	return wordpress_mcp_admin_format_media_record( $attachment );
}

/**
 * メディアライブラリの項目を取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, array<int, array<string, mixed>>>
 */
function wordpress_mcp_admin_execute_get_media_items( array $input = array() ): array {
	$search    = isset( $input['search'] ) ? sanitize_text_field( wp_unslash( (string) $input['search'] ) ) : '';
	$mime_type = isset( $input['mime_type'] ) ? sanitize_text_field( wp_unslash( (string) $input['mime_type'] ) ) : '';
	$per_page  = isset( $input['per_page'] ) ? max( 1, min( 50, (int) $input['per_page'] ) ) : 20;
	$query     = array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => $per_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( '' !== $search ) {
		$query['s'] = $search;
	}

	if ( '' !== $mime_type ) {
		$query['post_mime_type'] = $mime_type;
	}

	$attachments = get_posts( $query );
	$records     = array();

	foreach ( $attachments as $attachment ) {
		if ( $attachment instanceof WP_Post && 'attachment' === $attachment->post_type ) {
			$records[] = wordpress_mcp_admin_format_media_record( $attachment );
		}
	}

	return array(
		'items' => $records,
	);
}

/**
 * 投稿または固定ページのアイキャッチ画像を設定します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_set_featured_image( array $input = array() ) {
	$post_id       = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;
	$attachment_id = isset( $input['attachment_id'] ) ? max( 0, (int) $input['attachment_id'] ) : 0;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'post_id', 'attachment_id' ) );

	if ( $post_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_id',
			__( 'A valid post_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-featured-image', false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found',
			__( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-featured-image', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( $attachment_id > 0 ) {
		$attachment = wordpress_mcp_admin_get_attachment_post(
			$attachment_id,
			'wordpress_mcp_admin_invalid_featured_image',
			__( 'attachment_id must reference an existing media item.', 'wordpress-mcp-admin-tools' )
		);

		if ( is_wp_error( $attachment ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-featured-image', false, 'post', $post_id, $input_summary, $attachment->get_error_code(), $attachment->get_error_message() );

			return $attachment;
		}

		if ( ! wordpress_mcp_admin_attachment_is_image( $attachment ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_featured_image_not_image',
				__( 'The featured image must be an image attachment.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-featured-image', false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		set_post_thumbnail( $post_id, $attachment_id );
	} else {
		delete_post_thumbnail( $post_id );
	}

	$featured_image_id  = (int) get_post_thumbnail_id( $post_id );
	$featured_image_url = $featured_image_id > 0 ? wp_get_attachment_url( $featured_image_id ) : '';

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-featured-image', true, 'post', $post_id, $input_summary );

	return array(
		'post_id'            => $post_id,
		'featured_image_id'  => $featured_image_id,
		'featured_image_url' => is_string( $featured_image_url ) ? $featured_image_url : '',
		'edit_link'          => (string) get_edit_post_link( $post_id, 'raw' ),
	);
}

/**
 * サイトロゴとサイトアイコンを更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_update_site_media( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'custom_logo_id', 'site_icon_id' ) );
	$updated       = false;

	if ( array_key_exists( 'custom_logo_id', $input ) ) {
		$custom_logo_id = max( 0, (int) $input['custom_logo_id'] );

		if ( $custom_logo_id > 0 ) {
			$attachment = wordpress_mcp_admin_get_attachment_post(
				$custom_logo_id,
				'wordpress_mcp_admin_invalid_custom_logo',
				__( 'custom_logo_id must reference an existing media item.', 'wordpress-mcp-admin-tools' )
			);

			if ( is_wp_error( $attachment ) ) {
				wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-site-media', false, 'option', 0, $input_summary, $attachment->get_error_code(), $attachment->get_error_message() );

				return $attachment;
			}

			if ( ! wordpress_mcp_admin_attachment_is_image( $attachment ) ) {
				$error = new WP_Error(
					'wordpress_mcp_admin_custom_logo_not_image',
					__( 'custom_logo_id must reference an image attachment.', 'wordpress-mcp-admin-tools' )
				);

				wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-site-media', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

				return $error;
			}
		}

		set_theme_mod( 'custom_logo', $custom_logo_id );
		$updated = true;
	}

	if ( array_key_exists( 'site_icon_id', $input ) ) {
		$site_icon_id = max( 0, (int) $input['site_icon_id'] );

		if ( $site_icon_id > 0 ) {
			$attachment = wordpress_mcp_admin_get_attachment_post(
				$site_icon_id,
				'wordpress_mcp_admin_invalid_site_icon',
				__( 'site_icon_id must reference an existing media item.', 'wordpress-mcp-admin-tools' )
			);

			if ( is_wp_error( $attachment ) ) {
				wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-site-media', false, 'option', 0, $input_summary, $attachment->get_error_code(), $attachment->get_error_message() );

				return $attachment;
			}

			if ( ! wordpress_mcp_admin_attachment_is_image( $attachment ) ) {
				$error = new WP_Error(
					'wordpress_mcp_admin_site_icon_not_image',
					__( 'site_icon_id must reference an image attachment.', 'wordpress-mcp-admin-tools' )
				);

				wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-site-media', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

				return $error;
			}
		}

		update_option( 'site_icon', $site_icon_id );
		$updated = true;
	}

	if ( ! $updated ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_no_site_media_values',
			__( 'No site media values were provided for update.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-site-media', false, 'option', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$custom_logo_id  = (int) get_theme_mod( 'custom_logo', 0 );
	$site_icon_id    = (int) get_option( 'site_icon', 0 );
	$custom_logo_url = $custom_logo_id > 0 ? wp_get_attachment_url( $custom_logo_id ) : '';
	$site_icon_url   = $site_icon_id > 0 ? wp_get_attachment_url( $site_icon_id ) : '';

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/update-site-media', true, 'option', 0, $input_summary );

	return array(
		'custom_logo_id'  => $custom_logo_id,
		'custom_logo_url' => is_string( $custom_logo_url ) ? $custom_logo_url : '',
		'site_icon_id'    => $site_icon_id,
		'site_icon_url'   => is_string( $site_icon_url ) ? $site_icon_url : '',
	);
}

/**
 * ナビゲーションメニューを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_execute_get_navigation_menus( array $input = array() ): array {
	$include_items        = ! empty( $input['include_items'] );
	$menus                = wp_get_nav_menus();
	$menu_records         = array();
	$registered_locations = get_registered_nav_menus();
	$current_locations    = (array) get_nav_menu_locations();
	$location_records     = array();

	foreach ( $menus as $menu ) {
		if ( $menu instanceof WP_Term ) {
			$menu_records[] = wordpress_mcp_admin_format_navigation_menu_record( $menu, $include_items );
		}
	}

	foreach ( $registered_locations as $location => $label ) {
		$assigned_menu_id   = isset( $current_locations[ $location ] ) ? (int) $current_locations[ $location ] : 0;
		$assigned_menu      = $assigned_menu_id > 0 ? wp_get_nav_menu_object( $assigned_menu_id ) : false;
		$location_records[] = array(
			'location'         => (string) $location,
			'label'            => (string) $label,
			'assigned_menu_id' => $assigned_menu_id,
			'assigned_menu'    => $assigned_menu instanceof WP_Term ? (string) $assigned_menu->name : '',
		);
	}

	return array(
		'menus'            => $menu_records,
		'locations'        => $location_records,
		'navigation_posts' => wordpress_mcp_admin_get_navigation_posts( 20 ),
	);
}

/**
 * ナビゲーションメニューを作成または更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_set_navigation_menu( array $input = array() ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'menu', 'name', 'replace_items' ) );
	$menu          = null;
	$menu_name     = isset( $input['name'] ) ? sanitize_text_field( wp_unslash( (string) $input['name'] ) ) : '';

	if ( array_key_exists( 'menu', $input ) ) {
		$menu = wordpress_mcp_admin_resolve_navigation_menu( $input['menu'] );

		if ( is_wp_error( $menu ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', 0, $input_summary, $menu->get_error_code(), $menu->get_error_message() );

			return $menu;
		}
	}

	if ( ! $menu instanceof WP_Term ) {
		if ( '' === $menu_name ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_missing_navigation_menu_name',
				__( 'A name is required when creating a navigation menu.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$menu_id = wp_create_nav_menu( $menu_name );

		if ( is_wp_error( $menu_id ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', 0, $input_summary, $menu_id->get_error_code(), $menu_id->get_error_message() );

			return $menu_id;
		}

		$menu = wp_get_nav_menu_object( $menu_id );
	}

	if ( ! $menu instanceof WP_Term ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_navigation_menu_not_found_after_save',
			__( 'The navigation menu could not be loaded after saving.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$term_args = array();

	if ( '' !== $menu_name ) {
		$term_args['name'] = $menu_name;
	}

	if ( array_key_exists( 'slug', $input ) ) {
		$term_args['slug'] = sanitize_title( wp_unslash( (string) $input['slug'] ) );
	}

	if ( array_key_exists( 'description', $input ) ) {
		$term_args['description'] = sanitize_textarea_field( wp_unslash( (string) $input['description'] ) );
	}

	if ( ! empty( $term_args ) ) {
		$updated_term = wp_update_term( $menu->term_id, 'nav_menu', $term_args );

		if ( is_wp_error( $updated_term ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', (int) $menu->term_id, $input_summary, $updated_term->get_error_code(), $updated_term->get_error_message() );

			return $updated_term;
		}

		$menu = wp_get_nav_menu_object( $menu->term_id );
	}

	if ( ! $menu instanceof WP_Term ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_navigation_menu_not_found_after_update',
			__( 'The navigation menu could not be loaded after updating.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( array_key_exists( 'items', $input ) ) {
		if ( ! is_array( $input['items'] ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_navigation_items',
				__( 'items must be an array of navigation item objects.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', (int) $menu->term_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$save_items = wordpress_mcp_admin_save_navigation_menu_items( $menu->term_id, $input['items'], ! isset( $input['replace_items'] ) || ! empty( $input['replace_items'] ) );

		if ( is_wp_error( $save_items ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', (int) $menu->term_id, $input_summary, $save_items->get_error_code(), $save_items->get_error_message() );

			return $save_items;
		}
	}

	if ( array_key_exists( 'locations', $input ) ) {
		if ( ! is_array( $input['locations'] ) ) {
			$error = new WP_Error(
				'wordpress_mcp_admin_invalid_navigation_locations',
				__( 'locations must be an array of registered menu location slugs.', 'wordpress-mcp-admin-tools' )
			);

			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', (int) $menu->term_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

			return $error;
		}

		$locations = wordpress_mcp_admin_normalize_navigation_locations( $input['locations'] );

		if ( is_wp_error( $locations ) ) {
			wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', false, 'nav_menu', (int) $menu->term_id, $input_summary, $locations->get_error_code(), $locations->get_error_message() );

			return $locations;
		}

		wordpress_mcp_admin_assign_navigation_menu_locations( $menu->term_id, $locations );
	}

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/set-navigation-menu', true, 'nav_menu', (int) $menu->term_id, $input_summary );

	return wordpress_mcp_admin_format_navigation_menu_record( $menu, true );
}

/**
 * Site Health 状態を取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_execute_get_site_health_status( array $input = array() ): array {
	unset( $input );

	$result = wordpress_mcp_admin_get_site_health_status();

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/get-site-health-status', true, 'site-health', 0, '' );

	return $result;
}

/**
 * Site Health 修正を実行します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_run_site_health_fix( array $input = array() ) {
	$fix           = isset( $input['fix'] ) ? sanitize_key( (string) $input['fix'] ) : '';
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'fix' ) );

	if ( '' === $fix ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_site_health_fix',
			__( 'A supported fix value is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/run-site-health-fix', false, 'site-health', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$result = wordpress_mcp_admin_apply_site_health_fix( $fix );

	if ( is_wp_error( $result ) ) {
		wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/run-site-health-fix', false, 'site-health', 0, $input_summary, $result->get_error_code(), $result->get_error_message() );

		return $result;
	}

	$status = wordpress_mcp_admin_get_site_health_status();

	wordpress_mcp_admin_log_ability_execution( 'wordpress-mcp-admin/run-site-health-fix', true, 'site-health', 0, $input_summary );

	$result['summary_after']         = isset( $status['summary'] ) && is_array( $status['summary'] ) ? $status['summary'] : array();
	$result['available_fixes_after'] = isset( $status['available_fixes'] ) && is_array( $status['available_fixes'] ) ? $status['available_fixes'] : array();

	return $result;
}

/**
 * 監査ログを取得します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, array<int, array<string, mixed>>>
 */
function wordpress_mcp_admin_execute_get_audit_log( array $input = array() ): array {
	$limit = isset( $input['limit'] ) ? (int) $input['limit'] : 10;
	$limit = max( 1, min( 50, $limit ) );

	$logs = wordpress_mcp_admin_get_audit_logs();

	return array(
		'entries' => array_slice( $logs, 0, $limit ),
	);
}
