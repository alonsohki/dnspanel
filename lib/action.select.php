<div id="seldom">
  <div id="header">
    <h1>Select domain</h1>
    <p>Select one domain from the following list:</p>
    <hr />
  </div>
  <div id="content">
    <div id="header">
      <div id="domain">Domain name</div>
      <div id="select">Select</div>
    </div>
    <div id="domlist">
<?
  $res = mysql_query("SELECT id,domain FROM domains WHERE owner={$userid}", $db);
  $i = 0;
  while ($domain = mysql_fetch_array($res))
  {
    $i++;
?>
      <div id="domentry">
        <div id="domain"><p><?
          echo $domain['domain'];
          if (isset($_SESSION['domain']) && $_SESSION['domain'] == $domain['id'])
            echo ' *';
          ?></p></div>
        <a href="panel.php?action=selectdomain&id=<?=$domain['id']?>"><div id="select"><span>Select</span></div></a>
      </div>
<?
  }
?>
    </div>
    <div id="summary">
      <div id="totaldoms">Total number of domains: <?=$i?></div>
      <div id="update"><a href="panel.php?action=select">Update</a></div>
    </div>
  </div>
</div>
