<?php
/*
CNTP - PHP Page init
v0.1 sen
*/

require(dirname(__FILE__) .'/../../db.php');

$init = new init;
$init->get_config();

class init {
  private $title;
  private $theme;
  private $table;

  function get_config() {
    // check for mobile user agent
    $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android") !== false;
    $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone") !== false;
    if ($Android || $iPhone) {
      header('Location: /mobile.php');
      exit();
    }
    // connect to db
    $db = new db;
    $db->db_connect($_SESSION['uid']);
    // layout
    $layout = $db->get_layout();
    $this->title = $layout[0];
    $this->table = $layout[1];
    // theme
    $css_select = $db->get_theme();
    $this->theme = 'css/' . $css_select[0];
    if ($css_select[1]) {
      $this->theme = $css_select[1];
    }
    // update last_visit
    $db->post_last_visit(config::$version, date('Y-m-d', time()));
    // disconnect from db
    $db->db_disconnect();
  }

  function head() {
    echo '<title>' . $this->title . '</title>
  <link id="css_theme" rel="stylesheet" href="' . $this->theme . '" type="text/css" />';
  }

  function table() {
    echo $this->table;
  }

}
?>