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
 * メディアアップロード権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_upload_files( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'upload_files' );
}

/**
 * テーマオプション編集権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_edit_theme_options( array $input = array() ): bool {
	unset( $input );

	return current_user_can( 'edit_theme_options' );
}

/**
 * 任意の投稿タイプ項目を操作できるかを確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_manage_post_type_entries( array $input = array() ): bool {
	if ( isset( $input['post_id'] ) && (int) $input['post_id'] > 0 ) {
		$post = get_post( (int) $input['post_id'] );

		if ( $post instanceof WP_Post ) {
			$post_type_object = get_post_type_object( $post->post_type );

			if ( $post_type_object && isset( $post_type_object->cap->edit_post ) ) {
				return current_user_can( $post_type_object->cap->edit_post, $post->ID );
			}

			return current_user_can( 'edit_post', $post->ID ) || current_user_can( 'edit_posts' );
		}
	}

	$post_type = isset( $input['post_type'] ) ? sanitize_key( (string) $input['post_type'] ) : 'post';

	if ( ! post_type_exists( $post_type ) ) {
		return false;
	}

	$post_type_object = get_post_type_object( $post_type );
	$capability       = $post_type_object && isset( $post_type_object->cap->edit_posts )
		? (string) $post_type_object->cap->edit_posts
		: 'edit_posts';

	return current_user_can( $capability );
}

/**
 * 投稿またはタームのメタデータを操作できるかを確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_manage_object_meta( array $input = array() ): bool {
	$object_type = isset( $input['object_type'] ) ? sanitize_key( (string) $input['object_type'] ) : '';
	$object_id   = isset( $input['object_id'] ) ? (int) $input['object_id'] : 0;

	if ( 'post' === $object_type ) {
		if ( $object_id > 0 ) {
			$post = get_post( $object_id );

			if ( $post instanceof WP_Post ) {
				$post_type_object = get_post_type_object( $post->post_type );

				if ( $post_type_object && isset( $post_type_object->cap->edit_post ) ) {
					return current_user_can( $post_type_object->cap->edit_post, $post->ID );
				}

				return current_user_can( 'edit_post', $post->ID ) || current_user_can( 'edit_posts' );
			}
		}

		return current_user_can( 'edit_posts' );
	}

	if ( 'term' === $object_type ) {
		if ( $object_id > 0 ) {
			$term = get_term( $object_id );

			if ( $term instanceof WP_Term ) {
				$taxonomy = get_taxonomy( $term->taxonomy );

				if ( $taxonomy && isset( $taxonomy->cap->manage_terms ) ) {
					return current_user_can( $taxonomy->cap->manage_terms );
				}
			}
		}

		return current_user_can( 'manage_categories' );
	}

	return false;
}

/**
 * JSON 由来の入力値を再帰的に正規化します。
 *
 * @param mixed $value 入力値。
 * @return mixed
 */
function wordpress_mcp_admin_normalize_serializable_value( $value ) {
	if ( is_array( $value ) ) {
		$normalized = array();

		foreach ( $value as $key => $nested_value ) {
			$normalized[ $key ] = wordpress_mcp_admin_normalize_serializable_value( $nested_value );
		}

		return $normalized;
	}

	if ( is_string( $value ) ) {
		return wp_unslash( $value );
	}

	return $value;
}

/**
 * オプション名を検証します。
 *
 * @param mixed $value 入力値。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_normalize_option_name( $value ) {
	$option_name = is_scalar( $value ) ? trim( sanitize_text_field( wp_unslash( (string) $value ) ) ) : '';

	if ( '' === $option_name ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_option_name',
			__( 'Each option name must be a non-empty string.', 'wordpress-mcp-admin-tools' )
		);
	}

	return $option_name;
}

/**
 * メタキーを検証します。
 *
 * @param mixed $value 入力値。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_normalize_meta_key( $value ) {
	$meta_key = is_scalar( $value ) ? trim( sanitize_text_field( wp_unslash( (string) $value ) ) ) : '';

	if ( '' === $meta_key ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_meta_key',
			__( 'Each meta key must be a non-empty string.', 'wordpress-mcp-admin-tools' )
		);
	}

	return $meta_key;
}

/**
 * 監査ログ向けに文字列配列を要約します。
 *
 * @param string              $label ラベル。
 * @param array<int, string>  $values 値配列。
 * @return string
 */
function wordpress_mcp_admin_summarize_string_list( string $label, array $values ): string {
	$values = array_values( array_filter( array_map( 'strval', $values ), static fn( string $value ): bool => '' !== $value ) );

	if ( empty( $values ) ) {
		return '';
	}

	$preview = array_slice( $values, 0, 5 );
	$summary = $label . '=' . implode( ',', $preview );

	if ( count( $values ) > count( $preview ) ) {
		$summary .= ',...';
	}

	return $summary;
}

/**
 * オプション情報を返却用配列に整形します。
 *
 * @param string $option_name オプション名。
 * @param mixed  $value オプション値。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_option_record( string $option_name, $value ): array {
	return array(
		'name'  => $option_name,
		'value' => $value,
	);
}

/**
 * 投稿タイプ項目を返却用配列に整形します。
 *
 * @param WP_Post $post 投稿オブジェクト。
 * @param bool    $include_content content を含める場合は true。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_post_type_entry_record( WP_Post $post, bool $include_content = false ): array {
	$record = array(
		'post_id'    => (int) $post->ID,
		'post_type'  => (string) $post->post_type,
		'title'      => (string) get_the_title( $post ),
		'slug'       => (string) $post->post_name,
		'status'     => (string) get_post_status( $post ),
		'excerpt'    => (string) $post->post_excerpt,
		'menu_order' => (int) $post->menu_order,
		'edit_link'  => (string) get_edit_post_link( $post->ID, 'raw' ),
	);

	if ( $include_content ) {
		$record['content'] = (string) $post->post_content;
	}

	return $record;
}

/**
 * メタデータ項目を返却用配列に整形します。
 *
 * @param string            $meta_key メタキー。
 * @param array<int, mixed> $values メタ値一覧。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_object_meta_record( string $meta_key, array $values ): array {
	return array(
		'key'    => $meta_key,
		'values' => array_values( $values ),
	);
}

/**
 * コンタクトフォーム provider を検証します。
 *
 * @param mixed $value 入力値。
 * @return string|WP_Error
 */
function wordpress_mcp_admin_normalize_contact_form_provider( $value ) {
	$provider = is_scalar( $value ) ? trim( sanitize_text_field( wp_unslash( (string) $value ) ) ) : '';
	$allowed  = array( 'contact-form-7', 'wpforms-lite' );

	if ( ! in_array( $provider, $allowed, true ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_contact_form_provider',
			__( 'provider must be either contact-form-7 or wpforms-lite.', 'wordpress-mcp-admin-tools' )
		);
	}

	return $provider;
}


/**
 * コンタクトフォーム管理権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_view_contact_forms( array $input = array() ): bool {
	$provider = wordpress_mcp_admin_normalize_contact_form_provider( $input['provider'] ?? '' );
	$form_id  = isset( $input['form_id'] ) ? (int) $input['form_id'] : 0;

	if ( is_wp_error( $provider ) ) {
		return current_user_can( 'manage_options' );
	}

	if ( 'contact-form-7' === $provider ) {
		if ( $form_id > 0 ) {
			return current_user_can( 'manage_options' ) || current_user_can( 'wpcf7_edit_contact_form', $form_id ) || current_user_can( 'wpcf7_edit_contact_forms' );
		}

		return current_user_can( 'manage_options' ) || current_user_can( 'wpcf7_edit_contact_forms' );
	}

	if ( function_exists( 'wpforms_current_user_can' ) ) {
		if ( $form_id > 0 ) {
			return current_user_can( 'manage_options' ) || wpforms_current_user_can( array( 'view_form_single', 'edit_form_single' ), $form_id );
		}

		return current_user_can( 'manage_options' ) || wpforms_current_user_can( array( 'view_forms', 'create_forms' ) );
	}

	return current_user_can( 'manage_options' );
}


/**
 * コンタクトフォーム保存権限を確認します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return bool
 */
function wordpress_mcp_admin_can_save_contact_forms( array $input = array() ): bool {
	$provider = wordpress_mcp_admin_normalize_contact_form_provider( $input['provider'] ?? '' );
	$form_id  = isset( $input['form_id'] ) ? (int) $input['form_id'] : 0;

	if ( is_wp_error( $provider ) ) {
		return current_user_can( 'manage_options' );
	}

	if ( 'contact-form-7' === $provider ) {
		if ( $form_id > 0 ) {
			return current_user_can( 'manage_options' ) || current_user_can( 'wpcf7_edit_contact_form', $form_id ) || current_user_can( 'wpcf7_edit_contact_forms' );
		}

		return current_user_can( 'manage_options' ) || current_user_can( 'wpcf7_edit_contact_forms' );
	}

	if ( function_exists( 'wpforms_current_user_can' ) ) {
		if ( $form_id > 0 ) {
			return current_user_can( 'manage_options' ) || wpforms_current_user_can( 'edit_form_single', $form_id );
		}

		return current_user_can( 'manage_options' ) || wpforms_current_user_can( 'create_forms' );
	}

	return current_user_can( 'manage_options' );
}


/**
 * コンタクトフォーム provider が利用可能か確認します。
 *
 * @param string $provider provider 名。
 * @return WP_Error|null
 */
function wordpress_mcp_admin_assert_contact_form_provider_ready( string $provider ) {
	if ( 'contact-form-7' === $provider ) {
		if ( class_exists( 'WPCF7_ContactForm' ) && function_exists( 'wpcf7_contact_form' ) && function_exists( 'wpcf7_save_contact_form' ) ) {
			return null;
		}

		return new WP_Error(
			'wordpress_mcp_admin_contact_form_7_unavailable',
			__( 'Contact Form 7 must be installed and active to use this provider.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( function_exists( 'wpforms' ) && function_exists( 'wpforms_current_user_can' ) && function_exists( 'wpforms_decode' ) ) {
		$app = wpforms();

		if ( is_object( $app ) && method_exists( $app, 'obj' ) ) {
			$handler = $app->obj( 'form' );

			if ( is_object( $handler ) && method_exists( $handler, 'add' ) && method_exists( $handler, 'update' ) && method_exists( $handler, 'get' ) ) {
				return null;
			}
		}
	}

	return new WP_Error(
		'wordpress_mcp_admin_wpforms_lite_unavailable',
		__( 'WPForms Lite must be installed and active to use this provider.', 'wordpress-mcp-admin-tools' )
	);
}


/**
 * Contact Form 7 フォームを返却用配列に整形します。
 *
 * @param WPCF7_ContactForm $contact_form フォーム。
 * @param bool              $include_configuration 設定を含める場合は true。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_contact_form_7_record( WPCF7_ContactForm $contact_form, bool $include_configuration = false ): array {
	$form_id = (int) $contact_form->id();
	$record  = array(
		'provider'  => 'contact-form-7',
		'form_id'   => $form_id,
		'title'     => (string) $contact_form->title(),
		'status'    => (string) get_post_status( $form_id ),
		'edit_link' => (string) get_edit_post_link( $form_id, 'raw' ),
	);

	if ( $include_configuration ) {
		$record['configuration'] = array_merge(
			$contact_form->get_properties(),
			array(
				'locale' => (string) $contact_form->locale(),
			)
		);
	}

	return $record;
}


/**
 * WPForms Lite フォームを返却用配列に整形します。
 *
 * @param WP_Post $form_post フォーム投稿。
 * @param bool    $include_configuration 設定を含める場合は true。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_wpforms_lite_record( WP_Post $form_post, bool $include_configuration = false ): array {
	$record = array(
		'provider'  => 'wpforms-lite',
		'form_id'   => (int) $form_post->ID,
		'title'     => (string) get_the_title( $form_post ),
		'status'    => (string) get_post_status( $form_post ),
		'edit_link' => (string) get_edit_post_link( $form_post->ID, 'raw' ),
	);

	if ( $include_configuration ) {
		$configuration = wpforms_decode( (string) $form_post->post_content );
		$record['configuration'] = is_array( $configuration ) ? $configuration : array();
	}

	return $record;
}


/**
 * コンタクトフォーム一覧を取得します。
 *
 * @param string $provider provider 名。
 * @param int    $per_page 取得件数。
 * @param string $search   検索語。
 * @return array<int, array<string, mixed>>|WP_Error
 */
function wordpress_mcp_admin_get_contact_form_records( string $provider, int $per_page = 20, string $search = '' ) {
	$ready = wordpress_mcp_admin_assert_contact_form_provider_ready( $provider );

	if ( is_wp_error( $ready ) ) {
		return $ready;
	}

	$query = array(
		'post_type'      => 'contact-form-7' === $provider ? 'wpcf7_contact_form' : 'wpforms',
		'post_status'    => array( 'publish', 'draft', 'private' ),
		'posts_per_page' => max( 1, min( 50, $per_page ) ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( '' !== $search ) {
		$query['s'] = $search;
	}

	$posts   = get_posts( $query );
	$records = array();

	foreach ( $posts as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}

		if ( 'contact-form-7' === $provider ) {
			$contact_form = wpcf7_contact_form( $post->ID );

			if ( $contact_form instanceof WPCF7_ContactForm ) {
				$records[] = wordpress_mcp_admin_format_contact_form_7_record( $contact_form );
			}
		} else {
			$records[] = wordpress_mcp_admin_format_wpforms_lite_record( $post );
		}
	}

	return $records;
}



/**
 * 指定したコンタクトフォームを取得します。
 *
 * @param string $provider provider 名。
 * @param int    $form_id フォーム ID。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_get_contact_form_record( string $provider, int $form_id ) {
	$ready = wordpress_mcp_admin_assert_contact_form_provider_ready( $provider );

	if ( is_wp_error( $ready ) ) {
		return $ready;
	}

	if ( $form_id <= 0 ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_form_id',
			__( 'A valid form_id is required.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( 'contact-form-7' === $provider ) {
		$contact_form = wpcf7_contact_form( $form_id );

		if ( ! $contact_form instanceof WPCF7_ContactForm ) {
			return new WP_Error(
				'wordpress_mcp_admin_contact_form_not_found',
				__( 'The specified Contact Form 7 form could not be found.', 'wordpress-mcp-admin-tools' )
			);
		}

		return wordpress_mcp_admin_format_contact_form_7_record( $contact_form, true );
	}

	$form_post = get_post( $form_id );

	if ( ! $form_post instanceof WP_Post || 'wpforms' !== $form_post->post_type ) {
		return new WP_Error(
			'wordpress_mcp_admin_wpforms_form_not_found',
			__( 'The specified WPForms Lite form could not be found.', 'wordpress-mcp-admin-tools' )
		);
	}

	return wordpress_mcp_admin_format_wpforms_lite_record( $form_post, true );
}

/**
 * Contact Form 7 フォームを作成または更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_save_contact_form_7_record( array $input = array() ) {
	$configuration = array_key_exists( 'configuration', $input ) ? wordpress_mcp_admin_normalize_serializable_value( $input['configuration'] ) : null;
	$form_id       = isset( $input['form_id'] ) ? (int) $input['form_id'] : 0;
	$title         = isset( $input['title'] ) ? wp_unslash( (string) $input['title'] ) : null;
	$status        = array_key_exists( 'status', $input ) ? sanitize_key( (string) $input['status'] ) : null;

	if ( null !== $configuration && ! is_array( $configuration ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_contact_form_configuration',
			__( 'configuration must be an object for Contact Form 7.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( $form_id <= 0 && ( null === $title || '' === trim( $title ) ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_missing_contact_form_title',
			__( 'A title is required when creating a Contact Form 7 form.', 'wordpress-mcp-admin-tools' )
		);
	}

	$data = array(
		'id'                  => $form_id > 0 ? $form_id : -1,
		'title'               => $title,
		'locale'              => is_array( $configuration ) && array_key_exists( 'locale', $configuration ) ? (string) $configuration['locale'] : null,
		'form'                => is_array( $configuration ) && array_key_exists( 'form', $configuration ) ? (string) $configuration['form'] : null,
		'mail'                => is_array( $configuration ) && array_key_exists( 'mail', $configuration ) ? $configuration['mail'] : null,
		'mail_2'              => is_array( $configuration ) && array_key_exists( 'mail_2', $configuration ) ? $configuration['mail_2'] : null,
		'messages'            => is_array( $configuration ) && array_key_exists( 'messages', $configuration ) ? $configuration['messages'] : null,
		'additional_settings' => is_array( $configuration ) && array_key_exists( 'additional_settings', $configuration ) ? $configuration['additional_settings'] : null,
	);

	$contact_form = wpcf7_save_contact_form( $data );

	if ( ! $contact_form instanceof WPCF7_ContactForm ) {
		return new WP_Error(
			'wordpress_mcp_admin_contact_form_save_failed',
			__( 'Contact Form 7 could not save the form.', 'wordpress-mcp-admin-tools' )
		);
	}

	$created_form_id = (int) $contact_form->id();

	if ( null !== $status ) {
		$updated_post_id = wp_update_post(
			array(
				'ID'          => $created_form_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $updated_post_id ) ) {
			return $updated_post_id;
		}
	}

	return wordpress_mcp_admin_get_contact_form_record( 'contact-form-7', $created_form_id );
}

/**
 * WPForms Lite フォームを作成または更新します。
 *
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_save_wpforms_lite_record( array $input = array() ) {
	$app = wpforms();

	if ( ! is_object( $app ) || ! method_exists( $app, 'obj' ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_wpforms_lite_unavailable',
			__( 'WPForms Lite must be installed and active to use this provider.', 'wordpress-mcp-admin-tools' )
		);
	}

	$form_handler = $app->obj( 'form' );

	if ( ! is_object( $form_handler ) || ! method_exists( $form_handler, 'add' ) || ! method_exists( $form_handler, 'update' ) || ! method_exists( $form_handler, 'get' ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_wpforms_form_handler_unavailable',
			__( 'WPForms Lite form APIs are unavailable.', 'wordpress-mcp-admin-tools' )
		);
	}

	$form_id       = isset( $input['form_id'] ) ? (int) $input['form_id'] : 0;
	$title         = isset( $input['title'] ) ? sanitize_text_field( wp_unslash( (string) $input['title'] ) ) : '';
	$status        = array_key_exists( 'status', $input ) ? sanitize_key( (string) $input['status'] ) : null;
	$description   = array_key_exists( 'description', $input ) ? sanitize_textarea_field( wp_unslash( (string) $input['description'] ) ) : null;
	$configuration = array_key_exists( 'configuration', $input ) ? wordpress_mcp_admin_normalize_serializable_value( $input['configuration'] ) : null;

	if ( null !== $configuration && ! is_array( $configuration ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_wpforms_configuration',
			__( 'configuration must be an object for WPForms Lite.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( $form_id <= 0 ) {
		if ( '' === $title ) {
			return new WP_Error(
				'wordpress_mcp_admin_missing_wpforms_title',
				__( 'A title is required when creating a WPForms Lite form.', 'wordpress-mcp-admin-tools' )
			);
		}

		$created_form_id = $form_handler->add( $title, array(), array( 'builder' => false ) );

		if ( is_wp_error( $created_form_id ) ) {
			return $created_form_id;
		}

		if ( ! is_int( $created_form_id ) || $created_form_id <= 0 ) {
			return new WP_Error(
				'wordpress_mcp_admin_wpforms_create_failed',
				__( 'WPForms Lite could not create the form.', 'wordpress-mcp-admin-tools' )
			);
		}

		$form_id = $created_form_id;
	}

	$form_data = $form_handler->get( $form_id, array( 'content_only' => true, 'cap' => false ) );

	if ( ! is_array( $form_data ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_wpforms_form_not_found',
			__( 'The specified WPForms Lite form could not be loaded.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( is_array( $configuration ) ) {
		$form_data = array_replace_recursive( $form_data, $configuration );
	}

	if ( '' !== $title ) {
		$form_data['settings']['form_title'] = $title;
	}

	if ( null !== $description ) {
		$form_data['settings']['form_desc'] = $description;
	}

	$updated_form_id = $form_handler->update( $form_id, $form_data, array( 'cap' => $form_id > 0 ? 'edit_form_single' : 'create_forms' ) );

	if ( ! $updated_form_id ) {
		return new WP_Error(
			'wordpress_mcp_admin_wpforms_update_failed',
			__( 'WPForms Lite could not save the form.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( null !== $status ) {
		$status_result = wp_update_post(
			array(
				'ID'          => (int) $updated_form_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $status_result ) ) {
			return $status_result;
		}
	}

	return wordpress_mcp_admin_get_contact_form_record( 'wpforms-lite', (int) $updated_form_id );
}

/**
 * コンタクトフォームを作成または更新します。
 *
 * @param string              $provider provider 名。
 * @param array<string, mixed> $input 入力値。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_save_contact_form_record( string $provider, array $input = array() ) {
	$ready = wordpress_mcp_admin_assert_contact_form_provider_ready( $provider );

	if ( is_wp_error( $ready ) ) {
		return $ready;
	}

	if ( 'contact-form-7' === $provider ) {
		return wordpress_mcp_admin_save_contact_form_7_record( $input );
	}

	return wordpress_mcp_admin_save_wpforms_lite_record( $input );
}

/**
 * メタデータ対象オブジェクトを検証します。
 *
 * @param string $object_type オブジェクト種別。
 * @param int    $object_id オブジェクト ID。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_validate_object_meta_target( string $object_type, int $object_id ) {
	if ( $object_id <= 0 ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_object_id',
			__( 'A valid object_id is required.', 'wordpress-mcp-admin-tools' )
		);
	}

	if ( 'post' === $object_type ) {
		$post = get_post( $object_id );

		if ( ! $post instanceof WP_Post ) {
			return new WP_Error(
				'wordpress_mcp_admin_post_not_found',
				__( 'The specified post could not be found.', 'wordpress-mcp-admin-tools' )
			);
		}

		return array(
			'object_type' => 'post',
			'object_id'   => (int) $post->ID,
			'target_type' => 'post',
		);
	}

	if ( 'term' === $object_type ) {
		$term = get_term( $object_id );

		if ( ! $term instanceof WP_Term ) {
			return new WP_Error(
				'wordpress_mcp_admin_term_not_found',
				__( 'The specified term could not be found.', 'wordpress-mcp-admin-tools' )
			);
		}

		return array(
			'object_type' => 'term',
			'object_id'   => (int) $term->term_id,
			'target_type' => 'term',
			'taxonomy'    => (string) $term->taxonomy,
		);
	}

	return new WP_Error(
		'wordpress_mcp_admin_invalid_object_type',
		__( 'object_type must be either post or term.', 'wordpress-mcp-admin-tools' )
	);
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
 * プラグイン翻訳更新で使用する slug を返します。
 *
 * @param string $plugin_basename プラグイン basename。
 * @return string
 */
function wordpress_mcp_admin_get_plugin_translation_slug( string $plugin_basename ): string {
	$directory = dirname( $plugin_basename );

	if ( '.' !== $directory && '' !== $directory ) {
		return sanitize_key( basename( $directory ) );
	}

	return sanitize_key( basename( $plugin_basename, '.php' ) );
}

/**
 * 対象プラグインの翻訳更新を同期します。
 *
 * @param string $plugin_basename プラグイン basename。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_sync_plugin_translation_updates( string $plugin_basename ): array {
	$plugin_slug = wordpress_mcp_admin_get_plugin_translation_slug( $plugin_basename );
	$locale      = determine_locale();
	$result      = array(
		'locale'       => $locale,
		'attempted'    => false,
		'updated'      => false,
		'plugin_slug'  => $plugin_slug,
		'translations' => array(),
		'message'      => __( 'No translation updates were available for the plugin.', 'wordpress-mcp-admin-tools' ),
	);

	if ( '' === $plugin_slug ) {
		$result['message'] = __( 'Could not determine the plugin translation slug.', 'wordpress-mcp-admin-tools' );

		return $result;
	}

	if ( ! function_exists( 'wp_get_translation_updates' ) || ! function_exists( 'wp_update_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/update.php';
	}

	if ( ! class_exists( 'Language_Pack_Upgrader' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-bulk-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-language-pack-upgrader-skin.php';
		require_once ABSPATH . 'wp-admin/includes/class-language-pack-upgrader.php';
	}

	wp_clean_update_cache();
	wp_update_plugins();

	$translation_updates = array_values(
		array_filter(
			wp_get_translation_updates(),
			static function ( $update ) use ( $plugin_slug ): bool {
				return is_object( $update )
					&& isset( $update->type, $update->slug )
					&& 'plugin' === (string) $update->type
					&& $plugin_slug === (string) $update->slug;
			}
		)
	);

	if ( empty( $translation_updates ) ) {
		$translation_glob = glob( trailingslashit( WP_LANG_DIR ) . 'plugins/' . $plugin_slug . '-' . $locale . '*' );

		if ( is_array( $translation_glob ) && ! empty( $translation_glob ) ) {
			$result['message'] = __( 'The plugin translations are already up to date.', 'wordpress-mcp-admin-tools' );
		}

		return $result;
	}

	foreach ( $translation_updates as $translation_update ) {
		$result['translations'][] = array(
			'language' => isset( $translation_update->language ) ? (string) $translation_update->language : '',
			'version'  => isset( $translation_update->version ) ? (string) $translation_update->version : '',
			'package'  => isset( $translation_update->package ) ? (string) $translation_update->package : '',
		);
	}

	$result['attempted'] = true;

	$skin            = new Language_Pack_Upgrader_Skin(
		array(
			'skip_header_footer' => true,
		)
	);
	$language_packer = new Language_Pack_Upgrader( $skin );
	ob_start();
	$upgrade_results = $language_packer->bulk_upgrade( $translation_updates );
	ob_end_clean();

	if ( is_wp_error( $upgrade_results ) ) {
		$result['message'] = $upgrade_results->get_error_message();

		return $result;
	}

	if ( false === $upgrade_results ) {
		$skin_error = $skin->get_errors();

		if ( $skin_error instanceof WP_Error && $skin_error->has_errors() ) {
			$result['message'] = $skin_error->get_error_message();
		} else {
			$result['message'] = __( 'WordPress could not complete the translation update.', 'wordpress-mcp-admin-tools' );
		}

		return $result;
	}

	$updated = false;
	$failed  = false;

	if ( true === $upgrade_results ) {
		$updated = true;
	} elseif ( is_array( $upgrade_results ) ) {
		foreach ( $upgrade_results as $upgrade_result ) {
			if ( true === $upgrade_result || is_array( $upgrade_result ) ) {
				$updated = true;
				continue;
			}

			if ( is_wp_error( $upgrade_result ) || false === $upgrade_result ) {
				$failed = true;
			}
		}
	}

	$result['updated'] = $updated && ! $failed;
	$result['message'] = $result['updated']
		? __( 'Plugin translation updates were installed successfully.', 'wordpress-mcp-admin-tools' )
		: __( 'WordPress found plugin translation updates, but one or more translation packages could not be installed.', 'wordpress-mcp-admin-tools' );

	return $result;
}

/**
 * 添付ファイルを検証して返します。
 *
 * @param int    $attachment_id 添付ファイル ID。
 * @param string $error_code エラーコード。
 * @param string $error_message エラーメッセージ。
 * @return WP_Post|WP_Error
 */
function wordpress_mcp_admin_get_attachment_post( int $attachment_id, string $error_code, string $error_message ) {
	if ( $attachment_id <= 0 ) {
		return new WP_Error( $error_code, $error_message );
	}

	$attachment = get_post( $attachment_id );

	if ( ! $attachment instanceof WP_Post || 'attachment' !== $attachment->post_type ) {
		return new WP_Error( $error_code, $error_message );
	}

	return $attachment;
}

/**
 * 添付ファイルが画像かどうかを返します。
 *
 * @param WP_Post $attachment 添付ファイル投稿。
 * @return bool
 */
function wordpress_mcp_admin_attachment_is_image( WP_Post $attachment ): bool {
	$mime_type = (string) get_post_mime_type( $attachment );

	return '' !== $mime_type && str_starts_with( $mime_type, 'image/' );
}

/**
 * 添付ファイル情報を配列に正規化します。
 *
 * @param WP_Post $attachment 添付ファイル投稿。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_media_record( WP_Post $attachment ): array {
	$attachment_url = wp_get_attachment_url( $attachment->ID );

	return array(
		'attachment_id' => (int) $attachment->ID,
		'title'         => (string) get_the_title( $attachment ),
		'url'           => is_string( $attachment_url ) ? $attachment_url : '',
		'mime_type'     => (string) get_post_mime_type( $attachment ),
		'alt_text'      => (string) get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'caption'       => (string) $attachment->post_excerpt,
		'description'   => (string) $attachment->post_content,
		'edit_link'     => (string) get_edit_post_link( $attachment->ID, 'raw' ),
	);
}

/**
 * ナビゲーションメニュー識別子からメニューを解決します。
 *
 * @param mixed $identifier メニュー ID、スラッグ、または名前。
 * @return WP_Term|WP_Error
 */
function wordpress_mcp_admin_resolve_navigation_menu( $identifier ) {
	if ( ! is_scalar( $identifier ) ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_navigation_menu',
			__( 'A valid navigation menu identifier is required.', 'wordpress-mcp-admin-tools' )
		);
	}

	$menu_identifier = trim( sanitize_text_field( (string) $identifier ) );

	if ( '' === $menu_identifier ) {
		return new WP_Error(
			'wordpress_mcp_admin_invalid_navigation_menu',
			__( 'A valid navigation menu identifier is required.', 'wordpress-mcp-admin-tools' )
		);
	}

	$menu = ctype_digit( $menu_identifier )
		? wp_get_nav_menu_object( (int) $menu_identifier )
		: wp_get_nav_menu_object( $menu_identifier );

	if ( ! $menu instanceof WP_Term ) {
		return new WP_Error(
			'wordpress_mcp_admin_navigation_menu_not_found',
			__( 'The specified navigation menu could not be found.', 'wordpress-mcp-admin-tools' )
		);
	}

	return $menu;
}

/**
 * ナビゲーションメニュー項目を配列に正規化します。
 *
 * @param WP_Post $menu_item メニュー項目投稿。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_navigation_menu_item( WP_Post $menu_item ): array {
	$prepared_item = wp_setup_nav_menu_item( $menu_item );
	$classes       = is_array( $prepared_item->classes ) ? array_values( array_filter( array_map( 'strval', $prepared_item->classes ) ) ) : array();

	return array(
		'menu_item_id' => (int) $prepared_item->ID,
		'title'        => (string) $prepared_item->title,
		'type'         => (string) $prepared_item->type,
		'object'       => (string) $prepared_item->object,
		'object_id'    => (int) $prepared_item->object_id,
		'url'          => (string) $prepared_item->url,
		'parent_id'    => (int) $prepared_item->menu_item_parent,
		'target'       => (string) $prepared_item->target,
		'classes'      => $classes,
	);
}

/**
 * 指定メニューに割り当てられたロケーション一覧を返します。
 *
 * @param int $menu_id メニュー term ID。
 * @return string[]
 */
function wordpress_mcp_admin_get_navigation_menu_locations_for_menu( int $menu_id ): array {
	$locations          = (array) get_nav_menu_locations();
	$assigned_locations = array();

	foreach ( $locations as $location => $assigned_menu_id ) {
		if ( $menu_id === (int) $assigned_menu_id ) {
			$assigned_locations[] = (string) $location;
		}
	}

	return $assigned_locations;
}

/**
 * ナビゲーションメニュー情報を配列に正規化します。
 *
 * @param WP_Term $menu メニュー term。
 * @param bool    $include_items 項目を含める場合は true。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_navigation_menu_record( WP_Term $menu, bool $include_items = false ): array {
	$items = wp_get_nav_menu_items( $menu->term_id, array( 'post_status' => 'publish,draft' ) );

	$record = array(
		'menu_id'     => (int) $menu->term_id,
		'name'        => (string) $menu->name,
		'slug'        => (string) $menu->slug,
		'description' => (string) $menu->description,
		'locations'   => wordpress_mcp_admin_get_navigation_menu_locations_for_menu( (int) $menu->term_id ),
		'items_count' => is_array( $items ) ? count( $items ) : 0,
	);

	if ( $include_items ) {
		$record['items'] = array();

		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( $item instanceof WP_Post ) {
					$record['items'][] = wordpress_mcp_admin_format_navigation_menu_item( $item );
				}
			}
		}
	}

	return $record;
}

/**
 * ブロックナビゲーション投稿を配列に正規化します。
 *
 * @param WP_Post $navigation_post ナビゲーション投稿。
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_format_navigation_post_record( WP_Post $navigation_post ): array {
	return array(
		'post_id'   => (int) $navigation_post->ID,
		'title'     => (string) get_the_title( $navigation_post ),
		'status'    => (string) get_post_status( $navigation_post ),
		'edit_link' => (string) get_edit_post_link( $navigation_post->ID, 'raw' ),
	);
}

/**
 * ブロックナビゲーション投稿の一覧を取得します。
 *
 * @param int $limit 取得件数。
 * @return array<int, array<string, mixed>>
 */
function wordpress_mcp_admin_get_navigation_posts( int $limit = 20 ): array {
	if ( ! post_type_exists( 'wp_navigation' ) ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'      => 'wp_navigation',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => max( 1, min( 50, $limit ) ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$records = array();

	foreach ( $posts as $post ) {
		if ( $post instanceof WP_Post ) {
			$records[] = wordpress_mcp_admin_format_navigation_post_record( $post );
		}
	}

	return $records;
}

/**
 * ナビゲーションロケーションを検証します。
 *
 * @param array<int, mixed> $locations ロケーション一覧。
 * @return array<int, string>|WP_Error
 */
function wordpress_mcp_admin_normalize_navigation_locations( array $locations ) {
	$registered_locations = get_registered_nav_menus();
	$normalized_locations = array();

	foreach ( $locations as $location ) {
		if ( ! is_scalar( $location ) ) {
			return new WP_Error(
				'wordpress_mcp_admin_invalid_navigation_location',
				__( 'Each navigation location must be a string.', 'wordpress-mcp-admin-tools' )
			);
		}

		$location_key = sanitize_key( (string) $location );

		if ( '' === $location_key || ! array_key_exists( $location_key, $registered_locations ) ) {
			return new WP_Error(
				'wordpress_mcp_admin_unknown_navigation_location',
				__( 'One or more navigation locations are not registered by the active theme.', 'wordpress-mcp-admin-tools' )
			);
		}

		$normalized_locations[] = $location_key;
	}

	return array_values( array_unique( $normalized_locations ) );
}

/**
 * 指定メニューをナビゲーションロケーションへ割り当てます。
 *
 * @param int               $menu_id メニュー term ID。
 * @param array<int, string> $locations ロケーション一覧。
 * @return array<int, string>
 */
function wordpress_mcp_admin_assign_navigation_menu_locations( int $menu_id, array $locations ): array {
	$current_locations = (array) get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $locations as $location ) {
		$current_locations[ $location ] = $menu_id;
	}

	set_theme_mod( 'nav_menu_locations', $current_locations );

	return wordpress_mcp_admin_get_navigation_menu_locations_for_menu( $menu_id );
}

/**
 * ナビゲーションメニュー項目を作成または置換します。
 *
 * @param int               $menu_id メニュー term ID。
 * @param array<int, mixed> $items 項目配列。
 * @param bool              $replace_existing 既存項目を置換する場合は true。
 * @return array<int, int>|WP_Error
 */
function wordpress_mcp_admin_save_navigation_menu_items( int $menu_id, array $items, bool $replace_existing ) {
	if ( $replace_existing ) {
		$existing_items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'publish,draft' ) );

		if ( is_array( $existing_items ) ) {
			foreach ( $existing_items as $existing_item ) {
				if ( $existing_item instanceof WP_Post ) {
					wp_delete_post( $existing_item->ID, true );
				}
			}
		}
	}

	$created_item_ids = array();

	foreach ( $items as $index => $item ) {
		if ( ! is_array( $item ) ) {
			return new WP_Error(
				'wordpress_mcp_admin_invalid_navigation_item',
				__( 'Each navigation item must be an object.', 'wordpress-mcp-admin-tools' )
			);
		}

		$item_type      = isset( $item['type'] ) ? sanitize_key( (string) $item['type'] ) : 'custom';
		$item_title     = isset( $item['title'] ) ? sanitize_text_field( wp_unslash( (string) $item['title'] ) ) : '';
		$item_parent_id = 0;
		$menu_item_data = array(
			'menu-item-position' => $index + 1,
			'menu-item-status'   => 'publish',
		);

		if ( isset( $item['parent_index'] ) ) {
			$parent_index = (int) $item['parent_index'];

			if ( ! array_key_exists( $parent_index, $created_item_ids ) ) {
				return new WP_Error(
					'wordpress_mcp_admin_invalid_navigation_parent',
					__( 'parent_index must refer to a previously created navigation item in the same request.', 'wordpress-mcp-admin-tools' )
				);
			}

			$item_parent_id = $created_item_ids[ $parent_index ];
		}

		switch ( $item_type ) {
			case 'page':
			case 'post':
				$object_id = isset( $item['object_id'] ) ? (int) $item['object_id'] : 0;
				$post      = get_post( $object_id );

				if ( ! $post instanceof WP_Post || 'attachment' === $post->post_type ) {
					return new WP_Error(
						'wordpress_mcp_admin_invalid_navigation_object',
						__( 'A valid page or post object_id is required for navigation items.', 'wordpress-mcp-admin-tools' )
					);
				}

				if ( 'page' === $item_type && 'page' !== $post->post_type ) {
					return new WP_Error(
						'wordpress_mcp_admin_invalid_navigation_page',
						__( 'The specified object_id is not a page.', 'wordpress-mcp-admin-tools' )
					);
				}

				$menu_item_data['menu-item-type']      = 'post_type';
				$menu_item_data['menu-item-object']    = $post->post_type;
				$menu_item_data['menu-item-object-id'] = $object_id;
				$menu_item_data['menu-item-title']     = '' !== $item_title ? $item_title : (string) get_the_title( $post );
				break;

			case 'category':
				$object_id = isset( $item['object_id'] ) ? (int) $item['object_id'] : 0;
				$term      = get_term( $object_id, 'category' );

				if ( ! $term instanceof WP_Term ) {
					return new WP_Error(
						'wordpress_mcp_admin_invalid_navigation_category',
						__( 'A valid category object_id is required for category navigation items.', 'wordpress-mcp-admin-tools' )
					);
				}

				$menu_item_data['menu-item-type']      = 'taxonomy';
				$menu_item_data['menu-item-object']    = 'category';
				$menu_item_data['menu-item-object-id'] = $term->term_id;
				$menu_item_data['menu-item-title']     = '' !== $item_title ? $item_title : (string) $term->name;
				break;

			case 'custom':
				$item_url = isset( $item['url'] ) ? esc_url_raw( wp_unslash( (string) $item['url'] ) ) : '';

				if ( '' === $item_url ) {
					return new WP_Error(
						'wordpress_mcp_admin_missing_navigation_url',
						__( 'A valid url is required for custom navigation items.', 'wordpress-mcp-admin-tools' )
					);
				}

				$menu_item_data['menu-item-type']  = 'custom';
				$menu_item_data['menu-item-url']   = $item_url;
				$menu_item_data['menu-item-title'] = '' !== $item_title ? $item_title : $item_url;
				break;

			default:
				return new WP_Error(
					'wordpress_mcp_admin_invalid_navigation_type',
					__( 'Navigation item type must be page, post, category, or custom.', 'wordpress-mcp-admin-tools' )
				);
		}

		if ( isset( $item['description'] ) ) {
			$menu_item_data['menu-item-description'] = sanitize_textarea_field( wp_unslash( (string) $item['description'] ) );
		}

		if ( isset( $item['target'] ) ) {
			$menu_item_data['menu-item-target'] = '_blank' === (string) $item['target'] ? '_blank' : '';
		}

		if ( isset( $item['classes'] ) && is_array( $item['classes'] ) ) {
			$classes = array_filter(
				array_map(
					'sanitize_html_class',
					array_map( 'strval', $item['classes'] )
				)
			);

			$menu_item_data['menu-item-classes'] = implode( ' ', $classes );
		}

		if ( $item_parent_id > 0 ) {
			$menu_item_data['menu-item-parent-id'] = $item_parent_id;
		}

		$menu_item_id = wp_update_nav_menu_item( $menu_id, 0, $menu_item_data );

		if ( is_wp_error( $menu_item_id ) || ! is_int( $menu_item_id ) || $menu_item_id <= 0 ) {
			return is_wp_error( $menu_item_id )
				? $menu_item_id
				: new WP_Error(
					'wordpress_mcp_admin_navigation_item_save_failed',
					__( 'Failed to save a navigation menu item.', 'wordpress-mcp-admin-tools' )
				);
		}

		$created_item_ids[ $index ] = $menu_item_id;
	}

	return $created_item_ids;
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

/**
 * Site Health インスタンスを取得します。
 *
 * @return WP_Site_Health
 */
function wordpress_mcp_admin_get_site_health_instance(): WP_Site_Health {
	require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';

	return WP_Site_Health::get_instance();
}

/**
 * Site Health の HTML をプレーンテキストへ整形します。
 *
 * @param string $value 整形対象文字列。
 * @return string
 */
function wordpress_mcp_admin_normalize_site_health_text( string $value ): string {
	$text = html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES, get_bloginfo( 'charset' ) );
	$text = preg_replace( '/\s+/', ' ', $text );

	return is_string( $text ) ? trim( $text ) : '';
}

/**
 * Site Health REST テスト URL からルートを抽出します。
 *
 * @param string $test_url テスト URL。
 * @return string
 */
function wordpress_mcp_admin_get_site_health_rest_route( string $test_url ): string {
	$test_path = wp_parse_url( $test_url, PHP_URL_PATH );
	$rest_path = wp_parse_url( rest_url(), PHP_URL_PATH );

	if ( ! is_string( $test_path ) || ! is_string( $rest_path ) ) {
		return '';
	}

	$rest_path = untrailingslashit( $rest_path );

	if ( '' === $rest_path || ! str_starts_with( $test_path, $rest_path ) ) {
		return '';
	}

	$route = substr( $test_path, strlen( $rest_path ) );

	return '/' . ltrim( is_string( $route ) ? $route : '', '/' );
}

/**
 * Site Health テストを実行して結果を返します。
 *
 * @param WP_Site_Health      $site_health Site Health インスタンス。
 * @param string              $test_type テスト種別。
 * @param array<string, mixed> $test テスト定義。
 * @return array<string, mixed>|null
 */
function wordpress_mcp_admin_get_site_health_test_result( WP_Site_Health $site_health, string $test_type, array $test ): ?array {
	$callback = null;

	if ( 'direct' === $test_type ) {
		if ( isset( $test['test'] ) && is_string( $test['test'] ) ) {
			$method = sprintf( 'get_test_%s', $test['test'] );

			if ( method_exists( $site_health, $method ) && is_callable( array( $site_health, $method ) ) ) {
				$callback = array( $site_health, $method );
			}
		} elseif ( isset( $test['test'] ) && is_callable( $test['test'] ) ) {
			$callback = $test['test'];
		}
	} elseif ( isset( $test['async_direct_test'] ) && is_callable( $test['async_direct_test'] ) ) {
		$callback = $test['async_direct_test'];
	}

	if ( null !== $callback ) {
		$result = apply_filters( 'site_status_test_result', call_user_func( $callback ) );

		return is_array( $result ) ? $result : null;
	}

	if ( ! empty( $test['has_rest'] ) && isset( $test['test'] ) && is_string( $test['test'] ) ) {
		$route = wordpress_mcp_admin_get_site_health_rest_route( $test['test'] );

		if ( '' === $route ) {
			return null;
		}

		$request = new WP_REST_Request( 'GET', $route );

		if ( isset( $test['headers'] ) && is_array( $test['headers'] ) ) {
			foreach ( $test['headers'] as $header_name => $header_value ) {
				if ( is_string( $header_name ) && is_scalar( $header_value ) ) {
					$request->set_header( $header_name, (string) $header_value );
				}
			}
		}

		$response = rest_do_request( $request );

		if ( is_wp_error( $response ) ) {
			return array(
				'label'       => isset( $test['label'] ) && is_string( $test['label'] ) ? $test['label'] : __( 'Site Health test failed.', 'wordpress-mcp-admin-tools' ),
				'status'      => 'critical',
				'badge'       => array(
					'label' => __( 'Site Health', 'wordpress-mcp-admin-tools' ),
					'color' => 'red',
				),
				'description' => '<p>' . esc_html( $response->get_error_message() ) . '</p>',
				'actions'     => '',
				'test'        => isset( $test['label'] ) && is_string( $test['label'] ) ? sanitize_key( $test['label'] ) : 'site_health_test',
			);
		}

		$data = $response->get_data();

		return is_array( $data ) ? $data : null;
	}

	return null;
}

/**
 * Site Health 修正定義を返します。
 *
 * @param string $fix 修正スラッグ。
 * @return array<string, string>|null
 */
function wordpress_mcp_admin_get_site_health_fix_definition( string $fix ): ?array {
	switch ( $fix ) {
		case 'flush-permalinks':
			return array(
				'fix'         => 'flush-permalinks',
				'label'       => __( 'Flush permalinks', 'wordpress-mcp-admin-tools' ),
				'description' => __( 'Rebuild the permalink rewrite rules.', 'wordpress-mcp-admin-tools' ),
			);

		case 'enable-search-engine-indexing':
			return array(
				'fix'         => 'enable-search-engine-indexing',
				'label'       => __( 'Enable search engine indexing', 'wordpress-mcp-admin-tools' ),
				'description' => __( 'Allow search engines to index this site by updating the Reading setting.', 'wordpress-mcp-admin-tools' ),
			);

		case 'update-urls-to-https':
			return array(
				'fix'         => 'update-urls-to-https',
				'label'       => __( 'Update site URLs to HTTPS', 'wordpress-mcp-admin-tools' ),
				'description' => __( 'Switch the WordPress Address and Site Address options to HTTPS when supported.', 'wordpress-mcp-admin-tools' ),
			);
	}

	return null;
}

/**
 * Site Health テストに対して実行可能な修正一覧を返します。
 *
 * @param array<string, mixed> $test_result テスト結果。
 * @return array<int, array<string, string>>
 */
function wordpress_mcp_admin_get_site_health_fixes_for_test( array $test_result ): array {
	$test_name = isset( $test_result['test'] ) ? sanitize_key( (string) $test_result['test'] ) : '';
	$status    = isset( $test_result['status'] ) ? sanitize_key( (string) $test_result['status'] ) : '';
	$fixes     = array();

	if ( 'authorization_header' === $test_name && 'recommended' === $status ) {
		if ( ! function_exists( 'got_mod_rewrite' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		if ( function_exists( 'got_mod_rewrite' ) && got_mod_rewrite() ) {
			$fix = wordpress_mcp_admin_get_site_health_fix_definition( 'flush-permalinks' );

			if ( null !== $fix ) {
				$fixes[] = $fix;
			}
		}
	}

	if ( 'search_engine_visibility' === $test_name && 'recommended' === $status && ! get_option( 'blog_public' ) ) {
		$fix = wordpress_mcp_admin_get_site_health_fix_definition( 'enable-search-engine-indexing' );

		if ( null !== $fix ) {
			$fixes[] = $fix;
		}
	}

	if ( 'https_status' === $test_name && 'good' !== $status ) {
		if ( ! function_exists( 'wp_is_https_supported' ) ) {
			require_once ABSPATH . WPINC . '/https-detection.php';
		}

		if (
			function_exists( 'wp_is_https_supported' )
			&& wp_is_https_supported()
			&& ! defined( 'WP_HOME' )
			&& ! defined( 'WP_SITEURL' )
			&& current_user_can( 'update_https' )
		) {
			$fix = wordpress_mcp_admin_get_site_health_fix_definition( 'update-urls-to-https' );

			if ( null !== $fix ) {
				$fixes[] = $fix;
			}
		}
	}

	return $fixes;
}

/**
 * Site Health の状態を返します。
 *
 * @return array<string, mixed>
 */
function wordpress_mcp_admin_get_site_health_status(): array {
	$site_health = wordpress_mcp_admin_get_site_health_instance();
	$tests       = WP_Site_Health::get_tests();
	$results     = array();
	$counts      = array(
		'good'        => 0,
		'recommended' => 0,
		'critical'    => 0,
	);
	$available_fixes = array();

	foreach ( array( 'direct', 'async' ) as $test_type ) {
		if ( empty( $tests[ $test_type ] ) || ! is_array( $tests[ $test_type ] ) ) {
			continue;
		}

		foreach ( $tests[ $test_type ] as $test_id => $test ) {
			if ( ! is_array( $test ) ) {
				continue;
			}

			$result = wordpress_mcp_admin_get_site_health_test_result( $site_health, $test_type, $test );

			if ( null === $result ) {
				continue;
			}

			$status = isset( $result['status'] ) ? sanitize_key( (string) $result['status'] ) : '';

			if ( isset( $counts[ $status ] ) ) {
				++$counts[ $status ];
			}

			$fixes = wordpress_mcp_admin_get_site_health_fixes_for_test( $result );

			foreach ( $fixes as $fix ) {
				if ( isset( $fix['fix'] ) ) {
					$available_fixes[ $fix['fix'] ] = $fix;
				}
			}

			$results[] = array(
				'id'          => is_string( $test_id ) ? $test_id : '',
				'type'        => $test_type,
				'test'        => isset( $result['test'] ) ? sanitize_key( (string) $result['test'] ) : ( is_string( $test_id ) ? $test_id : '' ),
				'label'       => isset( $result['label'] ) ? wp_strip_all_tags( (string) $result['label'] ) : '',
				'status'      => $status,
				'badge'       => array(
					'label' => isset( $result['badge']['label'] ) ? wp_strip_all_tags( (string) $result['badge']['label'] ) : '',
					'color' => isset( $result['badge']['color'] ) ? sanitize_key( (string) $result['badge']['color'] ) : '',
				),
				'description' => wordpress_mcp_admin_normalize_site_health_text( isset( $result['description'] ) ? (string) $result['description'] : '' ),
				'actions'     => wordpress_mcp_admin_normalize_site_health_text( isset( $result['actions'] ) ? (string) $result['actions'] : '' ),
				'fixes'       => $fixes,
			);
		}
	}

	return array(
		'summary'         => array(
			'good'        => $counts['good'],
			'recommended' => $counts['recommended'],
			'critical'    => $counts['critical'],
			'total'       => count( $results ),
		),
		'tests'           => $results,
		'available_fixes' => array_values( $available_fixes ),
	);
}

/**
 * Site Health 修正を実行します。
 *
 * @param string $fix 修正スラッグ。
 * @return array<string, mixed>|WP_Error
 */
function wordpress_mcp_admin_apply_site_health_fix( string $fix ) {
	$fix = sanitize_key( $fix );

	switch ( $fix ) {
		case 'flush-permalinks':
			flush_rewrite_rules();

			return array(
				'fix'     => $fix,
				'applied' => true,
				'message' => __( 'Permalink rewrite rules were flushed.', 'wordpress-mcp-admin-tools' ),
			);

		case 'enable-search-engine-indexing':
			update_option( 'blog_public', '1' );

			return array(
				'fix'               => $fix,
				'applied'           => true,
				'message'           => __( 'Search engine indexing is now enabled.', 'wordpress-mcp-admin-tools' ),
				'search_visibility' => (bool) get_option( 'blog_public' ),
			);

		case 'update-urls-to-https':
			if ( ! function_exists( 'wp_update_urls_to_https' ) ) {
				require_once ABSPATH . WPINC . '/https-migration.php';
			}

			if ( ! function_exists( 'wp_is_https_supported' ) ) {
				require_once ABSPATH . WPINC . '/https-detection.php';
			}

			if ( defined( 'WP_HOME' ) || defined( 'WP_SITEURL' ) ) {
				return new WP_Error(
					'wordpress_mcp_admin_https_constants_defined',
					__( 'WP_HOME or WP_SITEURL is defined, so the URLs cannot be updated from the database.', 'wordpress-mcp-admin-tools' )
				);
			}

			if ( ! current_user_can( 'update_https' ) ) {
				return new WP_Error(
					'wordpress_mcp_admin_cannot_update_https',
					__( 'The current user is not allowed to update the site to HTTPS.', 'wordpress-mcp-admin-tools' )
				);
			}

			if ( ! function_exists( 'wp_is_https_supported' ) || ! wp_is_https_supported() ) {
				return new WP_Error(
					'wordpress_mcp_admin_https_not_supported',
					__( 'HTTPS is not supported for this site.', 'wordpress-mcp-admin-tools' )
				);
			}

			if ( ! wp_update_urls_to_https() ) {
				return new WP_Error(
					'wordpress_mcp_admin_https_update_failed',
					__( 'WordPress could not switch the site URLs to HTTPS.', 'wordpress-mcp-admin-tools' )
				);
			}

			return array(
				'fix'      => $fix,
				'applied'  => true,
				'message'  => __( 'WordPress Address and Site Address were updated to HTTPS.', 'wordpress-mcp-admin-tools' ),
				'home_url' => home_url(),
				'site_url' => site_url(),
			);
	}

	return new WP_Error(
		'wordpress_mcp_admin_unknown_site_health_fix',
		__( 'The requested Site Health fix is not supported.', 'wordpress-mcp-admin-tools' )
	);
}
