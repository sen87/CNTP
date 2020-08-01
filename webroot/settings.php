<?php session_start();if(!isset($_SESSION['uid'])){session_unset();session_destroy();header('Location: /portal.php');exit();}else{require('php/settings_worker.php');} ?>
<!DOCTYPE html>
<!--
CNTP - Settings
v0.1 sen
-->
<html>
<head>
  <meta charset="UTF-8">
  <title>CNTP</title>
  <link rel="stylesheet" href="css/_main/_min_settings_v0.1.css" type="text/css" />
  <script type="text/javascript" src="js/_min_settings_v0.3.js"></script>
  <link rel="icon" href="favicon/icon16.png" type="image/x-icon" />
</head>
<body>
  <div id="box">
    <ul>
      <li id="feeds" class="link">feeds</li>
      <li id="bookmarks" class="link">bookmarks</li>
      <li id="weather" class="link">weather</li>
      <a href="/" class="link"><img src="css/_main/logo_settings.png" alt="logo"></a>
      <li id="layout" class="link">layout</li>
      <li id="theme" class="link">theme</li>
      <li id="user" class="link">user</li>
    </ul>
    <form id="main" action="php/settings_worker.php" method="post"></form>
    <form id="feed" action="php/settings_worker.php" method="post"></form>
    <form id="chmail" action="php/settings_worker.php" method="post"></form>
    <form id="chpw" action="php/settings_worker.php" method="post"></form>
    <form id="delete_user" action="php/settings_worker.php" method="post"></form>
      <div id="content">
        <div id="tab_feeds" class="tab">
          <h1>Selection</h1>
          <p>Active feeds are updated <u>every 15 minutes</u>.</p>
          <?php $init->feed_select(); ?>
          <hr>
          <h1>Create & Edit</h1>
          <img src="css/_main/info.svg" alt="info" height="64" width="64"><br>
          <p>Feeds are <u>shared</u> across all users.</p>
          <p>You can only modify a feed if no other user has selected it (create a duplicate if needed).</p>
          <p><u>Please</u> do not delete working feeds!</p>
          <?php $init->feed_edit(); ?>
          <img src="css/_main/title.svg" alt="name" height="22" width="22"><input id="feed_name" class="inp_feed" type="text" name="name" form="feed" placeholder="Name..."><br>
          <img src="css/_main/desc.svg" alt="description" height="22" width="22"><input id="feed_desc" class="inp_feed" type="text" name="description" form="feed" placeholder="Short description..."><br>
          <img src="css/_main/url.svg" alt="feed" height="22" width="22"><input id="feed_url" class="inp_feed" type="text" name="url" form="feed" placeholder="Feed URL..."><br>
          <img src="css/_main/url.svg" alt="website" height="22" width="22"><input id="feed_website" class="inp_feed" type="text" name="website" form="feed" placeholder="Link to main site..."><br>
          <input id="feed_thumb" type="checkbox" name="thumbnails" form="feed">show Thumbnails (if available)</input><br>
          <button class="btn_feed" type="submit" name="submit_feed_delete" form="feed"><img src="css/_main/delete.svg" alt="delete" height="22" width="22">Delete</button>
          <button class="btn_feed" type="submit" name="submit_feed_edit" form="feed"><img src="css/_main/edit.svg" alt="edit" height="22" width="22">Edit</button>
          <button class="btn_feed" type="submit" name="submit_feed" form="feed"><img src="css/_main/add.svg" alt="create" height="22" width="22">Create</button>
        </div>
        <div id="tab_bookmarks" class="tab">
          <h1>Add a new Category</h1>
          <img src="css/_main/title.svg" alt="name" height="22" width="22"><input id="cat_name_new" type="text" placeholder="Name...">
          <button id="btn_cat_add" ><img src="css/_main/add.svg" alt="create" height="22" width="22">Add</button>
          <hr>
          <h1>Edit Category</h1>
          <?php $init->bookmarks(); ?>
          <br><br><br>
        </div>
        <div id="tab_weather" class="tab">
          <br><a href="https://www.yr.no"><img src="css/_main/yr.webp" alt="name" height="128" width="128"></a>
          <a href="https://hjelp.yr.no/hc/en-us/articles/360009342833-XML-weather-forecasts">
          <h1>Weather forecast from Yr, delivered by the Norwegian Meteorological Institute and NRK.</h1></a>
          <hr>
          <?php $init->weather(); ?>
        </div>
        <div id="tab_layout" class="tab">
          <h1>Tab Title</h1>
          <img src="css/_main/title.svg" alt="Mail" height="22" width="22">
          <?php $init->layout_title(); ?>
          <hr>
          <h1>Table</h1>
          <img src="css/_main/row.svg" alt="Mail" height="32" width="32">
          <select id="layout_row">
            <option value=1>1 Row</option>
            <option value=2>2 Rows</option>
            <option value=3>3 Rows</option>
            <option value=4>4 Rows</option>
            <option value=5>5 Rows</option>
          </select>
          <img src="css/_main/column.svg" alt="Mail" height="32" width="32">
          <select id="layout_column">
            <option value=1>1 Column</option>
            <option value=2>2 Columns</option>
            <option value=3>3 Columns</option>
            <option value=4>4 Columns</option>
            <option value=5>5 Columns</option>
            <option value=6>6 Columns</option>
            <option value=7>7 Columns</option>
            <option value=8>8 Columns</option>
          </select>
          <hr class="hr_480">
          <?php $init->layout_table(); ?><br>
          <div class="color_box B"></div> Bookmarks
          <div class="color_box N"></div> Notes
          <div class="color_box W"></div> Weather
          <div class="color_box F"></div> <label id="feed_count"></label> Feeds
          <hr class="hr_480">
          <div id="layout_create"></div>
          <hr class="hr_480">
          <button id="layout_save"><img src="css/_main/update.svg" alt="update" height="22" width="22">Update Layout</button><br><br><br><br>
        </div>
        <div id="tab_theme" class="tab">
          <h1>CSS</h1>
          <?php $init->theme(); ?>
        </div>
        <div id="tab_user" class="tab">
          <h1>Change Password</h1>
          <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input class="inp_pwd" type="password" name="pwd" form="chpw" placeholder="Old Password..."><br>
          <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input class="inp_pwd" type="password" name="pwd_new" form="chpw" placeholder="New Password..."><br>
          <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input class="inp_pwd" type="password" name="pwd_new_repeat" form="chpw" placeholder="Repeat New Password..."><br>
          <button type="submit" name="submit_chpw" form="chpw"><img src="css/_main/update.svg" alt="update" height="22" width="22">Change Password</button>
          <hr>
          <h1>Change Email Address</h1>
          <img src="css/_main/mail.svg" alt="Mail" height="22" width="22"><input class="inp_mail" type="text" name="mail" form="chmail" placeholder="Old Email Address..." autocomplete="email"><br>
          <img src="css/_main/mail.svg" alt="Mail" height="22" width="22"><input class="inp_mail" type="text" name="mail_new" form="chmail" placeholder="New Email Address..." autocomplete="email"><br>
          <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input class="inp_pwd" type="password" name="pwd" form="chmail" placeholder="Password..."><br>
          <button type="submit" name="submit_chmail" form="chmail"><img src="css/_main/update.svg" alt="update" height="22" width="22">Change Email Address</button>
          <hr>
          <h1>Delete Account</h1>
          <p><b>Warning:</b> Your complete user account will be removed!</p>
          <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input class="inp_pwd" type="password" name="pwd" form="delete_user" placeholder="Password..."><br>
          <button type="submit" name="submit_delete_user" form="delete_user"><img src="css/_main/delete.svg" alt="delete" height="22" width="22">Delete Account</button>    
        </div>
      </div>
      <div id="footer">
        <div class="btn_left"><button type="submit" name="submit_exit" form="main">✖ Exit</button></div>
        <h2 id="msg"></h2>
        <div class="btn_right"><button id="submit_save" type="submit" name="submit_save" form="main">⮹ Save/Exit</button></div>
      </div>
    </form>
  </div>
</body>
</html>
