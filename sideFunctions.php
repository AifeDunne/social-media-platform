<?php
if (isset($_POST['checkUsers'])) { 
$path = $_SERVER['DOCUMENT_ROOT'];
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/pluggable.php';

$error = "none";
$userName =  $_POST['checkUsers'];
$uEmail = $_POST['checkEmail'];
if ( username_exists( $userName ) ) { $error = 'un-exists'; }
else if ( ! validate_username( $userName ) ) { $error = 'un-invalid'; }
if ( !is_email( $uEmail ) ) { $error = 'email-invalid'; }
else if ( email_exists( $uEmail ) ) { $error = 'email-exists'; }
echo $error; }
?>
