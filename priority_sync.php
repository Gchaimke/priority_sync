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
$version = '1.0.1';
//sync timezone with wordpress
date_default_timezone_set(get_option('timezone_string'));
define('PRS_VERSION', $version);
define('PRS_PLAGIN_ROOT', plugin_dir_path(__FILE__));
define('PRS_PLAGIN_URL', plugin_dir_url(__FILE__));
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

use PrioritySync\PrsPlugin;
$plugin = new PrsPlugin();


