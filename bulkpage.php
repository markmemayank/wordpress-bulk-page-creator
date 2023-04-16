<?php
/**
 * Plugin Name: Bulk Page Creator
 * Plugin URI: To be updated
 * Description: A plugin that creates multiple pages in bulk.
 * Version: 1.1
 * Author: Mayank Kumar
 * Author URI: https://markmemayank.com/
 */

// Add the plugin settings page to the WordPress dashboard
add_action('admin_menu', 'bulk_page_creator_menu');
function bulk_page_creator_menu() {
    add_options_page('Bulk Page Creator Settings', 'Bulk Page Creator', 'manage_options', 'bulk-page-creator', 'bulk_page_creator_settings_page');
}

// Display the plugin settings page
function bulk_page_creator_settings_page() {
    ?>
    <div class="wrap">
        <h1>Bulk Page Creator Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('bulk-page-creator-group'); ?>
            <?php do_settings_sections('bulk-page-creator-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Page names:</th>
                    <td><textarea name="bulk_page_creator_page_names" rows="5" cols="50"></textarea></td>
                </tr>
            </table>
            <?php submit_button('Create Pages'); ?>
        </form>
    </div>
    <?php
}

// Register the plugin settings
add_action('admin_init', 'bulk_page_creator_settings');
function bulk_page_creator_settings() {
    register_setting('bulk-page-creator-group', 'bulk_page_creator_page_names');
}

// Create the pages when the plugin settings form is submitted
add_action('admin_post_bulk_page_creator_create_pages', 'bulk_page_creator_create_pages');
function bulk_page_creator_create_pages() {
    // Get the page names from the settings
    $page_names_raw = get_option('bulk_page_creator_page_names');
    $page_names_array = explode(",", $page_names_raw);
    
    // Loop through and create the pages
    foreach ($page_names_array as $page_name_raw) {
        $page_name = sanitize_text_field(trim($page_name_raw));
        
        if (!empty($page_name)) {
            $page_args = array(
                'post_title' => $page_name,
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page'
            );
            wp_insert_post($page_args);
        }
    }
    
    // Redirect back to the plugin settings page
    wp_redirect(admin_url('options-general.php?page=bulk-page-creator&created=true'));
    exit;
}

// Display a success message after pages have been created
add_action('admin_notices', 'bulk_page_creator_created_notice');
function bulk_page_creator_created_notice() {
    if (isset($_GET['created']) && $_GET['created'] == 'true') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Pages have been successfully created!', 'bulk-page-creator'); ?></p>
        </div>
        <?php
    }
}
