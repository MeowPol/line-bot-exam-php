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
	    . "host=ec2-3-222-183-44.compute-1.amazonaws.com;"
	    . "dbname=dbad8bmvhmfe4b;"
	    . "user=rydwjtvinqkklj;"
	    . "port=5432;"
	    . "sslmode=require;"
	    . "password=c24a6f1120bc465cc4598c764b4b5f5cb1dd4aed6fdbf2bae0559b787e9a9546";
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
			$firstchar = substr($str, 0, 1);
			if(strcmp($firstchar, "#") == 0){
				$str2 = explode("\n", substr($str, 1));
				$command = trim($str2[0]);
				$secondline = trim($str2[1]);
				if(strcmp($command,"สรุปยอด") == 0){
					$postdate = formatDate($secondline);
					$query = "select * from IOpoliceNPM where postdate='" . $postdate . "'";
					$result = $db->query($query);    
					//print_r($result->fetchAll());
					$dts .= "1. สถิติการปฏิบัติการ  IO  ประจำวันที่ " . $secondline . "\n";
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
					
				}else if(strcmp($command,"ลบรายการ") == 0){
					$postdate = formatDate($secondline);
					$stationname = trim($str2[2]);
					$query = "delete from IOpoliceNPM where postdate='" . $postdate . "' and stationname='". $stationname . "'";
					$result = $db->query($query);
					
					$dts .= "ลบข้อมูล " . $result->rowCount() . " รายการ\n";
					$dts .= $secondline . " " . $stationname . "\n";
						
					$result->closeCursor();
					
				}else if(strcmp($command,"ลบวัน") == 0){
					$postdate = formatDate($secondline);
					$query = "delete from IOpoliceNPM where postdate='" . $postdate . "'";
					$result = $db->query($query);
					
					$dts .= "ลบข้อมูล " . $result->rowCount() . " รายการ\n";
					$dts .= $secondline . "\n";
						
					$result->closeCursor();
					
				}
			}else{	
				$check = 0;
				//***** get station name *****
				$s1 = strpos($str, "สภ.");
				if ($s1 === false) { //not found สภ. ==> ภ.จว.
					
						/*
						$s1 = strpos($str, "ภ.จว.");
						$s2 = strpos($str, "\n", $s1);
						$stationname = trim(substr($str, $s1, $s2-$s1));

						//***** get number *****
						//$str = substr($str, strpos($str, "เพจ"));
						$s1 = strpos($str, "ยอดรวม") + strlen("ยอดรวม");
						$s2 = strpos($str, "เรื่อง", $s1);
						$numio = substr($str, $s1, $s2-$s1);					
						*/
						$str2 = explode("\n", $str);
						$stationname = trim($str2[0]);

						$s1 = strpos($str2[1], "ประจำวันที่") + strlen("ประจำวันที่");
						$datestr = trim(substr($str2[1], $s1));					
						$postdate = formatDate($datestr);

						$s1 = strpos($str2[2], "ยอดรวม") + strlen("ยอดรวม");
						$s2 = strpos($str2[2], "ครั้ง", $s1);
						$numio = substr($str2[2], $s1, $s2-$s1);
						
						$check = 1;
					
				}else{// สภ.	
					if(strpos(substr($str, 0, strpos($str, "สถิติ")), "สภ.")=== false){
					}else{
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
						$s1 = strpos($str, "ยอดรวม") + strlen("ยอดรวม");
						$s2 = strpos($str, "ครั้ง", $s1);
						$numio = substr($str, $s1, $s2-$s1);

						//***** get date ************
						$s1 = strpos($str, "ประจำวันที่");
						$s2 = strpos($str, "\n", $s1);
						$s1 += strlen("ประจำวันที่");
						$str2 = preg_replace('!\s+!', ' ', trim(substr($str, $s1, $s2-$s1)));
						//$dts .= "_" . $str2 . "_  ";
						$postdate = formatDate($str2);
						
						$check = 1;
					}
				}
				
				
				
				//$dts .= $str2 . " " . $stationname . " " . $num[0] . " เรื่อง\n";
				if($check == 1){
					$result = $sql->execute();
					if($result){
						$dts .= $postdate . "  ". $stationname . " " . $numio . " เรื่อง  เก็บข้อมูลแล้ว\n";
					}
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
	$str2 = str_replace("มกราคม", "01 ", $str2);
	$str2 = str_replace("กุมภาพันธ์", "02 ", $str2);
	$str2 = str_replace("มีนาคม", "03 ", $str2);
	$str2 = str_replace("เมษายน", "04 ", $str2);
	$str2 = str_replace("พฤษภาคม", "05 ", $str2);
	$str2 = str_replace("มิถุนายน", "06 ", $str2);
	$str2 = str_replace("กรกฎาคม", "07 ", $str2);
	$str2 = str_replace("กรกฏาคม", "07 ", $str2);
	$str2 = str_replace("สิงหาคม", "08 ", $str2);
	$str2 = str_replace("กันยายน", "09 ", $str2);
	$str2 = str_replace("ตุลาคม", "10 ", $str2);
	$str2 = str_replace("พฤศจิกายน", "11 ", $str2);
	$str2 = str_replace("ธันวาคม", "12 ", $str2);
	
	$str2 = str_replace("ม.ค.", "01 ", $str2);
	$str2 = str_replace("ก.พ.", "02 ", $str2);
	$str2 = str_replace("มี.ค.", "03 ", $str2);
	$str2 = str_replace("เม.ย.", "04 ", $str2);
	$str2 = str_replace("พ.ค.", "05 ", $str2);
	$str2 = str_replace("มิ.ย.", "06 ", $str2);
	$str2 = str_replace("ก.ค.", "07 ", $str2);
	$str2 = str_replace("ส.ค.", "08 ", $str2);
	$str2 = str_replace("ก.ย.", "09 ", $str2);
	$str2 = str_replace("ต.ค.", "10 ", $str2);
	$str2 = str_replace("พ.ย.", "11 ", $str2);
	$str2 = str_replace("ธ.ค.", "12 ", $str2);
	$str2 = str_replace("  ", " ", $str2);
	
	$str2 = preg_replace('!\s+!', ' ', $str2);
	
	$d = explode(" ",$str2);
	
	if(strlen($d[0])<2) $d[0] = "0" . $d[0];
	
	$year = (int) $d[2];
	if($year < 100){	
		$year = $year+2500-543;
	}else{
		$year = $year - 543;
	}
	return $year . "-" . $d[1] . "-" . $d[0];	
	
}
?>
