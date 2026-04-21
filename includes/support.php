<?php

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	$logs         = get_option( 'wordpress_mcp_admin_audit_log', null );
	$legacy_logs  = get_option( 'noveltool_mcp_admin_audit_log', null );
	$needs_update = false;

	if ( null === $logs ) {
		$logs = array();
	}

	if ( is_array( $legacy_logs ) && ! empty( $legacy_logs ) ) {
		$logs         = array_merge( $legacy_logs, is_array( $logs ) ? $logs : array() );
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
				$needs_update              = true;
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
			'timestamp'     => current_time( 'mysql', true ),
			'ability'       => is_string( $normalized_ability_name ) ? $normalized_ability_name : '',
			'user_id'       => get_current_user_id(),
			'target_id'     => $target_id,
			'target_type'   => sanitize_key( $target_type ),
			'input_summary' => sanitize_text_field( $input_summary ),
			'error_code'    => sanitize_key( $error_code ),
			'error_message' => sanitize_text_field( $error_message ),
			'success'       => $success,
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
