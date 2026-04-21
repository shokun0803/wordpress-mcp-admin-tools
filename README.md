# MCP Admin Tools for WordPress

MCP クライアントから WordPress の管理操作を行うための Ability を登録するプラグインです。

このプラグインは WordPress の Abilities API と WordPress の MCP Adapter を前提に動作します。投稿や固定ページの作成・更新・削除、テーマの追加・作成・編集・一覧取得・削除、プラグインの追加・作成・更新・一覧取得・削除、一部の一般設定更新、サイトヘルス状態の取得、管理画面で実行可能な一部のサイトヘルス修正、実行監査ログの取得を MCP 経由で利用できます。

## 前提条件

- WordPress 6.9 以上
- [WordPress MCP Adapter](https://github.com/WordPress/mcp-adapter) がインストール済みで有効化されていること
- Ability を実行するユーザーに適切な権限があること

## 主な機能

- 投稿の新規作成
- 投稿の更新
- 投稿の削除またはゴミ箱移動
- 固定ページの新規作成
- 固定ページの更新
- WordPress.org からのテーマ追加
- 新規テーマ雛形の作成
- インストール済みテーマファイルの編集
- インストール済みテーマファイルの読取
- インストール済みテーマ一覧の取得
- インストール済みテーマの削除
- WordPress.org からのプラグイン追加
- 新規プラグイン雛形の作成
- インストール済みプラグインファイルの読取
- インストール済みプラグインファイルの更新
- インストール済みプラグイン一覧の取得
- インストール済みプラグインの削除
- サイトタイトルとキャッチフレーズの更新
- サイトヘルス状態の取得
- サイトヘルスから実行可能な修正の一部実行
- MCP 経由の実行監査ログ取得
- WordPress 管理画面の Tools 配下での Activity Log 表示

## 公開される Ability

このプラグインは以下の Ability を `wordpress-mcp-admin/*` 名前空間で登録します。

- `wordpress-mcp-admin/create-post`
- `wordpress-mcp-admin/update-post`
- `wordpress-mcp-admin/delete-post`
- `wordpress-mcp-admin/create-page`
- `wordpress-mcp-admin/update-page`
- `wordpress-mcp-admin/edit-page-blocks`
- `wordpress-mcp-admin/edit-post-blocks`
- `wordpress-mcp-admin/get-page-blocks`
- `wordpress-mcp-admin/get-post-blocks`
- `wordpress-mcp-admin/edit-page-design`
- `wordpress-mcp-admin/install-theme`
- `wordpress-mcp-admin/create-theme`
- `wordpress-mcp-admin/get-theme-file`
- `wordpress-mcp-admin/edit-theme`
- `wordpress-mcp-admin/get-themes`
- `wordpress-mcp-admin/delete-theme`
- `wordpress-mcp-admin/install-plugin`
- `wordpress-mcp-admin/create-plugin`
- `wordpress-mcp-admin/get-plugin-file`
- `wordpress-mcp-admin/update-plugin`
- `wordpress-mcp-admin/get-plugins`
- `wordpress-mcp-admin/delete-plugin`
- `wordpress-mcp-admin/activate-plugin`
- `wordpress-mcp-admin/deactivate-plugin`
- `wordpress-mcp-admin/enable-plugin-auto-update`
- `wordpress-mcp-admin/disable-plugin-auto-update`
- `wordpress-mcp-admin/get-site-health-status`
- `wordpress-mcp-admin/run-site-health-fix`
- `wordpress-mcp-admin/update-general-settings`
- `wordpress-mcp-admin/get-audit-log`

これらの Ability は `mcp.public` を有効にしているため、MCP Adapter のデフォルトサーバから discover できます。

## インストール

1. このディレクトリを `wp-content/plugins/wordpress-mcp-admin-tools` に配置します。
2. WordPress 管理画面、または WP-CLI でプラグインを有効化します。
3. `mcp-adapter` プラグインが有効であることを確認します。

WP-CLI 例:

```bash
wp plugin activate wordpress-mcp-admin-tools --allow-root --path=/var/www/html
```

## MCP での確認例

### Ability 一覧の確認

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-discover-abilities","arguments":{}}}
EOF
```

### 投稿作成の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/create-post","parameters":{"title":"MCP Post","content":"Created via MCP.","status":"draft"}}}}
EOF
```

### 監査ログ取得の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-audit-log","parameters":{"limit":10}}}}
EOF
```

### サイトヘルス状態取得の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-site-health-status","parameters":{}}}}
EOF
```

### サイトヘルス修正実行の例

現在は以下の修正をサポートします。

- `flush-permalinks`
- `enable-search-engine-indexing`
- `update-urls-to-https`

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/run-site-health-fix","parameters":{"fix":"flush-permalinks"}}}}
EOF
```

### テーマ追加の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/install-theme","parameters":{"slug":"twentytwentyfour","activate":false}}}}
EOF
```

### テーマ作成の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/create-theme","parameters":{"slug":"mcp-custom-theme","name":"MCP Custom Theme","type":"block","description":"Created via MCP.","activate":false}}}}
EOF
```

### テーマ編集の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/edit-theme","parameters":{"theme":"mcp-custom-theme","relative_path":"templates/index.html","content":"<!-- wp:paragraph --><p>Updated via MCP.</p><!-- /wp:paragraph -->","create_missing":true}}}}
EOF
```

### テーマファイル読取の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-theme-file","parameters":{"theme":"twentytwentyfour","relative_path":"functions.php"}}}}
EOF
```

### 固定ページ本文のブロック編集例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/edit-page-blocks","parameters":{"page_id":123,"content":"<!-- wp:cover {\"dimRatio\":40,\"minHeight\":360} -->
<div class=\"wp-block-cover\"><span aria-hidden=\"true\" class=\"wp-block-cover__background has-background-dim-40 has-background-dim\"></span><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"textAlign\":\"center\",\"level\":1} -->
<h1 class=\"wp-block-heading has-text-align-center\">Landing Page</h1>
<!-- /wp:heading --><!-- wp:paragraph {\"align\":\"center\"} -->
<p class=\"has-text-align-center\">Updated via MCP block content.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->"}}}}
EOF
```

### 固定ページ本文のブロック取得例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-page-blocks","parameters":{"page_id":123}}}}
EOF
```

### 投稿やカスタム投稿タイプ本文のブロック編集例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/edit-post-blocks","parameters":{"post_id":456,"content":"<!-- wp:columns --><div class=\"wp-block-columns\"><!-- wp:column --><div class=\"wp-block-column\"><!-- wp:heading --><h2>Custom Entry</h2><!-- /wp:heading --></div><!-- /wp:column --></div><!-- /wp:columns -->"}}}}
EOF
```

### 投稿やカスタム投稿タイプ本文のブロック取得例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-post-blocks","parameters":{"post_id":456}}}}
EOF
```

### 固定ページデザイン編集の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/edit-page-design","parameters":{"page_id":123,"content":"<!-- wp:template-part {\"slug\":\"header\",\"tagName\":\"header\"} /-->
<!-- wp:group {\"tagName\":\"main\",\"layout\":{\"type\":\"constrained\"}} -->
<main class=\"wp-block-group\"><!-- wp:post-title {\"level\":1} /-->
<!-- wp:post-content /--></main>
<!-- /wp:group -->
<!-- wp:template-part {\"slug\":\"footer\",\"tagName\":\"footer\"} /-->"}}}}
EOF
```

### テーマ一覧取得の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-themes","parameters":{"include_inactive":true}}}}
EOF
```

### テーマ削除の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/delete-theme","parameters":{"theme":"mcp-custom-theme"}}}}
EOF
```

### プラグイン追加の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/install-plugin","parameters":{"slug":"classic-editor","activate":false}}}}
EOF
```

### プラグイン作成の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/create-plugin","parameters":{"slug":"mcp-custom-plugin","name":"MCP Custom Plugin","description":"Created via MCP.","activate":false}}}}
EOF
```

### プラグイン更新の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/update-plugin","parameters":{"plugin":"mcp-custom-plugin","relative_path":"includes/bootstrap.php","content":"<?php\n\ndeclare( strict_types = 1 );\n\nadd_action( 'init', static function (): void {\n\t// Updated via MCP.\n} );\n","create_missing":true}}}}
EOF
```

### プラグインファイル読取の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-plugin-file","parameters":{"plugin":"classic-editor","relative_path":"classic-editor.php"}}}}
EOF
```

### サイトヘルスのコード調査に関する考え方

Site Health の一部の問題は、未使用テーマや未使用プラグインの削除のように直接修正できるだけでなく、アクティブな子テーマの functions.php やプラグインファイルの修正で改善できる場合があります。

このため、`get-site-health-status` で問題を把握し、`get-themes` や `get-plugins` で対象候補を絞り込み、`get-theme-file` / `get-plugin-file` で既存コードを確認してから `edit-theme` / `update-plugin` で差分反映する流れを推奨します。

### プラグイン一覧取得の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-plugins","parameters":{"include_inactive":true}}}}
EOF
```

### プラグイン削除の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/delete-plugin","parameters":{"plugin":"mcp-custom-plugin"}}}}
EOF
```

### プラグイン有効化の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/activate-plugin","parameters":{"plugin":"mcp-custom-plugin"}}}}
EOF
```

### プラグイン無効化の例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/deactivate-plugin","parameters":{"plugin":"mcp-custom-plugin"}}}}
EOF
```

### プラグイン自動更新を有効化する例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/enable-plugin-auto-update","parameters":{"plugin":"classic-editor"}}}}
EOF
```

### プラグイン自動更新を無効化する例

```bash
cat <<'EOF' | wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/disable-plugin-auto-update","parameters":{"plugin":"classic-editor"}}}}
EOF
```

## 管理画面

有効化後、WordPress 管理画面の Tools 配下に `MCP Activity Log` が追加されます。

この画面では以下を確認できます。

- 実行日時
- Ability 名
- 成功または失敗
- 対象オブジェクト
- 入力要約
- エラーコードとエラーメッセージ

## 監査ログについて

- 監査ログは WordPress option に保存されます。
- 最新 50 件を保持します。
- 旧 namespace のログが残っている場合は、新 namespace へ自動移行されます。

## 注意事項

- このプラグインは WordPress の管理操作を外部クライアントから実行できるようにします。
- 本番環境では、MCP Adapter の接続経路、認証、実行ユーザー権限を必ず制御してください。
- 破壊的な Ability を有効にする場合は、監査ログ確認と権限分離を推奨します。