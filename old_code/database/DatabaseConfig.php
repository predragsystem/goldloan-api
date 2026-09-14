<?php
$config['DBHostName'] = "localhost";
$config['DBUserName']= "root";
$config['DBPassword']= "";
$config['DBName']= "girviloan";

/*$hostname = getenv('HTTP_HOST');
if($hostname == "nalge.predragsystem.in"){
$config['DBHostName'] = "localhost";
$config['DBUserName'] = "u255014993_clientloan";
$config['DBPassword'] = "58>IRU#ZB&yO";
$config['DBName'] = "u255014993_clientloan";
}

if($hostname == "loan.predragsystem.in"){
$config['DBHostName'] = "localhost";
$config['DBUserName'] = "u255014993_apnamunim";
$config['DBPassword'] = "kC1&LRs#";
$config['DBName'] = "u255014993_apnamunim";
}*/

 $conn = @mysqli_connect($config['DBHostName'],$config['DBUserName'],$config['DBPassword'],$config['DBName']);

// mysqli_select_db($conn,$config['DBName']);

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());
}
  
 
?>
