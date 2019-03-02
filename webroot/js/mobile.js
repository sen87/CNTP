/*
CNTP - JS Mobile Page Loader
v0.1 sen
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
    :::                  Menu                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function menu() {
  let item = document.getElementById('menu').value;
  document.getElementById('edit_notes').style.display = 'none';
  if (item == 'b') {
    bookmarks();
  } else if (item == 'n') {
    notes();
    document.getElementById('edit_notes').style.display = 'block';
  } else if (item == 'w') {
    weather();
  } else {
    feed(item);
  }
}

function menu_next() {
  let menu = document.getElementById('menu');
  let i = menu.selectedIndex;
  menu.options[++i%menu.options.length].selected = true;
  menu.click();
}

function menu_prev() {
  let menu = document.getElementById('menu');
  let i = menu.selectedIndex;
  menu.options[--i%menu.options.length].selected = true;
  menu.click();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Feeds                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function feed(id) {

  let req = xhr('GET', 'php/feed_reader.php?id=' + id);
  req.onload = function() {
    if (req.status >= 200 && req.status < 400) {
      // success
      let box = document.getElementsByClassName('feed_frame')[0];
      box.innerHTML = req.response;
      box.innerHTML = box.childNodes[2].innerHTML;
    } else {
      // fail
      console.log('[FEED ERROR] Failed to load Feed ID ' + id + '! Status: ' + req.status);
    }
  };
  req.send();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Bookmarks                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function bookmarks() {
  let req = xhr('GET', 'php/bookmarks.php?cache');
  req.onload = function() {
    if (req.status >= 200 && req.status < 400) {
      // success
      let box = document.getElementsByClassName('feed_frame')[0];
      box.innerHTML = req.response;
      box.innerHTML = box.childNodes[2].innerHTML;
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
        let target = document.getElementsByClassName('feed_frame')[0];
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
  let mode = document.getElementById('edit_notes');
  let notes_frame = document.getElementsByClassName('feed_frame')[0];
  mode.addEventListener('click', function() {
    if (mode.innerHTML == '⌦ Edit Notes ⌫') {
      // switch to edit mode
      notes_frame.outerHTML = '<textarea class="feed_frame"></textarea>';
      notes_load();
      mode.textContent = '>>> Save Notes <<<';
      notes_frame = document.getElementsByClassName('feed_frame')[0];
      notes_frame.focus();
    } else {
      // switch to view mode
      notes_save();
      let text = notes_label_html(notes_frame.value);
      notes_frame.outerHTML = '<label class="feed_frame"></label>';
      mode.textContent = '⌦ Edit Notes ⌫';
      notes_frame = document.getElementsByClassName('feed_frame')[0];
      notes_frame.innerHTML= text;
    }
  }, false);
}

function notes_save() {
  let req = xhr('POST', 'php/notes.php');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  let text = document.getElementsByClassName('feed_frame')[0].value;
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
      let box = document.getElementsByClassName('feed_frame')[0];
      box.innerHTML = req.response;
      box.innerHTML = box.childNodes[2].innerHTML;
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

document.addEventListener('DOMContentLoaded', function() {
   document.getElementById('menu').addEventListener('click', function() {menu();}, false);
   document.getElementById('m_left').addEventListener('click', function() {menu_prev();}, false);
   document.getElementById('m_right').addEventListener('click', function() {menu_next();}, false);
   menu();
});