<?php

$host="localhost";
$user="id22174203_iulia";
$pass="qrty09#smarT";
$db="id22174203_smartbudy";
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}
?>
