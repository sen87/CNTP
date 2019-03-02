<?php session_start();if(!isset($_SESSION['uid'])){session_unset();session_destroy();header('Location: /portal.php');exit();}else{require('php/init_mobile.php');} ?>
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
  <link id="css_base" rel="stylesheet" href="css/_main/_min_mobile_v0.1.css" type="text/css" />
  <?php $init->head(); ?>
  <script type="text/javascript" src="js/_min_mobile_v0.1.js"></script>
  <link rel="icon" href="favicon/icon16.png" type="image/x-icon" />
</head>
<body>
  <div id="user_panel_left" class="user_panel">
    <form action="php/logout.php" method="post">
      <button id="logout" type="submit" name="submit_logout" title="Logout">✖</button>
    </form>
  </div>
  <div id="user_panel_center" class="user_panel">
    <button id="edit_notes">⌦ Edit Notes ⌫</button>
  </div>
  <div id="user_panel_right" class="user_panel">
    <form action="/settings.php">
      <button id="settings" type="submit" title="Settings">⚙</button>
    </form>
  </div>
  <div class="feed_frame">
  </div>
    <div class="feed_box"><div id="m_buttons"><button id="m_left">◀</button><button id="m_right">▶</button></div>
    <?php $init->footer(); ?>
  </div>
</body>
</html>
