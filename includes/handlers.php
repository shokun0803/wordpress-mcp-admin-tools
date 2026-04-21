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
 * @return array<string, string>|WP_Error
 */
function wordpress_mcp_admin_execute_update_general_settings( array $input = array() ) {
	$updated       = false;
	$input_summary = wordpress_mcp_admin_build_input_summary( $input, array( 'blogname', 'blogdescription' ) );

	if ( array_key_exists( 'blogname', $input ) ) {
		update_option( 'blogname', sanitize_text_field( wp_unslash( (string) $input['blogname'] ) ) );
		$updated = true;
	}

	if ( array_key_exists( 'blogdescription', $input ) ) {
		update_option( 'blogdescription', sanitize_text_field( wp_unslash( (string) $input['blogdescription'] ) ) );
		$updated = true;
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
