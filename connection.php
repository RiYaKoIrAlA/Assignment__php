<?php 
$server_name = 'mysql';
$user_name = 'Riya_Koirala';
$password = 'student';
$database_name = 'car_auction';

$connection = new PDO('mysql:dbname='.$database_name.';host='.$server_name, $user_name, $password);
if ($connection) {
    echo 'connected';
}
?>
