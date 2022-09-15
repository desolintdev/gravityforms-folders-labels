<?php defined('ABSPATH') || die('No script kiddies please!');
if (! current_user_can('install_plugins')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'flgf'));
}

global $wpdb;
if (isset($_POST['submit'])) {
    $rows = array(
        array(
            'gfform_id'    => sanitize_text_field($_REQUEST['gform_name']),
            'gflabel_name' => sanitize_text_field($_REQUEST['gf_label_name']) == '' ? $gf_label_name = sanitize_text_field($_REQUEST['gf_label_name1']) : sanitize_text_field($_REQUEST['gf_label_name']),
        ),
    );
    foreach ($rows as $row) {
        $wpdb->insert("{$wpdb->prefix}gfform_labels", $row);
    }
    wp_redirect(add_query_arg('label-added', 'success', get_site_url() . '/wp-admin/admin.php?page=gform_labels'));
}
global $wp;

?>

<div class="wrap gfolders_setting_sec">
	<h2><?php echo 'Add New Folder'; ?> </h2>
	<div class="gf_label_text"><?php _e('You can add folder to the desire form', 'flgf'); ?>:</div>
</div>

<form class="nav-tab-content gf_label_form" action="<?php echo filter_input(INPUT_SERVER, 'REQUEST_URI');?>" method="post">

	<input type="hidden" name="gfolders_gf" value="<?php echo sanitize_text_field($_GET['t']); ?>"/>

	<div class="form-group">

		<label for="sel1">Select Form:</label>
		<select name="gform_name" class="form-control" id="sel1">
			<?php foreach ($wpdb->get_results("SELECT id, title FROM {$wpdb->prefix}gf_form ORDER BY date_created ASC") as $gformList) : ?>
				<option value="<?php  echo esc_attr($gformList->title); ?>"><?php esc_html_e($gformList->title); ?> </option>
			<?php endforeach; ?>
		</select>
	</div>


	<div class="form-group" id="gffolder-list">

		<label for="sel1">Select Folder:</label>

		<select name="gf_label_name1" class="form-control">

			<?php foreach ($wpdb->get_results("SELECT DISTINCT gflabel_name  FROM {$wpdb->prefix}gfform_labels ORDER BY gflabel_name ASC") as $gformFolder) : ?>
				<option value="<?php  echo esc_attr($gformFolder->gflabel_name); ?>"><?php esc_html_e($gformFolder->gflabel_name); ?> </option>
			<?php endforeach; ?>

		</select>

	</div>


	<div class="input-group">
		<input type="checkbox" name="gf_custom_label"> <label for="sel1">If you want to add new folder name</label>
	</div>

	<div class="input-group" id="new-folder-name">
		<label for="sel1">Type Folder Name:</label>
		<input type="text" name="gf_label_name" placeholder="Type Folder Name" class="form-control gf_label_input">
	</div>

	<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'flgf'); ?>" class="button button-primary" id="submit" name="submit"></p>

</form>

</div>






