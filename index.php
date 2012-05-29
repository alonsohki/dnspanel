<?
  require_once('lib/session.php');

  if (!isset($_SESSION['username']) || !isset($_SESSION['password']))
  {
    if (!isset($_POST['username']) || !isset($_POST['password']))
    {
      require_once('login.php');
      exit();
    }
    else
    {
      $_SESSION['username'] = $_POST['username'];
      $_SESSION['password'] = $_POST['password'];
    }
  }

  $username = $_SESSION['username'];
  $password = $_SESSION['password'];

  require_once('mysql.php');

  $res = mysql_query("SELECT id FROM users WHERE name='{$username}' AND password=MD5('{$password}')", $db);
  if (!@mysql_num_rows($res))
  {
    ClearSession();
    mysql_close($db);
    header('Location: /');
    exit();
  }

  if (isset($_POST['username']) && isset($_POST['password']))
  {
    require_once('lib/ip.php');
    $userid = mysql_result($res, 0, 0);
    $ip = getRealIP();
    $date = time();
    mysql_query("INSERT INTO log VALUES({$userid}, 'login', {$date}, '{$ip}')", $db);
  }

?>
<html>
  <head>
    <meta http-equiv="refresh" content="0; /panel.php" />
  </head>
</html>
<?
  mysql_close($db);
?>
