<?php
// テンプレート管理ページの表示
function newsletterz_template_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletterz_templates';

    // テンプレートの保存
    if (isset($_POST['save_template'])) {
        $template_name = sanitize_text_field($_POST['template_name']);
        $template_content = wp_kses_post($_POST['template_content']);

        if ($template_name && $template_content) {
            $wpdb->insert($table_name, [
                'template_name' => $template_name,
                'template_content' => $template_content
            ]);
            echo "<div class='updated'><p>テンプレートが保存されました。</p></div>";
        }
    }

    // テンプレートの削除
    if (isset($_POST['delete_template'])) {
        $template_id = intval($_POST['template_id']);
        $wpdb->delete($table_name, ['id' => $template_id]);
        echo "<div class='updated'><p>テンプレートが削除されました。</p></div>";
    }

    // テンプレートリストの取得
    $templates = $wpdb->get_results("SELECT * FROM $table_name");

    ?>
    <div class="wrap">
        <h1>テンプレート管理</h1>

        <!-- 新しいテンプレートの作成フォーム -->
        <h2>新しいテンプレートを作成</h2>
        <form method="POST">
            <label for="template_name">テンプレート名</label>
            <input type="text" name="template_name" required>
            <br>
            <label for="template_content">テンプレート内容</label>
            <textarea name="template_content" rows="10" cols="50" required></textarea>
            <br>
            <input type="submit" name="save_template" value="テンプレートを保存" class="button button-primary">
        </form>

        <!-- 保存されたテンプレートの表示 -->
        <h2>保存されたテンプレート</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>テンプレート名</th>
                    <th>内容</th>
                    <th>作成日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $template) : ?>
                    <tr>
                        <td><?php echo esc_html($template->id); ?></td>
                        <td><?php echo esc_html($template->template_name); ?></td>
                        <td><?php echo esc_html(wp_trim_words($template->template_content, 10)); ?></td>
                        <td><?php echo esc_html($template->created_at); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="template_id" value="<?php echo esc_attr($template->id); ?>">
                                <input type="submit" name="delete_template" value="削除" class="button button-secondary">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
