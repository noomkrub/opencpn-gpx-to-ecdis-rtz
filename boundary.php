<?php

header("content-type:text/plain");

if (php_sapi_name() != "cli") {
	echo "Utility to convert opencpn draw boundary to ecdis user chart\n";
  echo "This program is to be called via commandline\n\n";
  echo "Usage: php /path/to/script/convert.php input.gpx output.xml\n";
  exit;
}

if(!isset($argv[1])){
echo "Error: Need input file name\nUsage: php /path/to/script/convert.php input.gpx output.xml\n\n";
  exit;
}
if(!isset($argv[2])){
echo "Error: Need output file name\nUsage: php /path/to/script/convert.php input.gpx output.xml\n\n";
  exit;
}

$argv[1]="survey2.gpx";
$file=file_get_contents($argv[1]);
$file=str_replace("opencpn:","",$file);
$xml_in=simplexml_load_string($file);
//$xml_in=simplexml_load_file($argv[1]);
$chart_name=explode(".",$xml_in->path->name);

$op='<?xml version="1.0" encoding="UTF-8"?>
<!--userchart node-->
<userchart name="';
$op.=$chart_name[0];
$op.='" description="" version="1.0">
  <!--userchart area-->
  <areas>
    <area name="';
	$op.=$chart_name[0];
	$op.='" description="">
      <position>'."\n";

for ($i=0;$i<count ( $xml_in->path->ODPoint );$i++){
//<vertex id="1" latitude="44.974632" longitude="12.572256"/>
	$op.= '        <vertex id="';
	$op.= $i+1;
	$op.= '" latitude="';
	$op.= ( $xml_in->path->ODPoint[$i]->attributes()->lat);
	$op.= '" longitude="';
	$op.= ( $xml_in->path->ODPoint[$i]->attributes()->lon);
	$op.= '"/>';
	$op.= "\n";
}

$op.='      </position>
      <type checkDanger="0" displayRadar="0" hasNotes="0" notesType="0"/>
    </area>
  </areas>
</userchart>';

$f_op=fopen($argv[2],'w');
fwrite($f_op,$op);
fclose($f_op);

//echo $op;
echo "\nData save to ".$argv[2]."\n";
echo "Use data as your own risk ^_^\n\n";
