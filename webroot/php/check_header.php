<?php
/*
CNTP - PHP Header Validation
v0.1 sen
*/

if(!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'CNTP'))
{
  header('Location: /');
  exit;
}

?>