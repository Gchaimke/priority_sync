<?php

namespace PrioritySync;

class PrsHelper
{
    public static function num_format($num, $after_dot = 0)
    {
        if (!is_object($num)) {
            return number_format($num, $after_dot, '.', '');
        }
    }

    public static function debug($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

    public static function CallAPI($url, $username, $pass)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$username:$pass");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (!curl_errno($curl)) {
            switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    curl_close($curl);
                    return $result;
                    break;
                case 401:
                    echo "<h2> Error " . curl_getinfo($curl)['http_code'] . ": <ol><li>Wrong user name or password</li><li>Or Priority page not exists.</li></ol></h2>";
                    break;
                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        }
        curl_close($curl);
        return false;
    }
}
