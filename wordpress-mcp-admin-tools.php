<?php
/**
 * Plugin Name: MCP Admin Tools for WordPress
 * Plugin URI: https://example.com/
 * Description: Registers abilities for managing WordPress content and viewing audit logs from MCP clients.
 * Version: 0.4.6
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
		'wordpress-mcp-admin/edit-page-blocks',
		array(
			'label'               => __( 'Edit Page Blocks', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Replace the block content of an existing page while keeping it editable in the block editor.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_edit_page_blocks',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_pages',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'page_id' => array(
						'type'        => 'integer',
						'description' => __( 'Page ID whose block content should be replaced.', 'wordpress-mcp-admin-tools' ),
					),
					'content' => array(
						'type'        => 'string',
						'description' => __( 'Full block markup to save into the page content.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'page_id', 'content' ),
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
		'wordpress-mcp-admin/edit-post-blocks',
		array(
			'label'               => __( 'Edit Post Blocks', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Replace the block content of an existing post or custom post type entry while keeping it editable in the block editor.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_edit_post_blocks',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_posts',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_id' => array(
						'type'        => 'integer',
						'description' => __( 'Post ID whose block content should be replaced.', 'wordpress-mcp-admin-tools' ),
					),
					'content' => array(
						'type'        => 'string',
						'description' => __( 'Full block markup to save into the post content.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_id', 'content' ),
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
		'wordpress-mcp-admin/get-page-blocks',
		array(
			'label'               => __( 'Get Page Blocks', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve the current block content of an existing page.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_page_blocks',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_pages',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'page_id' => array(
						'type'        => 'integer',
						'description' => __( 'Page ID whose current block content should be retrieved.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'page_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'page_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Requested page ID.', 'wordpress-mcp-admin-tools' ),
					),
					'title'     => array(
						'type'        => 'string',
						'description' => __( 'Current page title.', 'wordpress-mcp-admin-tools' ),
					),
					'status'    => array(
						'type'        => 'string',
						'description' => __( 'Current page status.', 'wordpress-mcp-admin-tools' ),
					),
					'block_content' => array(
						'type'        => 'string',
						'description' => __( 'Current page block markup.', 'wordpress-mcp-admin-tools' ),
					),
					'parsed_blocks' => array(
						'type'        => 'array',
						'description' => __( 'Parsed block structure of the current page content.', 'wordpress-mcp-admin-tools' ),
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
					'readonly'   => true,
					'destructive' => false,
					'idempotent' => true,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/get-post-blocks',
		array(
			'label'               => __( 'Get Post Blocks', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve the current block content of an existing post or custom post type entry.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_post_blocks',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_posts',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_id' => array(
						'type'        => 'integer',
						'description' => __( 'Post ID whose current block content should be retrieved.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'       => array(
						'type'        => 'integer',
						'description' => __( 'Requested post ID.', 'wordpress-mcp-admin-tools' ),
					),
					'post_type'     => array(
						'type'        => 'string',
						'description' => __( 'Current post type.', 'wordpress-mcp-admin-tools' ),
					),
					'title'         => array(
						'type'        => 'string',
						'description' => __( 'Current post title.', 'wordpress-mcp-admin-tools' ),
					),
					'status'        => array(
						'type'        => 'string',
						'description' => __( 'Current post status.', 'wordpress-mcp-admin-tools' ),
					),
					'block_content' => array(
						'type'        => 'string',
						'description' => __( 'Current post block markup.', 'wordpress-mcp-admin-tools' ),
					),
					'parsed_blocks' => array(
						'type'        => 'array',
						'description' => __( 'Parsed block structure of the current post content.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link'     => array(
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
					'readonly'   => true,
					'destructive' => false,
					'idempotent' => true,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/edit-page-design',
		array(
			'label'               => __( 'Edit Page Design', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Create or replace a page-specific block template in the active block theme.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_edit_page_design',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'page_id' => array(
						'type'        => 'integer',
						'description' => __( 'Page ID whose design should be changed.', 'wordpress-mcp-admin-tools' ),
					),
					'content' => array(
						'type'        => 'string',
						'description' => __( 'Full block template content to save for the page-specific design.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'page_id', 'content' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'page_id'       => array(
						'type'        => 'integer',
						'description' => __( 'Page ID whose design was updated.', 'wordpress-mcp-admin-tools' ),
					),
					'theme'         => array(
						'type'        => 'string',
						'description' => __( 'Active theme stylesheet identifier.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path' => array(
						'type'        => 'string',
						'description' => __( 'Relative path of the saved page template file.', 'wordpress-mcp-admin-tools' ),
					),
					'bytes_written' => array(
						'type'        => 'integer',
						'description' => __( 'Number of bytes written to the page template file.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link'     => array(
						'type'        => 'string',
						'description' => __( 'Admin theme editor link when available.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/install-theme',
		array(
			'label'               => __( 'Install Theme', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Install a theme from WordPress.org and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_install_theme',
			'permission_callback' => 'wordpress_mcp_admin_can_install_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'slug'     => array(
						'type'        => 'string',
						'description' => __( 'WordPress.org theme slug to install.', 'wordpress-mcp-admin-tools' ),
					),
					'activate' => array(
						'type'        => 'boolean',
						'description' => __( 'Activate the theme after installation when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'slug' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'stylesheet' => array(
						'type'        => 'string',
						'description' => __( 'Installed theme stylesheet identifier.', 'wordpress-mcp-admin-tools' ),
					),
					'name'       => array(
						'type'        => 'string',
						'description' => __( 'Installed theme display name.', 'wordpress-mcp-admin-tools' ),
					),
					'version'    => array(
						'type'        => 'string',
						'description' => __( 'Installed theme version.', 'wordpress-mcp-admin-tools' ),
					),
					'activated'  => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the theme is now active.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/create-theme',
		array(
			'label'               => __( 'Create Theme', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Create a new classic or block theme scaffold and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_create_theme',
			'permission_callback' => 'wordpress_mcp_admin_can_create_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'slug'        => array(
						'type'        => 'string',
						'description' => __( 'Directory slug for the new theme.', 'wordpress-mcp-admin-tools' ),
					),
					'name'        => array(
						'type'        => 'string',
						'description' => __( 'Display name of the new theme.', 'wordpress-mcp-admin-tools' ),
					),
					'description' => array(
						'type'        => 'string',
						'description' => __( 'Theme description.', 'wordpress-mcp-admin-tools' ),
					),
					'author'      => array(
						'type'        => 'string',
						'description' => __( 'Theme author name.', 'wordpress-mcp-admin-tools' ),
					),
					'author_uri'  => array(
						'type'        => 'string',
						'description' => __( 'Theme author URL.', 'wordpress-mcp-admin-tools' ),
					),
					'theme_uri'   => array(
						'type'        => 'string',
						'description' => __( 'Theme homepage URL.', 'wordpress-mcp-admin-tools' ),
					),
					'version'     => array(
						'type'        => 'string',
						'description' => __( 'Theme version string. Defaults to 1.0.0.', 'wordpress-mcp-admin-tools' ),
					),
					'type'        => array(
						'type'        => 'string',
						'enum'        => array( 'classic', 'block' ),
						'description' => __( 'Theme scaffold type.', 'wordpress-mcp-admin-tools' ),
					),
					'template'    => array(
						'type'        => 'string',
						'description' => __( 'Optional parent theme stylesheet for child themes.', 'wordpress-mcp-admin-tools' ),
					),
					'activate'    => array(
						'type'        => 'boolean',
						'description' => __( 'Activate the theme after creation when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'slug', 'name' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'stylesheet' => array(
						'type'        => 'string',
						'description' => __( 'Created theme stylesheet identifier.', 'wordpress-mcp-admin-tools' ),
					),
					'name'       => array(
						'type'        => 'string',
						'description' => __( 'Created theme display name.', 'wordpress-mcp-admin-tools' ),
					),
					'type'       => array(
						'type'        => 'string',
						'description' => __( 'Created theme scaffold type.', 'wordpress-mcp-admin-tools' ),
					),
					'activated'  => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the created theme is now active.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/edit-theme',
		array(
			'label'               => __( 'Edit Theme', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update or create an allowed file inside an installed theme.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_edit_theme',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'theme'          => array(
						'type'        => 'string',
						'description' => __( 'Theme stylesheet identifier to edit.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path'  => array(
						'type'        => 'string',
						'description' => __( 'Relative path inside the theme directory.', 'wordpress-mcp-admin-tools' ),
					),
					'content'        => array(
						'type'        => 'string',
						'description' => __( 'Full replacement file content.', 'wordpress-mcp-admin-tools' ),
					),
					'create_missing' => array(
						'type'        => 'boolean',
						'description' => __( 'Create the file when it does not already exist.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'theme', 'relative_path', 'content' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'theme'         => array(
						'type'        => 'string',
						'description' => __( 'Edited theme stylesheet identifier.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path' => array(
						'type'        => 'string',
						'description' => __( 'Updated relative file path.', 'wordpress-mcp-admin-tools' ),
					),
					'bytes_written' => array(
						'type'        => 'integer',
						'description' => __( 'Number of bytes written to the file.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link'     => array(
						'type'        => 'string',
						'description' => __( 'Admin theme editor link when available.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/get-themes',
		array(
			'label'               => __( 'Get Themes', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve installed themes and their activation state.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_themes',
			'permission_callback' => 'wordpress_mcp_admin_can_view_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'include_inactive' => array(
						'type'        => 'boolean',
						'description' => __( 'Return inactive themes as well. Defaults to true.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'themes' => array(
						'type'        => 'array',
						'description' => __( 'Installed themes.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'stylesheet'   => array( 'type' => 'string' ),
								'name'         => array( 'type' => 'string' ),
								'version'      => array( 'type' => 'string' ),
								'status'       => array( 'type' => 'string' ),
								'parent_theme' => array( 'type' => 'string' ),
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

	wp_register_ability(
		'wordpress-mcp-admin/delete-theme',
		array(
			'label'               => __( 'Delete Theme', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Delete an installed inactive theme.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_delete_theme',
			'permission_callback' => 'wordpress_mcp_admin_can_delete_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'theme' => array(
						'type'        => 'string',
						'description' => __( 'Theme stylesheet identifier to delete.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'theme' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'theme'   => array(
						'type'        => 'string',
						'description' => __( 'Deleted theme stylesheet identifier.', 'wordpress-mcp-admin-tools' ),
					),
					'deleted' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the theme was deleted.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/install-plugin',
		array(
			'label'               => __( 'Install Plugin', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Install a plugin from WordPress.org and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_install_plugin',
			'permission_callback' => 'wordpress_mcp_admin_can_install_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'slug'     => array(
						'type'        => 'string',
						'description' => __( 'WordPress.org plugin slug to install.', 'wordpress-mcp-admin-tools' ),
					),
					'activate' => array(
						'type'        => 'boolean',
						'description' => __( 'Activate the plugin after installation when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'slug' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'    => array(
						'type'        => 'string',
						'description' => __( 'Installed plugin basename.', 'wordpress-mcp-admin-tools' ),
					),
					'name'      => array(
						'type'        => 'string',
						'description' => __( 'Installed plugin display name.', 'wordpress-mcp-admin-tools' ),
					),
					'version'   => array(
						'type'        => 'string',
						'description' => __( 'Installed plugin version.', 'wordpress-mcp-admin-tools' ),
					),
					'activated' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the plugin is now active.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/create-plugin',
		array(
			'label'               => __( 'Create Plugin', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Create a new plugin scaffold and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_create_plugin',
			'permission_callback' => 'wordpress_mcp_admin_can_create_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'slug'        => array(
						'type'        => 'string',
						'description' => __( 'Directory slug for the new plugin.', 'wordpress-mcp-admin-tools' ),
					),
					'name'        => array(
						'type'        => 'string',
						'description' => __( 'Display name of the new plugin.', 'wordpress-mcp-admin-tools' ),
					),
					'description' => array(
						'type'        => 'string',
						'description' => __( 'Plugin description.', 'wordpress-mcp-admin-tools' ),
					),
					'author'      => array(
						'type'        => 'string',
						'description' => __( 'Plugin author name.', 'wordpress-mcp-admin-tools' ),
					),
					'author_uri'  => array(
						'type'        => 'string',
						'description' => __( 'Plugin author URL.', 'wordpress-mcp-admin-tools' ),
					),
					'plugin_uri'  => array(
						'type'        => 'string',
						'description' => __( 'Plugin homepage URL.', 'wordpress-mcp-admin-tools' ),
					),
					'version'     => array(
						'type'        => 'string',
						'description' => __( 'Plugin version string. Defaults to 1.0.0.', 'wordpress-mcp-admin-tools' ),
					),
					'activate'    => array(
						'type'        => 'boolean',
						'description' => __( 'Activate the plugin after creation when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'slug', 'name' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'    => array(
						'type'        => 'string',
						'description' => __( 'Created plugin basename.', 'wordpress-mcp-admin-tools' ),
					),
					'name'      => array(
						'type'        => 'string',
						'description' => __( 'Created plugin display name.', 'wordpress-mcp-admin-tools' ),
					),
					'activated' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the created plugin is now active.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/update-plugin',
		array(
			'label'               => __( 'Update Plugin', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update or create an allowed file inside an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_plugin',
			'permission_callback' => 'wordpress_mcp_admin_can_update_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'         => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to update.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path'  => array(
						'type'        => 'string',
						'description' => __( 'Relative path inside the plugin directory.', 'wordpress-mcp-admin-tools' ),
					),
					'content'        => array(
						'type'        => 'string',
						'description' => __( 'Full replacement file content.', 'wordpress-mcp-admin-tools' ),
					),
					'create_missing' => array(
						'type'        => 'boolean',
						'description' => __( 'Create the file when it does not already exist.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin', 'relative_path', 'content' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'        => array(
						'type'        => 'string',
						'description' => __( 'Updated plugin basename.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path' => array(
						'type'        => 'string',
						'description' => __( 'Updated relative file path.', 'wordpress-mcp-admin-tools' ),
					),
					'bytes_written' => array(
						'type'        => 'integer',
						'description' => __( 'Number of bytes written to the file.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link'     => array(
						'type'        => 'string',
						'description' => __( 'Admin plugin editor link when available.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/get-plugins',
		array(
			'label'               => __( 'Get Plugins', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve installed plugins and their activation state.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_plugins',
			'permission_callback' => 'wordpress_mcp_admin_can_view_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'include_inactive' => array(
						'type'        => 'boolean',
						'description' => __( 'Return inactive plugins as well. Defaults to true.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugins' => array(
						'type'        => 'array',
						'description' => __( 'Installed plugins.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'plugin'    => array( 'type' => 'string' ),
								'name'      => array( 'type' => 'string' ),
								'version'   => array( 'type' => 'string' ),
								'status'    => array( 'type' => 'string' ),
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

	wp_register_ability(
		'wordpress-mcp-admin/delete-plugin',
		array(
			'label'               => __( 'Delete Plugin', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Delete an installed inactive plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_delete_plugin',
			'permission_callback' => 'wordpress_mcp_admin_can_delete_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin' => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to delete.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'   => array(
						'type'        => 'string',
						'description' => __( 'Deleted plugin basename.', 'wordpress-mcp-admin-tools' ),
					),
					'deleted'  => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the plugin was deleted.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/activate-plugin',
		array(
			'label'               => __( 'Activate Plugin', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Activate an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_activate_plugin',
			'permission_callback' => 'wordpress_mcp_admin_can_activate_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin' => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to activate.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'    => array(
						'type'        => 'string',
						'description' => __( 'Activated plugin basename.', 'wordpress-mcp-admin-tools' ),
					),
					'activated' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the plugin is active after the operation.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/deactivate-plugin',
		array(
			'label'               => __( 'Deactivate Plugin', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Deactivate an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_deactivate_plugin',
			'permission_callback' => 'wordpress_mcp_admin_can_activate_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin' => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to deactivate.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'      => array(
						'type'        => 'string',
						'description' => __( 'Deactivated plugin basename.', 'wordpress-mcp-admin-tools' ),
					),
					'deactivated' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether the plugin is inactive after the operation.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/enable-plugin-auto-update',
		array(
			'label'               => __( 'Enable Plugin Auto-Update', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Enable auto-updates for an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_enable_plugin_auto_update',
			'permission_callback' => 'wordpress_mcp_admin_can_activate_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin' => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to enable auto-updates for.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'              => array(
						'type'        => 'string',
						'description' => __( 'Plugin basename with auto-updates enabled.', 'wordpress-mcp-admin-tools' ),
					),
					'auto_update_enabled' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether plugin auto-updates are enabled after the operation.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/disable-plugin-auto-update',
		array(
			'label'               => __( 'Disable Plugin Auto-Update', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Disable auto-updates for an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_disable_plugin_auto_update',
			'permission_callback' => 'wordpress_mcp_admin_can_activate_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin' => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to disable auto-updates for.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'              => array(
						'type'        => 'string',
						'description' => __( 'Plugin basename with auto-updates disabled.', 'wordpress-mcp-admin-tools' ),
					),
					'auto_update_enabled' => array(
						'type'        => 'boolean',
						'description' => __( 'Whether plugin auto-updates remain enabled after the operation.', 'wordpress-mcp-admin-tools' ),
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
		return current_user_can( 'edit_posts' ) || current_user_can( 'edit_post', (int) $input['post_id'] );
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
		return current_user_can( 'edit_pages' ) || current_user_can( 'edit_post', (int) $input['page_id'] );
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
 * テーマインストール権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_install_themes( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'install_themes' );
}

/**
 * テーマ作成権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_create_themes( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'install_themes' ) || current_user_can( 'edit_themes' );
}

/**
 * テーマ編集権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_edit_themes( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'edit_themes' );
}

/**
 * テーマ一覧表示権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_view_themes( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'switch_themes' ) || current_user_can( 'edit_theme_options' );
}

/**
 * テーマ削除権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_delete_themes( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'delete_themes' );
}

/**
 * プラグインインストール権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_install_plugins( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'install_plugins' );
}

/**
 * プラグイン作成権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_create_plugins( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'install_plugins' ) || current_user_can( 'edit_plugins' );
}

/**
 * プラグイン更新権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_update_plugins( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'edit_plugins' );
}

/**
 * プラグイン一覧表示権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_view_plugins( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'activate_plugins' ) || current_user_can( 'install_plugins' );
}

/**
 * プラグイン削除権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_delete_plugins( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'delete_plugins' );
}

/**
 * プラグイン有効化権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_activate_plugins( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'activate_plugins' );
}

/**
 * プラグイン情報を配列に正規化します。
 *
 * @param string $plugin_basename プラグイン basename。
 * @param array<string, mixed> $plugin_data プラグインデータ。
 * @return array<string, string>
 */
function wordpress_mcp_admin_format_plugin_record( string $plugin_basename, array $plugin_data ): array {
	$status = 'inactive';

	if ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( $plugin_basename ) ) {
		$status = 'network-active';
	} elseif ( is_plugin_active( $plugin_basename ) ) {
		$status = 'active';
	}

	return array(
		'plugin'  => $plugin_basename,
		'name'    => isset( $plugin_data['Name'] ) ? (string) $plugin_data['Name'] : '',
		'version' => isset( $plugin_data['Version'] ) ? (string) $plugin_data['Version'] : '',
		'status'  => $status,
	);
}

/**
 * プラグイン識別子を検証して返します。
 *
 * @param mixed $value 入力値。
 * @param string $error_code エラーコード。
 * @param string $error_message エラーメッセージ。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_validate_plugin_identifier( $value, string $error_code, string $error_message ) {
	$identifier = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	$identifier = trim( str_replace( '\\', '/', $identifier ) );

	if ( '' === $identifier ) {
		return new WP_Error( $error_code, $error_message );
	}

	return $identifier;
}

/**
 * プラグイン編集対象パスを正規化します。
 *
 * @param string $relative_path 相対パス。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_normalize_plugin_relative_path( string $relative_path ) {
	$normalized_path = str_replace( '\\', '/', trim( $relative_path ) );
	$normalized_path = ltrim( $normalized_path, '/' );

	if ( '' === $normalized_path || str_contains( $normalized_path, '../' ) || str_contains( $normalized_path, '..\\' ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_plugin_path',
			__( 'A valid relative_path inside the plugin directory is required.', 'wordpress-mcp-admin-tools' )
		);
	}

	$allowed_extensions = array( 'css', 'html', 'js', 'json', 'jsx', 'md', 'php', 'svg', 'txt', 'ts', 'tsx' );
	$extension          = strtolower( pathinfo( $normalized_path, PATHINFO_EXTENSION ) );

	if ( '' === $extension || ! in_array( $extension, $allowed_extensions, true ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_disallowed_plugin_path',
			__( 'Only php, js, ts, jsx, tsx, css, json, html, svg, md, and txt files can be edited.', 'wordpress-mcp-admin-tools' )
		);
	}

	return $normalized_path;
}

/**
 * プラグイン情報を解決します。
 *
 * @param string $identifier プラグイン slug または basename。
 * @return array<string, string>|WP_Error
 */
function wordpress_mcp_admin_resolve_plugin_entry( string $identifier ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = get_plugins();

	if ( isset( $plugins[ $identifier ] ) ) {
		$directory = dirname( $identifier );

		return array(
			'plugin'    => $identifier,
			'directory' => '.' === $directory ? '' : $directory,
			'file'      => basename( $identifier ),
		);
	}

	$normalized_identifier = sanitize_key( basename( $identifier, '.php' ) );

	foreach ( array_keys( $plugins ) as $plugin_file ) {
		$directory = dirname( $plugin_file );
		$file_slug = sanitize_key( basename( $plugin_file, '.php' ) );

		if ( $plugin_file === $normalized_identifier || sanitize_key( $plugin_file ) === $normalized_identifier ) {
			return array(
				'plugin'    => $plugin_file,
				'directory' => '.' === $directory ? '' : $directory,
				'file'      => basename( $plugin_file ),
			);
		}

		if ( sanitize_key( basename( $directory ) ) === $normalized_identifier || $file_slug === $normalized_identifier ) {
			return array(
				'plugin'    => $plugin_file,
				'directory' => '.' === $directory ? '' : $directory,
				'file'      => basename( $plugin_file ),
			);
		}
	}

	return new WP_Error(
		'wordpress_mcp_admin_plugin_not_found',
		__( 'The specified plugin could not be found.', 'wordpress-mcp-admin-tools' )
	);
}

/**
 * プラグインファイルの管理画面リンクを返します。
 *
 * @param string $plugin_basename プラグイン basename。
 * @return string
 */
function wordpress_mcp_admin_get_plugin_editor_link( string $plugin_basename ): string {
	return (string) add_query_arg(
		array(
			'file'   => $plugin_basename,
			'plugin' => $plugin_basename,
		),
		admin_url( 'plugin-editor.php' )
	);
}

/**
 * プラグインヘッダーを生成します。
 *
 * @param array<string, string> $plugin_data プラグイン情報。
 * @return string
 */
function wordpress_mcp_admin_build_plugin_header( array $plugin_data ): string {
	$header_lines = array(
		'<?php',
		'/**',
		' * Plugin Name: ' . $plugin_data['name'],
		' * Description: ' . $plugin_data['description'],
		' * Version: ' . $plugin_data['version'],
	);

	if ( '' !== $plugin_data['author'] ) {
		$header_lines[] = ' * Author: ' . $plugin_data['author'];
	}

	if ( '' !== $plugin_data['author_uri'] ) {
		$header_lines[] = ' * Author URI: ' . $plugin_data['author_uri'];
	}

	if ( '' !== $plugin_data['plugin_uri'] ) {
		$header_lines[] = ' * Plugin URI: ' . $plugin_data['plugin_uri'];
	}

	$header_lines[] = ' * Text Domain: ' . $plugin_data['slug'];
	$header_lines[] = ' */';
	$header_lines[] = '';
	$header_lines[] = 'declare( strict_types = 1 );';
	$header_lines[] = '';
	$header_lines[] = "if ( ! defined( 'ABSPATH' ) ) {";
	$header_lines[] = "\texit;";
	$header_lines[] = '}';
	$header_lines[] = '';

	return implode( "\n", $header_lines );
}

/**
 * 新規プラグイン用のファイル一覧を生成します。
 *
 * @param array<string, string> $plugin_data プラグイン情報。
 * @return array<string, string>
 */
function wordpress_mcp_admin_get_plugin_scaffold_files( array $plugin_data ): array {
	$main_file = $plugin_data['slug'] . '.php';

	return array(
		$main_file    => wordpress_mcp_admin_build_plugin_header( $plugin_data ) . "add_action( 'init', static function (): void {\n\t// Plugin bootstrap.\n} );\n",
		'readme.txt'  => "=== " . $plugin_data['name'] . " ===\nContributors: admin\nRequires at least: 6.9\nTested up to: 6.9\nStable tag: " . $plugin_data['version'] . "\n\n" . $plugin_data['description'] . "\n",
		'includes/bootstrap.php' => "<?php\n\ndeclare( strict_types = 1 );\n",
	);
}

/**
 * テーマ情報を配列に正規化します。
 *
 * @param WP_Theme $theme テーマオブジェクト。
 * @return array<string, string>
 */
function wordpress_mcp_admin_format_theme_record( WP_Theme $theme ): array {
	$parent = $theme->parent();
	$status = $theme->get_stylesheet() === get_stylesheet() ? 'active' : 'inactive';

	return array(
		'stylesheet'   => (string) $theme->get_stylesheet(),
		'name'         => (string) $theme->get( 'Name' ),
		'version'      => (string) $theme->get( 'Version' ),
		'status'       => $status,
		'parent_theme' => $parent instanceof WP_Theme ? (string) $parent->get_stylesheet() : '',
	);
}

/**
 * テーマスラッグを検証して返します。
 *
 * @param mixed $value 入力値。
 * @param string $error_code エラーコード。
 * @param string $error_message エラーメッセージ。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_validate_theme_slug( $value, string $error_code, string $error_message ) {
	$slug = sanitize_key( is_scalar( $value ) ? (string) $value : '' );

	if ( '' === $slug ) {
		return new WP_Error( $error_code, $error_message );
	}

	return $slug;
}

/**
 * テーマ編集対象パスを正規化します。
 *
 * @param string $relative_path 相対パス。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_normalize_theme_relative_path( string $relative_path ) {
	$normalized_path = str_replace( '\\', '/', trim( $relative_path ) );
	$normalized_path = ltrim( $normalized_path, '/' );

	if ( '' === $normalized_path || str_contains( $normalized_path, '../' ) || str_contains( $normalized_path, '..\\' ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_theme_path',
			__( 'A valid relative_path inside the theme directory is required.', 'wordpress-mcp-admin-tools' )
		);
	}

	$allowed_extensions = array( 'css', 'php', 'json', 'html', 'txt' );
	$extension          = strtolower( pathinfo( $normalized_path, PATHINFO_EXTENSION ) );

	if ( '' === $extension || ! in_array( $extension, $allowed_extensions, true ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_disallowed_theme_path',
			__( 'Only css, php, json, html, and txt files can be edited.', 'wordpress-mcp-admin-tools' )
		);
	}

	return $normalized_path;
}

/**
 * テーマファイルの管理画面リンクを返します。
 *
 * @param string $theme_stylesheet テーマ stylesheet。
 * @param string $relative_path 相対パス。
 * @return string
 */
function wordpress_mcp_admin_get_theme_editor_link( string $theme_stylesheet, string $relative_path = '' ): string {
	$query_args = array(
		'theme' => $theme_stylesheet,
	);

	if ( '' !== $relative_path ) {
		$query_args['file'] = $relative_path;
	}

	return (string) add_query_arg( $query_args, admin_url( 'theme-editor.php' ) );
}

/**
 * 固定ページ用の block template 相対パスを返します。
 *
 * @param WP_Post $page 固定ページ。
 * @return string
 */
function wordpress_mcp_admin_get_page_design_relative_path( WP_Post $page ): string {
	$page_slug = sanitize_title( $page->post_name );

	if ( '' !== $page_slug ) {
		return 'templates/page-' . $page_slug . '.html';
	}

	return 'templates/page-' . (int) $page->ID . '.html';
}

/**
 * block 配列を MCP 向けに正規化します。
 *
 * @param array<int, array<string, mixed>> $blocks ブロック配列。
 * @return array<int, array<string, mixed>>
 */
function wordpress_mcp_admin_normalize_parsed_blocks( array $blocks ): array {
	$normalized = array();

	foreach ( $blocks as $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		$normalized[] = array(
			'blockName'    => $block['blockName'] ?? null,
			'attrs'        => isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array(),
			'innerHTML'    => isset( $block['innerHTML'] ) ? (string) $block['innerHTML'] : '',
			'innerContent' => isset( $block['innerContent'] ) && is_array( $block['innerContent'] ) ? $block['innerContent'] : array(),
			'innerBlocks'  => isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] )
				? wordpress_mcp_admin_normalize_parsed_blocks( $block['innerBlocks'] )
				: array(),
		);
	}

	return $normalized;
}

/**
 * 投稿系ブロックデータの返却配列を生成します。
 *
 * @param WP_Post $post 投稿オブジェクト。
 * @param string  $id_key ID キー名。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_build_post_block_response( WP_Post $post, string $id_key = 'post_id' ): array {
	return array(
		$id_key         => (int) $post->ID,
		'post_type'     => (string) $post->post_type,
		'title'         => (string) get_the_title( $post ),
		'status'        => (string) get_post_status( $post ),
		'block_content' => (string) $post->post_content,
		'parsed_blocks' => wordpress_mcp_admin_normalize_parsed_blocks( parse_blocks( (string) $post->post_content ) ),
		'edit_link'     => (string) get_edit_post_link( $post->ID, 'raw' ),
	);
}

/**
 * 投稿系 block 本文を更新します。
 *
 * @param string $ability_name Ability 名。
 * @param int    $post_id      投稿 ID。
 * @param string $content      保存内容。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_update_post_blocks( string $ability_name, int $post_id, string $content ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( array( 'post_id' => $post_id ), array( 'post_id' ) );

	if ( $post_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_id',
			__( 'A valid post_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found',
			__( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	if ( '' === $content ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_missing_post_block_content',
			__( 'Block content is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$updated_post_id = wp_update_post(
		array(
			'ID'           => $post_id,
			'post_type'    => $post->post_type,
			'post_content' => current_user_can( 'unfiltered_html' )
				? $content
				: wp_kses_post( $content ),
		),
		true
	);

	if ( is_wp_error( $updated_post_id ) ) {
		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'post', $post_id, $input_summary, $updated_post_id->get_error_code(), $updated_post_id->get_error_message() );

		return $updated_post_id;
	}

	wordpress_mcp_admin_log_ability_execution( $ability_name, true, 'post', (int) $updated_post_id, $input_summary );

	return array(
		'post_id'   => (int) $updated_post_id,
		'status'    => (string) get_post_status( $updated_post_id ),
		'edit_link' => (string) get_edit_post_link( $updated_post_id, 'raw' ),
	);
}

/**
 * 投稿系 block 本文を取得します。
 *
 * @param string $ability_name Ability 名。
 * @param int    $post_id      投稿 ID。
 * @param string $id_key       返却 ID キー名。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_get_post_blocks_response( string $ability_name, int $post_id, string $id_key = 'post_id' ) {
	$input_summary = wordpress_mcp_admin_build_input_summary( array( $id_key => $post_id ), array( $id_key ) );

	if ( $post_id <= 0 ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_invalid_post_id',
			'page_id' === $id_key ? __( 'A valid page_id is required.', 'wordpress-mcp-admin-tools' ) : __( 'A valid post_id is required.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'post', 0, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		$error = new WP_Error(
			'wordpress_mcp_admin_post_not_found',
			'page_id' === $id_key ? __( 'The specified page could not be found.', 'wordpress-mcp-admin-tools' ) : __( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
		);

		wordpress_mcp_admin_log_ability_execution( $ability_name, false, 'post', $post_id, $input_summary, $error->get_error_code(), $error->get_error_message() );

		return $error;
	}

	wordpress_mcp_admin_log_ability_execution( $ability_name, true, 'post', $post_id, $input_summary );

	return wordpress_mcp_admin_build_post_block_response( $post, $id_key );
}

/**
 * テーマヘッダーを生成します。
 *
 * @param array<string, string> $theme_data テーマ情報。
 * @return string
 */
function wordpress_mcp_admin_build_theme_stylesheet_header( array $theme_data ): string {
	$header_lines = array(
		'/*',
		'Theme Name: ' . $theme_data['name'],
		'Text Domain: ' . $theme_data['slug'],
		'Version: ' . $theme_data['version'],
	);

	if ( '' !== $theme_data['description'] ) {
		$header_lines[] = 'Description: ' . $theme_data['description'];
	}

	if ( '' !== $theme_data['author'] ) {
		$header_lines[] = 'Author: ' . $theme_data['author'];
	}

	if ( '' !== $theme_data['author_uri'] ) {
		$header_lines[] = 'Author URI: ' . $theme_data['author_uri'];
	}

	if ( '' !== $theme_data['theme_uri'] ) {
		$header_lines[] = 'Theme URI: ' . $theme_data['theme_uri'];
	}

	if ( '' !== $theme_data['template'] ) {
		$header_lines[] = 'Template: ' . $theme_data['template'];
	}

	$header_lines[] = '*/';
	$header_lines[] = '';

	return implode( "\n", $header_lines );
}

/**
 * 新規テーマ用のファイル一覧を生成します。
 *
 * @param array<string, string> $theme_data テーマ情報。
 * @return array<string, string>
 */
function wordpress_mcp_admin_get_theme_scaffold_files( array $theme_data ): array {
	$style_header = wordpress_mcp_admin_build_theme_stylesheet_header( $theme_data );
	$style_body   = "body {\n\tmargin: 0;\n\tfont-family: sans-serif;\n}\n";

	if ( 'block' === $theme_data['type'] ) {
		return array(
			'style.css'            => $style_header . $style_body,
			'functions.php'        => "<?php\n\ndeclare( strict_types = 1 );\n",
			'theme.json'           => wp_json_encode(
				array(
					'version'  => 2,
					'settings' => (object) array(),
					'styles'   => (object) array(),
				),
				JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			) . "\n",
			'templates/index.html' => "<!-- wp:group {\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group\"><!-- wp:post-content /--></div>\n<!-- /wp:group -->\n",
		);
	}

	return array(
		'style.css'     => $style_header . $style_body,
		'functions.php' => "<?php\n\ndeclare( strict_types = 1 );\n",
		'index.php'     => "<?php\n\ndeclare( strict_types = 1 );\n\nget_header();\n\nif ( have_posts() ) {\n\twhile ( have_posts() ) {\n\t\tthe_post();\n\t\tthe_content();\n\t}\n}\n\nget_footer();\n",
	);
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

	if ( plugin_basename( __FILE__ ) === $plugin['plugin'] ) {
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
			<li><?php echo esc_html__( 'Read and replace page, post, and custom post type block layouts while keeping them editable in the block editor.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Change page-specific designs by editing files in the active block theme.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Delete posts or move them to the trash.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Install, create, edit, list, and delete themes.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Install, create, update, activate, deactivate, list, and delete plugins.', 'wordpress-mcp-admin-tools' ); ?></li>
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
			'name'        => 'wordpress-mcp-admin/edit-page-blocks',
			'description' => __( 'Replace the block content of an existing page while keeping it editable in the block editor.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_pages',
		),
		array(
			'name'        => 'wordpress-mcp-admin/edit-post-blocks',
			'description' => __( 'Replace the block content of an existing post or custom post type entry while keeping it editable in the block editor.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_posts',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-page-blocks',
			'description' => __( 'Retrieve the current block content of an existing page.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_pages',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-post-blocks',
			'description' => __( 'Retrieve the current block content of an existing post or custom post type entry.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_posts',
		),
		array(
			'name'        => 'wordpress-mcp-admin/edit-page-design',
			'description' => __( 'Create or replace a page-specific block template in the active block theme.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-general-settings',
			'description' => __( 'Update the site title and tagline.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/install-theme',
			'description' => __( 'Install a theme from WordPress.org and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'install_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/create-theme',
			'description' => __( 'Create a new classic or block theme scaffold and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'install_themes or edit_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/edit-theme',
			'description' => __( 'Update or create an allowed file inside an installed theme.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-themes',
			'description' => __( 'Retrieve installed themes and their activation state.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'switch_themes or edit_theme_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/delete-theme',
			'description' => __( 'Delete an installed inactive theme.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'delete_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/install-plugin',
			'description' => __( 'Install a plugin from WordPress.org and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'install_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/create-plugin',
			'description' => __( 'Create a new plugin scaffold and optionally activate it.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'install_plugins or edit_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-plugin',
			'description' => __( 'Update or create an allowed file inside an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-plugins',
			'description' => __( 'Retrieve installed plugins and their activation state.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'activate_plugins or install_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/delete-plugin',
			'description' => __( 'Delete an installed inactive plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'delete_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/activate-plugin',
			'description' => __( 'Activate an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'activate_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/deactivate-plugin',
			'description' => __( 'Deactivate an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'activate_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/enable-plugin-auto-update',
			'description' => __( 'Enable auto-updates for an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'activate_plugins',
		),
		array(
			'name'        => 'wordpress-mcp-admin/disable-plugin-auto-update',
			'description' => __( 'Disable auto-updates for an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'activate_plugins',
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