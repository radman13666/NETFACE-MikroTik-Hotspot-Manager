<?php 
if (!isset($_SERVER['HTTP_REFERER'])){
    header('location:../login.php');
    exit;
}
?>


