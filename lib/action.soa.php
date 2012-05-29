<?
  if (!isset($domain['id']))
    exit();

  require_once('zoneparser.php');
  require_once('config.php');
  $zone = new ZoneParser($config['zones_path'] . '/' . $domain['name'] . '.zone');

  if (isset($_POST['ttl']) && isset($_POST['ns']) && isset($_POST['email']) && isset($_POST['refresh']) &&
      isset($_POST['retry']) && isset($_POST['expiry']) && isset($_POST['min']))
  {
    $zone->SetTTL($_POST['ttl']);
    $zone->SetSOA($_POST['ns'], $_POST['email'], $_POST['refresh'],
                  $_POST['retry'], $_POST['expiry'], $_POST['min']);
    $zone->ApplyChanges();
?>
  <div id="confirmation_message">SOA register changed successfuly</div>
<?
  }

  $ttl = $zone->GetRecords('TTL');
  $soa = $zone->GetRecords('SOA');
?>
<div id="soa">
  <div id="header">
    <h1>Domain SOA values</h1>
    <p>Here you can change the SOA values for your domain. This is the most important register of each domain, allowing to set the main nameserver, root email address and time values for each option.</p>
    <hr />
  </div>
  <div id="content">
    <form name="soa" action="panel.php?action=soa" method="post" onsubmit="return checkSOA();">
      <div id="ttl"><span>Default domain TTL</span><input type="text" name="ttl" value="<?=$ttl?>" /></div>
      <div id="ns"><span>Default nameserver</span><input type="text" name="ns" value="<?=$soa['ns']?>" /></div>
      <div id="email">
        <span id="maintext">Root email address</span>
        <input type="text" name="email" value="<?=$soa['email']?>" />
        <span id="email_domain">@<?=$domain['name']?></span>
      </div>
      <div id="refresh"><span>Refresh time</span><input type="text" name="refresh" value="<?=$soa['refresh']?>" /></div>
      <div id="retry"><span>Retry time</span><input type="text" name="retry" value="<?=$soa['retry']?>" /></div>
      <div id="expiry"><span>Expiry time</span><input type="text" name="expiry" value="<?=$soa['expiry']?>" /></div>
      <div id="min"><span>Negative caching time</span><input type="text" name="min" value="<?=$soa['min']?>" /></div>
      <div id="submit"><input type="submit" value="Change" /></div>
    </form>
  </div>
</div>

<script type="text/javascript"><!--
  function checkSOA()
  {
    if (!document.soa.ttl.value)
    {
      alert('Enter a TTL value');
      document.soa.ttl.focus();
      return false;
    }
    if (!document.soa.ns.value)
    {
      alert('Enter the default nameserver');
      document.soa.ns.focus();
      return false;
    }
    if (!document.soa.email.value)
    {
      alert('Enter the root email account name');
      document.soa.email.focus();
      return false;
    }
    if (!document.soa.refresh.value)
    {
      alert('Enter a refresh value');
      document.soa.refresh.focus();
      return false;
    }
    if (!document.soa.retry.value)
    {
      alert('Enter a retry value');
      document.soa.retry.focus();
      return false;
    }
    if (!document.soa.expiry.value)
    {
      alert('Enter a expiry value');
      document.soa.expiry.focus();
      return false;
    }
    if (!document.soa.min.value)
    {
      alert('Enter a negative caching time');
      document.soa.min.focus();
      return false;
    }
    return true;
  }

  document.soa.ttl.focus();
// --></script>
