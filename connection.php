<?php 
$server_name = 'mysql';
$user_name = 'Riya';
$password = 'student';
$database_name = 'ijdb';

$connection = new PDO('mysql:dbname='.$database_name.';host='.$server_name, $user_name, $password);
// if ($connection) {
//     echo 'connected';
// }
?>
