<?php
/**
 * Created by PhpStorm.
 * User: huangmin
 * Date: 2018/9/14
 * Time: 上午9:29
 */

header("content-type:text/html;charset=utf-8");

$host = '193.112.244.223';
$user = 'root';
$pwd = '493318';
$dbname = 'ifa_match';
$db = new mysqli($host, $user, $pwd, $dbname);

if ($db->connect_errno <> 0) {
    echo '连接失败';
    echo $db->connect_error;
    exit;
}
$db->query("SET NAMES UTF8");

$postArray = file_get_contents("php://input");//接收json
//开始分解
function ResolveJson($postArray)
{
    if (is_null($postArray) || empty($postArray)) {
        echo "false";
        exit;
    } else {
        $de_json = json_decode($postArray, TRUE);
        return $de_json;
    }
}

$ResultArray = [];
$ResultArray = ResolveJson($postArray);

if (array_key_exists('WxSpeak_Value', $ResultArray)) {
    $Speak_Key = $ResultArray['WxSpeak_Value'];
} else {
    echo "缺少Speak_Value";
    exit;
}

function MainSelectSpeak($Value, $db)
{
    $sql = "SELECT Speak_Key FROM AndRobotSpeak WHERE Speak_Value = '{$Value}'";
    $query = $db->query($sql);
    if ($query) {
        while ($row = $query->fetch_array(MYSQL_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }
    return "失败";
}

function url_encode($str)
{
    if (is_array($str)) {
        foreach ($str as $key => $value) {
            $str[urlencode($key)] = url_encode($value);
        }
    } else {
        $str = urlencode($str);
    }

    return $str;
}

echo urldecode(json_encode(url_encode(MainSelectSpeak($Speak_Key, $db))));

?>