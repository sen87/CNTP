<?php
/*
CNTP - PHP Feed Reader
v0.4 sen
------------------------------------------------------
supports: atom | rss 1.0 | rss 2.0 | mrss
------------------------------------------------------
*/

require('check_header.php');

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::               Parameter                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

if (isset($_GET['set']) && isset($_GET['index'])) {
  session_start();
  require(dirname(__FILE__) .'/../../db.php');
  $feed_reader = new feed_reader;
  $feed_reader->config($_GET['set'], $_GET['index'], '');
}

if (isset($_GET['id'])) {
  session_start();
  require(dirname(__FILE__) .'/../../db.php');
  $feed_reader = new feed_reader;
  $feed_reader->config('', '', $_GET['id']);
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Config                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

class feed_reader {
  private static $feed_item_count = 30; // max items per feed
  private static $feed_cache_time = 20; // in minutes -> precache 15 min

  function config($set, $index, $id) {
    $db = new db;
    // connect to db
    $db->db_connect($_SESSION['uid']);
    // [0]id, [1]name, [2]url, [3]web_url, [4]thumbnails, [5]last_updated
    if ($id) {
      $feed = $db->get_feed_para_id($id);
    } else {
      $feed = $db->get_feed_para_index($set, $index);
    }
    // check age of cache
    if (time() - strtotime($feed[5]) < (self::$feed_cache_time * 60)) {
      // load from cache
      echo $db->get_feed_cache($feed[0]);
    } else if (!($feed[1] && $feed[2])) {
      die('<b>[FEED ERROR] No Feed selected!</b>');
    } else {
      // update feed
      $this->update_feed($feed[0], $feed[1], $feed[2], $feed[3], $feed[4], 1);
    }
    // disconnect from db
    $db->db_disconnect();
  }

  function update_cache($feed_id, $html, $timestamp) {
    $db = new db;
    // connect to db
    $db->db_connect(0);
    // update cache and timestamp
    $db->post_feed_cache($feed_id, $html, $timestamp);
    // disconnect from db
    $db->db_disconnect();
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::            Fetch & Validate             :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function http_request($url) {
    if (extension_loaded('curl')) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_FAILONERROR, true);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_TIMEOUT, 20);
      curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
      curl_setopt($curl, CURLOPT_USERAGENT, 'Feed Reader');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      if ($result = curl_exec($curl)) {
        return $result;
      } else {
        error_log('[CURL Error] Failed to load ' . $url . ': ' . curl_error($curl));
      }
    } else {
      error_log('[PHP Error] Extension CURL is not loaded.');
    }
  }

  function load_xml($feed_name, $feed_url) {
    if ($data = mb_convert_encoding(trim($this->http_request($feed_url)), 'UTF-8')) {
    } else {
      die('<b>[FEED ERROR: ' . $feed_name . '] Failed to load URL:</b><br>' . $feed_url);
    }
    if (isset($data) && $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR)) {
      return $xml;
    } else {
      die('<b>[FEED ERROR: ' . $feed_name . '] No valid XML found in:</b><br>' . $feed_url);
    }
  }

  function load_html($source, $feed_name) {
    $data = new DOMDocument();
    $data->strictErrorChecking = false;
    if ($data->loadHTML(mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR)) {
      return $data;
    } else {
      error_log('[FEED ERROR: ' . $feed_name . '] Cannot load HTML: ' . $source);
    }
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Feed Type                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function feed_type($xml, $feed_name) {
    if ($xml->channel) {
      $version = 'rss';
      $selector = $xml->item;
      if ($xml->channel->item) {
        $version = 'rss2';
        $selector = $xml->channel->item;
        if ($xml->channel->item->children('media', true)) {
          $version = 'mrss';
        }
      }
    } else if ($xml->entry) {
      $version = 'atom';
      $selector = $xml->entry;
    }
    if (isset($version)) {
      return array($version, $selector);
    } else {
      die('<b>[FEED ERROR: ' . $feed_name . '] Invalid Feed!</b><br>Supported Types: Atom | RSS 1.0 | RSS 2.0 | Media RSS');
    }
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::               Feed Items                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function feed_item_title($item) {
    $title = htmlSpecialChars($item->title);
    if (isset($title)) {
      return $title;
    } else {
      return '--- No Title ---';
    }
  } 

  function feed_item_link($item, $type) {
    if ($type === 'atom') {
      $link = htmlSpecialChars($item->link['href']);
    } else {
      $link = htmlSpecialChars($item->link);
    }
    if (isset($link)) {
      return $link;
    }
  } 

  function feed_item_date($item) {
    if (isset($item->children('dc', true)->date)) {
      $date = strtotime($item->children('dc', true)->date);
    } elseif (isset($item->pubDate)) {
      $date = strtotime($item->pubDate);
    } elseif (isset($item->updated)) {
      $date = strtotime($item->updated);
    }
    if (isset($date)) {
      return date('H:i | D, F j', (int) $date);
    }
  }

  function feed_item_author($item, $type) {
    if (isset($item->children('dc', true)->creator)) {
      $author = htmlSpecialChars($item->children('dc', true)->creator);
    } elseif (isset($item->author->name)) {
      $author = htmlSpecialChars($item->author->name);
    } elseif ($type === 'mrss') {
      if (isset($item->children('media', true)->credit)) {
        $author = htmlSpecialChars($item->children('media', true)->credit[0]);
      }
    }
    if (isset($author)) {
      return ' | ' . $author;
    }
  }

  function feed_item_thumbnail($item, $type, $feed_name) {
    if ($type === 'mrss') {
      if (isset($item->children('media', true)->thumbnail)) {
        if (count($item->children('media', true)->thumbnail) === 1 ) {
          $thumb_select = 0;
        } else {
          $thumb_select = 1;
        }
        $img_url = htmlSpecialChars($item->children('media', true)->thumbnail[$thumb_select]->attributes()['url']);
      }
    } elseif ($type === 'rss2' && isset($item->description)) {
      if ($html = $this->load_html($item->description, $feed_name)) {
        if ($html->getElementsByTagName('img')->length > 0) {
          $img_url  = htmlSpecialChars($html->getElementsByTagName('img')->item(0)->getAttribute('src'));
        } else if ($html->getElementsByTagName('video')->length > 0) {
          $img_url  = htmlSpecialChars($html->getElementsByTagName('video')->item(0)->getAttribute('poster'));
        }
      }
    } elseif ($type === 'atom' && (isset($item->content) || isset($item->description))) {
      if ($html = $this->load_html($item->content, $feed_name)) {
        if ($html->getElementsByTagName('img')->length > 0) {
          $img_url  = htmlSpecialChars($html->getElementsByTagName('img')->item(0)->getAttribute('src'));
        } else if ($html->getElementsByTagName('video')->length > 0) {
          $img_url  = htmlSpecialChars($html->getElementsByTagName('video')->item(0)->getAttribute('poster'));
        }
      } else if ($html = $this->load_html($item->description, $feed_name)) {
        if ($html->getElementsByTagName('img')->length > 0) {
          $img_url  = htmlSpecialChars($html->getElementsByTagName('img')->item(0)->getAttribute('src'));
        } else if ($html->getElementsByTagName('video')->length > 0) {
          $img_url  = htmlSpecialChars($html->getElementsByTagName('video')->item(0)->getAttribute('poster'));
        }
      }
    }
    if (isset($img_url)) {
      return '<div class="feed_image_shadow"><img class="feed_image" src="' . $img_url . '"/></div>';
    }
  } 


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              Generate HTML              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function update_feed($feed_id, $feed_name, $feed_url, $feed_web_url, $feed_thumbnails, $out) {
    if (($xml = $this->load_xml($feed_name, $feed_url)) && ($type = $this->feed_type($xml, $feed_name))) {
      $html = '<div class="head"><a href="' . $feed_web_url. '"><h3>' . $feed_name . '</h3></a></div><div class="feed_frame">';
      foreach ($type[1] as $item) {
        $html .= '<a class="feed_entry" target="_blank" rel="noopener noreferrer" href="' . $this->feed_item_link($item, $type[0]) . '">';
        $html .= $this->feed_item_title($item);
        $html .= '<p>' . $this->feed_item_date($item) . $this->feed_item_author($item, $type[0]);
        if ($feed_thumbnails) {
          $html .= $this->feed_item_thumbnail($item, $type[0], $feed_name);
        }
        $html .= '</p></a>';
        // limit feed items
        self::$feed_item_count--;
        if (self::$feed_item_count === 0) {
          break;
        }
      }
      $html .= '</div>';
    }
    if ($out) {
      echo $html;
    }
    // update cache
    $this->update_cache($feed_id, $html, date('Y-m-d H:i:s', time()));
  }

}
?>