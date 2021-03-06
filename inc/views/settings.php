<h1>Priority Settings</h1>
<?php
echo ($required_plugins_str);
// $url = 'https://avdortest.wee.co.il/odata/Priority/tabula.ini/a121021/LOGPART?';
// $url_params = '$select=PARTNAME,PARTDES,EPARTDES,STATDES,WEBLEVEL,SUPDES,BASEPLPRICE,WSPLPRICE&$filter=WEBLEVEL%20eq%20\'1\'&$expand=PARTBALANCE_SUBFORM($select=BALANCE)';
?>
<form method="POST" action="options.php">
    <?php settings_fields('prs-plugin-settings'); ?>
    <?php do_settings_sections('prs-plugin-settings'); ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row" colspan="2">
                    <h2>Priority site settings</h2>
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_url">Priority URL</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_url" value="<?php echo get_option('prs_priority_url'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_api_username">Api Username</label>
                </th>
                <td>
                    <input type="text" name="prs_api_username" value="<?php echo get_option('prs_api_username'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_api_pass">Api Passoword</label>
                </th>
                <td>
                    <input type="password" name="prs_api_pass" value="<?php echo get_option('prs_api_pass'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row" colspan="2">
                    <h2>Priority Parts settings</h2>
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_parts_table">Parts Table</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_parts_table" value="<?php echo get_option('prs_priority_parts_table'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_parts_select">Select Parts Columns</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_parts_select" value="<?php echo get_option('prs_priority_parts_select'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_parts_filter">Filter Parts rows</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_parts_filter" value="<?php echo get_option('prs_priority_parts_filter'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_parts_expand">Expend Parts sub rows</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_parts_expand" value="<?php echo get_option('prs_priority_parts_expand'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th colspan="2"><a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_parts">Get Parts</a>
                    <a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&show_parts">Show parts File</a>
                    <a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_parts&save_file">Get Parts & Save to File</a>
                </th>
            </tr>
            <tr>
                <th scope="row" colspan="2">
                    <h2>Priority Customers settings</h2>
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_customers_table">Customers Table</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_customers_table" value="<?php echo get_option('prs_priority_customers_table'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_customers_select">Select Customers Columns</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_customers_select" value="<?php echo get_option('prs_priority_customers_select'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_customers_filter">Filter Customers rows</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_customers_filter" value="<?php echo get_option('prs_priority_customers_filter'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_customers_expand">Expend Customers sub rows</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_customers_expand" value="<?php echo get_option('prs_priority_customers_expand'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th colspan="2"><a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_customers">Get Customers</a>
                    <a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&show_customers">Show Customers File</a>
                    <a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_customers&save_file">Get Customers & Save to File</a>
                </th>
            </tr>
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>

<pre>
    <?php
    use PrioritySync\PrsCron;

    $json_data = "";
    if (isset($_GET['get_parts'])) {
        $to_file = isset($_GET['save_file']) ? true : false;
        $json_data = PrsCron::prs_sync_products($to_file);
        print_r(json_decode($json_data));
    }

    if (isset($_GET['get_customers'])) {
        $to_file = isset($_GET['save_file']) ? true : false;
        $json_data = PrsCron::prs_sync_customers($to_file);
        print_r(json_decode($json_data));
    }

    if (isset($_GET['show_parts'])) {
        if (file_exists(PRS_DATA_FOLDER . 'sync/products.json')) {
            $json_data = file_get_contents(PRS_DATA_FOLDER . 'sync/products.json');
            print_r(json_decode($json_data));
        }
    }
    if (isset($_GET['show_customers'])) {
        if (file_exists(PRS_DATA_FOLDER . 'sync/customers.json')) {
            $json_data = file_get_contents(PRS_DATA_FOLDER . 'sync/customers.json');
            print_r(json_decode($json_data));
        }
    }
    ?>
</pre>

<div>
    <h3>Cron Data</h3>
    <a class="button" href="?page=prs_settings&cron=run">Run Job</a>
    <h4>Next CRM cron job <?php echo date('d-m-Y H:i:s', wp_next_scheduled('prs_sync_data')) ?></h4>
</div>