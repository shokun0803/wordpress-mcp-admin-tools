<?php

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		'wordpress-mcp-admin/import-media-from-url',
		array(
			'label'               => __( 'Import Media From URL', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Download a remote file into the media library and optionally update its metadata.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_import_media_from_url',
			'permission_callback' => 'wordpress_mcp_admin_can_upload_files',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'source_url'     => array(
						'type'        => 'string',
						'description' => __( 'Remote file URL to import into the media library.', 'wordpress-mcp-admin-tools' ),
					),
					'title'          => array(
						'type'        => 'string',
						'description' => __( 'Optional attachment title override.', 'wordpress-mcp-admin-tools' ),
					),
					'alt_text'       => array(
						'type'        => 'string',
						'description' => __( 'Optional image alt text.', 'wordpress-mcp-admin-tools' ),
					),
					'caption'        => array(
						'type'        => 'string',
						'description' => __( 'Optional attachment caption.', 'wordpress-mcp-admin-tools' ),
					),
					'description'    => array(
						'type'        => 'string',
						'description' => __( 'Optional attachment description.', 'wordpress-mcp-admin-tools' ),
					),
					'parent_post_id' => array(
						'type'        => 'integer',
						'description' => __( 'Optional post ID to attach the imported media item to.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'source_url' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'attachment_id' => array( 'type' => 'integer' ),
					'title'         => array( 'type' => 'string' ),
					'url'           => array( 'type' => 'string' ),
					'mime_type'     => array( 'type' => 'string' ),
					'alt_text'      => array( 'type' => 'string' ),
					'caption'       => array( 'type' => 'string' ),
					'description'   => array( 'type' => 'string' ),
					'edit_link'     => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/get-media-items',
		array(
			'label'               => __( 'Get Media Items', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve recent items from the media library.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_media_items',
			'permission_callback' => 'wordpress_mcp_admin_can_upload_files',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'search'    => array(
						'type'        => 'string',
						'description' => __( 'Optional search term.', 'wordpress-mcp-admin-tools' ),
					),
					'mime_type' => array(
						'type'        => 'string',
						'description' => __( 'Optional MIME type filter such as image or image/jpeg.', 'wordpress-mcp-admin-tools' ),
					),
					'per_page'  => array(
						'type'        => 'integer',
						'description' => __( 'Number of items to return. Between 1 and 50.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'items' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'attachment_id' => array( 'type' => 'integer' ),
								'title'         => array( 'type' => 'string' ),
								'url'           => array( 'type' => 'string' ),
								'mime_type'     => array( 'type' => 'string' ),
								'alt_text'      => array( 'type' => 'string' ),
								'caption'       => array( 'type' => 'string' ),
								'description'   => array( 'type' => 'string' ),
								'edit_link'     => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/set-featured-image',
		array(
			'label'               => __( 'Set Featured Image', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Assign or clear the featured image for a post or page.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_set_featured_image',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_posts',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'       => array(
						'type'        => 'integer',
						'description' => __( 'Post or page ID to update.', 'wordpress-mcp-admin-tools' ),
					),
					'attachment_id' => array(
						'type'        => 'integer',
						'description' => __( 'Attachment ID to set. Use 0 to clear the current featured image.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_id', 'attachment_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'            => array( 'type' => 'integer' ),
					'featured_image_id'  => array( 'type' => 'integer' ),
					'featured_image_url' => array( 'type' => 'string' ),
					'edit_link'          => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/update-site-media',
		array(
			'label'               => __( 'Update Site Media', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update the site logo and site icon using existing media library items.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_site_media',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'custom_logo_id' => array(
						'type'        => 'integer',
						'description' => __( 'Attachment ID to use as the custom logo. Use 0 to clear it.', 'wordpress-mcp-admin-tools' ),
					),
					'site_icon_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Attachment ID to use as the site icon. Use 0 to clear it.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'custom_logo_id'  => array( 'type' => 'integer' ),
					'custom_logo_url' => array( 'type' => 'string' ),
					'site_icon_id'    => array( 'type' => 'integer' ),
					'site_icon_url'   => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/get-site-health-status',
		array(
			'label'               => __( 'Get Site Health Status', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve the current WordPress Site Health test results and supported fixes.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_site_health_status',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'summary'         => array(
						'type'       => 'object',
						'properties' => array(
							'good'        => array( 'type' => 'integer' ),
							'recommended' => array( 'type' => 'integer' ),
							'critical'    => array( 'type' => 'integer' ),
							'total'       => array( 'type' => 'integer' ),
						),
					),
					'tests'           => array(
						'type'        => 'array',
						'description' => __( 'Site Health test results.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'id'          => array( 'type' => 'string' ),
								'type'        => array( 'type' => 'string' ),
								'test'        => array( 'type' => 'string' ),
								'label'       => array( 'type' => 'string' ),
								'status'      => array( 'type' => 'string' ),
								'description' => array( 'type' => 'string' ),
								'actions'     => array( 'type' => 'string' ),
								'fixes'       => array(
									'type'  => 'array',
									'items' => array(
										'type'       => 'object',
										'properties' => array(
											'fix'         => array( 'type' => 'string' ),
											'label'       => array( 'type' => 'string' ),
											'description' => array( 'type' => 'string' ),
										),
									),
								),
							),
						),
					),
					'available_fixes' => array(
						'type'        => 'array',
						'description' => __( 'Supported Site Health fixes that can be applied right now.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'fix'         => array( 'type' => 'string' ),
								'label'       => array( 'type' => 'string' ),
								'description' => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/run-site-health-fix',
		array(
			'label'               => __( 'Run Site Health Fix', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Apply a supported fix for a current WordPress Site Health issue.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_run_site_health_fix',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'fix' => array(
						'type'        => 'string',
						'enum'        => array( 'flush-permalinks', 'enable-search-engine-indexing', 'update-urls-to-https' ),
						'description' => __( 'Supported Site Health fix to apply.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'fix' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'fix'                  => array( 'type' => 'string' ),
					'applied'              => array( 'type' => 'boolean' ),
					'message'              => array( 'type' => 'string' ),
					'home_url'             => array( 'type' => 'string' ),
					'site_url'             => array( 'type' => 'string' ),
					'search_visibility'    => array( 'type' => 'boolean' ),
					'summary_after'        => array(
						'type'       => 'object',
						'properties' => array(
							'good'        => array( 'type' => 'integer' ),
							'recommended' => array( 'type' => 'integer' ),
							'critical'    => array( 'type' => 'integer' ),
							'total'       => array( 'type' => 'integer' ),
						),
					),
					'available_fixes_after' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'fix'         => array( 'type' => 'string' ),
								'label'       => array( 'type' => 'string' ),
								'description' => array( 'type' => 'string' ),
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
			'description'         => __( 'Update the site title, tagline, and reading settings.', 'wordpress-mcp-admin-tools' ),
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
					'show_on_front'   => array(
						'type'        => 'string',
						'enum'        => array( 'posts', 'page' ),
						'description' => __( 'Whether the homepage shows latest posts or a static page.', 'wordpress-mcp-admin-tools' ),
					),
					'page_on_front'   => array(
						'type'        => 'integer',
						'description' => __( 'Page ID to use as the static front page.', 'wordpress-mcp-admin-tools' ),
					),
					'page_for_posts'  => array(
						'type'        => 'integer',
						'description' => __( 'Page ID to use as the posts index page.', 'wordpress-mcp-admin-tools' ),
					),
					'posts_per_page'  => array(
						'type'        => 'integer',
						'description' => __( 'Number of posts to show per archive page.', 'wordpress-mcp-admin-tools' ),
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
					'show_on_front'   => array(
						'type'        => 'string',
						'description' => __( 'Updated homepage display mode.', 'wordpress-mcp-admin-tools' ),
					),
					'page_on_front'   => array(
						'type'        => 'integer',
						'description' => __( 'Updated static front page ID.', 'wordpress-mcp-admin-tools' ),
					),
					'page_for_posts'  => array(
						'type'        => 'integer',
						'description' => __( 'Updated posts index page ID.', 'wordpress-mcp-admin-tools' ),
					),
					'posts_per_page'  => array(
						'type'        => 'integer',
						'description' => __( 'Updated number of posts shown per archive page.', 'wordpress-mcp-admin-tools' ),
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
		'wordpress-mcp-admin/get-options',
		array(
			'label'               => __( 'Get Options', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve arbitrary WordPress option values by option name.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_options',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'names' => array(
						'type'        => 'array',
						'description' => __( 'Option names to retrieve.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type' => 'string',
						),
					),
				),
				'required'   => array( 'names' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'items' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'name'  => array( 'type' => 'string' ),
								'value' => array(
									'description' => __( 'Stored option value.', 'wordpress-mcp-admin-tools' ),
								),
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
		'wordpress-mcp-admin/update-options',
		array(
			'label'               => __( 'Update Options', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update arbitrary WordPress options by option name.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_options',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'options' => array(
						'type'        => 'array',
						'description' => __( 'Option updates to apply.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'name'  => array( 'type' => 'string' ),
								'value' => array(
									'description' => __( 'New option value. Any JSON-serializable value is supported.', 'wordpress-mcp-admin-tools' ),
								),
							),
						),
					),
				),
				'required'   => array( 'options' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'items' => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'name'  => array( 'type' => 'string' ),
								'value' => array(
									'description' => __( 'Updated option value.', 'wordpress-mcp-admin-tools' ),
								),
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
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => true,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/get-post-type-entries',
		array(
			'label'               => __( 'Get Post Type Entries', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve entries for an arbitrary post type, including custom post types used by plugins.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_post_type_entries',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_post_type_entries',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_type'       => array(
						'type'        => 'string',
						'description' => __( 'Target post type slug.', 'wordpress-mcp-admin-tools' ),
					),
					'search'          => array(
						'type'        => 'string',
						'description' => __( 'Optional search term.', 'wordpress-mcp-admin-tools' ),
					),
					'statuses'        => array(
						'type'        => 'array',
						'description' => __( 'Optional list of post statuses to include.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type' => 'string',
						),
					),
					'per_page'        => array(
						'type'        => 'integer',
						'description' => __( 'Number of entries to return. Between 1 and 50.', 'wordpress-mcp-admin-tools' ),
					),
					'page'            => array(
						'type'        => 'integer',
						'description' => __( 'Results page number.', 'wordpress-mcp-admin-tools' ),
					),
					'include_content' => array(
						'type'        => 'boolean',
						'description' => __( 'Include raw post_content for each entry when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_type' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_type'     => array( 'type' => 'string' ),
					'page'          => array( 'type' => 'integer' ),
					'per_page'      => array( 'type' => 'integer' ),
					'found_posts'   => array( 'type' => 'integer' ),
					'max_num_pages' => array( 'type' => 'integer' ),
					'entries'       => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'post_id'    => array( 'type' => 'integer' ),
								'post_type'  => array( 'type' => 'string' ),
								'title'      => array( 'type' => 'string' ),
								'slug'       => array( 'type' => 'string' ),
								'status'     => array( 'type' => 'string' ),
								'excerpt'    => array( 'type' => 'string' ),
								'menu_order' => array( 'type' => 'integer' ),
								'content'    => array(
									'type'        => 'string',
									'description' => __( 'Returned only when include_content is true.', 'wordpress-mcp-admin-tools' ),
								),
								'edit_link'   => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/update-post-type-entry',
		array(
			'label'               => __( 'Update Post Type Entry', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update a single entry from any post type, including custom post types used by plugins.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_post_type_entry',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_post_type_entries',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'    => array(
						'type'        => 'integer',
						'description' => __( 'Entry ID to update.', 'wordpress-mcp-admin-tools' ),
					),
					'post_type'  => array(
						'type'        => 'string',
						'description' => __( 'Optional expected post type slug for validation.', 'wordpress-mcp-admin-tools' ),
					),
					'title'      => array(
						'type'        => 'string',
						'description' => __( 'New entry title.', 'wordpress-mcp-admin-tools' ),
					),
					'content'    => array(
						'type'        => 'string',
						'description' => __( 'New entry content.', 'wordpress-mcp-admin-tools' ),
					),
					'excerpt'    => array(
						'type'        => 'string',
						'description' => __( 'New entry excerpt.', 'wordpress-mcp-admin-tools' ),
					),
					'status'     => array(
						'type'        => 'string',
						'description' => __( 'New post status.', 'wordpress-mcp-admin-tools' ),
					),
					'slug'       => array(
						'type'        => 'string',
						'description' => __( 'New post slug.', 'wordpress-mcp-admin-tools' ),
					),
					'menu_order' => array(
						'type'        => 'integer',
						'description' => __( 'New menu order value.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'post_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'post_id'    => array( 'type' => 'integer' ),
					'post_type'  => array( 'type' => 'string' ),
					'title'      => array( 'type' => 'string' ),
					'slug'       => array( 'type' => 'string' ),
					'status'     => array( 'type' => 'string' ),
					'excerpt'    => array( 'type' => 'string' ),
					'menu_order' => array( 'type' => 'integer' ),
					'content'    => array(
						'type'        => 'string',
						'description' => __( 'Returned when content was requested in the update.', 'wordpress-mcp-admin-tools' ),
					),
					'edit_link'  => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/get-object-meta',
		array(
			'label'               => __( 'Get Object Meta', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve meta values for a post or term.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_object_meta',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_object_meta',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'object_type' => array(
						'type'        => 'string',
						'enum'        => array( 'post', 'term' ),
						'description' => __( 'Metadata object type.', 'wordpress-mcp-admin-tools' ),
					),
					'object_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Target post or term ID.', 'wordpress-mcp-admin-tools' ),
					),
					'keys'        => array(
						'type'        => 'array',
						'description' => __( 'Optional list of meta keys to retrieve. When omitted, all meta keys are returned.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type' => 'string',
						),
					),
				),
				'required'   => array( 'object_type', 'object_id' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'object_type' => array( 'type' => 'string' ),
					'object_id'   => array( 'type' => 'integer' ),
					'items'       => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'key'    => array( 'type' => 'string' ),
								'values' => array(
									'type'        => 'array',
									'description' => __( 'Stored values for the meta key.', 'wordpress-mcp-admin-tools' ),
									'items'       => array(),
								),
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
		'wordpress-mcp-admin/update-object-meta',
		array(
			'label'               => __( 'Update Object Meta', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Update meta values for a post or term.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_update_object_meta',
			'permission_callback' => 'wordpress_mcp_admin_can_manage_object_meta',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'object_type' => array(
						'type'        => 'string',
						'enum'        => array( 'post', 'term' ),
						'description' => __( 'Metadata object type.', 'wordpress-mcp-admin-tools' ),
					),
					'object_id'   => array(
						'type'        => 'integer',
						'description' => __( 'Target post or term ID.', 'wordpress-mcp-admin-tools' ),
					),
					'entries'     => array(
						'type'        => 'array',
						'description' => __( 'Meta entries to update.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'key'   => array( 'type' => 'string' ),
								'value' => array(
									'description' => __( 'New meta value. Any JSON-serializable value is supported.', 'wordpress-mcp-admin-tools' ),
								),
							),
						),
					),
				),
				'required'   => array( 'object_type', 'object_id', 'entries' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'object_type' => array( 'type' => 'string' ),
					'object_id'   => array( 'type' => 'integer' ),
					'items'       => array(
						'type'  => 'array',
						'items' => array(
							'type'       => 'object',
							'properties' => array(
								'key'    => array( 'type' => 'string' ),
								'values' => array(
									'type'        => 'array',
									'description' => __( 'Stored values after update.', 'wordpress-mcp-admin-tools' ),
									'items'       => array(),
								),
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
					'readonly'   => false,
					'destructive' => false,
					'idempotent' => false,
				),
			),
		)
	);

	wp_register_ability(
		'wordpress-mcp-admin/get-navigation-menus',
		array(
			'label'               => __( 'Get Navigation Menus', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Retrieve classic navigation menus, assigned locations, and existing block navigation posts.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_navigation_menus',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_theme_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'include_items' => array(
						'type'        => 'boolean',
						'description' => __( 'Include classic menu items in the response when true.', 'wordpress-mcp-admin-tools' ),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'menus' => array(
						'type'  => 'array',
						'items' => array( 'type' => 'object' ),
					),
					'locations' => array(
						'type'  => 'array',
						'items' => array( 'type' => 'object' ),
					),
					'navigation_posts' => array(
						'type'  => 'array',
						'items' => array( 'type' => 'object' ),
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
		'wordpress-mcp-admin/set-navigation-menu',
		array(
			'label'               => __( 'Set Navigation Menu', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Create or update a classic navigation menu, its items, and optional location assignments.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_set_navigation_menu',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_theme_options',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'menu'          => array(
						'type'        => 'string',
						'description' => __( 'Existing menu ID, slug, or name to update.', 'wordpress-mcp-admin-tools' ),
					),
					'name'          => array(
						'type'        => 'string',
						'description' => __( 'Menu name. Required when creating a new menu.', 'wordpress-mcp-admin-tools' ),
					),
					'slug'          => array(
						'type'        => 'string',
						'description' => __( 'Optional menu slug.', 'wordpress-mcp-admin-tools' ),
					),
					'description'   => array(
						'type'        => 'string',
						'description' => __( 'Optional menu description.', 'wordpress-mcp-admin-tools' ),
					),
					'replace_items' => array(
						'type'        => 'boolean',
						'description' => __( 'Replace existing items when items are provided. Defaults to true.', 'wordpress-mcp-admin-tools' ),
					),
					'locations'     => array(
						'type'        => 'array',
						'description' => __( 'Registered menu location slugs to assign this menu to.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type' => 'string',
						),
					),
					'items'         => array(
						'type'        => 'array',
						'description' => __( 'Menu items to create. parent_index can be used for nesting and must reference an earlier item in the same array.', 'wordpress-mcp-admin-tools' ),
						'items'       => array(
							'type'       => 'object',
							'properties' => array(
								'title'        => array( 'type' => 'string' ),
								'type'         => array( 'type' => 'string' ),
								'object_id'    => array( 'type' => 'integer' ),
								'url'          => array( 'type' => 'string' ),
								'parent_index' => array( 'type' => 'integer' ),
								'description'  => array( 'type' => 'string' ),
								'target'       => array( 'type' => 'string' ),
								'classes'      => array(
									'type'  => 'array',
									'items' => array( 'type' => 'string' ),
								),
							),
						),
					),
				),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'menu_id'     => array( 'type' => 'integer' ),
					'name'        => array( 'type' => 'string' ),
					'slug'        => array( 'type' => 'string' ),
					'description' => array( 'type' => 'string' ),
					'locations'   => array(
						'type'  => 'array',
						'items' => array( 'type' => 'string' ),
					),
					'items_count' => array( 'type' => 'integer' ),
					'items'       => array(
						'type'  => 'array',
						'items' => array( 'type' => 'object' ),
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
		'wordpress-mcp-admin/get-theme-file',
		array(
			'label'               => __( 'Get Theme File', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Read an allowed file inside an installed theme.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_theme_file',
			'permission_callback' => 'wordpress_mcp_admin_can_edit_themes',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'theme'         => array(
						'type'        => 'string',
						'description' => __( 'Theme stylesheet identifier to read.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path' => array(
						'type'        => 'string',
						'description' => __( 'Relative path inside the theme directory.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'theme', 'relative_path' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'theme'         => array( 'type' => 'string' ),
					'relative_path' => array( 'type' => 'string' ),
					'content'       => array( 'type' => 'string' ),
					'bytes'         => array( 'type' => 'integer' ),
					'edit_link'     => array( 'type' => 'string' ),
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
		'wordpress-mcp-admin/get-plugin-file',
		array(
			'label'               => __( 'Get Plugin File', 'wordpress-mcp-admin-tools' ),
			'description'         => __( 'Read an allowed file inside an installed plugin.', 'wordpress-mcp-admin-tools' ),
			'category'            => 'wordpress-mcp-admin',
			'execute_callback'    => 'wordpress_mcp_admin_execute_get_plugin_file',
			'permission_callback' => 'wordpress_mcp_admin_can_update_plugins',
			'input_schema'        => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'        => array(
						'type'        => 'string',
						'description' => __( 'Plugin slug or basename to read.', 'wordpress-mcp-admin-tools' ),
					),
					'relative_path' => array(
						'type'        => 'string',
						'description' => __( 'Relative path inside the plugin directory.', 'wordpress-mcp-admin-tools' ),
					),
				),
				'required'   => array( 'plugin', 'relative_path' ),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'plugin'        => array( 'type' => 'string' ),
					'relative_path' => array( 'type' => 'string' ),
					'content'       => array( 'type' => 'string' ),
					'bytes'         => array( 'type' => 'integer' ),
					'edit_link'     => array( 'type' => 'string' ),
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
