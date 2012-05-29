<html>
  <head>
    <title>DNS Control Panel - Recover password</title>
    <link rel="stylesheet" type="text/css" href="login.css" />
  </head>
  <body>
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

<?
  if (isset($_GET['u']) && isset($_GET['t']))
  {
    /* Password recover page */
    require_once('mysql.php');
    require_once('config.php');

    $userid = mysql_escape_string($_GET['u']);
    $token = mysql_escape_string($_GET['t']);

    $res = mysql_query("SELECT id,toktime,name,email FROM users WHERE id={$userid} AND token='{$token}'", $db);
    if (!@mysql_num_rows($res))
    {
?>
  <meta http-equiv="refresh" content="0; /" />
<?
    }
    else if (time() - mysql_result($res, 0, 1) > $config['token_expiration'] * 60)
    {
      /* Token has expired */
?>
  <meta http-equiv="refresh" content="0; /" />
<?
    }
    else
    {
      $fp = fopen('/dev/urandom', 'r');
      $data = fread($fp, 1024);
      fclose($fp);
      $newpass = substr(md5($data), 0, 7);
      $username = mysql_result($res, 0, 2);
      $email = mysql_result($res, 0, 3);
      mysql_query("UPDATE users SET password=MD5('{$newpass}'),token=NULL,toktime=NULL WHERE id={$userid}", $db);

      mail($email, 'DNS Control panel - Password recovery',
            "Your password for the DNS control panel has been reseted.\n" .
            "Your new login data is:\n\n" .
            "* Username: {$username}\n" .
            "* Password: {$newpass}\n\n" .
            "You can now login in http://{$config['real_host']}/\n\n" .
            "{$config['signature']}",
            "From: {$config['mail_from']}") or die('Error sending email');
?>
  <div id="recoverdone">
    <h1>Password reseted</h1>
    <p>A new email message has ben sent to your email address with your new password.</p>
  </div>
<?
    }
  }

  else if (!isset($_POST['username']) || !isset($_POST['email']))
  {
    /* Main page */
?>
    <div id="recoverform">
      <div id="header">
        <h1>Recover your password</h1>
        <p>If you forgot your password, type here your username and the email address in what you registered your account to get an activation token sent to your email.</p>
        <hr />
      </div>
      <form name="recover" method="post" action="recover.php" onsubmit="return checkRecover();">
        <div id="content">
          <div id="username"><span>Username</span><input type="text" name="username" /></div>
          <div id="email"><span>Email</span><input type="text" name="email" /></div>
          <div id="submit"><input type="submit" value="Send" /></div>
        </div>
      </form>
    </div>

    <script type="text/javascript"><!--
      function checkRecover()
      {
        if (!document.recover.username.value)
        {
          alert('Enter your user name');
          focusUsername();
          return false;
        }
        if (!document.recover.email.value)
        {
          alert('Enter your registrarion email address');
          document.recover.email.focus();
          return false;
        }
        return true;
      }

      function focusUsername()
      {
        document.recover.username.focus();
      }

      focusUsername();
    // --></script>
<?
  }
  else
  {
    /* Mail sent page */
    require_once('mysql.php');

    $username = mysql_escape_string($_POST['username']);
    $email = mysql_escape_string($_POST['email']);
    $res = mysql_query("SELECT id FROM users WHERE name='{$username}' AND email='{$email}'", $db);
    if (@mysql_num_rows($res))
    {
      require_once('lib/ip.php');
      require_once('config.php');

      /* Get some random string as token */
      $fp = fopen('/dev/urandom', 'r');
      $data = fread($fp, 1024);
      fclose($fp);
      $token = md5($data);

      /* Get all the rest neccessary data */
      $userid = mysql_result($res, 0 ,0);
      $date = time();
      $ip = getRealIP();

      mysql_query("UPDATE users SET token='{$token}', toktime={$date} WHERE id={$userid}", $db);
      mail($email, 'DNS Control panel - Password recovery',
        "Somebody from IP Address {$ip} requested a password recovery for username {$username}.\n\n" .
        "If you didn't request it just ignore this message.\n" .
        "In other case, follow this url:\n\n" .
        "  http://{$config['real_host']}/recover.php?u={$userid}&t={$token}\n\n" .
        "This url will stop working after {$config['token_expiration']} minutes and then you\n" .
        "will have to request another password recovery.\n\n" .
        "{$config['signature']}",
        "From: {$config['mail_from']}") or die('Error sending email');
    }
?>
  <div id="recoversent">
    <h1>Email sent</h1>
    <p>If you provided the correct username and associated email address, you will receive soon a validation email to reset your password.</p>
  </div>
<?
  }
?>
    <? require_once('config.php') ?>
    <div id="signature"><small><?=$config['signature']?></small></div>
  </body>
</html>
