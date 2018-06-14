<html>
 <head>
  <title></title>
 </head>
 <body>
  <table>
   <thead>
    <tr>
     <th>วันที่</th>
     <th>สภ.</th>
     <th>จำนวน</th>
    </tr>
   </thead>
   <tbody>
<?php
    
$dsn = "pgsql:"
    . "host=ec2-23-21-129-50.compute-1.amazonaws.com;"
    . "dbname=dfd97o1ehpqpnh;"
    . "user=greeojbcxckhvv;"
    . "port=5432;"
    . "sslmode=require;"
    . "password=e3221695be10dad64a793f3949720bc522c81d1f3c71c71d2d53d998b196f5e8";

$db = new PDO($dsn);

    $dt = date_create();
    $dt->setTime(0, 0);
    $month_start = date_format($dt,"Y-m-01 H:i:s");
    $month_end = date_format($dt,"Y-m-t H:i:s");
    echo $month_start.'<br/>';
    echo $month_end.'<br/>';

    $where = "where postdate between '" . $month_start . "' and '" .$month_end. "' ";
    //$query = "select * from IOpoliceNPM ".$where." order by postdate";
	   $query = "select postdate, stationname, numio from IOpoliceNPM ".$where." order by postdate";
	   
	   echo $query.'<br/>';
	   $result = $db->query($query);    
	   print_r($result->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP));
    
	   $query = "SELECT DISTINCT postdate FROM IOpoliceNPM " .$where;
	   echo $query.'<br/>';
	   $result = $db->query($query);    
	   print_r($result->fetchAll(PDO::FETCH_COLUMN, 0));
	   
	   $query = "SELECT DISTINCT stationname FROM IOpoliceNPM " .$where;
	   echo $query.'<br/>';
	   $result = $db->query($query);    
	   print_r($result->fetchAll(PDO::FETCH_COLUMN, 0));
	   
	   PDO::FETCH_COLUMN|PDO::FETCH_GROUP

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
