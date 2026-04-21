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

if ( ! defined( 'WORDPRESS_MCP_ADMIN_TOOLS_FILE' ) ) {
	define( 'WORDPRESS_MCP_ADMIN_TOOLS_FILE', __FILE__ );
}

if ( ! defined( 'WORDPRESS_MCP_ADMIN_TOOLS_DIR' ) ) {
	define( 'WORDPRESS_MCP_ADMIN_TOOLS_DIR', plugin_dir_path( WORDPRESS_MCP_ADMIN_TOOLS_FILE ) );
}

if ( ! defined( 'WORDPRESS_MCP_ADMIN_TOOLS_BASENAME' ) ) {
	define( 'WORDPRESS_MCP_ADMIN_TOOLS_BASENAME', plugin_basename( WORDPRESS_MCP_ADMIN_TOOLS_FILE ) );
}

require_once WORDPRESS_MCP_ADMIN_TOOLS_DIR . 'includes/support.php';
require_once WORDPRESS_MCP_ADMIN_TOOLS_DIR . 'includes/handlers.php';
require_once WORDPRESS_MCP_ADMIN_TOOLS_DIR . 'includes/admin.php';
require_once WORDPRESS_MCP_ADMIN_TOOLS_DIR . 'includes/abilities.php';

/**
 * 翻訳ファイルを読み込みます。
 *
 * @return void
 */
function wordpress_mcp_admin_load_textdomain(): void {
	load_plugin_textdomain( 'wordpress-mcp-admin-tools', false, dirname( plugin_basename( WORDPRESS_MCP_ADMIN_TOOLS_FILE ) ) . '/languages' );
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
