<?php
header("content-type:text/plain");
if (php_sapi_name() != "cli") {
	echo "This program is to be called via commandline\n\n";
	echo "Usage: php /path/to/script/convert.php input.rtz output.gpx \n";
	exit;
}
//var_dump($argv);
if(!isset($argv[1])){
echo "Error: Need input file name\nUsage: php /path/to/script/convert.php input.rtz output.gpx\n\n";
	exit;
}
if(!isset($argv[2])){
echo "Error: Need output file name\nUsage: php /path/to/script/convert.php input.rtz output.gpx\n\n";
	exit;
}
$xml=simplexml_load_file($argv[1]);
$c=count($xml->waypoints->waypoint);
$t=$xml->routeInfo[0]['routeName'];
$op='<?xml version="1.0"?>
<gpx version="1.1" creator="OpenCPN" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.topografix.com/GPX/1/1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www8.garmin.com/xmlschemas/GpxExtensionsv3.xsd" xmlns:opencpn="http://www.opencpn.org">
  <rte>
    <name>'.$t.'</name>';
$op.='    <extensions>
      <opencpn:guid></opencpn:guid>
      <opencpn:viz>1</opencpn:viz>
      <opencpn:start>'.$t.'</opencpn:start>
      <opencpn:end>'.$t.'</opencpn:end>
      <opencpn:planned_speed>6.00</opencpn:planned_speed>
      <opencpn:planned_departure></opencpn:planned_departure>
      <opencpn:time_display>PC</opencpn:time_display>
      <gpxx:RouteExtension>
        <gpxx:IsAutoNamed>false</gpxx:IsAutoNamed>
      </gpxx:RouteExtension>
    </extensions>';
    $op.="\n";
for ($i=0;$i<count($xml->waypoints->waypoint);$i++)
{
	//print_r($xml->waypoints->waypoint[$i]->position[lat]);
$op.='    <rtept lat="'.$xml->waypoints->waypoint[$i]->position['lat'].'" lon="'.$xml->waypoints->waypoint[$i]->position['lon'].'">
      <time></time>
      <name>';
      $op.=$i+1;
      $op.='</name>
      <sym>diamond</sym>
      <type>WPT</type>
      <extensions>
        <opencpn:guid></opencpn:guid>
        <opencpn:auto_name>1</opencpn:auto_name>
        <opencpn:arrival_radius>0.001</opencpn:arrival_radius>
        <opencpn:waypoint_range_rings visible="false" number="0" step="1" units="0" colour="#FF0000" />
        <opencpn:scale_min_max UseScale="false" ScaleMin="2147483646" ScaleMax="0" />
      </extensions>
    </rtept>';
$op.="\n";
}
$op.='  </rte>
</gpx>';
 // echo $op;
$f_op=fopen($argv[2],'w');
fwrite($f_op,$op);
fclose($f_op);
echo "Convert ".$t."\n";
echo "Convert $c\n waypoints";
echo "Data save to ".$argv[2]."\n\n";
echo "Use data as your own risk ^_^\n\n";
