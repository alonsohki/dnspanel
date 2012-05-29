<?
require_once('config.php');

class ZoneParser
{
  private $records = array('TTL' => 0,
                           'SOA' => array(),
                           'NS' => array(),
                           'A' => array(),
                           'AAAA' => array(),
                           'CNAME' => array(),
                           'MX' => array(),
                           'TXT' => array()
                          );

  private $file;

  public function __construct($file)
  {
    $this->ParseFile($file);
  }

  public function ParseFile($file)
  {
    $this->file = $file;
    $data = file($file) or die('Unable to read from ' . $file);

    foreach ($data as $line)
    {
      $words = split(' ', trim($line));

      if ($words[0] == '$TTL')
      {
        /* Special register */
        $this->records['TTL'] = $words[1];
      }
      else
      {
        switch ($words[2])
        {
          case 'SOA':
          {
            $this->records['SOA']['ns'] = $words[3];
            $this->records['SOA']['email'] = $words[4];
            $this->records['SOA']['serial'] = $words[6];
            $this->records['SOA']['refresh'] = $words[7];
            $this->records['SOA']['retry'] = $words[8];
            $this->records['SOA']['expiry'] = $words[9];
            $this->records['SOA']['min'] = $words[10];
            break;
          }

          case 'A':
          case 'AAAA':
          case 'CNAME':
          case 'NS':
          {
            $this->records[$words[2]][] = array($words[0], $words[3]);
            break;
          }

          case 'MX':
          {
            $this->records['MX'][] = array($words[0], array($words[3], $words[4]));
            break;
          }

          case 'TXT':
          {
            $key = $words[0];
            $value = str_replace('"', '', join(' ', array_slice($words, 3)));
            $this->records['TXT'][] = array($key, $value);
          }
        }
      }
    }

    sort($this->records['NS']);
    sort($this->records['A']);
    sort($this->records['AAAA']);
    sort($this->records['CNAME']);
    sort($this->records['MX']);
    sort($this->records['TXT']);
  }

  private function CheckValidDNS($type)
  {
    foreach ($this->records[$type] as $key => $value)
    {
      $host = substr($value[0], 0, 25);
      $newhost = '';
      $val = substr($value[1], 0, 25);
      $newval = '';

      if ($host == '@')
      {
        $newhost = $host;
      }
      else
      {
        $len = strlen($host);
        for ($i = 0; $i < $len; $i++)
        {
          if (ctype_alnum($host[$i]) || $host[$i] == '.' || $host[$i] == '*' || $host[$i] == '_')
            $newhost .= $host[$i];
        }
      }

      $len = strlen($val);
      for ($i = 0; $i < $len; $i++)
      {
        if ($type == 'A')
        {
          if (ctype_digit($val[$i]) || $val[$i] == '.')
            $newval .= $val[$i];
        }
        else if ($type == 'AAAA')
        {
          if (ctype_xdigit($val[$i]) || $val[$i] == '.' || $val[$i] == ':')
            $newval .= $val[$i];
        }
        else if ($type == 'TXT')
        {
          if (ctype_alnum($val[$i]) || $val[$i] == '.' || $val[$i] == ' ')
            $newval .= $val[$i];
        }
        else if (ctype_alnum($val[$i]) || $val[$i] == '.')
        {
            $newval .= $val[$i];
        }
      }

      $this->records[$type][$key] = array($newhost, $newval);
    }
  }

  private function FixRecords()
  {
    $this->CheckValidDNS('A');
    $this->CheckValidDNS('AAAA');
    $this->CheckValidDNS('CNAME');
    $this->CheckValidDNS('NS');
    $this->CheckValidDNS('TXT');
  }

  public function ApplyChanges()
  {
    global $config;

    /* Update the serial number */
    $this->records['SOA']['serial']++;

    $this->FixRecords();

    $fp = fopen($this->file, 'wb') or die('Unable to open the file for reading');
    fwrite($fp, "\$TTL {$this->records['TTL']}\n");
    fwrite($fp, "@ IN SOA {$this->records['SOA']['ns']} {$this->records['SOA']['email']} ( " .
                $this->records['SOA']['serial'] . ' ' .
                $this->records['SOA']['refresh'] . ' ' .
                $this->records['SOA']['retry'] . ' ' .
                $this->records['SOA']['expiry'] . ' ' .
                $this->records['SOA']['min'] . " )\n");

    foreach (array('NS', 'A', 'AAAA', 'CNAME') as $type)
    {
      foreach ($this->records[$type] as $reg)
      {
        fwrite($fp, "{$reg[0]} IN {$type} {$reg[1]}\n");
      }
    }

    foreach ($this->records['MX'] as $reg)
    {
      fwrite($fp, "{$reg[0]} IN MX {$reg[1][0]} {$reg[1][1]}\n");
    }

    foreach ($this->records['TXT'] as $reg)
    {
      fwrite($fp, "{$reg[0]} IN TXT \"{$reg[1]}\"\n");
    }

    fclose($fp);

    if ($config['restart_rndc'] == true)
    {
      if (system('lib/rndc_reload &>/dev/null'))
      {
        echo "<b><span style='color: red'>Error trying to reload bind zones</span></b>";
        exit();
      }
    }
  }

  public function ChangeMX($id, $prio, $value)
  {
    $this->records['MX'][$id][1] = array($prio, $value);
  }

  public function AddMX($host, $prio, $value)
  {
    $this->records['MX'][] = array($host, array($prio, $value));
    sort($this->records['MX']);
  }

  public function DeleteMX($id)
  {
    unset($this->records['MX'][$id]);
  }

  public function ChangeRecord($id, $type, $value)
  {
    $this->records[$type][$id][1] = $value;
  }

  public function AddRecord($host, $type, $value)
  {
    $this->records[$type][] = array($host, $value);
    sort($this->records[$type]);
  }

  public function DeleteRecord($type, $id)
  {
    unset($this->records[$type][$id]);
  }

  public function GetRecords($type)
  {
    return $this->records[$type];
  }

  public function SetTTL($value)
  {  $this->records['TTL'] = $value;
  }

  public function SetSOA($ns, $email, $refresh, $retry, $expiry, $min)
  {
    $this->records['SOA']['ns'] = $ns;
    $this->records['SOA']['email'] = $email;
    $this->records['SOA']['refresh'] = $refresh;
    $this->records['SOA']['retry'] = $retry;
    $this->records['SOA']['expiry'] = $expiry;
    $this->records['SOA']['min'] = $min;
  }
}
?>
