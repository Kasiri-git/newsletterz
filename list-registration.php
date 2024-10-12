<?php
// 直接のファイルアクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

function newsletterz_registration_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletterz_subscribers';

    if (isset($_POST['register_subscriber'])) {
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name']);

        if (is_email($email)) {
            $result = $wpdb->insert(
                $table_name,
                array(
                    'email' => $email,
                    'name' => $name,
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%s')
            );

            if ($result) {
                echo '<div class="updated"><p>購読者が正常に登録されました。</p></div>';
            } else {
                echo '<div class="error"><p>購読者の登録中にエラーが発生しました。</p></div>';
            }
        } else {
            echo '<div class="error"><p>有効なメールアドレスを入力してください。</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>リスト登録</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="email">メールアドレス:</label></th>
                    <td><input type="email" id="email" name="email" required></td>
                </tr>
                <tr>
                    <th><label for="name">名前:</label></th>
                    <td><input type="text" id="name" name="name" required></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="register_subscriber" class="button-primary" value="登録">
            </p>
        </form>
    </div>
    <?php
}