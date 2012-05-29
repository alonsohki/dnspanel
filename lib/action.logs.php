<div id="logs">
  <div id="header">
    <h1>Last logins on this account</h1>
    <p>Here you can see the last logins in this account, including date and ip address. This data is shown for security matters.</p>
    <hr />
  </div>
  <div id="content">
    <div id="header">
      <div id="date"><span>Date</span></div>
      <div id="ip"><span>IP Address</span></div>
    </div>
    <div id="entries">
<?
  $res = mysql_query("SELECT date,ip FROM log WHERE owner={$userid} AND action='login' ORDER BY date DESC LIMIT 0,100", $db);
  while ($entry = mysql_fetch_array($res))
  {
    $date = strftime('%A, %B %eth of %Y, %H:%M:%S', $entry['date']);
?>
      <div id="entry">
        <div id="date"><?=$date?></div>
        <div id="ip"><?=$entry['ip']?></div>
      </div>
<?
  }
?>
    </div>
  </div>
</div>
