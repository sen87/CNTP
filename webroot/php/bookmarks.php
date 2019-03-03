<?php
/*
CNTP - PHP Bookmark Loader
v0.1 sen
*/

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::               Parameter                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

if (isset($_GET['cache'])) {
  session_start();
  require('check_header.php');
  require(dirname(__FILE__) .'/../../db.php');
  $bookmarks = new bookmarks;
  $bookmarks->get_cache();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Config                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

class bookmarks {

  function update_cache() {
    $db = new db;
    // connect to db
    $db->db_connect($_SESSION['uid']);
    // get settings
    $favicon_switch = $db->get_favicon_switch();
    $bookmarks = $db->get_bookmarks();
    // create html
    $html = $this->html_out($bookmarks, $favicon_switch);
    // update cache
    $db->post_bookmark_cache($html);
    // disconnect from db
    $db->db_disconnect();
  }

   function get_cache() {
    $db = new db;
    // connect to db
    $db->db_connect($_SESSION['uid']);
    // update cache and timestamp
    echo $db->get_bookmark_cache();
    // disconnect from db
    $db->db_disconnect();
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Favicons                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function http_request($url) {
    if (extension_loaded('curl')) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_FAILONERROR, true);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_TIMEOUT, 5);
      curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
      curl_setopt($curl, CURLOPT_USERAGENT, 'Favicon');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      if ($result = curl_exec($curl)) {
        return $result;
      }
    } else {
      error_log('[PHP Error] Extension CURL is not loaded.');
    }
  }

  function get_favicon($id, $url) {
    // extract host from url
    if ($url_host = parse_url($url, PHP_URL_HOST)) {
      $dir = dirname(__DIR__) . '/favicon/cache/';
      $name = crc32($url_host) . '.ico';
      if (file_exists($dir . $name)) {
        // local favicon found
        $this->update_favicon($id, $name);
        return $name;
      } else {
        if ($data = $this->http_request($url_host . '/favicon.ico')) {
          // favicon found in webroot
        } else {
          // search for favicon in document
          $doc = new DOMDocument();
          $doc->strictErrorChecking = false;
          @$doc->loadHTML(file_get_contents($url), LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR);
          foreach ($doc->getElementsByTagName('link') as $link) {
            $rel = $link->getAttribute('rel');
            if ($rel === 'shortcut icon' || $rel === 'icon') {
              if ($data = $this->http_request($url_host . '/' . $link->getAttribute('href'))) {
              } else if ($data = $this->http_request($url_host . $link->getAttribute('href'))) {
              } else {$data = $this->http_request($link->getAttribute('href'));}
              break;
            }
          }
        }
        if ($data) {
          // save icon
          file_put_contents($dir . $name, $data);
          // save name to database
          $this->update_favicon($id, $name);
          return $name;
        } else {
          error_log('[BOOKMARKS] Failed to get Favicon for URL: ' . $url);
          // set favicon in database to placeholder
          $this->update_favicon($id, 'ph.png');
          return 'ph.png';
        }
      } 
    } else {
      error_log('[BOOKMARKS] Failed to get Host for URL: ' . $url);
    }
  }

  function update_favicon($id, $filename) {
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    $db->post_favicon($id, $filename);
    $db->db_disconnect();
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Output                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function html_out($bookmarks, $favicon_switch) {
    $html_out = '<div class="head"><h3>Bookmarks</h3></div><div id="b_frame">';
    $html_left = '<div id="b_left">';
    $html_right = '<div id="b_right">';
    $last_id = false;
    foreach ($bookmarks as list($cat_id, $cat_pos, $cat_name, $name, $url, $id, $favicon)) {
      $html = '';
      if ($cat_id !== $last_id) {
        // new category
        $html .= '<p class="space"></p><h2>' . htmlSpecialChars($cat_name) . '</h2>';
      }
      // link
      $html .= '<a rel="noopener" href="' . htmlSpecialChars($url) . '">';
      // favicon
      if ($favicon_switch) {
        if (!$favicon) {
          $favicon = $this->get_favicon($id, $url);
        }
        $html .= '<img src="favicon/cache/' . $favicon . '" alt=" " width="16" height="16"> ';
      }
      // name
      $html .= htmlSpecialChars($name) . '</a><br>';
      // position
      if ($cat_pos === 0) {
        $html_left .= $html;
      } else {
        $html_right .= $html;
      }
      $last_id = $cat_id;
    }
    // combine and output
    $html_out .= $html_left . '</div>' . $html_right . '</div></div>';
    return $html_out;
  }

}
?>