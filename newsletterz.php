<?php
/*
Plugin Name: Newsletterz
Plugin URI:  https://kasiri.icu/blog/wordpress/1545/
Description: ニュースレター送信用のプラグインです。テンプレート機能付き。
Version:     1.2.0
Author:      Kasiri
Author URI:  https://kasiri.icu/
*/

// 他のプラグインファイルを読み込む
require_once plugin_dir_path(__FILE__) . 'smtp-settings.php';
require_once plugin_dir_path(__FILE__) . 'send-list.php';
require_once plugin_dir_path(__FILE__) . 'list-registration.php';
require_once plugin_dir_path(__FILE__) . 'send-message.php';
require_once plugin_dir_path(__FILE__) . 'process-message.php';
require_once plugin_dir_path(__FILE__) . 'template-management.php';
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

// プラグインメニューを追加
function newsletterz_menu() {
    add_menu_page(
        'Newsletterz',
        'Newsletterz',
        'manage_options',
        'newsletterz',
        'newsletterz_main_page',
        'dashicons-email'
    );
    add_submenu_page('newsletterz', 'SMTP設定', 'SMTP設定', 'manage_options', 'newsletterz-smtp', 'newsletterz_smtp_page');
    add_submenu_page('newsletterz', '送信リスト', '送信リスト', 'manage_options', 'newsletterz-list', 'newsletterz_list_page');
    add_submenu_page('newsletterz', 'リスト登録', 'リスト登録', 'manage_options', 'newsletterz-registration', 'newsletterz_registration_page');
    add_submenu_page('newsletterz', 'テンプレート管理', 'テンプレート管理', 'manage_options', 'newsletterz-template', 'newsletterz_template_page'); // テンプレート管理ページ追加
    add_submenu_page('newsletterz', 'メッセージ送信', 'メッセージ送信', 'manage_options', 'newsletterz-send', 'newsletterz_send_page');
}
add_action('admin_menu', 'newsletterz_menu');

// プラグインのインストール時に実行する関数
function newsletterz_install() {
    global $wpdb;

    // マルチサイトでのテーブルプレフィックスの取得
    $table_name = $wpdb->prefix . 'newsletterz_subscribers';
    $table_template = $wpdb->prefix . 'newsletterz_templates';

    $charset_collate = $wpdb->get_charset_collate();

    $sql_subscribers = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(100) NOT NULL,
        name varchar(100) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    $sql_templates = "CREATE TABLE IF NOT EXISTS $table_template (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        template_name varchar(100) NOT NULL,
        template_content longtext NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_subscribers);
    dbDelta($sql_templates); // テンプレートテーブルの作成
}

// プラグインのアクティベーション時にインストール関数を実行
register_activation_hook(__FILE__, 'newsletterz_install');

// ネットワークアクティベーション時に実行する関数
function newsletterz_network_activate() {
    newsletterz_install();
}
register_activation_hook(__FILE__, 'newsletterz_network_activate');


// メインプラグインページ
function newsletterz_main_page() {
    ?>

<div class="wrap">
    <h1>Newsletterz</h1>
    <p>WordPressからSMTPを使用してメールマガジンを発行します。<br>メール配信数は配信メールの制限に起因するので、使用するメールアドレスの制限事項を確認してください。</p>

    <h2>ショートコードの使用方法</h2>
    <p>以下のショートコードを使用して、購読フォームや購読解除フォームをページや投稿に簡単に追加できます。</p>
    
    <ul>
        <li><strong>購読フォーム:</strong> <code>[newsletterz_subscribe]</code></li>
        <li><strong>購読解除フォーム:</strong> <code>[newsletterz_unsubscribe]</code></li>
    </ul>

    <h2>メール配信数の制限事項の例</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>サービス名</th>
                <th>プラン名</th>
                <th>1時間あたりの送信件数</th>
                <th>24時間あたりの送信件数</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Xサーバー</td>
                <td>全プラン共通</td>
                <td>1,500通</td>
                <td>15,000通</td>
            </tr>
            <tr>
                <td>シンレンタルサーバー</td>
                <td>全プラン共通</td>
                <td>1,500通</td>
                <td>15,000通</td>
            </tr>
            <tr>
                <td>さくらインターネット</td>
                <td>ライト/スタンダード/プレミアム</td>
                <td>400通</td>
                <td>9,600通</td>
            </tr>
            <tr>
                <td>さくらインターネット</td>
                <td>ビジネス/ビジネスプロ</td>
                <td>1,000通</td>
                <td>24,000通</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>エコノミープラン</td>
                <td>100件</td>
                <td>1,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>ライトプラン</td>
                <td>300件</td>
                <td>3,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>スタンダードプラン</td>
                <td>1,000件</td>
                <td>10,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>ハイスピードプラン</td>
                <td>1,000件</td>
                <td>10,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>エンタープライズプラン</td>
                <td>1,000件</td>
                <td>10,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>ロリポップ！for Education</td>
                <td>1,000件</td>
                <td>10,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>チカッパ優待プラン</td>
                <td>1,000件</td>
                <td>10,000件</td>
            </tr>
            <tr>
                <td>ロリポップ！</td>
                <td>ずっと無料プラン</td>
                <td>1,000件</td>
                <td>10,000件</td>
            </tr>
            <tr>
                <td>ConoHa WING</td>
                <td>非公開</td>
                <td>非公開</td>
                <td>非公開</td>
            </tr>
        </tbody>
    </table>
</div>

    <?php
}