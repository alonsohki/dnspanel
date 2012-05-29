<?
  require_once('config.php');
  $db = @mysql_connect($config['db_host'], $config['db_user'], $config['db_pass'])
    or die("<b>Error connecting to 'mysql://{$config['db_user']}@{$config['db_host']}/{$config['db_db']}':</b> " . mysql_error());
  mysql_select_db($config['db_db'], $db);
?>
