<?php
/*
CNTP - PHP Mail Draft
v0.1 sen
*/

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                HTML Mail                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function sendmail($to,$subject,$msg) {
  // headers
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type:text/html;charset=UTF-8\r\n";
  $headers .= "From: " . config::$domain . "\r\n";
  // message
  $message = '
  <html>
    <head>
      <style>
        * {
          color: black;
          text-align: center;
        }
        .tbl {
          background-color: #222222;
          width:100%;
        }
        .box {
          background: linear-gradient(#dcdcdc, #bcbcbc 36px, #aaaaaa);
          border-radius: 6px;
          border: 1px solid #000000;
          margin-bottom: 10px;
          margin-left: auto;
          margin-right: auto;
          margin-top: 10px;
          padding: 10px;
          width: 806px;
        }
        .logo,
        .tab {
          background-color: white;
          border: 2px solid #888888;
          border-radius: 6px;
          padding: 10px;
        }
        .logo {
          margin-bottom: 10px;
        }
        .tab {
          padding-bottom: 30px;
        }
        .tab a {
          background: linear-gradient(#dddddd, #bbbbbb);
          border: 1px solid #888888;
          border-radius: 6px;
          color: #222222;
          font-weight: bold;
          padding: 5px;
          text-decoration: none;
        }
      </style>
    </head>
    <body>
      <table class="tbl"><tr><td>
        <div class="box">
          <div class="logo">
             <a href="https://'.config::$domain.'"><img src="https://'.config::$domain.'/css/_main/logo_mail.png" alt="logo"></a>
          </div>
          <div class="tab">';
  $message .= $msg;
  $message .= '
          </div>
        </div>
      </td></tr></table>
    </body>
  </html>';
  if(mail($to,$subject,$message,$headers)) {
    return true;
  } else {
    return false;
  }
}

?>