<?php

use PrioritySync\PrsCron;
use PrioritySync\PrsProducts;
use PrioritySync\PrsLogger;

function prs_dashboard()
{
    include 'dashboard.php';
}


function prs_products()
{
    $product_class = new PrsProducts();
    if (isset($_GET['limit'])) {
        if ($_GET['limit'] == 'no') {
            $product_class->set_products_limit(count($product_class->products));
        } else {
            $product_class->set_products_limit($_GET['limit']);
        }
    }
    $table_data = $product_class->view_products();
    include 'products.php';
}

function prs_settings()
{
    $required_plugins = array(
        //'woocommerce-gateway-paypal-express-checkout' => 'WC_Gateway_PPEC_Plugin',
        'woocommerce-wholesale-prices' => 'WooCommerceWholeSalePrices'
    );

    $required_plugins_str = '<h4>תוספים שצרכים להיות מותקנים:</h4>';
    foreach ($required_plugins as $name => $class) {
        $required_plugins_str .= "<li>";
        if (class_exists($class)) {
            $required_plugins_str .= $name . '<span style="color:green;font-weight: 600;"> - פעיל';
        } else {
            $plugin_link = "plugin-install.php?tab=plugin-information&plugin={$name}";
            $required_plugins_str .= "$name<span style='color:red;font-weight: 600;'> - לא פעיל <a class='install-now button' href='$plugin_link' target='_blank'>Install</a>";
        }
        $required_plugins_str .= "</li>";
    }

    include 'settings.php'; //view settings page

    if (isset($_GET['cron']) && $_GET['cron'] == 'run') {
        PrsLogger::log_message('Cron by Click');
        PrsCron::prs_cron_exec();
    }

    if (isset($_GET['remove_cron']) && $_GET['remove_cron'] != '') {
        PrsCron::remove_cron($_GET['remove_cron']);
        echo '<h4>' . $_GET['remove_cron'] . ' job removed</h4>';
    }
    // Cron::get_all_jobs();
}

function prs_logs()
{
    $logs = PrsLogger::getFileList();
    $i = 0;
    $view_log_list = '<select name="logs" id="select_logs" onchange="view_selected_log()">';
    foreach ($logs as $log) {
        $log_name_array = explode('/', $log);
        $log_name = end($log_name_array);
        $log_date = substr($log_name, 0, -4);
        $view_log_list .= "<option value='$log_date'>$log_date</option>";
        $i++;
        if ($i > 10) break;
    }
    $view_log_list .= '</select>';
    if (isset($_GET['log'])) {
        $view_log = PrsLogger::getlogContent(date('d-m-Y', strtotime($_GET['log'])));
    } else {
        $view_log = PrsLogger::getlogContent(date('d-m-Y'));
    }

    if (isset($_GET['clear_logs'])) {
        $view_log = PrsLogger::clearLogs();
    }

    include 'logs.php';
}
