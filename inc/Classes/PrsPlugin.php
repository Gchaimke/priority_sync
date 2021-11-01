<?php

namespace PrioritySync;

class PrsPlugin
{
    private $settings_rows = array(
        'prs_priority_url',
        'prs_api_username',
        'prs_api_pass',
        'prs_priority_parts_table',
        'prs_priority_parts_select',
        'prs_priority_parts_filter',
        'prs_priority_parts_expand',
        'prs_priority_customers_table',
        'prs_priority_customers_select',
        'prs_priority_customers_filter',
        'prs_priority_customers_expand',
    );

    public function __construct()
    {
        add_action('admin_menu', [$this, 'prs_add_admin_pages']);
        add_action('admin_enqueue_scripts', [$this, 'prs_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'prs_user_scripts']);
        add_action('wp_dashboard_setup', [$this, 'prs_add_dashboard_widget']);
        add_filter('plugin_action_links_priority_sync/priority_sync.php', [$this, 'prs_settings_link']);
        add_action('admin_init', [$this, 'update_prs_settings']);
    }

    public function prs_add_admin_pages()
    {
        require_once PRS_PLAGIN_ROOT . 'inc/views/admin_pages_functions.php';
        add_menu_page('Priority', 'Priority sync', 'edit_pages', 'prs_dashboard', 'prs_dashboard', 'dashicons-businessman', 4);
        add_submenu_page('prs_dashboard', 'Clients', "Clients", 'edit_pages', 'prs_clients', 'prs_clients');
        add_submenu_page('prs_dashboard', "Products", "Products", 'edit_pages', 'prs_products', 'prs_products');
        add_submenu_page('prs_dashboard', "Settings", "Settings", 'edit_pages', 'prs_settings', 'prs_settings');
        add_submenu_page('prs_dashboard', "Logs", "Logs", 'edit_pages', 'prs_logs', 'prs_logs');
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

    public function prs_settings_link($links)
    {
        $url = esc_url(add_query_arg('page', 'prs_settings', get_admin_url() . 'admin.php'));
        $settings_link = "<a href='$url'>" . __('Settings') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    public function update_prs_settings()
    {
        foreach ($this->settings_rows as $value) {
            register_setting('prs-plugin-settings', $value);
        }
    }
}
