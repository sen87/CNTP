<?php session_start();if(isset($_SESSION['uid'])){header('Location: /');} ?>
<!DOCTYPE html>
<!--
Title:    CNTP - Login
Author:   sen
Version:  0.1
-->
<html>
<head>
  <meta charset="UTF-8">
  <title>CNTP</title>
  <link rel="stylesheet" href="css/_main/_min_portal_v0.1.css" type="text/css" />
  <script type="text/javascript" src="js/_min_portal_v0.1.js"></script>
  <link rel="icon" href="favicon/icon16.png" type="image/x-icon" />
</head>
<body>
  <div id="box">
    <a href="/portal.php"><img src="css/_main/logo_login.png" alt="logo"></a>
    <ul>
      <li id="login" class="link">login</li>
      <li id="registration" class="link">registration</li>
      <li id="demo" class="link">demo</li>
      <li id="about" class="link">about</li>
    </ul>
    <div id="tab_login" class="tab">
      <form action="php/portal_worker.php" method="post">
        <img src="css/_main/mail.svg" alt="Mail" height="22" width="22"><input class="mail" type="text" name="mail" placeholder="Email Address..." autocomplete="email"><br>
        <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input type="password" name="pwd" placeholder="Password..." autocomplete="password"><br>
        <button type="submit" name="submit_login">Login</button>
      </form>
      <div id="log_info" style="display:none">
        <p id="log_msg"></p>
        <hr>
        <form class"pwd_reset" action="php/portal_worker.php" method="post">
          <img src="css/_main/pwd_reset.svg" alt="Mail" height="22" width="22">
          <input class="mail" type="text" name="mail_pwd_reset" placeholder="Registered Email Address..." autocomplete="email"><br>
          <button type="submit" name="submit_repw">Reset Password</button>
        </form>
      </div>
    </div>
    <div id="tab_registration" class="tab">
      <form action="php/portal_worker.php" method="post">
        <img src="css/_main/mail.svg" alt="Mail" height="22" width="22"><input class="mail" type="text" name="mail" placeholder="Email Address..." autocomplete="email"><br>
        <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input type="password" name="pwd" placeholder="Password..." autocomplete="password"><br>
        <img src="css/_main/pwd.svg" alt="Password" height="22" width="22"><input type="password" name="pwd_repeat" placeholder="Repeat Password..." autocomplete="password"><br>
        <button type="submit" name="submit_registration">Register</button>
        <p id="reg_info" style="display:none"></p>
      </form>
    </div>
    <div id="tab_demo" class="tab">
      <h1>Live Demo</h1>
      <hr>
      <img src="css/_main/info.svg" alt="Info" height="64" width="64">
      <p>Settings cannot be changed while using the demo account!</p>
      <hr>
      <form action="php/portal_worker.php" method="post">
        <button type="submit" name="submit_demo">Launch Demo</button>
      </form>
    </div>
    <div id="tab_about" class="tab">
        <h1>CNTP - Custom New Tab Page</h1>
        Version <b id="version">0.7</b>
        <hr>
        <h1>Features</h1>
        Feed Reader (atom | rss 1.0 | rss 2.0 | mrss)<br>
        Bookmarks<br>
        Notes<br>
        Weather Forecast (<a href="https://hjelp.yr.no/hc/en-us/articles/360009342833-XML-weather-forecasts">provided by yr.no</a>)<br>
        <hr>
        <h1>Browser Addons (Set New Tab Page)</h1>
        <a href="https://chrome.google.com/webstore/detail/new-tab-redirect/icpgjfneehieebagbmdbhnlpiopdcmna">Chrome / Chromium</a><br>
        <a href="https://addons.mozilla.org/en-US/firefox/addon/change-new-tab/">Firefox</a><br>
        <hr>
        <h1>Disclaimer</h1>
        This is a non-commercial website run on a private server.<br>
        Speed may vary and (short) downtimes can be expected.
        <hr>
        <a id="mail_admin" href="mailto:"><img src="css/_main/mail.svg" alt="Mail" height="22" width="22">Contact</a><br>
        <a href="https://github.com/sen87/Chrome-CNTP"><img src="css/_main/gh.ico" alt="GitHub" height="22" width="22">Code on GitHub</a>
    </div>
  </div>
</body>
</html>
