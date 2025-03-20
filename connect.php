<?php
require('config.php');
$dsn= "mysql:host=$host;dbname=$db;charset=UTF8";

try {
	$conn = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
	echo $e->getMessage();
}

?>