<?php
$access_token = 'HhmHkpKXiDAcRf9y1OS5d5s9mRWDtSgf3fPE+Cc7HeIf32Du2KDCkIf2GaH7hTX/36fy72AxGD/+nAyF7oLv2Sd2g9nbKVJqjE9tEWft99ofTFRT7t7qW+BZSB2/5WFrDXT5fuBEl5a5WXg/x4UxvgdB04t89/1O/w1cDnyilFU=';


$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
