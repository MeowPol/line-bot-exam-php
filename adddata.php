<?php
$dsn = "pgsql:"
    . "host=ec2-23-21-129-50.compute-1.amazonaws.com;"
    . "dbname=dfd97o1ehpqpnh;"
    . "user=greeojbcxckhvv;"
    . "port=5432;"
    . "sslmode=require;"
    . "password=e3221695be10dad64a793f3949720bc522c81d1f3c71c71d2d53d998b196f5e8";
$db = new PDO($dsn);

/*
$sql = $db->prepare("INSERT INTO IOpoliceNPM ( stationname, postdate, numio) VALUES (? ,? ,?)");
$sql->bindParam(1, $name);
$sql->bindParam(2, $date);
$sql->bindParam(3, $num);

$name = "nawa";
$date = "2018-06-10";
$num = 3;
echo $sql->execute();
*/


$query = "delete from IOpoliceNPM where postdate='2018-06-21'";
$result = $db->query($query);    
print_r($result->rowCount());
$result->closeCursor();


?>
  
