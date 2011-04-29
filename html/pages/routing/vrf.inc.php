<?php

if ($_SESSION['userlevel'] >= '5') { 

  if(!isset($_GET['optb'])) { $_GET['optb'] = "all"; }
  if(!isset($_GET['optc'])) { $_GET['optc'] = "basic"; }

  print_optionbar_start();

  echo('<span style="font-weight: bold;">VRF</span> &#187; ');

  if ($_GET['opta'] == "vrf" && $_GET['optb'] == "all") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/all/'.$_GET['optc'].'/">All</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optb'] == "all") { echo("</span>"); }
  echo(' | ');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "basic") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/'.$_GET['optb'].'/basic/">Basic</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "basic") { echo("</span>"); }
  echo(" | ");
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "details") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/'.$_GET['optb'].'/details/">Details</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "details") { echo("</span>"); }
  echo(" | Graphs: ( ");
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "bits") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/'.$_GET['optb'].'/bits/">Bits</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "bits") { echo("</span>"); }
  echo(" | ");
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "upkts") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/'.$_GET['optb'].'/upkts/">Packets</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "upkts") { echo("</span>"); }
  echo(" | ");
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "nupkts") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/'.$_GET['optb'].'/nupkts/">NU Packets</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "nupkts") { echo("</span>"); }
  echo(" | ");
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "errors") { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="routing/vrf/'.$_GET['optb'].'/errors/">Errors</a>');
  if ($_GET['opta'] == "vrf" && $_GET['optc'] == "errors") { echo("</span>"); }
  
  echo(" )");

  print_optionbar_end();

if($_GET['optb'] == "all" ) {

  echo("<div style='margin: 5px;'><table border=0 cellspacing=0 cellpadding=5 width=100%>");
  $i = "1";
  $vrf_query = mysql_query("SELECT * FROM `vrfs` GROUP BY `mplsVpnVrfRouteDistinguisher`");
  while ($vrf = mysql_fetch_assoc($vrf_query))
  {
    if (!is_integer($i/2)) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }
    echo("<tr valign=top bgcolor='$bg_colour'>");
    echo("<td width=240><a class=list-large href='routing/vrf/".$vrf['mplsVpnVrfRouteDistinguisher']."/".$_GET['optc']."/'>" . $vrf['vrf_name'] . "</a><br /><span class=box-desc>" . $vrf['mplsVpnVrfDescription'] . "</span></td>");
    echo("<td width=100 class=box-desc>" . $vrf['mplsVpnVrfRouteDistinguisher'] . "</td>");
    #echo("<td width=200 class=box-desc>" . $vrf['mplsVpnVrfDescription'] . "</td>");
    echo("<td><table border=0 cellspacing=0 cellpadding=5 width=100%>");
    $devices = mysql_query("SELECT * FROM `vrfs` AS V, `devices` AS D WHERE `mplsVpnVrfRouteDistinguisher` = '".$vrf['mplsVpnVrfRouteDistinguisher']."' AND D.device_id = V.device_id");
    $x=1;
    while ($device = mysql_fetch_assoc($devices))
    {
      if (!is_integer($i/2))
      {
        if (!is_integer($x/2)) { $dev_colour = $list_colour_a_a; } else { $dev_colour = $list_colour_a_b; }
      } else {
        if (!is_integer($x/2)) { $dev_colour = $list_colour_b_b; } else { $dev_colour = $list_colour_b_a; }
      }
      echo("<tr bgcolor='$dev_colour'><td width=150>".generate_device_link($device, shorthost($device['hostname'])));

      if ($device['vrf_name'] != $vrf['vrf_name']) { echo("<a href='#' onmouseover=\" return overlib('Expected Name : ".$vrf['vrf_name']."<br />Configured : ".$device['vrf_name']."', CAPTION, '<span class=list-large>VRF Inconsistency</span>' ,FGCOLOR,'#e5e5e5', BGCOLOR, '#c0c0c0', BORDER, 5, CELLPAD, 4, CAPCOLOR, '#050505');\" onmouseout=\"return nd();\"> <img align=absmiddle src=images/16/exclamation.png></a>"); }
      echo("</td><td>");
      $ports = mysql_query("SELECT * FROM `ports` WHERE `ifVrf` = '".$device['vrf_id']."' and device_id = '".$device['device_id']."'");
      unset($seperator);

      while ($port = mysql_fetch_assoc($ports))
      {
        $port = array_merge ($device, $port);

        switch ($_GET['optc'])
        {
          case 'bits':
          case 'upkts':
          case 'nupkts':
          case 'errors':
            $port['width'] = "130";
            $port['height'] = "30";
            $port['from'] = $day;
            $port['to'] = $now;
            $port['bg'] = "#".$bg;
            $port['graph_type'] = "port_".$_GET['optc'];
            echo("<div style='display: block; padding: 3px; margin: 3px; min-width: 135px; max-width:135px; min-height:75px; max-height:75px;
               text-align: center; float: left; background-color: ".$list_colour_b_b.";'>
               <div style='font-weight: bold;'>".makeshortif($port['ifDescr'])."</div>");
            generate_port_thumbnail($port);
            echo("<div style='font-size: 9px;'>".truncate(short_port_descr($port['ifAlias']), 22, '')."</div>
              </div>");
            break;

          default:
            echo($seperator.generate_port_link($port,makeshortif($port['ifDescr'])));
            $seperator = ", ";
            break;
         }
       }
       echo("</td></tr>");
       $x++;
     } // End While

     echo("</table></td>");

     $i++;
   }
   echo("</table></div>");

} else {

  echo("<div style='background: $list_colour_b; padding: 10px;'><table border=0 cellspacing=0 cellpadding=5 width=100%>");
  $vrf_query = mysql_query("SELECT * FROM `vrfs` WHERE mplsVpnVrfRouteDistinguisher = '".$_GET['optb']."'");
  $vrf = mysql_fetch_assoc($vrf_query);
  echo("<tr valign=top bgcolor='$bg_colour'>");
  echo("<td width=200 class=list-large><a href='routing/vrf/".$vrf['mplsVpnVrfRouteDistinguisher']."/".$_GET['optc']."/'>" . $vrf['vrf_name'] . "</a></td>");
  echo("<td width=100 class=box-desc>" . $vrf['mplsVpnVrfRouteDistinguisher'] . "</td>");
  echo("<td width=200 class=box-desc>" . $vrf['mplsVpnVrfDescription'] . "</td>");
  echo("</table></div>");

  $devices = mysql_query("SELECT * FROM `vrfs` AS V, `devices` AS D WHERE `mplsVpnVrfRouteDistinguisher` = '".$vrf['mplsVpnVrfRouteDistinguisher']."' AND D.device_id = V.device_id");
  $x=1;

  while ($device = mysql_fetch_assoc($devices))
  {
    $hostname = $device['hostname'];
    #if (!is_integer($x/2)) { $device_colour = $list_colour_a; } else { $device_colour = $list_colour_b; }
    echo("<table cellpadding=7 cellspacing=0 class=devicetable width=100%>");
    include("includes/device-header.inc.php");
    echo("</table>");
    $ports = mysql_query("SELECT * FROM `ports` WHERE `ifVrf` = '".$device['vrf_id']."' and device_id = '".$device['device_id']."'");
    unset($seperator);
    echo("<table cellspacing=0 cellpadding=7>");
    $i=1;
    while ($interface = mysql_fetch_assoc($ports))
    {
      if (!is_integer($x/2))
      {
        if (is_integer($i/2)) { $int_colour = $list_colour_a_a; } else { $int_colour = $list_colour_a_b; }
      } else {
        if (is_integer($i/2)) { $int_colour = $list_colour_b_b; } else { $int_colour = $list_colour_b_a; }
      }
      include("includes/print-interface.inc.php");
      $i++;
    }
    $x++;
    echo("</table>");
    echo("<div style='height: 10px;'></div>");
  }

}

} else {

  include("includes/error-no-perm.inc.php");

} ## End Permission if

?>