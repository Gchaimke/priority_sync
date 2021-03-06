<?php
/*
Plugin Name: Priority Sync
Plugin URI: https://avdor-hlt.com
Description: Plugin to sync priority data
Version: 1.0
Author: Chaim Gorbov
Author URI: https://avdor-hlt.com
License: GPLv2 or later
Text Domain: priority_sync
*/

if (!defined('WPINC')) {
    die;
}
$version = '1.0.2';
//sync timezone with wordpress
date_default_timezone_set(get_option('timezone_string'));
define('PRS_VERSION', $version);
define('PRS_PLAGIN_ROOT', plugin_dir_path(__FILE__));
define('PRS_PLAGIN_URL', plugin_dir_url(__FILE__));
define('PRS_MIN_ORDER', 200);

//define erp-data folder
$upload_dir = wp_upload_dir();
define('PRS_DATA_FOLDER', $upload_dir['basedir'] . '/priority-data/');
$data_folders = array(PRS_DATA_FOLDER, PRS_DATA_FOLDER . 'sync/');
foreach ($data_folders as $folder) {
    if (!file_exists($folder)) {
        mkdir($folder, 0700);
        file_put_contents($folder . 'index.php', "<?php // Silence is golden.");
    }
}

// include the Composer autoload file
require PRS_PLAGIN_ROOT . 'vendor/autoload.php';

use PrioritySync\PrsClients;
use PrioritySync\PrsCron;
use PrioritySync\PrsHelper;
use PrioritySync\PrsProducts;
use PrioritySync\PrsPlugin;

$prs_clients = new PrsClients();
$prs_products = new PrsProducts();
$prs_plugin = new PrsPlugin();
$prs_cron = new PrsCron();
$prs_helper = new PrsHelper();


require PRS_PLAGIN_ROOT . 'priority_sync_wp_addons.php';

register_deactivation_hook(__FILE__, 'prs_deactivate');

function prs_deactivate()
{
    PrsCron::remove_cron('prs_sync_data');
    PrsLogger::log_message("Plugin erp deactivated");
}
