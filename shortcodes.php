<?php
// メルマガ購読フォームのショートコード
function newsletterz_subscribe_form() {
    ob_start();
    ?>
    <form method="post" action="">
        <h3>ニュースレター購読</h3>
        <p>
            <label for="newsletterz_name">お名前:</label>
            <input type="text" name="newsletterz_name" required>
        </p>
        <p>
            <label for="newsletterz_email">メールアドレス:</label>
            <input type="email" name="newsletterz_email" required>
        </p>
        <p>
            <input type="submit" name="newsletterz_subscribe" value="購読">
        </p>
    </form>
    <?php

    // フォーム送信処理
    if (isset($_POST['newsletterz_subscribe'])) {
        global $wpdb;
        $name = sanitize_text_field($_POST['newsletterz_name']);
        $email = sanitize_email($_POST['newsletterz_email']);
        $table_name = $wpdb->prefix . 'newsletterz_subscribers';

        // メールアドレスが既に存在するかを確認
        $subscriber = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));

        if ($subscriber) {
            echo "<p>既にこのメールアドレスで購読されています。</p>";
        } else {
            $wpdb->insert($table_name, array(
                'name' => $name,
                'email' => $email,
                'created_at' => current_time('mysql'),
            ));
            echo "<p>ニュースレターを購読しました。</p>";
        }
    }

    return ob_get_clean();
}
add_shortcode('newsletterz_subscribe', 'newsletterz_subscribe_form');

// メルマガ購読解除フォームのショートコード
function newsletterz_unsubscribe_form() {
    ob_start();
    ?>
    <form method="post" action="">
        <h3>ニュースレター購読解除</h3>
        <p>
            <label for="newsletterz_email">メールアドレス:</label>
            <input type="email" name="newsletterz_email" required>
        </p>
        <p>
            <input type="submit" name="newsletterz_unsubscribe" value="購読解除">
        </p>
    </form>
    <?php

    // フォーム送信処理
    if (isset($_POST['newsletterz_unsubscribe'])) {
        global $wpdb;
        $email = sanitize_email($_POST['newsletterz_email']);
        $table_name = $wpdb->prefix . 'newsletterz_subscribers';

        // メールアドレスが存在するかを確認
        $subscriber = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));

        if ($subscriber) {
            $wpdb->delete($table_name, array('email' => $email));
            echo "<p>ニュースレターの購読を解除しました。</p>";
        } else {
            echo "<p>このメールアドレスは購読されていません。</p>";
        }
    }

    return ob_get_clean();
}
add_shortcode('newsletterz_unsubscribe', 'newsletterz_unsubscribe_form');
?>
