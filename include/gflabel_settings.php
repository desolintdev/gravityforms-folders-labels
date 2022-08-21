<?php defined('ABSPATH') or die('No script please!');

if (!current_user_can('install_plugins')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'gfolders'));
}

global $wpdb;


if (isset($_POST['genre'])) {
    $label_name = esc_sql(sanitize_text_field($_POST['genre']));
    $items = $wpdb->get_results($wpdb->prepare("SELECT  gfform_id FROM $wpdb->prefix{gf_label_tags} WHERE gf_gfolder= '%s'", $label_name));
    $data['result'] = $items;
    echo json_encode($data);
}


if (isset($_POST["submit"])) {
    if (!wp_verify_nonce($_POST['gflabel_nonce'], 'create_gflabel')) {
        wp_die('Our Site is protected!!');
    } else {
        $rows = array(
            array(
                'gf_gfolder' => esc_sql(sanitize_text_field($_REQUEST['gf_label_name'])) == '' ? esc_sql(sanitize_text_field($_REQUEST['gf_label_name1'])) : esc_sql(sanitize_text_field($_REQUEST['gf_label_name'])),
            )
        );
        if ($wpdb->get_var("SELECT COUNT(*) FROM $wpdb->prefix{gf_label_tags} WHERE gf_gfolder = '$gf_label_name'") > 0) {
            wp_redirect(add_query_arg('label-added', 'failure', get_site_url() . '/wp-admin/admin.php?page=flgf_gform_labels'));
        } else {
            foreach ($rows as $row) {
                $wpdb->insert($wpdb->prefix."gf_label_tags", $row);
            }
            wp_redirect(add_query_arg('label-added', 'success', get_site_url() . '/wp-admin/admin.php?page=flgf_gform_labels'));
        }
    }
}

?>


<div class="wrap gfolders_setting_sec">


    <?php if (filter_input(INPUT_GET, 'label-added') === 'success') { ?>

        <div class="alert alert-success">
            <strong>Success!</strong> Label Successfully Added!
        </div>

    <?php } elseif (filter_input(INPUT_GET, 'label-added') === 'failure') { ?>

        <div class="alert alert-danger">
            <strong>Sorry!</strong> Label Already Exists!
        </div>

    <?php } elseif (filter_input(INPUT_GET, 'label-added') === 'limit-reached') { ?>

        <div class="alert alert-danger">
            <strong>Sorry!</strong> You've reached your maximum limit! You need to upgrade you version.
        </div>

    <?php } ?>

    <div class="wrap gfolders_setting_sec">
        <h2><?php echo esc_html("Add New Label"); ?> </h2>
        <div class="gf_label_text"><?php _e('You can add label to the desire form', 'gfolders'); ?>:</div>
        <!--</div>-->

        <?php
        $gformLists = $wpdb->get_results("SELECT id, title FROM $wpdb->prefix{gf_form} ORDER BY date_created ASC");
        $gformFolders = $wpdb->get_results("SELECT DISTINCT gf_gfolder  FROM $wpdb->prefix{gf_label_tags} ORDER BY gf_gfolder ASC");
        ?>

        <form class="nav-tab-content gf_label_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <?php wp_nonce_field('create_gflabel', 'gflabel_nonce'); ?>

            <!--<input type="hidden" name="gfolders_gf" value="<?php // echo wp_create_nonce($_GET['t']); ?>" />-->


            <div class="input-group" id="new-folder-name">
                <label for="sel1">Type Folder Name:</label>
                <input type="text" name="gf_label_name" placeholder="Type Label Name"
                       class="form-control gf_label_input" required>
            </div>

            <p class="submit"><input type="submit" value="<?php _e('Create Label', 'gfolders'); ?>"
                                     class="button button-primary" id="submit" name="submit"></p>

        </form>
    </div>

    <h1 class="wp-heading-inline"><?php echo esc_html("Gravity Form Labels"); ?>
        <!--<a class="page-title-action" href="admin.php?page=gform_add_label">Add New</a>--></h1>

    <div class="gflabels-list">

        <?php
        include_once("gf_labels_table.php");
        $_table_list = new GF_Labels_Table();
        echo '<form method="post">';
        $_table_list->prepare_items();

        echo '<input type="hidden" name="page" value="" />';
        echo '<input type="hidden" name="section" value="gflabels" />';

        $_table_list->views();
        $_table_list->search_box(__('Search Labels', 'textdomain'), 'key');
        $_table_list->display();
        echo '</form>';

        ?>


    </div>


</div>






