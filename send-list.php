<?php
// 直接のファイルアクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

function newsletterz_list_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletterz_subscribers';

    // ページネーション設定
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $offset = ($current_page - 1) * $per_page;

    // 総数を取得
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

    // リストを取得（IDが若い順に変更）
    $subscribers = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY id ASC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ),
        ARRAY_A
    );

    // 編集処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscriber_id'])) {
        $subscriber_id = absint($_POST['subscriber_id']);
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name']);
        
        // 更新処理
        $wpdb->update(
            $table_name,
            array(
                'email' => $email,
                'name' => $name,
            ),
            array('id' => $subscriber_id)
        );

        wp_redirect(admin_url('admin.php?page=newsletterz-list'));
        exit;
    }

    ?>
    <div class="wrap">
        <h1>送信リスト</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>メールアドレス</th>
                    <th>名前</th>
                    <th>登録日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $subscriber): ?>
                    <tr>
                        <td><?php echo esc_html($subscriber['id']); ?></td>
                        <td><?php echo esc_html($subscriber['email']); ?></td>
                        <td><?php echo esc_html($subscriber['name']); ?></td>
                        <td><?php echo esc_html($subscriber['created_at']); ?></td>
                        <td>
                            <button class="button edit-button" onclick="toggleEditForm(<?php echo esc_attr($subscriber['id']); ?>)">編集</button>
                            <a href="<?php echo admin_url('admin.php?page=newsletterz-list&action=delete&id=' . esc_attr($subscriber['id'])); ?>" class="button button-danger" onclick="return confirm('本当に削除しますか？');">削除</a>
                        </td>
                    </tr>
                    <tr class="edit-row" id="edit-form-<?php echo esc_attr($subscriber['id']); ?>" style="display:none;">
                        <td colspan="5">
                            <form method="post">
                                <input type="hidden" name="subscriber_id" value="<?php echo esc_attr($subscriber['id']); ?>">
                                <table class="form-table">
                                    <tr>
                                        <th><label for="email">メールアドレス</label></th>
                                        <td><input type="email" name="email" id="email" value="<?php echo esc_attr($subscriber['email']); ?>" required></td>
                                    </tr>
                                    <tr>
                                        <th><label for="name">名前</label></th>
                                        <td><input type="text" name="name" id="name" value="<?php echo esc_attr($subscriber['name']); ?>" required></td>
                                    </tr>
                                </table>
                                <?php submit_button('保存'); ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        // ページネーションを表示
        $total_pages = ceil($total_items / $per_page);
        echo paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $total_pages,
            'current' => $current_page
        ));
        ?>
    </div>
    <script>
        function toggleEditForm(id) {
            const formRow = document.getElementById('edit-form-' + id);
            formRow.style.display = formRow.style.display === 'none' ? '' : 'none';
        }
    </script>
    <?php

    // 削除処理
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $delete_id = absint($_GET['id']);
        $wpdb->delete($table_name, array('id' => $delete_id));
        wp_redirect(admin_url('admin.php?page=newsletterz-list'));
        exit;
    }
}
?>
