<?php

namespace PrioritySync;

class Plugin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'prs_add_admin_pages']);
        add_action('admin_enqueue_scripts', [$this, 'prs_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'prs_user_scripts']);
        add_action('wp_dashboard_setup', [$this, 'prs_add_dashboard_widget']);
    }

    public function prs_add_admin_pages()
    {
        require_once PRS_PLAGIN_ROOT . 'inc/views/admin_pages_functions.php';
        add_menu_page('Priority', 'Priority sync', 'edit_pages', 'prs_dashboard', 'prs_dashboard', 'dashicons-businessman', 4);
        add_submenu_page('prs_dashboard', "Products", "Products", 'edit_pages', 'prsProducts', 'prs_products');
        add_submenu_page('prs_dashboard', "Settings", "Settings", 'edit_pages', 'prsSettings', 'prs_settings');
        add_submenu_page('prs_dashboard', "Logs", "Logs", 'edit_pages', 'prsLogs', 'prs_logs');
    }

    public function prs_admin_scripts()
    {
        global $version;
        wp_enqueue_style('prs', PRS_PLAGIN_URL . 'inc/css/prs_admin.css', [], $version);
        wp_register_script('prs', PRS_PLAGIN_URL . 'inc/js/prs_admin.js', ['jquery'], $version, true);
        wp_localize_script('prs', 'settings', [
            'nonce' => wp_create_nonce('prs') // Add a nonce for security
        ]);
        wp_enqueue_script('prs');
    }

    public function prs_user_scripts()
    {
        global $version;
        wp_register_script('prs', PRS_PLAGIN_URL . 'inc/js/prs_user.js', ['jquery'], $version, true);
        wp_enqueue_script('prs');
    }

    public function prs_add_dashboard_widget()
    {
        new PrsDashboard();
    }
}
