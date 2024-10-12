<?php
// 直接のファイルアクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

add_action('admin_post_send_newsletter', 'process_send_newsletter');

function process_send_newsletter() {
    if (!isset($_POST['newsletter_nonce']) || !wp_verify_nonce($_POST['newsletter_nonce'], 'send_newsletter_nonce')) {
        wp_die('セキュリティチェックに失敗しました。');
    }

    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']); // HTMLタグを削除

    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletterz_subscribers';
    $subscribers = $wpdb->get_results("SELECT email, name FROM $table_name", ARRAY_A);

    $mail = new PHPMailer(true);

    $success = 0; // 成功した件数
    $error = 0; // エラー件数

    try {
        // サーバー設定
        $mail->isSMTP();
        $mail->Host       = get_option('newsletterz_smtp_host');
        $mail->SMTPAuth   = true;
        $mail->Username   = get_option('newsletterz_smtp_username');
        $mail->Password   = get_option('newsletterz_smtp_password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = get_option('newsletterz_smtp_port');

        // 文字エンコーディングの設定
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // 送信者情報
        $mail->setFrom(get_option('newsletterz_from_email'), get_option('newsletterz_from_name'));

        // コンテンツ
        $mail->isHTML(false); // HTMLメールを無効化
        $mail->Subject = $subject;
        $mail->Body    = $message;

        foreach ($subscribers as $subscriber) {
            $mail->addAddress($subscriber['email'], $subscriber['name']);
            $mail->send();
            $success++;
            $mail->clearAddresses();
        }

        // 成功時のリダイレクト
        wp_redirect(admin_url('admin.php?page=newsletterz-send&message=sent&success=' . $success . '&error=' . $error));
        exit;
    } catch (Exception $e) {
        // エラー時のリダイレクト
        wp_redirect(admin_url('admin.php?page=newsletterz-send&message=error'));
        exit;
    }
}
