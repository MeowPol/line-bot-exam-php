<?php // callback.php

require "vendor/autoload.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');

$access_token = 'HhmHkpKXiDAcRf9y1OS5d5s9mRWDtSgf3fPE+Cc7HeIf32Du2KDCkIf2GaH7hTX/36fy72AxGD/+nAyF7oLv2Sd2g9nbKVJqjE9tEWft99ofTFRT7t7qW+BZSB2/5WFrDXT5fuBEl5a5WXg/x4UxvgdB04t89/1O/w1cDnyilFU=';

$dts = new DateTime(); //this returns the current date time


// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	$i = 0;
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['source']['userId'];
			// Get replyToken
			$replyToken = $event['replyToken'];
			
				
			$str = $event['message']['text'];			
			
			//***** get station name
			$s1 = strpos($str, "สภ.");
			$s2 = strpos($str, " ", $s1);
			
			$s22 = strpos($str, "\n", $s1);
			if ($s22 === false) { //not found
			} else if($s22 < $s2){
				$s2 = $s22;	
			}
			
			$s23 = strpos($str, "\r", $s1);
			if ($s23 === false) { //not found
			} else if($s23 < $s2){
				$s2 = $s23;	
			}
			//substr(string,start,length)
			$stationname = substr($str, $s1, $s2-$s1+1);
			
			//****get number
			$s1 = strpos($str, "4.");
			$s2 = strpos($str, " ", $s1);
			
			$s22 = strpos($str, "\n", $s1);
			if ($s22 === false) { //not found
			} else if($s22 < $s2){
				$s2 = $s22;	
			}
			
			$s23 = strpos($str, "\r", $s1);
			if ($s23 === false) { //not found
			} else if($s23 < $s2){
				$s2 = $s23;	
			}
			$str = substr($str, $s1+2, $s2-($s1+2)+1);
			//$num = preg_replace("/[^0-9]/", '', $str);
			
			$dts = $stationname . " " . $num;
			
			
			
			
			

			// Build message to reply back
			$messages = [
				'type' => 'text',
				//'text' => $text
				'text' => $dts
			];
			
			

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}
		$i++;
	}
}
echo "OK";
