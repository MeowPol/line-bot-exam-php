<?php // callback.php

require "vendor/autoload.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');

$access_token = 'HhmHkpKXiDAcRf9y1OS5d5s9mRWDtSgf3fPE+Cc7HeIf32Du2KDCkIf2GaH7hTX/36fy72AxGD/+nAyF7oLv2Sd2g9nbKVJqjE9tEWft99ofTFRT7t7qW+BZSB2/5WFrDXT5fuBEl5a5WXg/x4UxvgdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	$dsn = "pgsql:"
	    . "host=ec2-23-21-129-50.compute-1.amazonaws.com;"
	    . "dbname=dfd97o1ehpqpnh;"
	    . "user=greeojbcxckhvv;"
	    . "port=5432;"
	    . "sslmode=require;"
	    . "password=e3221695be10dad64a793f3949720bc522c81d1f3c71c71d2d53d998b196f5e8";
	$db = new PDO($dsn);
	$sql = $db->prepare("INSERT INTO IOpoliceNPM ( stationname, postdate, numio) VALUES (? ,? ,?)");
	$sql->bindParam(1, $stationname);
	$sql->bindParam(2, $postdate);
	$sql->bindParam(3, $numio);

	$i = 0;
	$dts = "";
	
	// Loop through each event	
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['source']['userId'];
			// Get replyToken
			$replyToken = $event['replyToken'];
			
				
			$str = $event['message']['text'];
			
			$firstline = substr($str, 0, strpos($str, "\n"));
			if(strcmp($firstline,"สรุปยอด") == 0){
				$secondline = substr($str, strpos($str, "\n")+1);				
				//$dts = "1=" . $firstline . "\n2=" . $secondline;
				$postdate = formatDate($secondline);
				
				$query = "select * from IOpoliceNPM where postdate='" . $postdate . "'";
				$result = $db->query($query);    
				//print_r($result->fetchAll());
				
				$dts .= "1. สถิติการปฏิบัติการ  io  ประจำวันที่ " . $secondline . "\n";
				$dts .= "2. จำนวนหัวข้อเผยแพร่ทางสื่อ Social Network ดังนี้\n";
				$j = 0;
				$totalio = 0;
				$provincial = 0;
				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$cmp = strcmp($row["stationname"],"ภ.จว.นครพนม");
					if($cmp == 0){
						$provincial = $row["numio"];
					}					
					$totalio += $row["numio"];
										
					$dts .= "  2." . ($j+1) . " " . $row["stationname"] . " " . $row["numio"] . " เรื่อง\n" ;
					$j++;
				}
				$dts .= "3. จำนวนเพจที่เผยแพร่ " . ($j+1) . " เพจ\n";
				$dts .= "4 จำนวน IO หน่วยงาน " . $totalio . " เรื่อง\n";
				$dts .= "5. ยอดรวม " . ($totalio+$provincial) . " ครั้ง\n";
				$result->closeCursor();				
			}else{
			
				//***** get station name *****
				$s1 = strpos($str, "สภ.");
				if ($s1 === false) { //not found ภ.จว.
					$s1 = strpos($str, "ภ.จว.");
					$s2 = strpos($str, "\n", $s1);
					$stationname = trim(substr($str, $s1, $s2-$s1));
					
					//***** get number *****
					//$str = substr($str, strpos($str, "เพจ"));
					$s1 = strpos($str, "ยอดรวม");
					$s2 = strpos($str, "เรื่อง", $s1);
					$str2 = substr($str, $s1+2, $s2-($s1+2));
					$dts .= $str2;
					$num = preg_replace("/[^0-9]/", '', $str2);
					
					$numio = $num[0];
					
				}else{// สภ.				
					$s2 = strpos($str, " ", $s1);

					$s22 = strpos($str, "\n", $s1);
					if ($s22 === false) { //not found
					} else if($s22 < $s2){
						$s2 = $s22;	
					}
					//substr(string,start,length)
					$stationname = trim(substr($str, $s1, $s2-$s1));
					
					//***** get number *****
					//$str = substr($str, strpos($str, "เพจ"));
					$s1 = strpos($str, "ยอดรวม");
					$s2 = strpos($str, "ครั้ง", $s1);
					$str2 = substr($str, $s1+2, $s2-($s1+2));
					$dts .= $str2;
					$num = preg_replace("/[^0-9]/", '', $str2);
					
					$numio = $num[0];
				}
				

				//***** get date ************
				$s1 = strpos($str, "ประจำวันที่");
				$s2 = strpos($str, "\n", $s1);

				$s1 += strlen("ประจำวันที่");
				$str2 = preg_replace('!\s+!', ' ', trim(substr($str, $s1, $s2-$s1)));
				//$dts .= "_" . $str2 . "_  ";
				$postdate = formatDate($str2);
				
				//$dts .= $str2 . " " . $stationname . " " . $num[0] . " เรื่อง\n";
				
				$result = $sql->execute();
				if($result){
					$dts .= $postdate . "  ". $stationname . " " . $numio . " เรื่อง  เก็บข้อมูลแล้ว\n";
				}
				
			}
		}
		$i++;
	}//end foreach
	
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
echo "OK";

function formatDate($str2){
	$d = explode(" ",$str2);
	if(strlen($d[0])<2) $d[0] = "0" . $d[0];

	$d[1] = str_replace("มกราคม", "01", $d[1]);
	$d[1] = str_replace("กุมภาพันธ์", "02", $d[1]);
	$d[1] = str_replace("มีนาคม", "03", $d[1]);
	$d[1] = str_replace("เมษายน", "04", $d[1]);
	$d[1] = str_replace("พฤษภาคม", "05", $d[1]);
	$d[1] = str_replace("มิถุนายน", "06", $d[1]);
	$d[1] = str_replace("กรกฎาคม", "07", $d[1]);
	$d[1] = str_replace("สิงหาคม", "08", $d[1]);
	$d[1] = str_replace("กันยายน", "09", $d[1]);
	$d[1] = str_replace("ตุลาคม", "10", $d[1]);
	$d[1] = str_replace("พฤศจิกายน", "11", $d[1]);
	$d[1] = str_replace("ธันวาคม", "12", $d[1]);

	$d[1] = str_replace("ม.ค.", "01", $d[1]);
	$d[1] = str_replace("ก.พ.", "02", $d[1]);
	$d[1] = str_replace("มี.ค.", "03", $d[1]);
	$d[1] = str_replace("เม.ย.", "04", $d[1]);
	$d[1] = str_replace("พ.ค.", "05", $d[1]);
	$d[1] = str_replace("มิ.ย.", "06", $d[1]);
	$d[1] = str_replace("ก.ค.", "07", $d[1]);
	$d[1] = str_replace("ส.ค.", "08", $d[1]);
	$d[1] = str_replace("ก.ย.", "09", $d[1]);
	$d[1] = str_replace("ต.ค.", "10", $d[1]);
	$d[1] = str_replace("พ.ย.", "11", $d[1]);
	$d[1] = str_replace("ธ.ค.", "12", $d[1]);

	$year = (int) $d[2];
	if($year < 100){	
		$year = $year+2500-543;
	}else{
		$year = $year - 543;
	}
	return $year . "-" . $d[1] . "-" . $d[0];	
	
}
?>
