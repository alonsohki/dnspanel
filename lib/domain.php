<div id="domain">
  <div id="header">
    <h1><?=$domain['name']?></h1>
  </div>
  <div id="content">
    <ul>
      <li id="reg_soa"><span><a href="panel.php?action=soa">Change SOA values</a></span></li>
      <li id="reg_ns"><span><a href="panel.php?action=reg&t=NS">NS registers</a></span></li>
      <li id="reg_a"><span><a href="panel.php?action=reg&t=A">Address registers</a></span></li>
      <li id="reg_aaaa"><span><a href="panel.php?action=reg&t=AAAA">IPv6 registers</a></span></li>
      <li id="reg_cname"><span><a href="panel.php?action=reg&t=CNAME">CName registers</a></span></li>
      <li id="reg_mx"><span><a href="panel.php?action=mx">Mail eXchange</a></span></li>
      <li id="reg_txt"><span><a href="panel.php?action=reg&t=TXT">Text registers</a></span></li>
    </ul>
  </div>
</div>
