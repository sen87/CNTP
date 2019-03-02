/*
Title:    CNTP - Login
Author:   sen
Version:  0.1
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

function init() {
  let req = xhr('GET', 'php/portal_worker.php?init');
  req.onload = function() {
    if (req.status >= 200 && req.status < 400) {
      // success
      let init_array = JSON.parse(req.response);
      document.getElementById('version').innerHTML = init_array[0];
      document.getElementById('mail_admin').setAttribute('href', 'mailto:' + init_array[1]);
    }
  };
  req.send();
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::                  Tabs                   :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

function show_tab(id) {
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
  let mail;
  let token;
  let verify;
  let reset;
  let items = url.split("?");
  for (let i = 0; i < items.length; i++) {
    let item = items[i].split("=");
    let msg = decodeURIComponent(item[1]);
    // login info
    if (item[0] == 'log') {
      document.getElementById('log_info').style.display = 'block';
      let html = document.getElementById('log_msg');
      html.style.display = 'block';
      show_tab('login');
      if (msg == 'exists') {
        html.innerHTML += '<hr>There is already an Account associated with that Email Address!\
                           <br>Please try to login.';
      } else if (msg == 'empty') {
        html.innerHTML += '<hr>Please fill out all fields!';
      } else if (msg == 'nouser') {
        html.innerHTML += '<hr>There is no Account registered to this Email Address!\
                           <br>Please register first if you are new.';
      } else if (msg == 'pwnope') {
        html.innerHTML += '<hr>Wrong Password!';
      } else if (msg == 'pwfail') {
        html.innerHTML += '<hr>Password reset failed!<br>Please try again or contact admin.';
      } else if (msg == 'pwinit') {
        html.innerHTML += '<hr>Password reset initiated.<br>Please check your inbox!';
      } else if (msg == 'pwreset') {
        reset = 1;
      } else if (msg == 'pwsend') {
        html.innerHTML += '<hr>Password reset was successfull!\
                           <br>A new password will be delivered to your inbox.';
      } else if (msg == 'pwchanged') {
        html.innerHTML += "<hr>Password change was successfull!<br>Please Login.";
      } else if (msg == 'mailchanged') {
        html.innerHTML += '<hr>Email address change in progress!\
                           <br>A verification link will be delivered to your inbox.';
      } else if (msg == 'chmail') {
        verify = 1;
      } else if (msg == 'verified') {
        html.innerHTML += '<hr>Registration completed successfully!<br>Please Login.';
      }
    }
    // registration info
    if (item[0] == 'reg') {
      let html = document.getElementById('reg_info');
      html.style.display = 'block';
      show_tab('registration');
      if (msg == 'empty') {
        html.innerHTML += '<hr>Please fill out all fields!';
      } else if (msg == 'pwnomatch') {
        html.innerHTML += "<hr>Passwords don't match! tYpO?";
      } else if (msg == 'invalid') {
        html.innerHTML += '<hr>Invalid Email Address!';
      } else if (msg == 'verify') {
        verify = 1;
      } else if (msg == 'success') {
        html.innerHTML += '<hr>Registration initiated.\
                           <br>A verification link will be delivered to your inbox.';
      } else if (msg == 'failed') {
        html.innerHTML += '<hr>Registration failed!<br>Please try again or contact admin.';
      }
    }
    // set token
    if (item[0] == 'token') {
      token = msg;
    }
    // Refill Email
    if (item[0] == 'mail') {
      mail = msg;
      let mail_fields = document.getElementsByClassName('mail');
      for (i = 0; i < mail_fields.length; i++) {
        mail_fields[i].value = msg;
      }
    }
  }
  // submit registration and password reset
  if (token && mail) {
    let req = xhr('POST', 'php/portal_worker.php');
    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    req.onreadystatechange = function() {
      if (req.readyState == 4 && req.status == 200) {
        // change url on response
        document.location = req.responseText;
      }
    };
    if (verify) {
      req.send('verify&token=' + encodeURIComponent(token) + '&mail=' + encodeURIComponent(mail));  
    } else if (reset) {
      req.send('do_repw&token=' + encodeURIComponent(token) + '&mail=' + encodeURIComponent(mail));  
    }   
  }
}


/*  :::::::::::::::::::::::::::::::::::::::::::::::
    :::              On DOM Loaded              :::
    :::::::::::::::::::::::::::::::::::::::::::::::  */

document.addEventListener('DOMContentLoaded', function() {
  init();
  // show login form on load
  show_tab('login');
  // tab switch events
  document.getElementById('login').addEventListener('click', function() {show_tab('login');}, false);
  document.getElementById('registration').addEventListener('click', function() {show_tab('registration');}, false);
  document.getElementById('demo').addEventListener('click', function() {show_tab('demo');}, false);
  document.getElementById('about').addEventListener('click', function() {show_tab('about');}, false);
  // check url parameters
  parse_url(location.search.substr(1));
});