<?php
/*
CNTP - Settings
v0.1 sen
*/

require(dirname(__FILE__) .'/../../db.php');
require('bookmarks.php');

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Exit                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

if (isset($_POST['submit_exit'])) {
  header('Location: /');
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Save                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['submit_save'])) {
  session_start();
  // write protection for demo user
  if ($_SESSION['uid'] !== 1) {
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // feeds
    $feed_count = $db->get_feed_count($_SESSION['uid']);
    for ($s=1; $s <= 2; $s++) {
      for ($i=0; $i < $feed_count; $i++) {
        $db->post_feed_select($s, $i, $_POST['fs_' . $s . '_' . $i]);
      }
    }
    // bookmarks
    $favicon_switch = 1;
    if (!isset($_POST['favicon_switch'])) {
      $favicon_switch = 0;
    }
    // weather
    $place_id = $_POST['place_id'];
    $place = $_POST['place_name'];
    $forecast = 1;
    if (!isset($_POST['forecast'])) {
      $forecast = 0;
    }
    $forecast_h = $_POST['forecast_height'];
    $icons = $_POST['weather_icon'];
    // layout
    $title = $_POST['title'];
    // theme
    $theme = $_POST['theme'];
    $ext_css = $_POST['ext_css'];
    // save config
    if ($db->post_layout2($title, $favicon_switch) &&
        $db->post_weather_data($place_id, $place, $forecast, $forecast_h, $icons) &&
        $db->post_theme($theme, $ext_css)) {
      // update bookmark cache
      $bookmarks = new bookmarks;
      $bookmarks->update_cache();
      header('Location: /');
    } else {
      header('Location: /settings.php?failed');
    }
    // disconnect from db
    $db->db_disconnect();
  } else {
    header('Location: /settings.php?demo');
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Feeds                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['submit_feed'])) {
  session_start();
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // create feed
  $name = $_POST['name'];
  $description = $_POST['description'];
  $url = $_POST['url'];
  $website_url = $_POST['website'];
  $img = 1;
  if (empty($name) || empty($description) || empty($url) || empty($website_url) || empty($img)) {
    header('Location: /settings.php?tab=feeds?empty');
  } else {
    if (!isset($_POST['thumbnails'])) {
      $img = 0;
    }
    if ($db->post_feed($name, $description, $url, $website_url, $img)) {
      header('Location: /settings.php?tab=feeds?feed_created');
    } else {
      header('Location: /settings.php?tab=feeds?failed');
    }
    // disconnect from db
    $db->db_disconnect();
  }
}

else if (isset($_GET['feed_edit'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // get feed parameter
  $feed_para = $db->get_feed_para($_GET['feed_edit']);
  echo json_encode($feed_para);
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['submit_feed_edit']) || isset($_POST['submit_feed_delete'])) {
  session_start();
  if ($_POST['feed_id'] !== 'none') {
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // create feed
    $feed_id = $_POST['feed_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $website_url = $_POST['website'];
    $img = 1;
    if (empty($name) || empty($description) || empty($url) || empty($website_url) || empty($img)) {
      header('Location: /settings.php?tab=feeds?empty');
    } else {
      if (!isset($_POST['thumbnails'])) {
        $img = 0;
      }
      $uid_arr = $db->get_feed_status($feed_id);
      if (sizeof($uid_arr) === 0 || (sizeof($uid_arr) === 1 && $uid_arr[0][0] === $_SESSION['uid'])) {
        if (isset($_POST['submit_feed_edit'])
        && $db->post_feed_update($feed_id, $name, $description, $url, $website_url, $img)) {
          header('Location: /settings.php?tab=feeds?feed_updated');
        } else if (isset($_POST['submit_feed_delete']) && $db->post_feed_delete($feed_id)) {
          header('Location: /settings.php?tab=feeds?feed_removed');
        } else {
          header('Location: /settings.php?tab=feeds?failed');
        }
      }
      else {
        header('Location: /settings.php?tab=feeds?feed_locked');
      }
    }
    // disconnect from db
    $db->db_disconnect();
  } else {
    header('Location: /settings.php?tab=feeds');
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Bookmarks                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['b_cat_add'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // create bookmark category
  $b_list = $db->post_cat_new($_POST['b_cat_add']);
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_GET['b_cat_load'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // get bookmarks in category
  $cat_pos = $db->get_cat_pos($_GET['b_cat_load']);
  $b_list = $db->get_cat($_GET['b_cat_load']);
  echo json_encode(array($cat_pos, $b_list));
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['b_cat_del'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // delete bookmark category
  $db->post_cat_del($_POST['b_cat_del']);
  // trigger cache update
  $bookmarks = new bookmarks;
  $bookmarks->update_cache();
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['cat_id']) && isset($_POST['cat_pos'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // set position for bookmark category
  $db->post_cat_pos($_POST['cat_id'], $_POST['cat_pos']);
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['cat_id']) && isset($_POST['b_id'])
&& isset($_POST['b_name']) && isset($_POST['b_url'])) {
  session_start();
  require('check_header.php');
  $cat_id = $_POST['cat_id'];
  $id = $_POST['b_id'];
  $name = $_POST['b_name'];
  $url = $_POST['b_url'];
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // create/update bookmark
  if ($id === 'new') {
    if ($db->get_cat_own($cat_id) === $_SESSION['uid']) {
      $db->post_b_new($cat_id, $name, $url);
    }
  } else {
    $db->post_b_update($id, $name, $url);
  }
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['b_del']) && isset($_POST['cat_id'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // remove bookmark
  $db->post_b_del($_POST['b_del'], $_POST['cat_id']);
  // trigger cache update
  $bookmarks = new bookmarks;
  $bookmarks->update_cache();
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['b_cache'])) {
  session_start();
  require('check_header.php');
  // trigger cache update
  $bookmarks = new bookmarks;
  $bookmarks->update_cache();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Weather                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_GET['w_icon'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // get bookmarks in category
  $icon = $db->get_weather_icon($_GET['w_icon']);
  echo json_encode($icon);
  // disconnect from db
  $db->db_disconnect();
}

else if (isset($_POST['w_update'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // remove bookmark
  $db->post_weather_cache('', '0000-00-00 00:00:00');
  // disconnect from db
  $db->db_disconnect();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Layout                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['feed_count']) && isset($_POST['table'])) {
  session_start();
  require('check_header.php');
  // connect to db
  $db = new db;
  $db->db_connect($_SESSION['uid']);
  // save layout
  $db->post_layout($_POST['feed_count'], $_POST['table'], $_POST['b'], $_POST['n'], $_POST['w']);
  $db->post_feed_count($_POST['feed_count']);
  // disconnect from db
  $db->db_disconnect();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::             Change Password             :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['submit_chpw'])) {
  session_start();
  // $_POST
  $pwd = $_POST['pwd'];
  $pwd_new = $_POST['pwd_new'];
  $pwd_new_repeat = $_POST['pwd_new_repeat'];
  // empty fields?
  if (empty($pwd) || empty($pwd_new) || empty($pwd_new_repeat)) {
    header('Location: /settings.php?tab=user?empty');
  // passwords match?
  } else if ($pwd_new !== $pwd_new_repeat) {
    header('Location: /settings.php?tab=user?match');
  } else {
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // correct password?
    $pwd_hash = $db->get_pwd($_SESSION['uid']);
    if (password_verify($pwd, $pwd_hash)) {
      // change password
      $pwd_hash = password_hash($pwd_new, PASSWORD_DEFAULT);
      if ($db->post_pwd($_SESSION['uid'], $pwd_hash)) {
        session_unset();
        session_destroy();
        header('Location: /portal.php?log=pwchanged');
      } else {
        header('Location: /settings.php?tab=user?failed');
      }
    } else {
      header('Location: /settings.php?tab=user?pwd');
    }
    // disconnect from db
    $db->db_disconnect();
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              Change Email               :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['submit_chmail'])) {
  session_start();
  // $_POST
  $mail = $_POST['mail'];
  $mail_new = $_POST['mail_new'];
  $pwd = $_POST['pwd'];
  // empty fields?
  if (empty($mail) || empty($mail_new) || empty($pwd)) {
    header('Location: /settings.php?tab=user?empty');
  // new mail address valid?
  } else if (!filter_var($mail_new, FILTER_VALIDATE_EMAIL)) {
    header('Location: /settings.php?tab=user?invalid');
  } else {
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // correct password and email?
    $pwd_hash = $db->get_pwd($_SESSION['uid']);
    if (password_verify($pwd, $pwd_hash) &&
        $db->get_mail() === $mail) {
      // send emails to old and new address
      require('mail.php');
      // msg to old account
      $subject_old = 'CNTP - Email address change';
      $msg_old = '
        <h1>Email address change</h1>
        <p>An email address change was initiated for your account.</p>
        <p>If this was not done by you, somebody has access to your account!</p>
        <hr>
        <p>A verification Link will be send to: ' . $mail_new . '</p>';
      sendmail($mail, $subject_old, $msg_old);
      // generate token
      $token = bin2hex(random_bytes(100));
      // set up mail
      $subject_new = 'CNTP - Email address change';
      $msg_new= '
        <h1>Email address change</h1>
        <p>Please click on the following link to verify your new email address:</p>
        <a href="https://'.config::$domain.'/portal.php?log=chmail?token='.$token.'?mail='.$mail_new.'">Verify email address</a>';
      if ($db->post_token($_SESSION['uid'], $token) &&
          $db->post_mail($mail_new) &&
          $db->post_verified(0, $_SESSION['uid'])) {
        if (sendmail($mail_new, $subject_new, $msg_new)) {
          session_unset();
          session_destroy();
          header('Location: /portal.php?log=mailchanged');
        }
      } else {
        header('Location: /settings.php?tab=user?failed');
      }
    } else {
      header('Location: /settings.php?tab=user?pwdmail');
    }
    // disconnect from db
    $db->db_disconnect();
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::             Delete Account              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else if (isset($_POST['submit_delete_user'])) {
  session_start();
  // $_POST
  $pwd = $_POST['pwd'];
  // empty fields?
  if (empty($pwd)) {
    header('Location: /settings.php?tab=user?empty');
  } else {
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // correct password?
    $pwd_hash = $db->get_pwd($_SESSION['uid']);
    if (password_verify($pwd, $pwd_hash)) {
      // delete user
      if ($db->delete_user($_SESSION['uid'])) {
        session_unset();
        session_destroy();
        header('Location: /portal.php');
      } else {
        header('Location: /settings.php?tab=user?failed');
      }
    } else {
      header('Location: /settings.php?tab=user?pwd');
    }
    // disconnect from db
    $db->db_disconnect();
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Init                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

else {
  class init {
    private $feeds;
    private $feed_index;
    private $feed_count;
    private $favicon_switch;
    private $weather_data;
    private $weather_icons;
    private $b_cats;
    private $layout;
    private $themes;
    private $theme_conf;

    function get_config() {
      // connect to db
      $db = new db;
      $db->db_connect($_SESSION['uid']);
      // feeds
      $this->feeds = $db->get_all_feeds();
      $this->feed_count = $db->get_feed_count($_SESSION['uid']);
      for ($s=1; $s <= 2; $s++) {
        for ($i=0; $i < $this->feed_count; $i++) {
          $this->feed_index[$s][] = $db->get_feed_id($_SESSION['uid'], $s, $i);
        }
      }
      // bookmarks
      $this->favicon_switch = $db->get_favicon_switch();
      $this->b_cats = $db->get_cats();
      // weather
      $this->weather_data = $db->get_weather_data();
      $this->weather_icons = $db->get_weather_icons();
      // layout
      $this->layout = $db->get_layout();
      // Theme
      $this->themes = $db->get_themes();
      $this->theme_conf = $db->get_theme_conf();
      // disconnect from db
      $db->db_disconnect();
    }

    function feed_select() {
      $html = ''; 
      // feed select dropdown
      for ($s=1; $s <= 2; $s++) {
        $html .= '<hr class="hr_480"><p><b>Feed Set ' . $s . '</b></p>';
        for ($i = 0; $i < $this->feed_count; $i++) {
          $html .= ($i + 1) . ': <select class="feed_select" name="fs_' . $s . '_' . $i . '" form="main">';
          $html .= '<option value="0">--- select a feed ---</option>';
          foreach ($this->feeds as $feed) {
            $thumb = ' 🖾 ';
            if ($feed[3]) {
              $thumb = ' 🖼 ';
            }
            $html .= '<option value="' . $feed[0] . '"';
            if ($feed[0] === $this->feed_index[$s][$i]) {
              $html .= ' selected="selected"';
            }
            $html .= '>' . $feed[1] . $thumb . $feed[2] . '</option>';
          }
          $html .= '</select><br>';
        }
      }
      echo $html;
    }

    function feed_edit() {
      $html = '<select id="feed_edit_select" class="feed_select" name="feed_id" form="feed">';
      $html .= '<option value="none">--- select a feed for editing ---</option>';
      foreach ($this->feeds as $feed) {
        $thumb = ' 🖾 ';
        if ($feed[3]) {
          $thumb = ' 🖼 ';
        }
        $html .= '<option value="' . $feed[0] . '">' . $feed[1] . $thumb . $feed[2] . '</option>';
      }
      $html .= '</select><br>';
      echo $html;
    }

    function bookmarks() {
      $html = '<select id="b_cats"><option value="none">--- select a category for editing ---</option>';
      foreach ($this->b_cats as $cat) {
        $html .= '<option value="' . $cat[0] . '">' . $cat[1] . '</option>';
      }
      $html .= '</select><br><div id="b_box"></div><hr>';
      $html .= '<h1>Favicons</h1><p>Processing new favicons may take a while when bookmarks are added.</p>';
      $html .= '<input type="checkbox" name="favicon_switch" form="main"';
      if ($this->favicon_switch) {
        $html .= ' checked';
      }
      $html .= '>enabled</input><br>';
      echo $html;
    }

    function weather() {
      $html = '<h1>Update</h1><p>Weather data is updated <u>every 60 minutes</u>. Last update: <i>' . date('i', time() - strtotime($this->weather_data[2]));
      $html .= ' minutes ago</i></p><p>If you make changes on this page please trigger an update to renew the cache.</p>';
      $html .= '<button id="weather_update"><img src="css/_main/update.svg" alt="update" height="22" width="22">Trigger Update</button><hr><h1>Location</h1>';
      $html .= '<p>Please visit <a href="https://www.yr.no">yr.no</a> and search for your location. Then copy the URL from the address bar into the field below.</p>';
      $html .= '<img src="css/_main/place.svg" alt="yr.no" height="22" width="22"><input id="w_place_id" type="text" ';
      $html .= 'name="place_id" form="main" placeholder="yr.no place URL..." value="' . $this->weather_data[1] . '">';
      $html .= '<hr class="hr_480"><img src="css/_main/title.svg" alt="name" height="22" width="22"><input id="w_place_name" ';
      $html .= 'type="text" name="place_name" form="main" placeholder="(optional) Location Alias..." value="' . $this->weather_data[0] . '">';
      $html .= '<hr><h1>Icons</h1><select id="weather_icon" name="weather_icon" form="main">';
      foreach ($this->weather_icons as $icon) {
        $html .= '<option value="' . $icon[0] . '"';
        if ($icon[1] === $this->weather_data[5]) {
          $html .= ' selected="selected"';
        }
        $html .= '>' . $icon[1] . '</option>';
      }
      $html .= '</select><br><div id="weather_icon_prev"></div><hr><h1>Forecast Graph</h1><input type="checkbox" name="forecast" form="main"';
      if ($this->weather_data[3]) {
        $html .= ' checked';
      }
      $html .= '>enabled</input><br><img src="css/_main/ratio.svg" alt="yr.no" height="22" width="22"><input id="w_forecast_h" type="range" ';
      $html .= 'name="forecast_height" form="main" min="80" max="800" step="10" value="' . $this->weather_data[4] . '"> Height<br><br><br><br>';
      echo $html;
    }

    function layout_title() {
      echo '<input type="text" name="title" form="main" placeholder="Title..." value="' . $this->layout[0] . '">';
    }

    function layout_table() {
      echo '<table id="layout_preview">'.$this->layout[1].'</table>';
    }

    function theme() {
      $html = '<select id="theme_select" name="theme" form="main">';
      foreach ($this->themes as $theme) {
        $html .= '<option value="' . $theme[0] . '"';
        if ($theme[0] === $this->theme_conf[0]) {
          $html .= ' selected="selected"';
        }
        $html .= '>' . $theme[1] . ' [' . $theme[2] . ']</option>';
      }
      $html .= '</select><br><br><div id="theme_prev"></div><hr><h1>External CSS</h1><p>You can create your own themes with pure CSS!</p>';
      $html .= '<img src="css/_main/info.svg" alt="info" height="64" width="64"><p>Any URL entered in the field below will override the CSS Settings!</p>';
      $html .= '<p>To enable the built-in CSS files again, make sure the field is <u>empty</u>.</p><img src="css/_main/url.svg" alt="url" height="22" width="22">';
      $html .= '<input id="ext_css" name="ext_css" form="main" type="text" placeholder="direct URL to an external CSS file..." value="' . $this->theme_conf[1] . '">';
      $html .= '<p>If you have created a nice theme <a href="mailto:sen@archlinux.us">send it to me</a> and it will be included.</p>';
      $html .= '<hr class="hr_480"><p>You can modify one of the existing themes by using these imports:</p><div id="css_imp_box">';
      foreach ($this->themes as $theme) {
        $html .= '<p class="css_imp">@import url("https://' . config::$domain . '/css/' . $theme[3] . '")</p>';
      }
      echo $html . '</div><br><br>';
    }

  }
  $init = new init;
  $init->get_config();
}

?>