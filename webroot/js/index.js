/*
CNTP - JS Page Loader
v0.5 sen
*/

/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::             XMLHttpRequest              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function xhr(type, url) {
  let req = new XMLHttpRequest();
  req.open(type, url);
  req.setRequestHeader("X-Requested-With",'CNTP');
  req.onerror = function() {
    console.log('[XHR ERROR] Failed to "' + type + '" ' + url + ': ' + req.status);
  };
  return req;
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Page Init                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function page_init() {
  //  set row height based on number of columns
  let rows = document.getElementById('window_tab').rows;
  let row_count = rows.length;
  let row_height = 100 / row_count;
  for (let i = 0; i < row_count; i++) {
    rows[i].style.height = row_height + '%';
  }
  // load modules
  if (document.getElementById('bookmarks')) {
    bookmarks();
  }
  if (document.getElementById('notes')) {
    document.getElementById('notes').innerHTML = '<div class="head"><h3>⌦ Notes ⌫</h3></div><label id="notes_frame"></label>';
    notes();
  }
  if (document.getElementById('weather')) {
    weather();
  }
  feeds(1);
  document.querySelectorAll('.fs').forEach(function(fs) {
    fs.addEventListener('click', function() {feeds(fs.getAttribute("name"));}, false);
  });
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Feeds                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function feeds(set) {
  document.querySelectorAll('.fs').forEach(function(fs) {
    if (fs.getAttribute("name") == set) {
      fs.id = 'fs_act';
    } else {
      fs.id = '';
    }
  });
  document.querySelectorAll('.feed_box').forEach(function(feed_box, index) {
    let req = xhr('GET', 'php/feed_reader.php?set=' + set + '&index=' + index);
    req.onload = function() {
      if (req.status >= 200 && req.status < 400) {
        // success
        feed_box.innerHTML = req.response;
      } else {
        // fail
        console.log('[FEED ERROR] Failed to load Feed at index ' + index + '! Status: ' + req.status);
      }
    };
    req.send();
  });
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Bookmarks                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function bookmarks() {
  let req = xhr('GET', 'php/bookmarks.php?cache');
  req.onload = function() {
    if (req.status >= 200 && req.status < 400) {
      // success
      document.getElementById('bookmarks').innerHTML = req.response;
    } else {
      // fail
      console.log('[BOOKMARKS ERROR] Failed to load! Status: ' + req.status);
    }
  };
  req.send();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Notes                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function notes_load() {
  return new Promise(function (resolve) {
    let req = xhr('GET', 'php/notes.php?notes=load');
    req.setRequestHeader('Notes', 'Load');
    req.onload = function() {
      if (req.status >= 200 && req.status < 400) {
        // success
        text = req.response.trim();
        let target = document.getElementById('notes_frame');
        if (target.tagName.toLowerCase() == 'textarea') {
          // textarea
          target.value = text;
        } else {
          // label
          resolve(target.innerHTML = notes_label_html(text));
        }
      } else {
        // fail
        console.log('[NOTES ERROR] Failed to load! Status: ' + req.status);
      }
    };
    req.send();
  });
}

async function notes() {
  // wait
  let result = await notes_load();
  // continue
  let notes = document.getElementById('notes');
  let notes_head = notes.getElementsByClassName('head')[0];
  let mode = notes_head.getElementsByTagName('h3')[0];
  let notes_frame = document.getElementById('notes_frame');
  notes_head.addEventListener('click', function() {
    if (mode.innerHTML == '⌦ Notes ⌫') {
      // switch to edit mode
      notes_frame.outerHTML = '<textarea id="notes_frame"></textarea>';
      notes_load();
      mode.textContent = '>>> Save <<<';
      notes_frame = document.getElementById('notes_frame');
      notes_frame.focus();
    } else {
      // switch to view mode
      notes_save();
      let text = notes_label_html(notes_frame.value);
      notes_frame.outerHTML = '<label id="notes_frame"></label>';
      mode.textContent = '⌦ Notes ⌫';
      notes_frame = document.getElementById('notes_frame');
      notes_frame.innerHTML= text;
    }
  }, false);
}

function notes_save() {
  let req = xhr('POST', 'php/notes.php');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  let text = document.getElementById('notes_frame').value;
  req.send('notes=save&text=' + encodeURIComponent(text));
}

function notes_label_html(text) {
    // line breaks
    let line_break = /\n/gim;
    text = text.replace(line_break, '<br \>');
    // urls starting with http://, https://, or ftp://
    let url = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    text = text.replace(url, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
    // urls starting with www. (without //)
    let www = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    text = text.replace(www, '$1<a href="http://$2" target="_blank" rel="noopener noreferrer">$2</a>');
    // change email addresses to mailto:: links
    let mailto = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
    text = text.replace(mailto, '<a href="mailto:$1">$1</a>');
    return text;
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Weather                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function weather() {
  let req = xhr('GET', 'php/weather.php');
  req.onload = function() {
    if (req.status >= 200 && req.status < 400) {
      // success
      document.getElementById('weather').innerHTML = req.response;
    } else {
      // fail
      console.log('[WEATHER ERROR] Failed to load! Status: ' + req.status);
    }
  };
  req.send();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              On DOM Loaded              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

document.addEventListener('DOMContentLoaded', function() {page_init();});