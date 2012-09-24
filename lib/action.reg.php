<?
  if (!isset($domain['id']))
    exit();

  if (!isset($_GET['t']))
    exit();

  switch ($_GET['t'])
  {
    case 'NS':
    case 'A':
    case 'AAAA':
    case 'CNAME':
    case 'TXT':
      $type = $_GET['t'];
      break;
    default: exit();
  }

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
  <div id="reglist">
    <div id="header">
      <h1><?=$type?> registers</h1>
      <p>Add, edit or delete <?=$type?> registers</p>
      <hr />
    </div>
    <div id="content">
      <form action="panel.php?action=reg&t=<?=$type?>&do=add" method="post">
        <div id="addnew">
          <h1>Add a new register</h1>
          <div id="host"><input type="text" name="host" />.<?=$domain['name']?></div>
          <div id="type"><?=$type?></div>
          <div id="dest"><input type="text" name="dest" /></div>
          <div id="addbutton"><input type="submit" value="Add" /></div>
        </div>
      </form>
      <hr />
      <div id="header">
        <div id="host"><span>Host</span></div>
        <div id="type"><span>Type</span></div>
        <div id="dest"><span>Destination</span></div>
        <div id="dele"><span>Delete</span></div>
      </div>
      <form action="panel.php?action=reg&t=<?=$type?>&do=edit" method="post">
        <div id="entries">
<?
  $records = $zone->GetRecords($type);
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
            <div id="type"><?=$type?></div>
            <div id="dest"><input type="text" name="reg_<?=$key?>" value="<?=$entry[1]?>" /></div>
            <a href="panel.php?action=reg&t=<?=$type?>&do=delete&id=<?=$key?>"><div id="dele"><span>Delete</span></div></a>
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
      </form>
    </div>
  </div>
<?
  }

  /**
   ** Actions
   **/
  else
  {
    if ($do == 'add' && isset($_POST['host']) && isset($_POST['dest']))
    {
      if (empty($_POST['host']))
        $_POST['host'] = '@';
      if (empty($_POST['dest']))
        $_POST['dest'] = $domain['name'] . '.';

      $zone->AddRecord($_POST['host'], $type, $_POST['dest']);
      $zone->ApplyChanges();
    }
    else if ($do == 'delete' && isset($_GET['id']))
    {
      $zone->DeleteRecord($type, $_GET['id']);
      $zone->ApplyChanges();
    }
    else if ($do == 'edit' && count($_POST) > 0)
    {
      foreach ($_POST as $post => $value)
      {
        if (substr($post, 0, 4) == 'reg_')
        {
          $id = (int)substr($post, 4);
          if (empty($value))
            $value = $domain['name'] . '.';
          $zone->ChangeRecord($id, $type, $value);
        }
      }
      $zone->ApplyChanges();
    }
?>
  <meta http-equiv="refresh" content="0; panel.php?action=reg&t=<?=$type?>" />
<?
  }
?>
