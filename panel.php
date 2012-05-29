<?
  require_once('lib/session.php');

  if (!isset($_SESSION['username']) || !isset($_SESSION['password']))
  {
    header('Location: /');
    exit();
  }

  require_once('mysql.php');

  $username = $_SESSION['username'];
  $password = $_SESSION['password'];
  $res = mysql_query("SELECT id FROM users WHERE name='{$username}' AND password=MD5('{$password}')", $db);

  if (!@mysql_num_rows($res))
  {
    ClearSession();
    mysql_close($db);
    header('Location: /');
    exit();
  }
  $userid = mysql_result($res, 0, 0);

  require_once('config.php');
  $date = time();
  if (isset($_SESSION['last_activity']))
  {
    if ($date - $_SESSION['last_activity'] >= $config['session_timeout'])
    {
      /* TIMEOUT! */
      ClearSession();
      mysql_close($db);
      header('Location: /');
      exit();
    }
  }
  $_SESSION['last_activity'] = $date;
?>
<html>
  <head>
    <title>DNS Control Panel - <?=$username?></title>
    <link rel="stylesheet" type="text/css" href="panel.css" />
  </head>
  <body>
    <div id="logo"><img src="/img/logo.png" /></div>
    <div id="menu">
<?
  /* Menu */
  require_once('lib/tasks.php');

  $domain = array();
  if (isset($_SESSION['domain']))
  {
    $res = mysql_query("SELECT id,domain FROM domains WHERE owner={$userid} AND id={$_SESSION['domain']}", $db);
    if (!@mysql_num_rows($res))
    {
      unset($_SESSION['domain']);
    }
    else
    {
      $domain['id'] = mysql_result($res, 0, 0);
      $domain['name'] = mysql_result($res, 0, 1);
    }
  }

  if (isset($domain['id']))
  {
    require_once('lib/domain.php');
  }
?>
    </div>
<?
  /* Section */
  if (!isset($_GET['action']))
  {
    $action = 'select';
  }
  else
  {
    switch ($_GET['action'])
    {
      case 'select': $action = 'select'; break;
      case 'logs': $action = 'logs'; break;
      case 'logout': $action = 'logout'; break;
      case 'selectdomain': $action = 'selectdomain'; break;
      case 'reg': $action = 'reg'; break;
      case 'soa': $action = 'soa'; break;
      case 'mx': $action = 'mx'; break;
      default: $action = 'select'; break;
    }
  }
?>
    <div id="section">
<? require_once('lib/action.' . $action . '.php'); ?>
    </div>
  </body>
</html>
<?
  mysql_close($db);
?>
