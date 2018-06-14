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
    /*
$dsn = "pgsql:"
    . "host=ec2-23-21-129-50.compute-1.amazonaws.com;"
    . "dbname=dfd97o1ehpqpnh;"
    . "user=greeojbcxckhvv;"
    . "port=5432;"
    . "sslmode=require;"
    . "password=e3221695be10dad64a793f3949720bc522c81d1f3c71c71d2d53d998b196f5e8";

$db = new PDO($dsn);
*/
$dt = new DateTime;
$dt->setTime(0, 0);
echo $dt->format('H:i:s').'<br/>';

    //$dt1 = clone $dt;
    
$d = $dt->format('Y') . "-" . $dt->format('m') . "-01";    
$date1 = date_create($d);
echo $date1->format('Y m d').'<br/>';
$month_end = strtotime('last day of this month', $date1);
echo $month_end.'<br/>';
    
    
$month_start = strtotime('first day of this month', time());
$month_end = strtotime('last day of this month', time());
echo date('D, M jS Y', $month_start).'<br/>';
echo date('D, M jS Y', $month_end).'<br/>';
    
    
/*
$today = getdate();
$postdate = "2018-06-13";
    
$query = "select * from IOpoliceNPM where postdate='" . $postdate . "'";
$result = $db->query($query);    
//print_r($result->fetchAll());
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["postdate"] . "</td>";
    echo "<td>" . htmlspecialchars($row["stationname"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["numio"]) . "</td>";
    echo "</tr>";
}
$result->closeCursor();
*/
?>
   </tbody>
  </table>
 </body>
</html>
