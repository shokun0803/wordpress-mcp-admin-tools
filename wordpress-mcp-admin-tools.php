<?php
/**
 * Plugin Name: MCP Admin Tools for WordPress
 * Plugin URI: https://example.com/
 * Description: Registers abilities for managing WordPress content and viewing audit logs from MCP clients.
 * Version: 0.1.9
 * Author: GitHub Copilot
 * License: GPL-2.0-or-later
 * Text Domain: wordpress-mcp-admin-tools
 * Domain Path: /languages
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 翻訳ファイルを読み込みます。
 *
 * @return void
 */
function wordpress_mcp_admin_load_textdomain(): void {
	load_plugin_textdomain( 'wordpress-mcp-admin-tools', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wordpress_mcp_admin_load_textdomain' );

/**
 * Ability カテゴリを登録します。
 *
 * @return void
 */
function wordpress_mcp_admin_register_ability_categories(): void {
	wp_register_ability_category(
		'wordpress-mcp-admin',
		array(
			'label'       => __( 'MCP Admin Tools', 'wordpress-mcp-admin-tools' ),
			'description' => __( 'Administrative abilities available to MCP clients.', 'wordpress-mcp-admin-tools' ),
		)
	);
}
add_action( 'wp_abilities_api_categories_init', 'wordpress_mcp_admin_register_ability_categories' );

/**
 * 管理画面メニューを登録します。
 *
 * @return void
 */
function wordpress_mcp_admin_register_admin_menu(): void {
	add_management_page(
		__( 'MCP Admin Tools', 'wordpress-mcp-admin-tools' ),
		__( 'MCP Admin Tools', 'wordpress-mcp-admin-tools' ),
		'manage_options',
		'wordpress-mcp-admin-audit-log',
		'wordpress_mcp_admin_render_audit_log_page'
	);
}
add_action( 'admin_menu', 'wordpress_mcp_admin_register_admin_menu' );

/**
 * Ability を登録します。
 *
 * @return void
 */
function wordpress_mcp_admin_register_abilities(): void {
	wp_register_ability(
		'wordpress-mcp-admin/create-page',
		array(
			'label'               => __( 'Create Page', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Create a new page with the provided content.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_create_page',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_pages',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'title'   => array(
						'type'        => 'string',
						'description' => __( 'Page title.', 'wordpress-mcp-admin-tools' ),
					),
					'content' => array(
						'type'        => 'string',
						'description' => __( 'Page content.', 'wordpress-mcp-admin-tools' ),
					),
					'excerpt' => array(
						'type'        => 'string',
						'description' => __( 'Page excerpt.', 'wordpress-mcp-admin-tools' ),
					),
					'status'  => array(
						'type'        => 'string',
						'enum'        => array( 'draft', 'pending', 'private', 'publish' ),
						'description' => __( 'Page status.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'title' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'page_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Created page ID.', 'wordpress-mcp-admin-tools' ),
					),
					'status'    => array(
						'type'        => 'string',
						'description' => __( 'Saved page status.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link' => array(
						'type'        => 'string',
						'description' => __( 'Admin edit link.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => false,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/create-post',
		array(
			'label'               => __( 'Create Post', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Create a new post with the provided content.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_create_post',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_posts',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'title'   => array(
						'type'        => 'string',
						'description' => __( 'Post title.', 'wordpress-mcp-admin-tools' ),
					),
					'content' => array(
						'type'        => 'string',
						'description' => __( 'Post content.', 'wordpress-mcp-admin-tools' ),
					),
					'excerpt' => array(
						'type'        => 'string',
						'description' => __( 'Post excerpt.', 'wordpress-mcp-admin-tools' ),
					),
					'status'  => array(
						'type'        => 'string',
						'enum'        => array( 'draft', 'pending', 'private', 'publish' ),
						'description' => __( 'Post status.', 'wordpress-mcp-admin-tools' ),
					),
					'type'    => array(
						'type'        => 'string',
						'description' => __( 'Post type. Defaults to post.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'title' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Created post ID.', 'wordpress-mcp-admin-tools' ),
					),
					'status'    => array(
						'type'        => 'string',
						'description' => __( 'Saved post status.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link' => array(
						'type'        => 'string',
						'description' => __( 'Admin edit link.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => false,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/update-post',
		array(
			'label'               => __( 'Update Post', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update the title, content, excerpt, or status of an existing post.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_post',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_posts',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_id' => array(
						'type'        => 'integer',
						'description' => __( 'Post ID to update.', 'wordpress-mcp-admin-tools' ),
					),
					'title'   => array(
						'type'        => 'string',
						'description' => __( 'New post title.', 'wordpress-mcp-admin-tools' ),
					),
					'content' => array(
						'type'        => 'string',
						'description' => __( 'New post content.', 'wordpress-mcp-admin-tools' ),
					),
					'excerpt' => array(
						'type'        => 'string',
						'description' => __( 'New post excerpt.', 'wordpress-mcp-admin-tools' ),
					),
					'status'  => array(
						'type'        => 'string',
						'enum'        => array( 'draft', 'pending', 'private', 'publish' ),
						'description' => __( 'New post status.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Updated post ID.', 'wordpress-mcp-admin-tools' ),
					),
					'status'    => array(
						'type'        => 'string',
						'description' => __( 'Updated post status.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link' => array(
						'type'        => 'string',
						'description' => __( 'Admin edit link.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => false,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/delete-post',
		array(
			'label'               => __( 'Delete Post', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Delete an existing post or move it to the trash.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_delete_post',
			'permission_callback' => 'wordpress_mcp_admin_can_delete_posts',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_id' => array(
						'type'        => 'integer',
						'description' => __( 'Post ID to delete.', 'wordpress-mcp-admin-tools' ),
					),
					'force'   => array(
						'type'        => 'boolean',
						'description' => __( 'Permanently delete when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'         => array(
						'type'        => 'integer',
						'description' => __( 'Post ID that was deleted.', 'wordpress-mcp-admin-tools' ),
					),
					'deleted'         => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the delete operation succeeded.', 'wordpress-mcp-admin-tools' ),
					),
					'previous_status' => array(
						'type'        => 'string',
						'description' => __( 'Post status before deletion.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => false,
					'destructive' => true,
					'idempotent' => false,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/update-page',
		array(
			'label'               => __( 'Update Page', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update the title, content, excerpt, or status of an existing page.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_page',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_pages',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'page_id'  => array(
						'type'        => 'integer',
						'description' => __( 'Page ID to update.', 'wordpress-mcp-admin-tools' ),
					),
					'title'    => array(
						'type'        => 'string',
						'description' => __( 'New page title.', 'wordpress-mcp-admin-tools' ),
					),
					'content'  => array(
						'type'        => 'string',
						'description' => __( 'New page content.', 'wordpress-mcp-admin-tools' ),
					),
					'excerpt'  => array(
						'type'        => 'string',
						'description' => __( 'New page excerpt.', 'wordpress-mcp-admin-tools' ),
					),
					'status'   => array(
						'type'        => 'string',
						'enum'        => array( 'draft', 'pending', 'private', 'publish' ),
						'description' => __( 'New page status.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'page_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'page_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Updated page ID.', 'wordpress-mcp-admin-tools' ),
					),
					'status'    => array(
						'type'        => 'string',
						'description' => __( 'Updated page status.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link' => array(
						'type'        => 'string',
						'description' => __( 'Admin edit link.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => false,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/update-general-settings',
		array(
			'label'               => __( 'Update General Settings', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update the site title and tagline.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_general_settings',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'blogname'        => array(
						'type'        => 'string',
						'description' => __( 'New site title.', 'wordpress-mcp-admin-tools' ),
					),
					'blogdescription' => array(
						'type'        => 'string',
						'description' => __( 'New tagline.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'blogname'        => array(
						'type'        => 'string',
						'description' => __( 'Updated site title.', 'wordpress-mcp-admin-tools' ),
					),
					'blogdescription' => array(
						'type'        => 'string',
						'description' => __( 'Updated tagline.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => true,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/get-audit-log',
		array(
			'label'               => __( 'Get Audit Log', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve the audit log for ability executions.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_audit_log',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'limit' => array(
						'type'        => 'integer',
						'description' => __( 'Number of entries to retrieve. Must be between 1 and 50.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'entries' => array(
						'type'        => 'array',
						'description' => __( 'Array of audit log entries.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'timestamp'   => array( 'type' => 'string' ),
								'ability'     => array( 'type' => 'string' ),
								'user_id'     => array( 'type' => 'integer' ),
								'target_id'   => array( 'type' => 'integer' ),
								'target_type' => array( 'type' => 'string' ),
								'input_summary' => array( 'type' => 'string' ),
								'error_code'  => array( 'type' => 'string' ),
								'error_message' => array( 'type' => 'string' ),
								'success'     => array( 'type' => 'boolean' ),
							),
						),
					),
				),
			),
			'meta'                => array(
				'show_in_rest' => true,
				'mcp'          => array(
					'public' => true,
				),
				'annotations'  => array(
					'readonly'   => true,
					'destructive' => false,
					'idempotent' => true,
				),
			),
		)
	);
}
add_action( 'wp_abilities_api_init', 'wordpress_mcp_admin_register_abilities' );

/**
 * 投稿編集権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_edit_posts( array $input = array() ): bool {
	if ( isset( $input['post_id'] ) && (int) $input['post_id'] > 0 ) {
		return current_user_can( 'edit_post', (int) $input['post_id'] );
	}

	return current_user_can( 'edit_posts' );
}

/**
 * 投稿削除権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_delete_posts( array $input = array() ): bool {
	if ( isset( $input['post_id'] ) && (int) $input['post_id'] > 0 ) {
		return current_user_can( 'delete_post', (int) $input['post_id'] );
	}

	return current_user_can( 'delete_posts' );
}

/**
 * 固定ページ編集権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_edit_pages( array $input = array() ): bool {
	if ( isset( $input['page_id'] ) && (int) $input['page_id'] > 0 ) {
		return current_user_can( 'edit_post', (int) $input['page_id'] );
	}

	return current_user_can( 'edit_pages' );
}

/**
 * 設定変更権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_manage_options( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'manage_options' );
}

/**
 * 監査ログ配列を取得します。
 *
 * 旧 option 名からの移行もここで処理します。
 *
 * @return array<int, array<string, mixed>>
 */
function wordpress_mcp_admin_get_audit_logs(): array {
	$logs = get_option( 'wordpress_mcp_admin_audit_log', null );
	$legacy_logs = get_option( 'noveltool_mcp_admin_audit_log', null );
	$needs_update = false;

	if ( null === $logs ) {
		$logs = array();
	}

	if ( is_array( $legacy_logs ) && ! empty( $legacy_logs ) ) {
		$logs = array_merge( $legacy_logs, is_array( $logs ) ? $logs : array() );
		$needs_update = true;
	}

	if ( ! is_array( $logs ) ) {
		return array();
	}

	foreach ( $logs as $index => $log_entry ) {
		if ( ! is_array( $log_entry ) ) {
			continue;
		}

		if ( isset( $log_entry['ability'] ) && is_string( $log_entry['ability'] ) ) {
			$normalized_ability = $log_entry['ability'];

			if ( str_starts_with( $normalized_ability, 'noveltool-mcp-admin/' ) ) {
				$normalized_ability = 'wordpress-mcp-admin/' . substr( $normalized_ability, strlen( 'noveltool-mcp-admin/' ) );
			}

			if ( preg_match( '/^noveltool-mcp-admin([a-z].*)$/', $normalized_ability, $matches ) ) {
				$normalized_ability = 'wordpress-mcp-admin/' . $matches[1];
			}

			if ( $normalized_ability !== $log_entry['ability'] ) {
				$logs[ $index ]['ability'] = $normalized_ability;
				$needs_update = true;
			}
		}
	}

	$deduplicated_logs = array();
	$seen_entries      = array();

	foreach ( $logs as $log_entry ) {
		if ( ! is_array( $log_entry ) ) {
			continue;
		}

		$entry_hash = md5( wp_json_encode( $log_entry ) );

		if ( isset( $seen_entries[ $entry_hash ] ) ) {
			$needs_update = true;
			continue;
		}

		$seen_entries[ $entry_hash ] = true;
		$deduplicated_logs[]         = $log_entry;
	}

	$logs = array_slice( $deduplicated_logs, 0, 50 );

	if ( $needs_update ) {
		update_option( 'wordpress_mcp_admin_audit_log', $logs, false );

		if ( false !== get_option( 'noveltool_mcp_admin_audit_log', false ) ) {
			delete_option( 'noveltool_mcp_admin_audit_log' );
		}
	}

	return $logs;
}

/**
 * 監査ログを保存します。
 *
 * @param string $ability_name Ability 名。
 * @param bool   $success 成功可否。
 * @param string $target_type 対象種別。
 * @param int    $target_id 対象 ID。
 * @param string $input_summary 入力要約。
 * @param string $error_code エラーコード。
 * @param string $error_message エラーメッセージ。
 * @return void
 */
function wordpress_mcp_admin_log_ability_execution( string $ability_name, bool $success, string $target_type = '', int $target_id = 0, string $input_summary = '', string $error_code = '', string $error_message = '' ): void {
	$normalized_ability_name = strtolower( $ability_name );
	$normalized_ability_name = preg_replace( '/[^a-z0-9\/-]/', '', $normalized_ability_name );

	$logs = wordpress_mcp_admin_get_audit_logs();

	array_unshift(
		$logs,
		array(
			'timestamp'   => current_time( 'mysql', true ),
			'ability'     => is_string( $normalized_ability_name ) ? $normalized_ability_name : '',
			'user_id'     => get_current_user_id(),
			'target_id'   => $target_id,
			'target_type' => sanitize_key( $target_type ),
			'input_summary' => sanitize_text_field( $input_summary ),
			'error_code'  => sanitize_key( $error_code ),
			'error_message' => sanitize_text_field( $error_message ),
			'success'     => $success,
		)
	);

	$logs = array_slice( $logs, 0, 50 );

	update_option( 'wordpress_mcp_admin_audit_log', $logs, false );
}

/**
 * 監査ログ用の入力要約を生成します。
 *
 * @param array<string, mixed> $input 入力値。
 * @param string[]             $keys 要約対象キー。
 * @return string
 */
function wordpress_mcp_admin_build_input_summary( array $input, array $keys ): string {
	$parts = array();

	foreach ( $keys as $key ) {
		if ( ! array_key_exists( $key, $input ) ) {
			continue;
		}

		$value = $input[ $key ];

		if ( is_bool( $value ) ) {
			$parts[] = $key . '=' . ( $value ? 'true' : 'false' );
			continue;
		}

		if ( is_scalar( $value ) ) {
			$parts[] = $key . '=' . sanitize_text_field( wp_unslash( (string) $value ) );
		}
	}

	return implode( '; ', $parts );
}

/**
 * 新規投稿を作成します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_execute_create_post( array $input = array() ) {
	$title = isset( $input['title'] ) ? sanitize_text_field( wp_unslash( (string) $input['title'] ) ) : '';
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
	$title = isset( $input['title'] ) ? sanitize_text_field( wp_unslash( (string) $input['title'] ) ) : '';
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
	$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;
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
	$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;
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
	$page_id = isset( $input['page_id'] ) ? (int) $input['page_id'] : 0;
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
 * 一般設定を更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, string>|WP_Error
 */
function wordpress_mcp_admin_execute_update_general_settings( array $input = array() ) {
	$updated = false;
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

/**
 * 監査ログ画面を描画します。
 *
 * @return void
 */
function wordpress_mcp_admin_render_audit_log_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'wordpress-mcp-admin-tools' ) );
	}

	$logs = wordpress_mcp_admin_get_audit_logs();
	$abilities = wordpress_mcp_admin_get_admin_page_abilities();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'MCP Admin Tools', 'wordpress-mcp-admin-tools' ); ?></h1>
		<p><?php echo esc_html__( 'Use this page to review what this plugin can do through MCP and to inspect the recent activity log in one place.', 'wordpress-mcp-admin-tools' ); ?></p>

		<h2><?php echo esc_html__( 'What You Can Do', 'wordpress-mcp-admin-tools' ); ?></h2>
		<p><?php echo esc_html__( 'The plugin publishes the following administrative actions to MCP clients.', 'wordpress-mcp-admin-tools' ); ?></p>
		<ul class="ul-disc">
			<li><?php echo esc_html__( 'Create posts and pages.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Update existing posts and pages.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Delete posts or move them to the trash.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Update the site title and tagline.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Review the audit log of MCP-triggered actions.', 'wordpress-mcp-admin-tools' ); ?></li>
		</ul>

		<h2><?php echo esc_html__( 'Published Abilities', 'wordpress-mcp-admin-tools' ); ?></h2>
		<p><?php echo esc_html__( 'These abilities are exposed under the wordpress-mcp-admin/* namespace.', 'wordpress-mcp-admin-tools' ); ?></p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php echo esc_html__( 'Ability', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Purpose', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Required Capability', 'wordpress-mcp-admin-tools' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $abilities as $ability ) : ?>
					<tr>
						<td><?php echo esc_html( $ability['name'] ); ?></td>
						<td><?php echo esc_html( $ability['description'] ); ?></td>
						<td><?php echo esc_html( $ability['capability'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2><?php echo esc_html__( 'Activity Log', 'wordpress-mcp-admin-tools' ); ?></h2>
		<p><?php echo esc_html__( 'Displays a history of operations executed through MCP clients. Up to 50 recent entries are kept.', 'wordpress-mcp-admin-tools' ); ?></p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php echo esc_html__( 'Timestamp', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Ability', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Result', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Target', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Input Summary', 'wordpress-mcp-admin-tools' ); ?></th>
					<th scope="col"><?php echo esc_html__( 'Error', 'wordpress-mcp-admin-tools' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $logs ) ) : ?>
					<tr>
						<td colspan="6"><?php echo esc_html__( 'No audit log entries yet.', 'wordpress-mcp-admin-tools' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $logs as $log_entry ) : ?>
						<tr>
							<td><?php echo esc_html( wordpress_mcp_admin_format_audit_timestamp( $log_entry ) ); ?></td>
							<td><?php echo esc_html( isset( $log_entry['ability'] ) ? (string) $log_entry['ability'] : '' ); ?></td>
							<td><?php echo esc_html( ! empty( $log_entry['success'] ) ? __( 'Success', 'wordpress-mcp-admin-tools' ) : __( 'Failed', 'wordpress-mcp-admin-tools' ) ); ?></td>
							<td><?php echo esc_html( wordpress_mcp_admin_format_audit_target( $log_entry ) ); ?></td>
							<td><?php echo esc_html( isset( $log_entry['input_summary'] ) ? (string) $log_entry['input_summary'] : '' ); ?></td>
							<td><?php echo esc_html( wordpress_mcp_admin_format_audit_error( $log_entry ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * 管理画面で表示する Ability 一覧を取得します。
 *
 * @return array<int, array<string, string>>
 */
function wordpress_mcp_admin_get_admin_page_abilities(): array {
	return array(
		array(
			'name'        => 'wordpress-mcp-admin/create-post',
			'description' => __( 'Create a new post with the provided content.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_posts',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-post',
			'description' => __( 'Update the title, content, excerpt, or status of an existing post.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_posts',
		),
		array(
			'name'        => 'wordpress-mcp-admin/delete-post',
			'description' => __( 'Delete an existing post or move it to the trash.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'delete_posts',
		),
		array(
			'name'        => 'wordpress-mcp-admin/create-page',
			'description' => __( 'Create a new page with the provided content.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_pages',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-page',
			'description' => __( 'Update the title, content, excerpt, or status of an existing page.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_pages',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-general-settings',
			'description' => __( 'Update the site title and tagline.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-audit-log',
			'description' => __( 'Retrieve the audit log for ability executions.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
	);
}

/**
 * 監査ログのタイムスタンプを WordPress の表示設定に合わせて整形します。
 *
 * @param array<string, mixed> $log_entry ログエントリー。
 * @return string
 */
function wordpress_mcp_admin_format_audit_timestamp( array $log_entry ): string {
	$timestamp = isset( $log_entry['timestamp'] ) ? (string) $log_entry['timestamp'] : '';

	if ( '' === $timestamp ) {
		return '';
	}

	$date_time = date_create_immutable_from_format( 'Y-m-d H:i:s', $timestamp, new DateTimeZone( 'UTC' ) );

	if ( false === $date_time ) {
		return $timestamp;
	}

	return wp_date(
		get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
		$date_time->getTimestamp(),
		wp_timezone()
	);
}

/**
 * 監査ログの対象表示文字列を生成します。
 *
 * @param array<string, mixed> $log_entry ログエントリー。
 * @return string
 */
function wordpress_mcp_admin_format_audit_target( array $log_entry ): string {
	$target_type = isset( $log_entry['target_type'] ) ? (string) $log_entry['target_type'] : '';
	$target_id   = isset( $log_entry['target_id'] ) ? (int) $log_entry['target_id'] : 0;

	if ( '' === $target_type && 0 === $target_id ) {
		return '';
	}

	if ( $target_id > 0 ) {
		return sprintf( '%s #%d', $target_type, $target_id );
	}

	return $target_type;
}

/**
 * 監査ログのエラー表示文字列を生成します。
 *
 * @param array<string, mixed> $log_entry ログエントリー。
 * @return string
 */
function wordpress_mcp_admin_format_audit_error( array $log_entry ): string {
	$error_code    = isset( $log_entry['error_code'] ) ? (string) $log_entry['error_code'] : '';
	$error_message = isset( $log_entry['error_message'] ) ? (string) $log_entry['error_message'] : '';

	if ( '' === $error_code && '' === $error_message ) {
		return '';
	}

	if ( '' !== $error_code && '' !== $error_message ) {
		return $error_code . ': ' . $error_message;
	}

	return '' !== $error_message ? $error_message : $error_code;
}