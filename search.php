<?php
session_start();
?>
<?php
if(isset($_POST['submit'])) {
    $search = $_POST['search'];
    
}

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
                echo'yaakkkk';
            }
}
?>