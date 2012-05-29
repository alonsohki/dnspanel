<?
  session_start();

  function UnsetSessionVars()
  {
    global $_SESSION;
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['last_activity']);
    unset($_SESSION['domain']);
    unset($_SESSION['address']);
  }

  function ClearSession()
  {
    UnsetSessionVars();
    session_regenerate_id(true);
  }

  require_once('ip.php');
  $ip = getRealIP();
  if (isset($_SESSION['address']))
  {
    if ($_SESSION['address'] != $ip)
    {
      ClearSession();
      echo "<b><span style='color: red'>Session spoofing attempt detected.</span></b><br />";
      exit();
    }
  }
  $_SESSION['address'] = $ip;
?>
