<?php
/*
CNTP - PHP Logout
*/

if (isset($_POST['submit_logout'])) {
  session_start();
  session_unset();
  session_destroy();
  header('Location: ../portal.php');
}

?>