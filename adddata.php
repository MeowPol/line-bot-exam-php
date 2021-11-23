<?php
$dsn = "pgsql:"
	    . "host=ec2-3-222-183-44.compute-1.amazonaws.com;"
	    . "dbname=dbad8bmvhmfe4b;"
	    . "user=rydwjtvinqkklj;"
	    . "port=5432;"
	    . "sslmode=require;"
	    . "password=c24a6f1120bc465cc4598c764b4b5f5cb1dd4aed6fdbf2bae0559b787e9a9546";
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
  
