<?php session_start();if(!isset($_SESSION['uid'])){session_unset();session_destroy();header('Location: /portal.php');exit();}else{require('php/init.php');} ?>
<!DOCTYPE html>
<!--
Title:    CNTP
Author:   sen
Version:  0.7
-->
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
  <link id="css_base" rel="stylesheet" href="css/_main/_min_base_v1.2.css" type="text/css" />
  <?php $init->head(); ?>
  <script type="text/javascript" src="js/_min_index_v0.5.js"></script>
  <link rel="icon" href="favicon/icon16.png" type="image/x-icon" />
</head>
<body>
  <table id="window_tab"><?php $init->table(); ?></table>
  <div id="user_panel_left" class="user_panel">
    <button class="fs" name="1" title="Feed Set 1" id="fs_act">◐</button><br>
    <button class="fs" name="2" title="Feed Set 2">◑</button>
  </div>
  <div id="user_panel_right" class="user_panel">
    <form action="/settings.php">
      <button id="settings" type="submit" title="Settings">⚙</button>
    </form>
    <form action="php/logout.php" method="post">
      <button id="logout" type="submit" name="submit_logout" title="Logout">✖</button>
    </form>
  </div>
</body>
</html>
