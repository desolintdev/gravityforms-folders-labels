<?php

class FLGF_Labels_Table extends WP_List_Table
{
    /**
     * Initialize the table list.
     */

    public function __construct()
    {
        parent::__construct(
            array(

                'singular' => __('gflabel', 'textdomain'),

                'plural'   => __('gflabels', 'textdomain'),

                'ajax'     => false,

            )
        );
    }

    /**
     * Column cb.
     */
    public function column_cb($gflabel)
    {
        // return sprintf( '<input type="checkbox" name="gflabel[]" value="%1$s" />', $gflabel['id'] );
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $gflabel['gf_gfolder']);
    }

    public function column_id($item)
    {
        global $ad_code_manager;
        $output  = '<div id="inline_' . $item['post_id'] . '" style="display:none;">';
        $output .= '<div class="id">' . $item['post_id'] . '</div>';
        // Build the fields for the conditionals
        $output .= '<div class="acm-conditional-fields"><div class="form-new-row">';
        $output .= '<h4 class="acm-section-label">' . __('Conditionals', 'ad-code-manager') . '</h4>';
        if (! empty($item['conditionals'])) {
            foreach ($item['conditionals'] as $conditional) {
                $function  = $conditional['function'];
                $arguments = $conditional['arguments'];
                $output   .= '<div class="conditional-single-field"><div class="conditional-function">';
                $output   .= '<select name="acm-conditionals[]">';
                $output   .= '<option value="">' . __('Select conditional', 'ad-code-manager') . '</option>';
                foreach ($ad_code_manager->whitelisted_conditionals as $key) {
                    $output .= '<option value="' . esc_attr($key) . '" ' . selected($function, $key, false) . '>';
                    $output .= esc_html(ucfirst(str_replace('_', ' ', $key)));
                    $output .= '</option>';
                }
                $output .= '</select>';
                $output .= '</div><div class="conditional-arguments">';
                $output .= '<input name="acm-arguments[]" type="text" value="' . esc_attr(implode(';', $arguments)) . '" size="20" />';
                $output .= '<a href="#" class="acm-remove-conditional">Remove</a></div></div>';
            }
        }
        $output .= '</div><div class="form-field form-add-more"><a href="#" class="button button-secondary add-more-conditionals">' . __('Add more', 'ad-code-manager') . '</a></div>';
        $output .= '</div>';
        // Build the fields for the normal columns
        $output .= '<div class="acm-column-fields">';
        $output .= '<h4 class="acm-section-label">' . __('URL Variables', 'ad-code-manager') . '</h4>';
        foreach ((array) $item['url_vars'] as $slug => $value) {
            $output   .= '<div class="acm-section-single-field">';
            $column_id = 'acm-column[' . $slug . ']';
            $output   .= '<label for="' . esc_attr($column_id) . '">' . esc_html($slug) . '</label>';
            // Support for select dropdowns
            $ad_code_args = wp_filter_object_list($ad_code_manager->current_provider->ad_code_args, array( 'key' => $slug ));
            $ad_code_arg  = array_shift($ad_code_args);
            if (isset($ad_code_arg['type']) && 'select' == $ad_code_arg['type']) {
                $output .= '<select name="' . esc_attr($column_id) . '">';
                foreach ($ad_code_arg['options'] as $key => $label) {
                    $output .= '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_attr($label) . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<input name="' . esc_attr($column_id) . '" id="' . esc_attr($column_id) . '" type="text" value="' . esc_attr($value) . '" size="20" aria-required="true">';
            }
            $output .= '</div>';
        }
        $output .= '</div>';
        // Build the field for the priority
        $output .= '<div class="acm-priority-field">';
        $output .= '<h4 class="acm-section-label">' . __('Priority', 'ad-code-manager') . '</h4>';
        $output .= '<input type="text" name="priority" value="' . esc_attr($item['priority']) . '" />';
        $output .= '</div>';
        // Build the field for the logical operator
        $output   .= '<div class="acm-operator-field">';
        $output   .= '<h4 class="acm-section-label">' . __('Logical Operator', 'ad-code-manager') . '</h4>';
        $output   .= '<select name="operator">';
        $operators = array(
            'OR'  => __('OR', 'ad-code-manager'),
            'AND' => __('AND', 'ad-code-manager'),
        );
        foreach ($operators as $key => $label) {
            $output .= '<option ' . selected($item['operator'], $key) . '>' . esc_attr($label) . '</option>';
        }
        $output .= '</select>';
        $output .= '</div>';

        $output .= '</div>';
        return $output;
    }

    /**
     * Return gflabel column
     */

    public function column_gflabel($gflabel)
    {
        return "<div class='folder' id='label-id-" . $gflabel['id'] . "'>" . $gflabel['gf_gfolder'] . "</div><div class='label-edit-div' style='display:none;' id='label-edit-" . $gflabel['id'] . "'><input class='glabelval' id='" . $gflabel['id'] . "' type='text' value='" . $gflabel['gf_gfolder'] . "'></div><div class='row-actions'></span><span class='trash'><a href='javascript:void(0)' class='delete-label' data-labelid=" . $gflabel['id'] . ">Delete</a> | </span><span class='inline-edit'><a href='javascript:void(0)' class='edit-label' data-labelid=" . $gflabel['id'] . ">Edit</a><a style='display:none;' href='javascript:void(0)' class='update-label-btn' data-labelid=" . $gflabel['id'] . '>Update</a> | </span></div>';
    }

    /**
     * Prepare table list items.
     */

    public function flgf_usort_reorder($a, $b)
    {
        $orderby = (! empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'id'; // If no sort, default to title
        $order   = (! empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'desc'; // If no order, default to asc
        $result  = strnatcmp($a[ $orderby ], $b[ $orderby ]); // Determine sort order
        return ($order === 'asc') ? $result : -$result; // Send final sort direction to usort
    }

    public function prepare_items()
    {
        global $wpdb, $table_prefix;
        $sql_gflabel_table = $wpdb->prefix . 'gf_label_tags';
        $sql_gfform_table  = $wpdb->prefix . 'gf_form';

        $per_page = 10;
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        // Column headers
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();

        $current_page = $this->get_pagenum();
        if (1 < $current_page) {
            $offset = $per_page * ($current_page - 1);
        } else {
            $offset = 0;
        }

        $search = '';

        if (! empty($_REQUEST['s'])) {
            $search = $wpdb->prepare("AND gf_gfolder LIKE '%%%s%%'", $wpdb->esc_like(sanitize_text_field($_REQUEST['s'])));
        }

        $items = $wpdb->get_results('SELECT id, gf_gfolder FROM ' . $sql_gflabel_table . " WHERE 1 = 1 {$search}" . $wpdb->prepare('GROUP BY gf_gfolder ORDER BY id DESC LIMIT %d OFFSET %d;', $per_page, $offset), ARRAY_A);

      

        usort($items, [FLGF_Labels_Table::class,'flgf_usort_reorder']);
        $count       = $wpdb->get_var('SELECT COUNT(id) FROM ' . $sql_gflabel_table . " WHERE 1 = 1 {$search};");
        $this->items = $items;

        // Set the pagination
        $this->set_pagination_args(
            array(
                'total_items' => $count,
                'per_page'    => $per_page,
                'total_pages' => ceil($count / $per_page),
            )
        );
    }

    /**
     * Get list columns.
     *
     * @return array
     */

    public function get_columns()
    {
        return array(

            'cb'      => '<input type="checkbox" />',
            'gflabel' => __('Label Name', 'textdomain'),

        );
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array( 'gflabel' => array( 'gf_gfolder', false ) );
        return $sortable_columns;
    }

    public function process_bulk_action()
    {
        if ('delete' === $this->current_action()) {
            self::flgf_delete_label_records(absint($_GET['record']));
        }

        if ((isset($_POST['action']) && sanitize_text_field($_POST['action']) == 'bulk-delete') || (isset($_POST['action2']) && sanitize_text_field($_POST['action2']) == 'bulk-delete')) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            foreach ($delete_ids as $id) {
                self::flgf_delete_label_records($id);
            }
        }
    }

    public static function flgf_delete_label_records($id)
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'gf_label_tags', array( 'gf_gfolder' => $id ));
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array( 'bulk-delete' => 'Delete' );
        return $actions;
    }

    public function inline_edit()
    {
        ?>
		<form method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
			<table style="display: none">
				<tbody id="inlineedit">
				<tr id="inline-edit" class="inline-edit-row" style="display: none">
					<td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">
						<fieldset>
							<div class="inline-edit-col">
								<input type="hidden" name="id" value=""/>
								<input type="hidden" name="action" value="acm_admin_action"/>
								<input type="hidden" name="method" value="edit"/>
								<input type="hidden" name="doing_ajax" value="true"/>
								<?php wp_nonce_field('acm-admin-action', 'nonce'); ?>
								<div class="acm-float-left">
									<div class="acm-column-fields"></div>
									<div class="acm-priority-field"></div>
									<div class="acm-operator-field"></div>
								</div>
								<div class="acm-conditional-fields"></div>
								<div class="clear"></div>
							</div>
						</fieldset>
						<p class="inline-edit-save submit">
							<?php $cancel_text = __('Cancel', 'ad-code-manager'); ?>
							<a href="#inline-edit" title="<?php echo esc_attr($cancel_text); ?>"
							   class="cancel button-secondary alignleft"><?php echo esc_html($cancel_text); ?></a>
							<?php $update_text = __('Update', 'ad-code-manager'); ?>
							<a href="#inline-edit" title="<?php echo esc_attr($update_text); ?>"
							   class="save button-primary alignright"><?php echo esc_html($update_text); ?></a>
							<img class="waiting" style="display:none;"
								 src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt=""/>

							<span class="error" style="display:none;"></span>
							<br class="clear"/>
						</p>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		<?php
    }
}
