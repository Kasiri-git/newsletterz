<?php
// 直接のファイルアクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

function newsletterz_smtp_page() {
    // 設定の保存
    if (isset($_POST['save_settings'])) {
        update_option('newsletterz_smtp_host', sanitize_text_field($_POST['smtp_host']));
        update_option('newsletterz_smtp_username', sanitize_email($_POST['smtp_username']));
        update_option('newsletterz_smtp_password', sanitize_text_field($_POST['smtp_password']));
        update_option('newsletterz_smtp_port', absint($_POST['smtp_port']));
        update_option('newsletterz_from_email', sanitize_email($_POST['from_email']));
        update_option('newsletterz_from_name', sanitize_text_field($_POST['from_name']));
        echo '<div class="updated"><p>設定が正常に保存されました。</p></div>';
    }

    // 現在の設定を取得
        $smtp_host = get_option('newsletterz_smtp_host', 'smtp.example.com');
        $smtp_username = get_option('newsletterz_smtp_username', 'sample@example.com');
        $smtp_password = get_option('newsletterz_smtp_password', 'sample_password');
        $smtp_port = get_option('newsletterz_smtp_port', 587);
        $from_email = get_option('newsletterz_from_email', 'noreply@example.com');
        $from_name = get_option('newsletterz_from_name', 'Sample Newsletter');
    ?>

    <div class="wrap">
        <h1>SMTP設定</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="smtp_host">SMTPホスト:</label></th>
                    <td><input type="text" id="smtp_host" name="smtp_host" value="<?php echo esc_attr($smtp_host); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_username">SMTPユーザー名:</label></th>
                    <td><input type="email" id="smtp_username" name="smtp_username" value="<?php echo esc_attr($smtp_username); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_password">SMTPパスワード:</label></th>
                    <td><input type="password" id="smtp_password" name="smtp_password" value="<?php echo esc_attr($smtp_password); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="smtp_port">SMTPポート:</label></th>
                    <td><input type="number" id="smtp_port" name="smtp_port" value="<?php echo esc_attr($smtp_port); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="from_email">送信元メールアドレス:</label></th>
                    <td><input type="email" id="from_email" name="from_email" value="<?php echo esc_attr($from_email); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="from_name">送信者名:</label></th>
                    <td><input type="text" id="from_name" name="from_name" value="<?php echo esc_attr($from_name); ?>" required></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="save_settings" class="button-primary" value="設定を保存">
            </p>
        </form>
    </div>
    <?php
}