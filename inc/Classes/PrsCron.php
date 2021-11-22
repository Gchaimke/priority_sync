<?php

namespace PrioritySync;

class PrsCron
{
    public function __construct()
    {
        add_action('prs_sync_data', [$this, 'prs_cron_exec']);
        add_filter('cron_schedules', [$this, 'prs_cron_interval']);
        $timestamp = date('d-m-Y H:i:s', wp_next_scheduled('prs_sync_data'));
        if (!wp_next_scheduled('prs_sync_data')) {
            wp_schedule_event(time(), 'ten_minutes', 'prs_sync_data');
        }
    }

    public static function remove_cron($job)
    {
        $timestamp = wp_next_scheduled($job);
        wp_unschedule_event($timestamp, $job);
    }

    public static function prs_cron_exec()
    {
        PrsLogger::log_message('=== Cron job Sart ===');
        //Cron Sync data files with Gdrive
        $sync_status = self::prs_sync_files();
        if ($sync_status > 0) {
            //Cron Update products data
            $prs_product_class = new PrsProducts();
            $prs_product_class->update_all_products();
        } else {
            PrsLogger::log_message('No Updates');
        }
        PrsLogger::log_message('*** Cron job End ***');
    }

    private static function prs_sync_files()
    {

    }


    public static function get_all_jobs()
    {
        echo ' <table class="widefat striped ">';
        echo '<tr><th>Time</th><th>Job Name</th><th>Interval</th><th>Action</th></tr>';
        foreach (_get_cron_array() as $key => $job) {
            foreach ($job as $jkey => $job_name) {
                foreach ($job_name as $data) {
                    echo '<tr><td>' . date('d-m-Y H:i:s', $key) . '</td>';
                    if ($jkey == 'prs_crm_sync_data') {
                        echo '<td><b>' . $jkey . '</b></td>';
                    } else {
                        echo '<td>' . $jkey . '</td>';
                    }
                    echo '<td>' . $data['schedule'] . '</td>';
                    echo '<td><a class="button" href="?page=prs_settings&remove_cron=' . $jkey . '">remove</a></td>';
                }
            }
            echo '</tr>';
        }
    }

    function prs_cron_interval($schedules)
    {
        $schedules['ten_minutes'] = array(
            'interval' => 600,
            'display'  => esc_html__('Every Ten Minutes'),
        );
        return $schedules;
    }
}
