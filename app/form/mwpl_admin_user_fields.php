<?php
namespace mwplite\app\form;

use mwplite\app\mwpl_user_fields;
if(defined('ABSPATH') && !class_exists('mwpl_admin_user_fields'))
{
    class mwpl_admin_user_fields
    {
        static function do($form_data)
        {
            global $wpdb;
            $tabel_name = $wpdb->prefix . 'mihanpanelfields';
            if(isset($form_data['save']))
            {
                // new mode
                self::new_mode($wpdb, $tabel_name, $form_data);
            }
            if(isset($form_data['update']))
            {
                // update mode
                self::update_mode($wpdb, $tabel_name, $form_data);
            }
            if(isset($form_data['delete']))
            {
                // delete mode
                self::delete_mode($wpdb, $tabel_name, $form_data);
            }
        }
        static function new_mode($wpdb, $tabel_name, $form_data)
        {
            if (isset($form_data['slug'])) {
                if(!wp_verify_nonce(sanitize_text_field($form_data['mwpl_nonce']), 'mwpl_create_field_item'))
                {
                    printf('<p class="alert error">%s</p>', __('The operation failed due to security issues.', 'mihanpanel'));
                    return false;
                }
                $data = [
                    'slug' => sanitize_text_field($form_data['slug']),
                    'label' => sanitize_text_field($form_data['label']),
                    'required' => sanitize_text_field($form_data['required']),
                    'type' => sanitize_text_field($form_data['type']),
                    'meta' => '',
                    'priority' => 0
                ];
                $success = $wpdb->insert(
                    $tabel_name,
                    $data
                );
                if ($success) {
    
                    echo '<p class="alert success">'.__("Settings successfully updated!", 'mihanpanel').'</p>';
    
                } else {
    
                    echo '<p class="alert error">'.__('An error occurred!', 'mihanpanel').'</p>';
    
                }
    
            } else {
    
                echo '<p class="alert error">'.__('Please fill all the fields', 'mihanpanel').'</p>';
    
            }
        }
        static function update_mode($wpdb, $tabel_name, $form_data)
        {
            $field_id = isset($form_data['id']) && $form_data['id'] ? sanitize_text_field(intval($form_data['id'])) : false;
            if ($field_id) {
                if(!wp_verify_nonce(sanitize_text_field($form_data['mwpl_nonce']), 'mwpl_update_field_item_' . $field_id))
                {
                    printf('<p class="alert error">%s</p>', __('The operation failed due to security issues.', 'mihanpanel'));
                    return false;
                }
                $slug = isset($form_data['slug']) && $form_data['slug'] ? sanitize_text_field($form_data['slug']) : false;
                $label = isset($form_data['label']) && $form_data['label'] ? sanitize_text_field($form_data['label']) : false;
                $required_field = isset($form_data['required_field']) && $form_data['required_field'] ? sanitize_text_field($form_data['required_field']) : false;
                $type = isset($form_data['type']) && $form_data['type'] ? sanitize_text_field($form_data['type']) : false;

                $data = [];
                $slug ? $data['slug'] = $slug : false;
                $label ? $data['label'] = $label : false;
                $required_field ? $data['required'] = $required_field : false;
                $type ? $data['type'] = $type : false;
                $update_res = $wpdb->update(
                    $tabel_name,
                    $data,
                    ['id' => $field_id]
                );
                if ($update_res) {
                    echo '<p class="alert success">'.__('Successfully edited!', 'mihanpanel').'</p>';
                } else {
                    echo '<p class="alert error">'.__('An error occurred!', 'mihanpanel').'</p>';
                }
            }
        }
        static function delete_mode($wpdb, $tabel_name, $form_data)
        {
            $field_id = isset($form_data['id']) && $form_data['id'] ? sanitize_text_field(intval($form_data['id'])) : false;
            if(!$field_id)
            {
                return false;
            }
            if(!wp_verify_nonce(sanitize_text_field($form_data['mwpl_nonce']), 'smwpl_update_field_item_' . $field_id))
            {
                printf('<p class="alert error">%s</p>', __('The operation failed due to security issues.', 'mihanpanel'));
                return false;
            }
            $success = $wpdb->delete(
                $tabel_name,
                array(
                    'id' => $field_id,
                )
            );
    
            if ($success) {
                echo '<p class="alert success">'.__('Successfully deleted!', 'mihanpanel').'</p>';
            } else {
                echo '<p class="alert error">'.__('An error occurred!', 'mihanpanel').'</p>';
            }
        }
        static function render_user_fields()
        {
            $fields = mwpl_user_fields::get_fields();
            $field_types = mwpl_user_fields::get_types();
            $pro_items = mwpl_user_fields::get_pro_items();
            ?>
            <div class="mw_menus_table">
                <div class="mw_head">
                    <div class="mw_row">
                        <div class="mw_th"></div>
                        <div class="mw_th"><?php _e('Field Id', 'mihanpanel'); ?></div>
                        <div class="mw_th"><?php _e('Title', 'mihanpanel'); ?></div>
                        <div class="mw_th"><?php _e('Required', 'mihanpanel'); ?></div>
                        <div class="mw_th"><?php _e('Type', 'mihanpanel'); ?></div>
                        <div class="mw_th"></div>
                    </div>
                </div>
                <div class="mw_body mw_sortable">
                    <?php foreach($fields as $field): ?>
                        <form method="post" class="mw_rows">
                            <?php wp_nonce_field('mwpl_update_field_item_' . $field->id, 'mwpl_nonce'); ?>
                            <div class="mw_row">
                                <div style="display: none;" class="mw_td">
                                    <input type="hidden" name="id" value="<?php echo esc_attr($field->id); ?>">
                                </div>
                                <div class="mw_td">
                                    <span class="mw_drag_handle mw_icon mw_sort_icon dashicons dashicons-menu"></span>
                                </div>
                                <div class="mw_td">
                                    <input type="text" name="slug" value="<?php echo esc_attr($field->slug); ?>">
                                </div>
                                <div class="mw_td">
                                    <input type="text" name="label" value="<?php echo esc_attr($field->label);?>">
                                </div>
                                <div class="mw_td">
                                    <select name="required_field">
                                        <option <?php selected($field->required, 'yes'); ?> value="yes"><?php _e('Required', 'mihanpanel')?></option>
                                        <option <?php selected($field->required, 'no');?> value="no"><?php _e('Optional', 'mihanpanel');?></option>
                                    </select>
                                </div>
                                <div class="mw_td">
                                    <select name="type">
                                        <?php foreach($field_types as $type => $name):
                                            $pro_item = in_array($type, $pro_items);
                                            $name = $pro_item ? sprintf('%s (%s)', $name, __("Pro", 'mihanpanel')) : $name;        
                                            ?>
                                            <option <?php echo $pro_item ? 'disabled' : ''; selected($type, $field->type);?> value="<?php echo esc_attr($type); ?>"><?php echo esc_html($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mw_th">
                                    <input type="submit" name="update" value="<?php _e('Save', 'mihanpanel')?>">
                                </div>
                                <div class="mw_th">
                                    <input type="submit" name="delete" value="<?php _e('Remove', 'mihanpanel')?>">
                                </div>
                                
                            </div>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
        static function render_new_record_fields()
        {
            $field_types = mwpl_user_fields::get_types();
            $pro_items = mwpl_user_fields::get_pro_items();
            wp_nonce_field('mwpl_create_field_item', 'mwpl_nonce');
            ?>
            <div class="mw_menus_table new_record">
                <div class="mw_head">
                    <div class="mw_row">
                        <div class="mw_th"></div>
                        <div class="mw_th"><?php _e('Field Id', 'mihanpanel'); ?></div>
                        <div class="mw_th"><?php _e('Title', 'mihanpanel'); ?></div>
                        <div class="mw_th"><?php _e('Required', 'mihanpanel'); ?></div>
                        <div class="mw_th"><?php _e('Type', 'mihanpanel'); ?></div>
                        <div class="mw_th"></div>
                    </div>
                </div>
                <div class="mw_body">
                    <div class="mw_row">
                        <div style="display: none;" class="mw_td"></div>
                        <div class="mw_td"></div>
                        <div class="mw_td">
                            <input type="text" name="slug">
                        </div>
                        <div class="mw_td">
                            <input type="text" name="label">
                        </div>
                        <div class="mw_td">
                            <select name="required">
                                <option value="yes"><?php _e('Required', 'mihanpanel')?></option>
                                <option alue="no"><?php _e('Optional', 'mihanpanel');?></option>
                            </select>
                        </div>
                        <div class="mw_td">
                            <select name="type">
                                <?php foreach($field_types as $type => $name):
                                    $pro_item = in_array($type, $pro_items);
                                    $name = $pro_item ? sprintf('%s (%s)', $name, __("Pro", 'mihanpanel')) : $name;
                                    ?>
                                    <option <?php echo $pro_item ? 'disabled' : ''; ?> value="<?php echo esc_attr($type); ?>"><?php echo esc_html($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mw_th">
                            <input type="submit" name="save" value="<?php _e('Save', 'mihanpanel')?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}