<?php

/*
 * Plugin Name:       WooCommerce LikeCard 
 * Description:       Integrates LikeCard with your WooCommerce Store.
 * Version:           0.1.4
 * Requires PHP:      7.2
 * Author:            LikeCard
 * Author URI:        https://like4card.com/
 */

define('PLUGIN_FILE_PATH', __FILE__);
define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('PRODUCT_MANAGEMENT_DIR_PATH', plugin_dir_path(__FILE__) . 'features/product-management');
define('LOG_MANAGEMENT_DIR_PATH', plugin_dir_path(__FILE__) . 'features/log-management');

require_once plugin_dir_path(__FILE__) . 'helpers/request-helper.php';
require_once plugin_dir_path(__FILE__) . 'helpers/admin-notice.php';

require_once plugin_dir_path(__FILE__) . 'features/log-management/create-error-logs-file.php';
require_once plugin_dir_path(__FILE__) . 'features/log-management/create-like4card-logs-table.php';
require_once plugin_dir_path(__FILE__) . 'features/log-management/create-like4card-general-logs-table.php';
require_once plugin_dir_path(__FILE__) . 'features/log-management/log-request.php';
require_once plugin_dir_path(__FILE__) . 'features/log-management/log-data.php';
require_once plugin_dir_path(__FILE__) . 'features/log-management/download-log.php';

require_once plugin_dir_path(__FILE__) . 'features/order-management/create-like4card-order-table.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/order-management.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/get-products-that-has-like4card-product-id-meta.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/create-order-error-handler.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/decrypt-serial-code.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/when-order-completed.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/when-payment-approved.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/when-new-product-added-to-cart.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/show-serial-code-in-order-page.php';
require_once plugin_dir_path(__FILE__) . 'features/order-management/filter-available-payment-ways.php';

require_once plugin_dir_path(__FILE__) . 'features/product-management/category-management.php';
require_once plugin_dir_path(__FILE__) . 'features/product-management/product-management.php';
require_once plugin_dir_path(__FILE__) . 'features/product-management/product-component.php';
require_once plugin_dir_path(__FILE__) . 'features/product-management/when-category-changed.php';
require_once plugin_dir_path(__FILE__) . 'features/product-management/set-featured-image-from-external-url.php';
require_once plugin_dir_path(__FILE__) . 'features/product-management/refresh-products-info-cron-job.php';

require_once plugin_dir_path(__FILE__) . 'features/settings-management/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'features/settings-management/check-api-config.php';
require_once plugin_dir_path(__FILE__) . 'features/settings-management/validate-like4card-configs.php';

// require_once plugin_dir_path(__FILE__) . 'features/order-management/get-order-details-endpoint.php';


function enqueue_style_and_script_files()
{
    $variables = [
        'admin_ajax_url' => admin_url('admin-ajax.php')
    ];

    wp_register_script('settings_page_script', plugin_dir_url(__FILE__) . 'features/settings-management/settings-page.js', array('jquery'));
    wp_enqueue_script('settings_page_script');
    wp_localize_script('settings_page_script', "ajax_obj", $variables);

    wp_register_style('settings_page_styles', plugin_dir_url(__FILE__) . 'features/settings-management/settings-page.css');
    wp_enqueue_style('settings_page_styles');

    wp_register_script('products_page_script', plugin_dir_url(__FILE__) . 'features/product-management/products-page.js', array('jquery'));
    wp_enqueue_script('products_page_script');
    wp_localize_script('settings_page_script', "ajax_obj", $variables);

    wp_register_style('products_page_styles', plugin_dir_url(__FILE__) . 'features/product-management/products-page.css');
    wp_enqueue_style('products_page_styles');

    wp_enqueue_style('styles', plugin_dir_url(__FILE__) . 'style.css');

    wp_register_script('log_page_script', plugin_dir_url(__FILE__) . 'features/log-management/log-page.js', array('jquery'));
    wp_enqueue_script('log_page_script');
    wp_localize_script('log_page_script', "ajax_obj", $variables);
}
add_action('wp_enqueue_scripts', 'enqueue_style_and_script_files');
add_action('admin_enqueue_scripts', 'enqueue_style_and_script_files');


function register_admin_notice_actions()
{
    $admin_notice = new AdminNotice;

    add_action('admin_notices', [$admin_notice, 'displayAdminNotice']);
    add_action('admin_notices', [$admin_notice, 'displayError']);
    add_action('admin_notices', [$admin_notice, 'displayWarning']);
    add_action('admin_notices', [$admin_notice, 'displayInfo']);
    add_action('admin_notices', [$admin_notice, 'displaySuccess']);
}
register_admin_notice_actions();
