<?php
/*
CNTP - PHP Weather Forecast
Weather forecast from Yr, delivered by the Norwegian Meteorological Institute and NRK.
v0.2 sen
*/

session_start();
require('check_header.php');
require(dirname(__FILE__) .'/../../db.php');

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Config                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

$weather = new weather;
$weather->get_config();

class weather {
  private static $cache_time = 60; // in minutes
  private $place;
  private $place_id;
  private $forecast;
  private $forecast_height;
  private $icons;
  private $icon_format;

  function get_config() {
    $db = new db;
    // connect to db
    $db->db_connect($_SESSION['uid']);
    $weather_data = $db->get_weather_data();
    $this->place = htmlSpecialChars($weather_data[0]);
    $this->place_id = htmlSpecialChars(parse_url($weather_data[1], PHP_URL_PATH));
    $last_updated = $weather_data[2];
    $this->forecast = $weather_data[3];
    $this->forecast_height = $weather_data[4];
    $this->icons = $weather_data[5];
    $this->icon_format = $weather_data[6];
    if ($last_updated
    && time() - strtotime($last_updated) < (self::$cache_time * 60)
    && $html = $db->get_weather_cache()) {
      echo $html;
    } else {
      $this->load_xml();
    }
    // disconnect from db
    $db->db_disconnect();
  }

  function update_cache($data, $timestamp) {
    $db = new db;
    // connect to db
    $db->db_connect($_SESSION['uid']);
    // update cache and timestamp
    $db->post_weather_cache($data, $timestamp);
    // disconnect from db
    $db->db_disconnect();
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::            Load Weather Data            :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function http_request($url) {
    if (extension_loaded('curl')) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_FAILONERROR, true);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_TIMEOUT, 20);
      curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
      curl_setopt($curl, CURLOPT_USERAGENT, 'Weather');
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

  function load_xml() {
    if ($data = mb_convert_encoding(trim($this->http_request('https://www.yr.no' . $this->place_id . 'forecast_hour_by_hour.xml')), 'UTF-8')) {
    } else {
      die('<b>[WEATHER ERROR] Failed to load!</b>');
    }
    if (isset($data) && $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOWARNING | LIBXML_NOERROR)) {
      $this->parse_data($xml);
    } else {
      die('<b>[WEATHER ERROR] No valid XML found!</b>');
    }
  }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::           Parse Weather Data            :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

    function parse_data($xml) {
      // current
      if (!$this->place) {
        $this->place = htmlSpecialChars($xml->location->name);
      }
      $time = htmlSpecialChars($xml->forecast->tabular->time['from']);
      $condition = htmlSpecialChars($xml->forecast->tabular->time->symbol['name']);
      $symbol = htmlSpecialChars($xml->forecast->tabular->time->symbol['var']);
      $wind = htmlSpecialChars($xml->forecast->tabular->time->windSpeed['name']);
      $rain = htmlSpecialChars($xml->forecast->tabular->time->precipitation['value']);
      $temperature = htmlSpecialChars($xml->forecast->tabular->time->temperature['value']);
      $pressure = htmlSpecialChars($xml->forecast->tabular->time->pressure['value']);
      // forecast
      $graph = [];
      $temp_min = 0;
      $temp_max = 0;
      if ($this->forecast) {
        $first_run = 1;
        foreach ($xml->forecast->tabular->time as $hour) {
          $g_hour = date('H', strtotime($hour['from']));
          $g_temp = (int)$hour->temperature['value'];
          if ($first_run) {
            $temp_max = $g_temp;
            $temp_min = $g_temp;
            $first_run = 0;
          }
          if ($g_temp < $temp_min) {$temp_min = $g_temp;}
          if ($g_temp > $temp_max) {$temp_max = $g_temp;}
          $g_rain = 0;
          if ($hour->precipitation['value'] > 0) {
            $g_rain = 1;
          }
          $graph[] = array($g_hour, $g_temp, $g_rain);
        }
      }
      $this->html_out($time, $condition, $symbol, $wind, $rain, $temperature, $pressure, $graph, $temp_min, $temp_max+1);
    }


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Output                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

  function html_out($time, $condition, $symbol, $wind, $rain, $temperature, $pressure, $graph, $temp_min, $temp_max) {
    // head
    // ----
    $html = '<div class="head"><a href="https://www.yr.no' . $this->place_id . '"><h3>Weather</h3></a></div>';
    // current
    // -------
    $html .= '<div id="weather_frame"><a href="https://www.yr.no' . $this->place_id . '"><table id="weather_tbl"><tr><td>';
    $html .= '<h4>' . $temperature . '&degC</h4>';
    $html .= '<p><span class="weather_symbol">⚑</span> ' . $this->place . '</p>';
    $html .= '<p><span class="weather_symbol">☀</span> ' . $condition . '</p>';
    $html .= '<p><span class="weather_symbol">☴</span> ' . $wind . '</p>';
    $html .= '<p><span class="weather_symbol">☔</span> ' . $rain . ' mm</p>';
    $html .= '<p><span class="weather_symbol">㍱</span> ' . $pressure . '</p>';
    $html .= '</td><td><img src="weather/'. $this->icons . '/' . $symbol . '.' . $this->icon_format . '" id="weather_symbol"></td></tr>';
    // forecast graph
    // --------------
    if ($this->forecast) {
      $height = $this->forecast_height;
      $html .= '<tr><td colspan="2"><svg viewBox="0 0 505 ' . $height . '" preserveAspectRatio="xMinYMin meet" id="weather_forecast">';
      $html .= '<linearGradient x1="0%" y1="0%" x2="0%" y2="100%" id="weather_rain_gradient">';
      $html .= '<stop id="weather_rain_gradient_top" offset="0%" />';
      $html .= '<stop id="weather_rain_gradient_bottom" offset="100%" />';
      $html .= '</linearGradient>';
      $html .= '<line x1="35" x2="35" y1="0" y2="' . $height . '" class="weather_forecast_border"></line>';
      // y-axis calculation
      $temp_range_arr = [];
      $temp_range = 0;
      $switch = 1;
      do {
        if ($switch === 1) {
          $temp_min = $temp_min -1;
          $switch = 0;
        } else {
          $temp_max = $temp_max +1;
          $switch = 1;
        }
        $temp_range_arr = range($temp_min, $temp_max);
        $temp_range = count($temp_range_arr);
      } while ($height % $temp_range);
      $y_pos_multi = $height / $temp_range;
      if ($temp_range < 10) {
        $mod = 1;
      } else {
        if ($temp_range < 20) {
          $mod = 2;
        } else {
          $mod = 5;
        }
      }
      // y-axis labels
      foreach ($temp_range_arr as $temp_range_item) {
        if (!($temp_range_item % $mod)) {
          $y_pos = ($temp_max - $temp_range_item) * $y_pos_multi;
          if ($y_pos > 5 && $y_pos < ($height - 5)) {
            $html .= '<text x="29" y="' . ($y_pos+4) . '" class="weather_forecast_label y">' . $temp_range_item . '°</text>';
            $html .= '<line x1="29" x2="35" y1="' . $y_pos . '" y2="' . $y_pos . '" class="weather_forecast_border"></line>';
          }
          if ($temp_range_item === 0 && ($y_pos > 10 && $y_pos < ($height - 10))) {
            // horizontal line at 0°C
            $html .= '<line x1="35" x2="505" y1="' . $y_pos . '" y2="' . $y_pos . '" class="weather_forecast_border"></line>';
          }
        }
      }
      // x-axis labels
      $day = 1;
      $polyline_cords = '';
      for ($i = 0; $i < 48; $i++) {
        $x_pos = $i*10+35;
        // time
        if (($graph[$i][0] == 0 || $graph[$i][0] == 12) &&  $x_pos > 36) {
          $html .= '<line x1="' . $x_pos . '" x2="' . $x_pos . '" y1="0" y2="' . $height . '" id="weather_forecast_12h"></line>';
          $pos_text = $x_pos + 5;
          if ($x_pos < 465) {
            if ($graph[$i][0] == 0) {
              $html .= '<text x="' . $pos_text . '" y="' . $height . '" class="weather_forecast_label x">0:00</text>';
              $html .= '<text x="' . $pos_text . '" y="12" class="weather_forecast_label x">' . date('l', strtotime($time . ' +' . $day . ' day')) . '</text>';
              $day++;
            } else {
              $html .= '<text x="' . $pos_text . '" y="' . $height . '" class="weather_forecast_label x">12:00</text>';
            }
          }
        }
        // rain
        if ($graph[$i][2]) {
          $html .= '<rect x="' . $x_pos . '" y="0" width="10" height="' . $height . '" fill="url(#weather_rain_gradient)"></rect>';
        }
        // polyline coordinates
        $y_pos = ($temp_max - $graph[$i][1]) * $y_pos_multi;
        $polyline_cords .= $x_pos . ',' . $y_pos . ' ';
      }
      // polyline (temperature)
      $html .= '<polyline id="weather_forecast_temperature" points="' . $polyline_cords . '"</polyline></svg></td></tr>';
    }
    $html .= '</table></a></div>';
    echo $html;
    // update cache
    $this->update_cache($html, date('Y-m-d H:i:s', time()));
  }

}
?>