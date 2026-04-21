# MCP Admin Tools for WordPress

MCP クライアントから WordPress の管理操作を行うための Ability を登録するプラグインです。

このプラグインは WordPress の Abilities API と WordPress の MCP Adapter を前提に動作します。投稿や固定ページの作成・更新・削除、一部の一般設定更新、実行監査ログの取得を MCP 経由で利用できます。

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
- サイトタイトルとキャッチフレーズの更新
- MCP 経由の実行監査ログ取得
- WordPress 管理画面の Tools 配下での Activity Log 表示

## 公開される Ability

このプラグインは以下の Ability を `wordpress-mcp-admin/*` 名前空間で登録します。

- `wordpress-mcp-admin/create-post`
- `wordpress-mcp-admin/update-post`
- `wordpress-mcp-admin/delete-post`
- `wordpress-mcp-admin/create-page`
- `wordpress-mcp-admin/update-page`
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
printf '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-discover-abilities","arguments":{}}}' \
| wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
```

### 投稿作成の例

```bash
printf '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/create-post","parameters":{"title":"MCP Post","content":"Created via MCP.","status":"draft"}}}}' \
| wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
```

### 監査ログ取得の例

```bash
printf '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp-adapter-execute-ability","arguments":{"ability_name":"wordpress-mcp-admin/get-audit-log","parameters":{"limit":10}}}}' \
| wp mcp-adapter serve --allow-root --user=1 --server=mcp-adapter-default-server --path=/var/www/html
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