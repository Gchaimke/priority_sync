<h1>Settings</h1>
<?php
echo ($required_plugins_str);

$url = 'https://avdortest.wee.co.il/odata/Priority/tabula.ini/a121021/LOGPART?';
$url_params = '$select=PARTNAME,PARTDES,EPARTDES,STATDES,WEBLEVEL,SUPDES,BASEPLPRICE,WSPLPRICE&$filter=WEBLEVEL%20eq%20\'1\'&$expand=PARTBALANCE_SUBFORM($select=BALANCE)';

//$select=PARTNAME,PARTDES,EPARTDES,STATDES,WEBLEVEL,SUPDES,BASEPLPRICE,WSPLPRICE&$filter=WEBLEVEL eq '1'&$expand=PARTBALANCE_SUBFORM($select=BALANCE)
$username = "Chaim";
$pass = "";
$json_data = "";
function CallAPI($url, $username, $pass)
{
    $curl = curl_init();
    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$pass");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    if (!curl_errno($curl)) {
        switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
            case 200:  # OK
                break;
            case 401:
                echo "<h2> Error " . curl_getinfo($curl)['http_code'] . ": <ol><li>Wrong user name or password</li><li>Or Priority page not exists.</li></ol></h2>";
                break;
            default:
                echo 'Unexpected HTTP code: ', $http_code, "\n";
        }
    }
    curl_close($curl);
    return $result;
}


?>
<h3>DEMO</h3>
<div>Url: <?= $url ?></div>
<div>Parameters: <?= $url_params ?></div>
<!-- <form method="POST" action="<?php echo admin_url('admin.php?page=prs_settings'); ?>"> -->
<form method="POST" action="options.php">
    <?php settings_fields('prs-plugin-settings'); ?>
    <?php do_settings_sections('prs-plugin-settings'); ?>
    <table class="form-table">
        <tbody>
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
                    <label for="prs_priority_select">Select Columns</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_select" value="<?php echo get_option('prs_priority_select'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_filter">Filter rows</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_filter" value="<?php echo get_option('prs_priority_filter'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_priority_expand">Expend sub rows</label>
                </th>
                <td>
                    <input type="text" name="prs_priority_expand" value="<?php echo get_option('prs_priority_expand'); ?>" style="width: 550px">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="prs_url_parameters">URL Optional Parameters</label>
                </th>
                <td>
                    <input type="text" name="prs_url_parameters" value="<?php echo get_option('prs_url_parameters'); ?>" style="width: 500px">
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
        </tbody>
    </table>
    <?php submit_button(); ?>
</form>
<a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_data">Get Data from Server</a>
<a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_file">Get Data from File</a>
<a class="button button-secondary" href="/wp-admin/admin.php?page=prs_settings&get_data&save_file">Get Data & Save to File</a>
<pre>
    <?php
    if (isset($_GET['get_data'])) {
        ini_set('max_execution_time', 0);
        $url = get_option('prs_priority_url');
        $url_params = str_replace(
            ' ',
            '%20',
            '$select=' . get_option('prs_priority_select') .
                '&$filter=' . get_option('prs_priority_filter') .
                '&$expand=' . get_option('prs_priority_expand') . "&" .
                get_option('prs_url_parameters')
        );
        $username = get_option('prs_api_username');
        $pass = get_option('prs_api_pass');
        $json_data = CallAPI($url . "?" . $url_params, $username, $pass);
        if (isset($_GET['save_file'])) {
            file_put_contents(PRS_DATA_FOLDER . 'sync/products.json', $json_data);
        }
        print_r(json_decode($json_data));
    }
    if (isset($_GET['get_file'])) {
        if (file_exists(PRS_DATA_FOLDER . 'sync/products.json')) {
            $json_data = file_get_contents(PRS_DATA_FOLDER . 'sync/products.json');
            print_r(json_decode($json_data));
        }
    }
    ?>
</pre>