<html>
 <head>
  <title>io รายเดือน</title>
 </head>
 <body>
	   <table border=1>
	   <thead>
		<tr>
	 
<?php
	$getmonth = $_GET["month"];
	$getyear = $_GET["year"];
	//echo "MONTH = ". $getmonth.'<br/>';
	//echo "YEAR = ". $getyear.'<br/>';
		
	$month_start = $getyear. "-" . $getmonth . "-01 00:00:00";			
	$month_end = date("Y-m-t H:i:s", strtotime($month_start)); 
			
// 	echo $month_start.'<br/>';
// 	echo $month_end.'<br/>';
	
			

	$dsn = "pgsql:"
	    . "host=ec2-3-222-183-44.compute-1.amazonaws.com;"
	    . "dbname=dbad8bmvhmfe4b;"
	    . "user=rydwjtvinqkklj;"
	    . "port=5432;"
	    . "sslmode=require;"
	    . "password=c24a6f1120bc465cc4598c764b4b5f5cb1dd4aed6fdbf2bae0559b787e9a9546"; 

	$db = new PDO($dsn);

			/*
		   $dt = date_create();
		   $dt->setTime(0, 0);

			//current month	
		   //$month_start = date_format($dt,"Y-m-01 H:i:s");
		   //$month_end = date_format($dt,"Y-m-t H:i:s");


		   $month_start = date("Y-m-d H:i:s",strtotime("first day of last month midnight"));
		   $month_end = date("Y-m-d H:i:s",strtotime("last day of last month midnight"));


		   echo $month_start.'<br/>';
		   echo $month_end.'<br/>';
			*/

		   $where = "where postdate between '" . $month_start . "' and '" .$month_end. "' "; 
		   //$query = "select * from IOpoliceNPM ".$where." order by postdate";
		   $query = "SELECT DISTINCT postdate FROM IOpoliceNPM " .$where . " order by postdate";
		   //echo $query.'<br/>';
		   $result = $db->query($query);
		   $postdatearr = $result->fetchAll(PDO::FETCH_COLUMN, 0);
		   //print_r($postdatearr);
		   //echo "<br/><br/>";	   

		   $query = "SELECT DISTINCT stationname FROM IOpoliceNPM " .$where . " order by stationname";
		   //echo $query.'<br/>';
		   $result = $db->query($query);
		   $namearr = $result->fetchAll(PDO::FETCH_COLUMN, 0);
		   //print_r($namearr);
		   //echo "<br/><br/>";

		   $array = array();
				$totalio = array(); 
		   for($i=0; $i<count($postdatearr); $i++){
			   $array[$postdatearr[$i]] = array();
			   $totalio[$postdatearr[$i]] = 0;
			   for($j=0; $j<count($namearr); $j++){
				   $array[$postdatearr[$i]][$namearr[$j]] = 0;
			   }
		   }

		   //print_r($array);
		   //echo "<br/><br/>";



	       $query = "select postdate, stationname, numio from IOpoliceNPM ".$where." order by postdate, stationname";	   
		   //echo $query.'<br/>';
		   //echo "<br/><br/>";
		   $result = $db->query($query);    
		   while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			   $cmp = strcmp($row["stationname"],"ภ.จว.นครพนม");
			   if($cmp == 0){
				   $num = $row["numio"]*2;
			   }else{
				   $num = $row["numio"];						
			   }
			   $array[$row["postdate"]][$row["stationname"]] += $num;
			   $totalio[$row["postdate"]] += $num;

			   /*
			   echo "*********************************************";
			   print_r($row);
			   echo "<br/><br/>";
			   //print_r($array);
			   echo "<br/><br/>";
			   print_r($totalio);
			   echo "<br/><br/>";
			   */
		   }
		   //print_r($array);    	
		   //echo "<br/><br/>";


			echo "<td>test</td>";
			for($i=0; $i<count($postdatearr); $i++){
				echo "<td>". $postdatearr[$i]."</td>";
			}
			echo "</tr></thead>";


			echo "<tbody>";
			//print_r($namearr);
			$stationnamearr = array("ภ.จว.นครพนม","สภ.กุตาไก้","สภ.ท่าอุเทน","สภ.ธาตุพนม","สภ.นาแก","สภ.นาโดน","สภ.นาทม","สภ.นาหว้า","สภ.บ้านกลาง","สภ.บ้านแพง","สภ.ปลาปาก","สภ.พระซอง","สภ.โพนสวรรค์","สภ.เมืองนครพนม","สภ.เรณูนคร","สภ.วังยาง","สภ.ศรีสงคราม","สภ.หนองบ่อ","สภ.หนองฮี","สภ.หลักศิลา");
			//$namearr is from db	   
			for($j=0; $j<count($stationnamearr); $j++){
				echo "<tr><td>".$stationnamearr[$j]."</td>";
				$indexinarr = array_search($stationnamearr[$j], $namearr);
				//echo $indexinarr;
				if($indexinarr !== false) {
					for($i=0; $i<count($postdatearr); $i++){
						echo "<td>". $array[$postdatearr[$i]][$namearr[$indexinarr]]."</td>";
					}

				}else{//not found stationname
					for($i=0; $i<count($postdatearr); $i++){
						echo "<td></td>";
					}
				}
				echo "</tr>";
			}

				echo "<tr><td>รวม</td>";
				for($i=0; $i<count($postdatearr); $i++){
					echo "<td>". $totalio[$postdatearr[$i]]."</td>";
				}
				echo "</tr>";
				//print_r($totalio);

		   //$result = $db->query($query);    

		   //print_r($result->fetchAll());
		   /*
		   while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		   echo "<tr>";
		   echo "<td>" . $row["postdate"] . "</td>";
		   echo "<td>" . htmlspecialchars($row["stationname"]) . "</td>";
		   echo "<td>" . htmlspecialchars($row["numio"]) . "</td>";
		   echo "</tr>";
		   }
		   */
		   $result->closeCursor();
?>

   </tbody>
  </table>
 </body>
</html>
