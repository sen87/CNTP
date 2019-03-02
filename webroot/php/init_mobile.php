<?php
/*
CNTP - PHP Mobile Page init
v0.1 sen
*/

require(dirname(__FILE__) .'/../../db.php');

$init = new init;
$init->get_config();

class init {
  private $title;
  private $theme;
  private $feed_count;
  private $feeds;
  private $b;
  private $nt;
  private $w;

  function get_config() {
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // layout
    $layout = $db->get_layout();
    $this->title = $layout[0];
    // theme
    $css_select = $db->get_theme();
    $this->theme = 'css/' . $css_select[0];
    if ($css_select[1]) {
      $this->theme = $css_select[1];
    }
    // feeds
    $this->feed_count = $layout[2];
    $this->b = $layout[3];
    $this->n = $layout[4];
    $this->w = $layout[5];
    $this->feeds = $db->get_feeds();
    // update last_visit
    $db->post_last_visit(config::$version, date('Y-m-d', time()));
    // disconnect from db
    $db->db_disconnect();
  }

  function head() {
    echo '<title>' . $this->title . '</title>
  <link id="css_theme" rel="stylesheet" href="' . $this->theme . '" type="text/css" />';
  }

  function footer() {
    $html = '<select id="menu">';
    if ($this->b) {
      $html .= '<option value="b">Bookmarks</option>';
    }
    if ($this->n) {
      $html .= '<option value="n">Notes</option>';
    }
    if ($this->w) {
      $html .= '<option value="w">Weather</option>';
    }
    foreach ($this->feeds as $feed) {
      $html .= '<option value="' . $feed[0] . '">' . $feed[1] . '</option>';
    }
    $html .= '</select>';
    echo $html;
  }

}
?>