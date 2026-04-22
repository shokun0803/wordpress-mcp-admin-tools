<?php

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

	$logs      = wordpress_mcp_admin_get_audit_logs();
	$abilities = wordpress_mcp_admin_get_admin_page_abilities();
	?>
	<div class="wrap">
		<style>
			.wordpress-mcp-admin-log-table-wrapper {
				max-height: min(70vh, 640px);
				overflow: auto;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				background: #fff;
			}

			.wordpress-mcp-admin-log-table-wrapper .widefat {
				margin-top: 0;
				border: 0;
			}

			.wordpress-mcp-admin-log-table-wrapper thead th {
				position: sticky;
				top: 0;
				z-index: 1;
				background: #f6f7f7;
			}

			.wordpress-mcp-admin-log-table-wrapper td:first-child,
			.wordpress-mcp-admin-log-table-wrapper th:first-child {
				white-space: nowrap;
			}
		</style>
		<h1><?php echo esc_html__( 'MCP Admin Tools', 'wordpress-mcp-admin-tools' ); ?></h1>
		<p><?php echo esc_html__( 'Use this page to review what this plugin can do through MCP and to inspect the recent activity log in one place.', 'wordpress-mcp-admin-tools' ); ?></p>

		<h2><?php echo esc_html__( 'What You Can Do', 'wordpress-mcp-admin-tools' ); ?></h2>
		<p><?php echo esc_html__( 'The plugin publishes the following administrative actions to MCP clients.', 'wordpress-mcp-admin-tools' ); ?></p>
		<ul class="ul-disc">
			<li><?php echo esc_html__( 'Create posts and pages.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Update existing posts and pages.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Read and update arbitrary WordPress options exposed by core or plugins.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Inspect and update custom post type entries used by plugins for structured settings or content.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Read and update post meta and term meta without adding plugin-specific abilities.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Configure the front page, posts page, and other reading settings.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Inspect the current Site Health results from WordPress.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Apply selected Site Health fixes that are already available from wp-admin.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Read and replace page, post, and custom post type block layouts while keeping them editable in the block editor.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Change page-specific designs by editing files in the active block theme.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Import media from remote URLs, browse the media library, and assign featured images.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Update the site logo and site icon from existing media items.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Create or update classic navigation menus and assign them to theme locations while also inspecting block navigation posts.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Read theme and plugin files before updating them through MCP.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Delete posts or move them to the trash.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Install, create, edit, list, and delete themes.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Install, create, update, activate, deactivate, list, and delete plugins.', 'wordpress-mcp-admin-tools' ); ?></li>
			<li><?php echo esc_html__( 'Update the site title, tagline, and other selected settings.', 'wordpress-mcp-admin-tools' ); ?></li>
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
		<div class="wordpress-mcp-admin-log-table-wrapper">
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
			'name'        => 'wordpress-mcp-admin/get-site-health-status',
			'description' => __( 'Retrieve the current WordPress Site Health test results and supported fixes.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/run-site-health-fix',
			'description' => __( 'Apply a supported fix for a current WordPress Site Health issue.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
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
			'name'        => 'wordpress-mcp-admin/get-theme-file',
			'description' => __( 'Read an allowed file inside an installed theme.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/edit-page-design',
			'description' => __( 'Create or replace a page-specific block template in the active block theme.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_themes',
		),
		array(
			'name'        => 'wordpress-mcp-admin/import-media-from-url',
			'description' => __( 'Download a remote file into the media library and optionally update its metadata.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'upload_files',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-media-items',
			'description' => __( 'Retrieve recent items from the media library.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'upload_files',
		),
		array(
			'name'        => 'wordpress-mcp-admin/set-featured-image',
			'description' => __( 'Assign or clear the featured image for a post or page.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_posts',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-site-media',
			'description' => __( 'Update the site logo and site icon using existing media library items.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-general-settings',
			'description' => __( 'Update the site title, tagline, and reading settings.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-options',
			'description' => __( 'Retrieve arbitrary WordPress option values by option name.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-options',
			'description' => __( 'Update arbitrary WordPress options by option name.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'manage_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-post-type-entries',
			'description' => __( 'Retrieve entries for an arbitrary post type, including custom post types used by plugins.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'post-type-specific edit_posts capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-post-type-entry',
			'description' => __( 'Update a single entry from any post type, including custom post types used by plugins.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'post-type-specific edit_post capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-object-meta',
			'description' => __( 'Retrieve meta values for a post or term.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'post/term-specific edit or manage capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/update-object-meta',
			'description' => __( 'Update meta values for a post or term.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'post/term-specific edit or manage capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/list-contact-forms',
			'description' => __( 'Retrieve forms managed by Contact Form 7 or WPForms Lite.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'provider-specific form view capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-contact-form',
			'description' => __( 'Retrieve a single Contact Form 7 or WPForms Lite form with provider-specific configuration.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'provider-specific form view capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/save-contact-form',
			'description' => __( 'Create or update a Contact Form 7 or WPForms Lite form through its native APIs.', 'wordpress-mcp-admin-tools' ),
			'capability'  => __( 'provider-specific form edit capability', 'wordpress-mcp-admin-tools' ),
		),
		array(
			'name'        => 'wordpress-mcp-admin/get-navigation-menus',
			'description' => __( 'Retrieve classic navigation menus, assigned locations, and existing block navigation posts.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_theme_options',
		),
		array(
			'name'        => 'wordpress-mcp-admin/set-navigation-menu',
			'description' => __( 'Create or update a classic navigation menu, its items, and optional location assignments.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_theme_options',
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
			'name'        => 'wordpress-mcp-admin/get-plugin-file',
			'description' => __( 'Read an allowed file inside an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'capability'  => 'edit_plugins',
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
