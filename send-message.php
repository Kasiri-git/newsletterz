<?php
// 直接のファイルアクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

// CSSファイルを読み込む関数
function newsletterz_enqueue_styles() {
    wp_enqueue_style('newsletterz-styles', plugin_dir_url(__FILE__) . 'newsletterz-styles.css');
}
add_action('admin_enqueue_scripts', 'newsletterz_enqueue_styles');

function newsletterz_send_page() {
    global $wpdb;
    $table_template = $wpdb->prefix . 'newsletterz_templates';
    $templates = $wpdb->get_results("SELECT * FROM $table_template");

    ?>
    <div class="wrap">
        <h1>メッセージ送信</h1>
        <?php
        // メッセージの表示
        if (isset($_GET['message']) && $_GET['message'] == 'sent') {
            $success = isset($_GET['success']) ? intval($_GET['success']) : 0;
            $error = isset($_GET['error']) ? intval($_GET['error']) : 0;
            echo '<div class="updated"><p>' . sprintf('メッセージを送信しました。成功: %d, 失敗: %d', $success, $error) . '</p></div>';
        } elseif (isset($_GET['message']) && $_GET['message'] == 'error') {
            echo '<div class="error"><p>メッセージの送信中にエラーが発生しました。</p></div>';
        }
        ?>
        <div class="newsletter-container">
            <div class="newsletter-form">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="send_newsletter">
                    <?php wp_nonce_field('send_newsletter_nonce', 'newsletter_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><label for="subject">件名:</label></th>
                            <td><input type="text" id="subject" name="subject" required></td>
                        </tr>
                        <tr>
                            <th><label for="template">テンプレート:</label></th>
                            <td>
                                <select id="template" name="template_id" onchange="populateMessage()">
                                    <option value="">選択してください</option>
                                    <?php foreach ($templates as $template) : ?>
                                        <option value="<?php echo esc_attr($template->id); ?>" data-content="<?php echo esc_attr($template->template_content); ?>">
                                            <?php echo esc_html($template->template_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="message">本文:</label></th>
                            <td>
                                <textarea id="message" name="message" rows="15" required></textarea>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="send_newsletter" class="button-primary" value="送信">
                    </p>
                </form>
            </div>
            <div class="newsletter-preview">
                <h2>プレビュー</h2>
                <div id="preview"></div>
            </div>
        </div>
    </div>

    <script>
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        function populateMessage() {
            var templateSelect = document.getElementById('template');
            var messageTextarea = document.getElementById('message');
            var previewDiv = document.getElementById('preview');
            var selectedOption = templateSelect.options[templateSelect.selectedIndex];
            var content = selectedOption.dataset.content || '';
            messageTextarea.value = content;
            updatePreview(content);
        }

        function updatePreview(content) {
            var previewDiv = document.getElementById('preview');
            var escapedContent = escapeHtml(content);
            previewDiv.innerHTML = escapedContent.replace(/\n/g, '<br>');
        }

        document.getElementById('message').addEventListener('input', function() {
            updatePreview(this.value);
        });
    </script>
    <?php
}
