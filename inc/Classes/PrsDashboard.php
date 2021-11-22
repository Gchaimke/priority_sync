<?php

namespace PrioritySync;

class PrsDashboard
{
    public function __construct()
    {
        wp_add_dashboard_widget(
            'prs_dashboard_widget',                          // Widget slug.
            esc_html__('Priority sync v.'.PRS_VERSION , 'prs'), // Title.
            [$this, 'prs_dashboar_content']                    // Display function.
        );
    }

    function prs_dashboar_content()
    {
        $dir = PRS_DATA_FOLDER . "sync/";
        $files = glob($dir . "*.json");
        $html = "<h1>Last update</h1><div class='prs_dash_updates'>";
        foreach ($files as $file) {
            $html .= "<div class='prs_row'><span>" . basename($file) .  "</span>  <span>" . date("d-m-Y H:i:s", filemtime($file)) . "</span></div>";
        }
        $html .= "</div><a class='button' target='_blank' href='/wp-admin/admin.php?page=prs_settings&cron=run'>Sync Now</a>";
        echo $html;
    }
}
