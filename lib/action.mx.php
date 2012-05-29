<?
  if (!isset($domain['id']))
    exit();

  require_once('zoneparser.php');
  require_once('config.php');

  $zone = new ZoneParser($config['zones_path'] . '/' . $domain['name'] . '.zone');

  if (isset($_GET['do']))
  {
    switch ($_GET['do'])
    {
      case 'add':
      case 'delete':
      case 'edit':
        $do = $_GET['do'];
        break;
      default:
        unset($_GET['do']);
    }
  }

  if (!isset($_GET['do']))
  {
?>
<div id="mxregs">
  <div id="header">
    <h1>Mail eXchange</h1>
    <p>Here you can add, edit or delete your Mail eXchange registers</p>
    <hr />
  </div>
  <form action="panel.php?action=mx&do=add" method="post">
    <div id="addnew">
      <h1>Add a new register</h1>
      <div id="host"><input type="text" name="host" /><span>.<?=$domain['name']?></span></div>
      <div id="type">MX</div>
      <div id="prio"><span>Priority</span><input type="text" name="prio" value="10" /></diV>
      <div id="dest"><input type="text" name="dest" /></div>
      <div id="addbutton"><input type="submit" value="Add" /></div>
    </div>
  </form>
  <hr />
  <div id="content">
    <div id="header">
      <div id="host">Host</div>
      <div id="type">Type</div>
      <div id="prio">Priority</div>
      <div id="dest">Destination</div>
      <div id="dele">Delete</div>
    </div>
    <form action="panel.php?action=mx&do=edit" method="post">
      <div id="entries">
<?
      $records = $zone->GetRecords('MX');
      foreach ($records as $key => $entry)
      {
?>
        <div id="entry">
          <div id="host"><span><?
            if ($entry[0] == '@')
              echo $domain['name'];
            else
              echo $entry[0] . '.' . $domain['name'];
          ?></span></div>
          <div id="type">MX</div>
          <div id="prio"><input type="text" name="prio_<?=$key?>" value="<?=$entry[1][0]?>" /></div>
          <div id="dest"><input type="text" name="reg_<?=$key?>" value="<?=$entry[1][1]?>" /></div>
          <a href="panel.php?action=mx&do=delete&id=<?=$key?>"><div id="dele"><span>Delete</span></div></a>
        </div>
<?
      }

      if (count($records) > 0)
      {
?>
        <div id="submit"><input type="submit" value="Apply changes" /></div>
<?
      }
?>
      </div>
    </div>
  </div>
</div>
<?
  }

  /**
   ** Actions
   **/
  else
  {
    if ($do == 'add' && isset($_POST['host']) && isset($_POST['prio']) && isset($_POST['dest']))
    {
      if (empty($_POST['host']))
        $_POST['host'] = '@';
      if (empty($_POST['prio']) || !ctype_digit($_POST['prio']))
        $_POST['prio'] = 10;
      if (empty($_POST['dest']))
        $_POST['dest'] = $domain['name'] . '.';
      $zone->AddMX($_POST['host'], $_POST['prio'], $_POST['dest']);
      $zone->ApplyChanges();
    }
    else if ($do == 'delete' && isset($_GET['id']))
    {
      $zone->DeleteMX($_GET['id']);
      $zone->ApplyChanges();
    }
    else if ($do == 'edit' && count($_POST) > 0)
    {
      foreach ($_POST as $post => $value)
      {
        if (substr($post, 0, 4) == 'reg_')
        {
          $id = (int)substr($post, 4);
          if (isset($_POST['prio_' . $id]))
          {
            $prio = $_POST['prio_' . $id];
            if (empty($prio) || !ctype_digit($prio))
              $prio = 10;
            if (empty($value))
              $value = $domain['name'] . '.';
            $zone->ChangeMX($id, $prio, $value);
          }
        }
      }
      $zone->ApplyChanges();
    }
?>
  <meta http-equiv="refresh" content="0; /panel.php?action=mx" />
<?
  }
?>
