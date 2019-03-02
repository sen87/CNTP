<?php
/*
CNTP - precache feeds every 15 minutes for active users
v0.1 sen
*/

$_SERVER['HTTP_X_REQUESTED_WITH'] = 'CNTP';
require(dirname(__FILE__) .'/../db.php');
require(dirname(__FILE__) .'/../webroot/php/feed_reader.php');

$feeds = [];
$db = new db;
// connect to db
$db->db_connect(0);
# get active users
$active_users = call_user_func_array('array_merge', $db->get_active_users());
foreach ($active_users as $uid) {
  # get feeds
  $feed_count = $db->get_feed_count($uid);
  for ($s=1; $s <= 2; $s++) {
    for ($i = 0; $i < $feed_count; $i++) { 
      $feeds[] = $db->get_feed_id($uid, $s, $i);
    }
  }
}
// remove empty array elements
$feeds = array_filter($feeds);
// remove duplicates
$feeds = array_unique($feeds, SORT_REGULAR);
// update feeds
$feed_reader = new feed_reader;
foreach ($feeds as $feed_id) {
  $feed = $db->get_feed_para($feed_id);
  $feed_reader->update_feed($feed_id, $feed[0], $feed[2], $feed[3], $feed[4], 0);
}
// disconnect from db
$db->db_disconnect();

?>