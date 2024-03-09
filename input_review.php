<?php
// session_start();
// require 'connection.php';
?>

<?php
if(isset($_POST['submit'])) {
        $insertRecord = $connection->prepare('INSERT INTO review (username, date) 
        VALUES (:username,:date)');
         $criteria=[
            'username'=>$_POST['reviewtext'],
            // 'date'=>$_POST['date']
        ];
        $insertRecord->execute($criteria);
            if($insertRecord){
                echo'';
            }
}
?>
        