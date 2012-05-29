<?
  $config = array(
   'db_host'          => 'localhost',
   'db_user'          => 'dns',
   'db_pass'          => 'dnsblu3',
   'db_db'            => 'dns',
   'session_timeout'  => 300,  /* In seconds */
   'token_expiration' => 10  , /* In minutes */
   'real_host'        => 'dns.azulverde.net',
   'mail_from'        => 'dns@azulverde.net',
   'signature'        => 'DNS Control panel - azulverde.net',
   'zones_path'       => '/chroot/dns/etc/bind/sec',
   'restart_rndc'     => true
  );
?>
