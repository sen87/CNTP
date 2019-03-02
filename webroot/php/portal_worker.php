<?php
/*
CNTP - PHP User Management
v0.1 sen
*/

require(dirname(__FILE__) .'/../../db.php');
require('mail.php');

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Init                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

if (isset($_GET['init'])) {
  require('check_header.php');
  $init_conf = array(config::$version, config::$mail);
  echo json_encode($init_conf);
  exit();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Login                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

if (isset($_POST['submit_login'])) {
  // $_POST
  $mail = $_POST['mail'];
  $pwd = $_POST['pwd'];
  // empty fields?
  if (empty($mail) || empty($pwd)) {
    header('Location: ../portal.php?log=empty?mail=' . $mail);
    exit();
  } else {
    // connect to db
    $db = new db;
    $db->db_connect(0);
    // email registered & account verified?
    $uid = $db->get_uid($mail);
    if (isset($uid) && $db->get_verified($uid)) {
      // correct password?
      $pwd_hash = $db->get_pwd($uid);
      if (password_verify($pwd, $pwd_hash)) {
        // login
        session_start();
        $_SESSION['uid'] = $uid;
        header('Location: /');
      } else {
        header('Location: ../portal.php?log=pwnope?mail=' . $mail);
      }
    } else {
      header('Location: ../portal.php?log=nouser?mail=' . $mail);
    }
    // disconnect from db
    $db->db_disconnect();
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Demo                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

if (isset($_POST['submit_demo'])) {
  session_start();
  $_SESSION['uid'] = 1;
  header('Location: /');
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::               Registration              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

// init registration
if (isset($_POST['submit_registration'])) {
  // $_POST
  $mail = $_POST['mail'];
  $pwd = $_POST['pwd'];
  $pwd_repeat = $_POST['pwd_repeat'];
  // empty fields?
  if (empty($mail) || empty($pwd) || empty($pwd_repeat)) {
    header('Location: ../portal.php?reg=empty?mail=' . $mail);
    exit();
  }
  // passwords match?
  else if ($pwd !== $pwd_repeat) {
    header('Location: ../portal.php?reg=pwnomatch?mail=' . $mail);
    exit();
  }
  // email valid?
  else if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../portal.php?reg=invalid?mail=' . $mail);
    exit();
  } else {
    // connect to db
    $db = new db;
    $db->db_connect(0);
    // email already registered?
    if ($db->get_uid($mail)) {
      header('Location: ../portal.php?log=exists?mail=' . $mail);
    } else {
      // hash the password
      $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
      // generate token
      $token = bin2hex(random_bytes(100));
      // set up mail
      $subject = "CNTP - Registration";
      $msg= '
        <h1>Registration</h1>
        <p>Please click on the following link to verify your account:</p>
        <a href="https://'.config::$domain.'/portal.php?reg=verify?token='.$token.'?mail='.$mail.'">Verify Account</a>
        <hr><br>
        <p>If you did not register on '.config::$domain.' you can safely ignore this mail.</p>';
      // register user & send email
      if ($db->register_user($mail, $pwd_hash, $token) && sendmail($mail,$subject,$msg)) {
        header('Location: ../portal.php?reg=success'); 
      } else {
        header('Location: ../portal.php?reg=failed');
      }
    }
    // disconnect from db
    $db->db_disconnect();
  }
}

// verify account
if (isset($_POST['verify']) && isset($_POST['token']) && isset($_POST['mail'])) {
  require_once('check_header.php');
  $token_received = $_POST['token'];
  $mail_received = $_POST['mail'];
  // connect to db
  $db = new db;
  $db->db_connect(0);
  // account check
  $uid = $db->get_uid($mail_received);
  if (isset($uid)) {
    // get saved token
    $token = $db->get_token($uid);
    if ($token && $token === $token_received) {
      if ($db->post_verified(1, $uid)) {
        $db->post_token($uid, '');
        echo 'portal.php?log=verified?mail=' . $mail_received;
      } else {
        echo 'portal.php?reg=failed?mail=' . $mail_received;
      } 
    } else {
      echo 'portal.php?reg=failed?mail=' . $mail_received;
    }
  } else {
    echo 'portal.php?log=nouser?mail=' . $mail_received;
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              Reset Password             :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

// init pw reset
if (isset($_POST['submit_repw'])) {
  // $_POST
  $mail = $_POST['mail_pwd_reset'];
  // connect to db
  $db = new db;
  $db->db_connect(0);
  // email registered & account verified?
  $uid = $db->get_uid($mail);
  if (isset($uid) && $db->get_verified($uid)) {
    // generate token
    $token = bin2hex(random_bytes(100));
    // set up mail
    $subject = "CNTP - New Password Requested";
    $msg= '
      <h1>Password Reset</h1>
      <p>A password reset was requested for your account.</p>
      <p>You can safely ignore this email if this was a mistake.</p>
      <hr>
      <p>Please click on the following link to get a new password:</p>
      <a href="https://'.config::$domain.'/portal.php?log=pwreset?token='.$token.'?mail='.$mail.'">Reset Password</a>';
    if ($db->post_token($uid, $token)) {
      //send email
      if(sendmail($mail,$subject,$msg)) {
        header('Location: ../portal.php?log=pwinit?mail=' . $mail);
      } else {
        header('Location: ../portal.php?log=pwfail?mail=' . $mail);
      }
    } else {
        header('Location: ../portal.php?log=pwfail?mail=' . $mail);
    }
  } else {
    header('Location: ../portal.php?log=nouser?mail=' . $mail);
  }
  // disconnect from db
  $db->db_disconnect();
}

// do pw reset
if (isset($_POST['do_repw']) && isset($_POST['token']) && isset($_POST['mail'])) {
  require_once('check_header.php');
  $token_received = $_POST['token'];
  $mail_received = $_POST['mail'];
  // connect to db
  $db = new db;
  $db->db_connect(0);
  // get saved token
  $uid = $db->get_uid($mail_received);
  $token = $db->get_token($uid);
  if ($token && $token === $token_received) {
    // generate new password
    $pwd = bin2hex(random_bytes(15));
    // set up mail
    $subject = "CNTP - New Password";
    $msg= '
      <h1>New Password</h1>
      <p>Password: <b>'.$pwd.'</b></p>
      <a href="https://'.config::$domain.'/portal.php?mail='.$mail_received.'">Login</a>';
    // udpate pwd_hash send mail
    $pwd_hash = password_hash($pwd, PASSWORD_DEFAULT);
    if($db->post_pwd($uid, $pwd_hash) && sendmail($mail_received,$subject,$msg)) {
      $db->post_token($uid, '');
      echo 'portal.php?log=pwsend?mail=' . $mail_received;
    } else {
      echo 'portal.php?log=pwfail?mail=' . $mail_received;
    }
  } else {
    echo 'portal.php?log=pwfail?mail=' . $mail_received;
  }
}

?>