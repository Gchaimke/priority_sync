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
                echo "<h2> Error ".curl_getinfo($curl)['http_code'].": <ol><li>Wrong user name or password</li><li>Or Priority page not exists.</li></ol></h2>";
                break;
            default:
                echo 'Unexpected HTTP code: ', $http_code, "\n";
        }
    }
    curl_close($curl);
    return $result;
}

if (isset($_POST['priority_url'])) {
    $pass = $_POST['api_pass'];
    $json_data = CallAPI($url . $url_params, $username, $pass);
}
?>
<h3>DEMO</h3>
<div>Url: <?= $url ?></div>
<div>Parameters: <?= $url_params ?></div>
<form method="POST" action="<?php echo admin_url('admin.php?page=prs_settings'); ?>">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="priority_url">Priority URL</label>
                </th>
                <td>
                    <input type="text" name="priority_url" value="<?= $url ?>" style="width: 80VW">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="url_parameters">URL Parameters</label>
                </th>
                <td>
                    <input type="text" name="url_parameters" value="<?= $url_params ?>" style="width: 80VW">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="api_username">Api Username</label>
                </th>
                <td>
                    <input type="text" name="api_username" value="<?=$username?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="api_pass">Api Passoword</label>
                </th>
                <td>
                    <input type="password" name="api_pass"  value="<?=$pass?>">
                </td>
            </tr>
            <tr>
                <th><label for="api_pass">Save settings</label></th>
                <td>
                    <input class="button action" type="submit" value="Save" class="Send" />
                </td>
            </tr>
        </tbody>
    </table>
</form>

<pre>
    <?= print_r(json_decode($json_data)) ?>
</pre>