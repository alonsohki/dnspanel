<html>
  <head>
    <title>DNS control panel - Login</title>
    <script type="text/javascript"><!--
      function checkLogin()
      {
        if (!document.login.username.value)
        {
          alert('Enter your username');
          focusLogin();
          return false;
        }
        if (!document.login.password.value)
        {
          alert('Enter your password');
          document.login.password.focus();
          return false;
        }
        return true;
      }

      function focusLogin()
      {
        document.login.username.focus();
      }
    // --></script>
    <link rel="stylesheet" type="text/css" href="login.css" />
  </head>
  <body onload="focusLogin();">
    <div id="logo"><img src="img/logo.png" /></div>
    <div id="tools">
      <div id="header">
        <h1>Tools</h1>
      </div>
      <div id="content">
        <ul>
          <li id="main"><span><a href="/">Login</a></span></li>
          <li id="recover"><span><a href="recover.php">Recover password</a></span></li>
        </ul>
      </div>
    </div>
    <div id="login">
      <div id="box">
        <div id="header">
          <h1>Login into the DNS control panel</h1>
          <p>Please, fill the following login form to manage your domains:</p>
          <hr />
        </div>
        <form action="index.php" name="login" method="post" onsubmit="return checkLogin();">
          <div id="username"><span>Username</span><input type="text" name="username" /></div>
          <div id="password"><span>Password</span><input type="password" name="password" /></div>
          <div id="submit"><input type="submit" value="Login" /></div>
        </form>
      </div>
    </div>
    <? require_once('config.php') ?>
    <div id="signature"><small><?=$config['signature']?></small></div>
  </body>
</html>
