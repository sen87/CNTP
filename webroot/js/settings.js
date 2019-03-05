/*
CNTP - Settings
v0.2 sen
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
    :::                  Tabs                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function show_tab(id) {
  // clear msg
  document.getElementById('msg').innerHTML = '';
  // content
  let tabcontent = document.getElementsByClassName('tab');
  for (i = 0; i < tabcontent.length; i++) {
    if (tabcontent[i].id == 'tab_' + id) {
      // display tab content
      tabcontent[i].style.display = 'block';
    } else {
      // hide tab content
      tabcontent[i].style.display = 'none';
    }
  }
  // links
  let tablink = document.getElementsByClassName('link');
  for (i = 0; i < tablink.length; i++) {
    if (tablink[i].id == id) {
      // display tab content
      tablink[i].className += ' active';
    } else {
      // hide tab content
      tablink[i].className = 'link';
    }
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                   URL                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function parse_url(url) {
  let msg_field = document.getElementById('msg');
  let items = url.split("?");
  for (let i = 0; i < items.length; i++) {
    let item = items[i].split("=");
    // login info
    if (item[0] == 'tab') {
      show_tab(item[1]);
    } else if (item[0] == 'b_cat') {
      document.getElementById('b_cats').value = item[1];
      cat_load();
    } else if (item[0] == 'saved') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Configuration updated!";
    } else if (item[0] == 'feed_updated') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Feed updated!";
    } else if (item[0] == 'feed_created') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Feed created!";
    } else if (item[0] == 'feed_removed') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Feed removed!";
    } else if (item[0] == 'feed_locked') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Feed is in use!";
    } else if (item[0] == 'b_cat_added') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> New Bookmark Category added!";
    } else if (item[0] == 'b_cat_saved') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Bookmark Category saved!";
    } else if (item[0] == 'b_cat_removed') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Bookmark Category removed!";
    } else if (item[0] == 'b_removed') {
      msg_field.innerHTML = "<b id='ok'>[SUCCESS]</b> Bookmark deleted!";
    } else if (item[0] == 'failed') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Configuration could not be saved!";
    } else if (item[0] == 'demo') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Configuration cannot be changed during the demo!";
    } else if (item[0] == 'empty') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Please fill out all fields!";
    } else if (item[0] == 'css_url') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> External CSS file not found!";
    } else if (item[0] == 'invalid') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> New Email Address seems to be invalid, please check!";
    } else if (item[0] == 'match') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Passwords do not match! tYp0?";
    } else if (item[0] == 'pwd') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Wrong Password!";
    } else if (item[0] == 'pwdmail') {
      msg_field.innerHTML = "<b id='warn'>[ERROR]</b> Wrong Password or email address!";
    } 
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Feed                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function feed_edit() {
  let feed_id = document.getElementById('feed_edit_select').value;
  if (feed_id !== 'none') {
    let req = xhr('GET', 'php/settings_worker.php?feed_edit=' + feed_id);
    req.onload = function() {
      if (req.status >= 200 && req.status < 400) {
        // success
        let feed_para = JSON.parse(req.response);
        // [0]name [1]description [2]url [3]website_url [4]img
        document.getElementById('feed_name').value = feed_para[0];
        document.getElementById('feed_desc').value = feed_para[1];
        document.getElementById('feed_url').value = feed_para[2];
        document.getElementById('feed_website').value = feed_para[3];
        let feed_thumb = document.getElementById('feed_thumb');
        if (feed_para[4]) {
          feed_thumb.checked = true;
        } else {
          feed_thumb.checked = false;
        }
      }
    };
    req.send();
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                Bookmarks                :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function cat_add() {
  let cat_name = document.getElementById('cat_name_new').value;
  if (cat_name) {
    let req = xhr('POST', 'php/settings_worker.php');
    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    req.onreadystatechange = function() {
      if (req.readyState == 4 && req.status == 200) {
        // change url on response
        window.location.replace('/settings.php?tab=bookmarks?b_cat_added');
      }
    };
    req.send('b_cat_add=' + encodeURIComponent(cat_name));
  } else {
    window.location.replace('/settings.php?tab=bookmarks?empty');
  }
}

function cat_load() {
  let cat_id = document.getElementById('b_cats').value;
  if (cat_id !== 'none') {
    let req = xhr('GET', 'php/settings_worker.php?b_cat_load=' + cat_id);
    req.onload = function() {
      if (req.status >= 200 && req.status < 400) {
        // create bookmark list
        let resp = JSON.parse(req.response);
        let cat_pos = resp[0];
        let b_list = resp[1];
        let b_box = document.getElementById('b_box');
        let html = '<br><input type="radio" id="cat_left" name="position" value="0" checked="checked">Left</input>'
                 + '<input type="radio" id="cat_right" name="position" value="1">Right</input><br>'
        let buttons = '<button class="btn_cat_del"><img src="css/_main/delete.svg" alt="delete" height="22" width="22">Remove Category</button>'
                    + '<button class="btn_b_add"><img src="css/_main/add.svg" alt="create" height="22" width="22">Add Bookmark</button>'
                    + '<button class="btn_cat_save"><img src="css/_main/save.svg" alt="create" height="22" width="22">Save Category</button>';
        html += buttons + '<br><div id="b_box_cont">';
        if (b_list.length !== 0) {
          for (let i = 0; i < b_list.length; i++) {
            html += '<div class="b_entry" id="' + b_list[i][0] + '">'
                  + '<div class="b_name"><img src="css/_main/title.svg" alt="name" height="22" width="22">' 
                  + '<input class="inp_b_name" type="text" placeholder="Name..." value="' + b_list[i][1] + '"></div>'
                  + '<div class="b_url"><img src="css/_main/url.svg" alt="url" height="22" width="22">'
                  + '<input class="inp_b_url" type="text" placeholder="URL..." value="' + b_list[i][2] + '">'
                  + '<button class="btn_b_del" id="' + b_list[i][0] + '"><img src="css/_main/remove.svg" alt="create" height="22" width="22">'
                  + '</button></div></div>';
          }
          html += '</div><br>' + buttons;
        }
        b_box.innerHTML = html;
        // position: right
        if (cat_pos) {
          document.getElementById('cat_right').checked = true;
        }
        // create events
        document.querySelectorAll('.btn_b_del').forEach(function(btn) {
          btn.addEventListener('click', function() {bookmark_del(btn.id, cat_id);}, false);
        });
        document.querySelectorAll('.btn_cat_del').forEach(function(btn) {
          btn.addEventListener('click', function() {cat_del(cat_id);}, false);
        });
        document.querySelectorAll('.btn_b_add').forEach(function(btn) {
          btn.addEventListener('click', bookmark_add, false);
        });
        document.querySelectorAll('.btn_cat_save').forEach(function(btn) {
          btn.addEventListener('click', function() {cat_save(cat_id);}, false);
        });
      }
    };
    req.send();
  } else {
    document.getElementById('b_box').innerHTML = '';
  }
}

function cat_del(cat_id) {
  let req = xhr('POST', 'php/settings_worker.php');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      // change url on response
      window.location.replace('/settings.php?tab=bookmarks?b_cat_removed');
    }
  };
  req.send('b_cat_del=' + cat_id);
}

function cat_save(cat_id) {
  let req_pos = xhr('POST', 'php/settings_worker.php');
  req_pos.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  // position
  let cat_pos = 0;
  if (document.getElementById('cat_right').checked) {
    cat_pos = 1;
  }
  req_pos.send('cat_id=' + cat_id + '&cat_pos=' + cat_pos);
  // bookmarks
  let b_entries = document.querySelectorAll('.b_entry');
  let counter = b_entries.length
  b_entries.forEach(function(bookmark) {
    let id = bookmark.id;
    let name = bookmark.children[0].children[1].value;
    let url = bookmark.children[1].children[1].value;
    if (!name || !url) {
      document.getElementById('msg').innerHTML = "<b id='warn'>[ERROR]</b> Please fill out all fields!";
    } else {
      let req = xhr('POST', 'php/settings_worker.php');
      req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
      req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200) {
          counter--;
          if (counter === 0) {
            let req_cache = xhr('POST', 'php/settings_worker.php');
            req_cache.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            req_cache.onreadystatechange = function() {
              if (req_cache.readyState == 4 && req_cache.status == 200) {
                // redirect when all bookmarks are saved and cache update is triggered
                window.location.replace('/settings.php?tab=bookmarks?b_cat=' + cat_id + '?b_cat_saved');
              }
            };
            req_cache.send('b_cache');
          }
        }
      };
      if (!id) {
        // create new bookmark
        req.send('cat_id=' + cat_id + '&b_id=new&b_name=' + encodeURIComponent(name) + '&b_url=' + encodeURIComponent(url));
      } else {
        // update bookmark
        req.send('cat_id=' + cat_id + '&b_id=' + id + '&b_name=' + encodeURIComponent(name) + '&b_url=' + encodeURIComponent(url));
      }
    }
  });
}

function bookmark_add() {
  let b_box_cont = document.getElementById('b_box_cont');
  let html = '<div class="b_entry new">'
           + '<div class="b_name"><img src="css/_main/title.svg" alt="name" height="22" width="22">' 
           + '<input class="inp_b_name" type="text" placeholder="Name..."></div>'
           + '<div class="b_url"><img src="css/_main/url.svg" alt="url" height="22" width="22">'
           + '<input class="inp_b_url" type="text" placeholder="URL...">'
           + '<button class="btn_b_del btn_new"><img src="css/_main/remove.svg" alt="create" height="22" width="22"></button>'
           + '</div></div><br>';
  b_box_cont.innerHTML += html;
  document.querySelectorAll('.btn_new').forEach(function(btn) {
    btn.addEventListener('click', function() {btn.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);}, false);
  });
}

function bookmark_del(id, cat_id) {
  let req = xhr('POST', 'php/settings_worker.php');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      // change url on response
      window.location.replace('/settings.php?tab=bookmarks?b_cat=' + cat_id + '?b_removed');
    }
  };
  req.send('b_del=' + id + '&cat_id=' + cat_id);
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Weather                 :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function weather_update() {
  let req = xhr('POST', 'php/settings_worker.php');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      document.getElementById('msg').innerHTML = "<b id='ok'>[SUCCESS]</b> Weather cache cleared!";
    }
  };
  req.send('w_update');
}

function weather_icon() {
  let id = document.getElementById('weather_icon').value;
  let req = xhr('GET', 'php/settings_worker.php?w_icon=' + id);
  req.onload = function() {
    if (req.status >= 200 && req.status < 400) {
      // success
      let icon = JSON.parse(req.response);
      let html = '<a href="' + icon[2] + '">'
               + '<img src="weather/' + icon[0] + '/01d.' + icon[1] + '">'
               + '<img src="weather/' + icon[0] + '/02n.' + icon[1] + '">'
               + '<img src="weather/' + icon[0] + '/25d.' + icon[1] + '">'
               + '<br>' + icon[0] + '</a>';
      document.getElementById('weather_icon_prev').innerHTML = html;
    }
  };
  req.send();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                 Layout                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function layout_creater_options() {
  let row_count = Number(document.getElementById('layout_row').value);
  let column_count = Number(document.getElementById('layout_column').value);
  let html = '';
  let i = 1;
  for (let r = 0; r < row_count; r++) {
    for (let c = 0; c < column_count; c++) {
      html += '<select id="ls_' + i + '" class="layout_select" value="feed">';
      html += '<option value="feed" selected="selected">Feed</option>\
               <option value="bookmarks">Bookmarks</option>\
               <option value="notes">Notes</option>\
               <option value="weather">Weather</option>';
      if (r > 0) {
        html += '<option value="rowspan">↓ Rowspan</option>';
      }
      if (c > 0) {
        html += '<option value="colspan">→ Colspan</option>';
      }
      html += '</select>';
      i++;
    }
    html += '<br>';
  }
  // create selectors
  document.getElementById('layout_create').innerHTML = html;
  // create events
  document.querySelectorAll('.layout_select').forEach(function(select) {
    select.addEventListener('click', layout_creater_update, false);
  });
}

function layout_creater_init() {
  // get row and column count
  let table = document.getElementById('layout_preview');
  let rows = table.rows;
  let row_count = rows.length;
  document.getElementById('layout_row').value = row_count;
  let cells_r0 = rows[0].cells;
  let column_count = cells_r0.length;
  for (let c = 0; c < cells_r0.length; c++) {
    if (cells_r0[c].colSpan > 1) {
      column_count += (cells_r0[c].colSpan - 1) ;
    }
  }
  document.getElementById('layout_column').value = column_count;
  // set up select boxes
  layout_creater_options();
  // get table layout including row- and columnspan
  let table_2d = new Array(row_count).fill(0).map(item =>(new Array(column_count).fill(0)));
  for (let r = 0; r < row_count; r++) {
    let cells = rows[r].cells;
    let x = 0;
    for (let c = 0, cell_count = cells.length; c < cell_count; c++) {
      let cell = cells[c];
      while (table_2d[r][x]) {
        x++;
      }
      // colspan check
      let colspan = 0;
      if (cell.colSpan > 1) {
        var x3 = x + cell.colSpan;
        colspan = 1;
      } else {
        var x3 = x + 1;
      }
      // rowspan check
      let rowspan = 0;
      if (cell.rowSpan > 1) {
        var y3 = r + cell.rowSpan;
        rowspan = 1;
      } else {
        var y3 = r + 1;
      }
      for (let y2 = r, first_rs = 1; y2 < y3; y2++) {
        for (let x2 = x, first_cs = 1; x2 < x3; x2++) {
          if (colspan && first_cs === 0) {
            // insert colspan placeholder
            table_2d[y2][x2] = 'colspan';
          } else if (rowspan && first_rs === 0) {
            // insert rowspan placeholder
            table_2d[y2][x2] = 'rowspan';
          } else {
            if (colspan && first_cs === 1) {
              // first element with colspan
              first_cs = 0;
            }
            if (rowspan && first_rs === 1) {
              // first element with rowspan
              first_rs = 0;
            }
            // insert element
            if (cell.id) {
              table_2d[y2][x2] = cell.id;
            } else {
              table_2d[y2][x2] = 'feed';
            }
          }
        }
      }
      x = x3;
    }
  }
  // set select options
  let i = 1;
  for (let r = 0; r < row_count; r++) {
    for (let c = 0; c < column_count; c++) {
      let select = document.getElementById('ls_' + i);
      if (table_2d[r][c] === 'rowspan') {
        // set rowspan
        select.value = 'rowspan';
      } else if (table_2d[r][c] === 'colspan') {
        // set colspan
        select.value = 'colspan';
      } else if (table_2d[r][c] === 'feed') {
        select.value = 'feed';
        feed_count++;
      } else if (table_2d[r][c] === 'bookmarks') {
        select.value = 'bookmarks';
      } else if (table_2d[r][c] === 'notes') {
        select.value = 'notes';
      } else if (table_2d[r][c] === 'weather') {
        select.value = 'weather';
      }
      i++;
    }
  }
  layout_creater_update();
}

function layout_creater_update() {
  for (let rerun = 0; rerun < 2; rerun++) {
    let row_count = Number(document.getElementById('layout_row').value);
    let column_count = Number(document.getElementById('layout_column').value);
    let rowspan_arr = new Array(row_count).fill(0).map(item =>(new Array(column_count).fill(0)));
    let colspan_arr = new Array(row_count).fill(0).map(item =>(new Array(column_count).fill(0)));
    let bookmarks = 0;
    let weather = 0;
    let notes = 0;
    let i = 1;
    for (let r = 0; r < row_count; r++) {
      for (let c = 0, html = ''; c < column_count; c++) {
        // get dropdown element
        let select = document.getElementById('ls_' + i);
        // re-enable
        select.disabled = false;
        // get selected option
        let option = select.value;
        // get module index
        if (option === 'bookmarks') {
          bookmarks = i;
        } else if (option === 'notes') {
          notes = i;
        } else if (option === 'weather') {
          weather = i;
        } else if (option === 'rowspan') {
          // rowspan map
          rowspan_arr[r][c] = 1;
        } else if (option === 'colspan') {
          // colspan map
          colspan_arr[r][c] = 1;
        }
        // re-insert all options
        html = '<option value="feed">Feed</option>';
        html += '<option value="bookmarks">Bookmarks</option>';
        html += '<option value="notes">Notes</option>';
        html += '<option value="weather">Weather</option>';
        if (r > 0) {
          html += '<option value="rowspan">↓ Rowspan</option>';
        }
        if (c > 0) {
          html += '<option value="colspan">→ Colspan</option>';
        }
        select.innerHTML = html;
        // re-select option
        select.value = option;
        i++;
      }
    }
    // row- and colspan (in)sanity check
    i = 1;
    for (let r = 0; r < row_count; r++) {
      for (let c = 0; c < column_count; c++) {
        // get selected option
        let option = document.getElementById('ls_' + i).value;
        // set rowspan on previous and/or next element when below colspan
        if (option === 'rowspan' && (colspan_arr[r - 1][c] || colspan_arr[r - 1][c + 1])) {
          // right
          let x = 1;
          while ((c + x) < column_count) {
            if (!colspan_arr[r - 1][c + x]) {
              break;
            }
            let element = document.getElementById('ls_' + (i + x));
            element.value = 'rowspan';
            x++;
          }
          // left
          let y = 1;
          while ((c - y) >= 0) {
            if (!colspan_arr[r - 1][c - y + 1]) {
              break;
            }
            let element = document.getElementById('ls_' + (i - y));
            element.value = 'rowspan';
            y++;
          }
        }
        // rows below
        if (option === 'rowspan' && r > 1 && rowspan_arr[r - 1][c]) {
          let y = 1;
          while ((r - y) >= 0) {
            if (colspan_arr[r - y][c] || colspan_arr[r - y][c + 1]) {
              // colspan above - get length
              let p = 0
              while (colspan_arr[r - y][c - p]) {
                p++;
              }
              let n = 0
              while (colspan_arr[r - y][c + n]) {
                n++;
              }
              for (let w = -p; w < n; w++) {
                let element = document.getElementById('ls_' + (i + w)).value = 'rowspan';
              }
            }
            let element = document.getElementById('ls_' + (i - (column_count * y)));
            if (element.value !== 'rowspan') {
              break;
            }
            y++;
          }
        }
        // set colspan next to rowspan
        if (option === 'colspan' && (rowspan_arr[r][c - 1] || ((r + 1) < row_count && rowspan_arr[r + 1][c - 1]))) {
          // above
          let x = 0;
          while ((r - x) > 0) {
            if (!rowspan_arr[r - x][c - 1]) {
              break;
            }
            let element = document.getElementById('ls_' + (i - (column_count * x)));
            element.value = 'rowspan';
            x++
          }
          let element_top = document.getElementById('ls_' + (i - (column_count * x)));
          element_top.value = 'colspan';
          // below
          let y = 1;
          while ((r + y) < row_count) {
            if (!rowspan_arr[r + y][c - 1]) {
              break;
            }
            let element = document.getElementById('ls_' + (i + (column_count * y)));
            element.value = 'rowspan';
            y++
          }
        }
        i++;
      }
    }
    // update options and disable elements
    i = 1;
    for (let r = 0; r < row_count; r++) {
      for (let c = 0, html = ''; c < column_count; c++) {
        // get dropdown element
        let select = document.getElementById('ls_' + i);
        // get selected option
        let option = select.value;
        html = '<option value="feed">Feed</option>';
        if (!bookmarks || bookmarks === i) {
          html += '<option value="bookmarks">Bookmarks</option>';
        }
        if (!notes || notes === i) {
          html += '<option value="notes">Notes</option>';
        }
        if (!weather || weather === i) {
          html += '<option value="weather">Weather</option>';
        }
        if (r > 0) {
          html += '<option value="rowspan">↓ Rowspan</option>';
          if (option === 'rowspan') {
            // multiple rowspans
            if (r < (row_count - 1) && rowspan_arr[r + 1][c]) {
              select.disabled = true;
            }
            // rowspan under colspan
            let y = 1;
            while ((r - y) >= 0) {
              if (colspan_arr[r - y][c]) {
                select.disabled = true;
                document.getElementById('ls_' + (i - 1)).disabled = true;
              }
              let element = document.getElementById('ls_' + (i - (column_count * y)));
              if (element.value !== 'rowspan') {
                break;
              }
              y++;
            }
          }
        }
        if (c > 0) {
          html += '<option value="colspan">→ Colspan</option>';
          if (option === 'colspan') {
            // multiple colspans
            if (c < (column_count - 1) && colspan_arr[r][c + 1]) {
              select.disabled = true;
            }
          }
        }
        select.innerHTML = html;
        // re-select option
        select.value = option;
        i++;
      }
    }
  }
  layout_update_preview(1);
}

function layout_update_preview(label_toggle) {
  let row_count = Number(document.getElementById('layout_row').value);
  let column_count = Number(document.getElementById('layout_column').value);
  let bookmarks = false;
  let weather = false;
  let notes = false;
  let label = '';
  let feed_count = 0;
  let html = '<tbody>';
  let i = 1;
  for (let r = 0; r < row_count; r++) {
    html += '<tr>';
    for (let c = 0; c < column_count; c++) {
      let select = document.getElementById('ls_' + i);
      if (select.value !== 'rowspan' && select.value !== 'colspan') {
        if (select.value === 'bookmarks' && bookmarks === false) {
          html += '<td id="bookmarks"';
          bookmarks = true;
          label = 'B';
        } else if (select.value === 'notes' && notes === false) {
          html += '<td id="notes"';
          notes = true;
          label = 'N';
        } else if (select.value === 'weather' && weather === false) {
          html += '<td id="weather"';
          weather = true;
          label = 'W';
        } else {
          html += '<td class="feed_box"';
          feed_count++;
          label = feed_count;
        }
        // rowspan count
        let rowspan = 1;
        let next_row = i + column_count;
        while  (document.getElementById('ls_' + next_row)) {
          if (document.getElementById('ls_' + next_row).value !== 'rowspan') {
            break;
          }
          rowspan++;
          next_row = next_row + column_count;
        }
        // colspan count
        let colspan = 1;
        let next_col = i + 1;
        while  (document.getElementById('ls_' + next_col)) {
          if (document.getElementById('ls_' + next_col).value !== 'colspan') {
            break;
          }
          colspan++;
          next_col++;
        }
        if (rowspan > 1) {
          html += ' rowspan="' + rowspan + '"';
        }
        if (colspan > 1) {
           html += ' colspan="' + colspan + '"';
        }
        html += '>';
        if (label_toggle) {
          html += label;
        }
        html += '</td>'
      }
      i++;
    }
    html += '</tr>';
  }
  html += '</tbody>';
  document.getElementById('layout_preview').innerHTML = html;
  // update feed count 
  document.getElementById('feed_count').innerHTML = feed_count;
}

function layout_save(exit) {
  layout_update_preview(0);
  let feed_count = document.getElementById('feed_count').innerHTML;
  let table = document.getElementById('layout_preview');
  let table_html = table.children[0].innerHTML;
  let bookmarks = 0;
  let notes = 0;
  let weather = 0;
  if (table.querySelector("#bookmarks") != null) {
    bookmarks = 1;
  }
  if (table.querySelector("#notes") != null) {
    notes = 1;
  }
  if (table.querySelector("#weather") != null) {
    weather = 1;
  }
  console.log('b:' + bookmarks + ' n:' + notes + ' w:' + weather);
  let req = xhr('POST', 'php/settings_worker.php');
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
  req.onreadystatechange = function() {
    if (req.readyState == 4 && req.status == 200) {
      // change url on response
      if (exit === 0) {
        window.location.replace('/settings.php?tab=layout?saved');
      } else {
        window.location.replace('/');
      }
    }
  };
  req.send('feed_count=' + feed_count + '&table=' + encodeURIComponent(table_html) +
           '&b=' + bookmarks + '&n=' + notes + '&w=' + weather);
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Theme                  :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function theme_preview() {
  let id = document.getElementById('theme_select').value;
  document.getElementById('theme_prev').innerHTML = '<img src="css/_prev/' + id + '.png" alt="preview">';
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              On DOM Loaded              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

document.addEventListener('DOMContentLoaded', function() {
  // show first tab on load
  show_tab('feeds');
  // tab switch events
  document.getElementById('layout').addEventListener('click', function() {show_tab('layout');}, false);
  document.getElementById('theme').addEventListener('click', function() {show_tab('theme');}, false);
  document.getElementById('user').addEventListener('click', function() {show_tab('user');}, false);
  document.getElementById('feeds').addEventListener('click', function() {show_tab('feeds');}, false);
  document.getElementById('bookmarks').addEventListener('click', function() {show_tab('bookmarks');}, false);
  document.getElementById('weather').addEventListener('click', function() {show_tab('weather');}, false);
  // check url parameters
  parse_url(location.search.substr(1));
  // feed
  document.getElementById('feed_edit_select').addEventListener('click', function() {feed_edit();}, false);
  // bookmarks
  document.getElementById('btn_cat_add').addEventListener('click', function() {cat_add();}, false);
  document.getElementById('b_cats').addEventListener('click', function() {cat_load();}, false);
  // weather
  document.getElementById('weather_update').addEventListener('click', function() {weather_update();}, false);
  document.getElementById('weather_icon').addEventListener('click', function() {weather_icon();}, false);
  weather_icon();
  // layout
  layout_creater_init();
  document.getElementById('layout_row').addEventListener('click', function() {layout_creater_options();layout_creater_update()}, false);
  document.getElementById('layout_column').addEventListener('click', function() {layout_creater_options();layout_creater_update()}, false);
  // theme
  document.getElementById('theme_select').addEventListener('click', function() {theme_preview();}, false);
  theme_preview();
  // save
  document.getElementById('layout_save').addEventListener('click', function() {layout_save(0);}, false);
  document.getElementById('submit_save').addEventListener('click', function() {layout_save(1);}, false);
});