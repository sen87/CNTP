<?php
/*
CNTP - DB Configuration Handler for MariaDB using PHP-MySQLi
v0.2 sen
*/

require_once('config.php');

class db {

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              DB Connection              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  private $db_connection = 0;
  private $user_id = 0;

  function db_connect($user_id) {
    $this->user_id = $user_id;
    require_once('db_credentials.php');
    $db_connection = new mysqli(db_cred::$host, db_cred::$user, db_cred::$pass, db_cred::$db);
    if ($db_connection->connect_error) {
        die('[MYSQLI ERROR] Could not connect to DB: ' . $db_connection->connect_error);
    }
    $this->db_connection = $db_connection;
  }

  function db_disconnect() {
    $this->db_connection->close();
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              DB Operation               :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function db_op($sql, $param, $op, $obj) {
    if ($op === 'write' && $this->user_id === 1) {
      // write protection --> demo user
      return false;
    } else {
      // prepare statement
      $stmt = $this->db_connection->stmt_init();
      if(!$stmt->prepare($sql))
      {
          error_log("[MYSQLI ERROR] Failed to prepare statement: ".$sql."\n--> ".mysqli_error($this->db_connection));
          return false;
      }
      // prepare parameters --> "..." splat an array as multiple parameters
      if ($param) {
        $stmt->bind_param(...$param);
      }
      // execute statement
      if(!$stmt->execute()) {
        error_log("[MYSQLI ERROR] Failed to execute statement: ".$sql."\n--> ".mysqli_error($this->db_connection));
        return false;
      } else {
        if ($op === 'write') {
          return true;
        } else {
          $result = $stmt->get_result();
          if ($op === 'read') {
            if ($obj) {
              // return an object
              while($row = mysqli_fetch_object($result)) {
                return $row->$obj;
              }
            } else {
              // return an array (1 dimensional)
              return mysqli_fetch_array($result, MYSQLI_NUM);
            }
          } else if ($op === 'read_multi') {
            // return an array (2 dimensional)
            return mysqli_fetch_all($result, MYSQLI_NUM);
          }
          mysqli_free_result($result);
        }
      }
    }
  }


####################### SQL #######################

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Theme                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_theme() {
    $sql = 'SELECT theme.min, theme_select.ext_css FROM theme_select
            INNER JOIN theme ON theme_select.theme_id = theme.id
            WHERE theme_select.user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function get_themes() {
    $sql = 'SELECT id, name, author, file FROM theme ORDER BY name';
    return $this->db_op($sql, '', 'read_multi', 'file');
  }

  function get_theme_conf() {
    $sql = 'SELECT theme_id, ext_css FROM theme_select WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function post_theme($id, $ext_css) {
    $sql = 'UPDATE theme_select SET theme_id=?, ext_css=? WHERE user_id=?';
    $param = array('sss', $id, $ext_css, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::               Page Layout               :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_layout() {
    $sql = 'SELECT title, `table`, feed_count, b, n, w FROM layout WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function post_layout($feed_count, $table, $b, $n, $w) {
    $sql = 'UPDATE layout SET feed_count=?, `table`=?, b=?, n=?, w=? WHERE user_id=?';
    $param = array('ssssss', $feed_count, $table, $b, $n, $w, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_layout2($title, $favicon_switch) {
    $sql = 'UPDATE layout SET title=?, favicon_switch=? WHERE user_id=?';
    $param = array('sss', $title, $favicon_switch, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Notes                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_notes() {
    $sql = 'SELECT text FROM notes WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', 'text');
  }

  function post_notes($text) {
    $sql = 'UPDATE notes SET text=? WHERE user_id=?';
    $param = array('ss', $text, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Feeds                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_feed_count($uid) {
    $sql = 'SELECT feed_count FROM layout WHERE user_id=?';
    $param = array('s', $uid);
    return $this->db_op($sql, $param, 'read','feed_count');
  }

  function get_feed_id($uid, $set, $index) {
    $sql = 'SELECT feed_id FROM feed_select WHERE user_id=? AND `set`=? AND`index`=?';
    $param = array('sss', $uid, $set, $index);
    return $this->db_op($sql, $param, 'read', 'feed_id');
  }

  function get_feed_para($id) {
    $sql = 'SELECT name, description, url, website_url, img FROM feed WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function get_feed_para_id($id) {
    $sql = 'SELECT id, name, url, website_url, img, last_updated FROM feed WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function get_feed_para_index($set, $index) {
    $sql = 'SELECT id, name, url, website_url, img, last_updated FROM feed
            INNER JOIN feed_select ON feed_select.feed_id = feed.id
            WHERE feed_select.user_id=? AND feed_select.`set`=? AND feed_select.`index`=?';
    $param = array('sss', $this->user_id, $set, $index);
    return $this->db_op($sql, $param, 'read', '');
  }

  function get_feeds() {
    $sql = 'SELECT id, name FROM feed
            INNER JOIN feed_select ON feed.id = feed_select.feed_id
            WHERE feed_select.user_id=? ORDER BY name';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read_multi', '');
  }

  function get_all_feeds() {
    $sql = 'SELECT id, name, description, img FROM feed ORDER BY name';
    return $this->db_op($sql, '', 'read_multi', '');
  }

  function get_feed_cache($id) {
    $sql = 'SELECT cache FROM feed WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read','cache');
  }

  function get_feed_status($id) {
    $sql = 'SELECT user_id FROM feed_select WHERE feed_id =?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read_multi', '');
  }

  function post_feed_update($id, $name, $description, $url, $website_url, $img) {
    $sql = 'UPDATE feed SET name=?, description=?, url=?, website_url=?, img=? WHERE id=?';
    $param = array('ssssss', $name, $description, $url, $website_url, $img, $id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_feed($name, $description, $url, $website_url, $img) {
    $sql = 'INSERT INTO feed (name, description, url, website_url, img) VALUES (?, ?, ?, ?, ?)';
    $param = array('sssss', $name, $description, $url, $website_url, $img);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_feed_select($set, $index, $feed_id) {
    $sql = 'INSERT INTO feed_select (user_id, `set`, `index`, feed_id) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE feed_id=?';
    $param = array('sssss', $this->user_id, $set, $index, $feed_id, $feed_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_feed_count($feed_count) {
    $sql = 'DELETE FROM feed_select WHERE user_id=? AND `index`>=?';
    $param = array('ss', $this->user_id, $feed_count);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_feed_cache($id, $cache, $timestamp) {
    $sql = 'UPDATE feed SET cache=?, last_updated=? WHERE id=?';
    $param = array('sss', $cache, $timestamp, $id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_feed_delete($id) {
    $sql = 'DELETE FROM feed WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'write', '');
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Bookmarks                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_cats() {
    $sql = 'SELECT id, name FROM bookmark_category WHERE user_id=?
            ORDER BY name';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read_multi', '');
  }

  function get_cat($id) {
    $sql = 'SELECT bookmarks.id, bookmarks.name, bookmarks.url FROM bookmarks
            INNER JOIN bookmark_category ON bookmarks.cat_id = bookmark_category.id
            WHERE bookmarks.cat_id=? AND bookmark_category.user_id=?
            ORDER BY bookmarks.name';
    $param = array('ss', $id, $this->user_id);
    return $this->db_op($sql, $param, 'read_multi', '');
  }

  function get_cat_pos($id) {
    $sql = 'SELECT `position` FROM bookmark_category WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read', 'position');
  }

  function get_cat_own($id) {
    $sql = 'SELECT user_id FROM bookmark_category WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read', 'user_id');
  }

  function get_bookmarks() {
    $sql = 'SELECT bookmark_category.id, bookmark_category.`position`, bookmark_category.name, bookmarks.name, bookmarks.url, bookmarks.id, bookmarks.favicon
            FROM bookmark_category
            INNER JOIN bookmarks ON bookmark_category.id = bookmarks.cat_id
            WHERE bookmark_category.user_id=?
            ORDER BY bookmark_category.name, bookmark_category.id, bookmarks.name';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read_multi', '');
  }

  function get_favicon_switch() {
    $sql = 'SELECT favicon_switch FROM layout WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', 'favicon_switch');
  }

  function get_bookmark_cache() {
    $sql = 'SELECT bookmark_cache FROM layout WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', 'bookmark_cache');
  }

  function post_favicon($id, $favicon) {
    $sql = 'UPDATE bookmarks SET favicon=? WHERE id=?';
    $param = array('ss', $favicon, $id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_cat_pos($id, $position) {
    $sql = 'UPDATE bookmark_category SET position=? WHERE id=? AND user_id=?';
    $param = array('sss', $position, $id, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_b_new($cat_id, $name, $url) {
    $sql = 'INSERT INTO bookmarks (cat_id, name, url) VALUES (?, ?, ?)';
    $param = array('sss', $cat_id, $name, $url);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_b_del($id, $cat_id) {
    $sql = 'DELETE FROM bookmarks WHERE id=? AND cat_id=?
            AND EXISTS(SELECT * FROM bookmark_category WHERE bookmark_category.id=? AND bookmark_category.user_id=?)';
    $param = array('ssss', $id, $cat_id, $cat_id, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_b_update($id, $name, $url) {
    $sql = 'UPDATE bookmarks, bookmark_category SET bookmarks.name=?, bookmarks.url=?
            WHERE bookmarks.cat_id = bookmark_category.id
            AND bookmarks.id=? AND bookmark_category.user_id=?';
    $param = array('ssss', $name, $url, $id, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_cat_new($name) {
    $sql = 'INSERT INTO bookmark_category (user_id, `position`, name) VALUES (?, 0, ?)';
    $param = array('ss', $this->user_id, $name);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_cat_del($id) {
    $sql = 'DELETE FROM bookmark_category WHERE id=? AND user_id=?';
    $param = array('ss', $id, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_bookmark_cache($cache) {
    $sql = 'UPDATE layout SET bookmark_cache=? WHERE user_id=?';
    $param = array('ss', $cache, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Weather                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_weather_data() {
    $sql = 'SELECT place, place_id, last_updated, forecast, forecast_h, weather_icons.name, weather_icons.img_format
            FROM weather
            INNER JOIN weather_icons ON weather.icons = weather_icons.id
            WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function get_weather_icons() {
    $sql = 'SELECT id, name FROM weather_icons';
    return $this->db_op($sql, '', 'read_multi', '');
  }

  function get_weather_icon($id) {
    $sql = 'SELECT name, img_format, url FROM weather_icons WHERE id=?';
    $param = array('s', $id);
    return $this->db_op($sql, $param, 'read', '');
  }

  function get_weather_cache() {
    $sql = 'SELECT cache FROM weather WHERE user_id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', 'cache');
  }

  function post_weather_cache($cache, $timestamp) {
    $sql = 'UPDATE weather SET cache=?, last_updated=? WHERE user_id=?';
    $param = array('sss', $cache, $timestamp, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_weather_data($place_id, $place, $forecast, $forecast_h, $icons) {
    $sql = 'UPDATE weather SET place_id=?, place=?, forecast=?, forecast_h=?, icons=? WHERE user_id=?';
    $param = array('ssssss', $place_id, $place, $forecast, $forecast_h, $icons, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::             User Management             :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function get_uid($mail) {
    $sql = 'SELECT id FROM `user` WHERE mail=?';
    $param = array('s', $mail);
    return $this->db_op($sql, $param, 'read', 'id');
  }

  function get_pwd($uid) {
    $sql = 'SELECT pwd_hash FROM `user` WHERE id=?';
    $param = array('s', $uid);
    return $this->db_op($sql, $param, 'read', 'pwd_hash');
  }

  function get_mail() {
    $sql = 'SELECT mail FROM `user` WHERE id=?';
    $param = array('s', $this->user_id);
    return $this->db_op($sql, $param, 'read', 'mail');
  }

  function get_verified($uid) {
    $sql = 'SELECT verified FROM `user` WHERE id=?';
    $param = array('s', $uid);
    return $this->db_op($sql, $param, 'read', 'verified');
  }

  function get_token($uid) {
    $sql = 'SELECT token FROM `user` WHERE id=?';
    $param = array('s', $uid);
    return $this->db_op($sql, $param, 'read', 'token');
  }

  function get_active_users() {
    $sql = 'SELECT id FROM `user` WHERE last_visit > CURDATE()-2';
    return $this->db_op($sql, '', 'read_multi', '');
  }

  function post_last_visit($version, $timestamp) {
    $sql = 'UPDATE `user` SET version=?, last_visit=? WHERE id=?';
    $param = array('sss', $version, $timestamp, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_pwd($uid, $pwd_hash) {
    // write protection --> demo user
    if ($uid === 1) {
      return true;
    }
    $sql = 'UPDATE `user` SET pwd_hash=? WHERE id=?';
    $param = array('ss', $pwd_hash, $uid);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_verified($status, $uid) {
    // write protection --> demo user
    if ($uid === 1) {
      return true;
    }
    $sql = 'UPDATE `user` SET verified=? WHERE id=?';
    $param = array('ss', $status, $uid);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_token($uid, $token) {
    // write protection --> demo user
    if ($uid === 1) {
      return true;
    }
    $sql = 'UPDATE `user` SET token=? WHERE id=?';
    $param = array('ss', $token, $uid);
    return $this->db_op($sql, $param, 'write', '');
  }

  function post_mail($mail) {
    $sql = 'UPDATE `user` SET mail=? WHERE id=?';
    $param = array('ss', $mail, $this->user_id);
    return $this->db_op($sql, $param, 'write', '');
  }

  function delete_user($uid) {
    // write protection --> demo user
    if ($uid === 1 || $uid === 2) {
      return true;
    }
    $sql = 'DELETE FROM `user` WHERE id=?';
    $param = array('s', $uid);
    return $this->db_op($sql, $param, 'write', '');
  }

  function register_user($mail, $pwd_hash, $token) {
    $sql = 'INSERT INTO `user` (mail, pwd_hash, token, verified) VALUES (?, ?, ?, ?)';
    $param = array('ssss', $mail, $pwd_hash, $token, 0);
    if ($this->db_op($sql, $param, 'write', '')) {
      $uid = $this->get_uid($mail);
      if (!$this->restore_defaults($uid)) {
        $this->delete_user($uid);
        return false;
      }
      else {
        return true;
      }
    }
  }

  function restore_defaults($uid) {
    // clone from demo-user (uid=1) over to $uid
    $sql = 'CREATE TEMPORARY TABLE tmp SELECT * FROM bookmark_category WHERE user_id=1;
            ALTER TABLE tmp drop id;
            UPDATE tmp SET user_id=' . $uid . ' WHERE user_id=1;
            INSERT INTO bookmark_category (user_id, `position`, name) SELECT user_id, `position`, name FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT bookmarks.* FROM bookmarks INNER JOIN bookmark_category ON bookmarks.cat_id = bookmark_category.id
            WHERE bookmark_category.user_id=1 AND bookmark_category.name="Search";
            ALTER TABLE tmp drop id;
            UPDATE tmp SET cat_id= (SELECT id FROM bookmark_category WHERE bookmark_category.user_id=' . $uid . ' AND bookmark_category.name="Search");
            INSERT INTO bookmarks (cat_id, name, url, favicon) SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT bookmarks.* FROM bookmarks INNER JOIN bookmark_category ON bookmarks.cat_id = bookmark_category.id
            WHERE bookmark_category.user_id=1 AND bookmark_category.name="Music";
            ALTER TABLE tmp drop id;
            UPDATE tmp SET cat_id= (SELECT id FROM bookmark_category WHERE bookmark_category.user_id=' . $uid . ' AND bookmark_category.name="Music");
            INSERT INTO bookmarks (cat_id, name, url, favicon) SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT bookmarks.* FROM bookmarks INNER JOIN bookmark_category ON bookmarks.cat_id = bookmark_category.id
            WHERE bookmark_category.user_id=1 AND bookmark_category.name="Video";
            ALTER TABLE tmp drop id;
            UPDATE tmp SET cat_id= (SELECT id FROM bookmark_category WHERE bookmark_category.user_id=' . $uid . ' AND bookmark_category.name="Video");
            INSERT INTO bookmarks (cat_id, name, url, favicon) SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT * FROM feed_select WHERE user_id=1;
            UPDATE tmp SET user_id=' . $uid . ' WHERE user_id=1;
            INSERT INTO feed_select SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT * FROM layout WHERE user_id=1;
            UPDATE tmp SET user_id=' . $uid . ' WHERE user_id=1;
            INSERT INTO layout SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT * FROM notes WHERE user_id=1;
            UPDATE tmp SET user_id=' . $uid . ' WHERE user_id=1;
            INSERT INTO notes SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT * FROM theme_select WHERE user_id=1;
            UPDATE tmp SET user_id=' . $uid . ' WHERE user_id=1;
            INSERT INTO theme_select SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;
            CREATE TEMPORARY TABLE tmp SELECT * FROM weather WHERE user_id=1;
            UPDATE tmp SET user_id=' . $uid . ' WHERE user_id=1;
            INSERT INTO weather SELECT * FROM tmp;
            DROP TEMPORARY TABLE tmp;';
    if (!$this->db_connection->multi_query($sql)) {
      error_log("[MYSQLI ERROR] Failed to create user: " . mysqli_error($this->db_connection));
      return false;
    } else {
      return true;
    }
  }

}
?> 
