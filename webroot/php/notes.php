<?php
/*
CNTP - PHP Notes
v0.1 sen
*/

session_start();
require('check_header.php');
require(dirname(__FILE__) .'/../../db.php');

// connect to db
$db = new db;
$db->db_connect($_SESSION['uid']);

// load notes
if(isset($_GET['notes']) && $_GET['notes'] === 'load') {
  echo $db->get_notes();   
}

// save notes
if(isset($_POST['notes']) && $_POST['notes'] === 'save') {
  if (isset($_POST['text'])) {
    $db->post_notes($_POST['text']);
  }   
}

// disconnect from db
$db->db_disconnect();

?>