<?
  function goBack()
  {
?>
    <meta http-equiv="refresh" content="0; /panel.php" />
<?
    exit();
  }

  if (!isset($_GET['id']))
  {
    goBack();
  }

  $domid = $_GET['id'];
  $res = mysql_query("SELECT id FROM domains WHERE owner={$userid} AND id={$domid}", $db);
  if (!@mysql_num_rows($res))
  {
    goBack();
  }
  $_SESSION['domain'] = mysql_result($res, 0, 0);
  goBack();
?>
