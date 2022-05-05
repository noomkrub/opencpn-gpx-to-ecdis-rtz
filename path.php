<?php
header("content-type:text/plain");
if (php_sapi_name() != "cli") {
  echo "This program is to be called via commandline\n\n";
  echo "Usage: php /path/to/script/convert.php input.gpx output.rtz step width\n";
  echo "For AS should be \"php /var/www/html/ocpn_gpx_to_ecdis/convert.php input.gpx output.rtz\"\n";
  echo "option: step width\n\tstep: distant between each waypoint\n\twidth: route width";
  exit;
}

if(!isset($argv[1])){
echo "gpx <> rtz converter\nThis program to convert between opencpn gpx and ecdis rtz.\n";
echo "Error: Need input file name\nUsage: php /path/to/script/convert.php input.gpx(rtz) output.rtz(gpx)\n\n";
  exit;
}
if(!isset($argv[2])){
echo "gpx <> rtz converter\nThis program to convert between opencpn gpx and ecdis rtz.\n";
echo "Error: Need output file name\nUsage: php /path/to/script/convert.php input.gpx(rtz) output.rtz(gpx)\n\n";
  exit;
}
if (isset($argv[3])){$space=$argv[3];}else{$space=0;}
if (isset($argv[4])){$width=$argv[4];}else{$width=0.37*2;}

$path=pathinfo($argv[1]);
echo $path['extension'];
if ($path['extension']=="gpx"){
	$xml=simplexml_load_file($argv[1]);

	$op="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!--route node-->\n<route version=\"1.0\" xmlns=\"http://www.cirm.org/RTZ/1/0\">\n  <!--route node-->\n";
	$op.="  <routeInfo routeName=\"";
	$op.=$xml->trk->name;
	$op.='" optimizationMethod="MAX speed">
		<extensions>
		  <extension manufacturer="Furuno" name="AdditionalRouteInfo" version="1.0">
			<property income="0" channelLimitMode="0" safetyContour="8" ukcLimit="30.000000"/>
		  </extension>
		</extensions>
	  </routeInfo>
	  <!--waypoints node-->
	  <waypoints>';
	$wpt_num=1;
	foreach ($xml->trk->trkseg->trkpt as $wpt){
	  if ($wpt_num==1){
		$op.='
		<!--No.'.$wpt_num.' waypoint-->
		<waypoint id="'.$wpt_num.'" name="" radius="0.010000">
		  <position lat="';
		  $op.=$wpt['lat'];
		  $op.='" lon="';
		  $op.=$wpt['lon'];
		  $op.='"/>
		</waypoint>';
		  $lastlat=abs(floatval($wpt['lat']));
		  $lastlon=abs(floatval($wpt['lon']));
		  $wpt_num++;
		}
	  else {
		$curlat=abs(floatval($wpt['lat']));
		$curlon=abs(floatval($wpt['lon']));
		$dlat = abs(floatval($curlat)-floatval($lastlat));
		$dlon = abs(floatval($curlon)-floatval($lastlon));
		$dist = sqrt(($dlat*$dlat) + ($dlon*$dlon));
		$dist_mi=$dist*60;
		if ($dist_mi >= $space){
		  $op.='
		<!--No.'.$wpt_num.' waypoint-->
		<waypoint id="'.$wpt_num.'" name="" radius="0.010000">
		  <position lat="';
		  $op.=$wpt['lat'];
		  $op.='" lon="';
		  $op.=$wpt['lon'];
		  $op.='"/>
		  <leg portsideXTD="';
		  $op.=$width/2;
		  $op.='" starboardXTD="';
		  $op.=$width/2;
		  $op.='" safetyContour="8" geometryType="Loxodrome" speedMax="11.000000" draughtForward="10.000000" draughtAft="10.000000" staticUKC="30.000000"/>
		  <extensions>
			<extension manufacturer="Furuno" name="AdditionalLegInfo" version="1.0">
			  <property margin="0.000000" parallelLine1="0.000000" parallelLine2="0.000000"/>
			</extension>
		  </extensions>
		</waypoint>';
			$lastlat=$curlat;
			$lastlon=$curlon;
			$wpt_num++;
		  }
		} // end else

	  } //end foreach

	$op.='
	  </waypoints>
	  <!--schedules node-->
	  <schedules/>
	</route>';

	$f_op=fopen($argv[2],'w');
	fwrite($f_op,$op);
	fclose($f_op);
	echo "Convert ".$xml->trk->name."\n";
	echo "Convert $wpt_num waypoints\n";
	echo "Data save to ".$argv[2]."\n\n";
	echo "Use data as your own risk ^_^\n\n";
	}

else if ($path['extension']=="rtz"){
	$xml=simplexml_load_file($argv[1]);
	$op= '
	<?xml version="1.0"?>
	<gpx version="1.1" creator="OpenCPN" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.topografix.com/GPX/1/1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" xmlns:opencpn="http://www.opencpn.org">
	  <rte>
	    <name>'.$xml->routeInfo["routeName"].'</name>
	    <extensions>
	      <opencpn:guid>7658ffff-7e02-4fc1-9d2c-cc8940c4ffff</opencpn:guid>
	      <opencpn:viz>1</opencpn:viz>
	      <opencpn:planned_speed>6.00</opencpn:planned_speed>
	      <opencpn:time_display>PC</opencpn:time_display>
	      <gpxx:RouteExtension>
	        <gpxx:IsAutoNamed>false</gpxx:IsAutoNamed>
	      </gpxx:RouteExtension>
	    </extensions>
	';

	foreach ($xml->waypoints->waypoint as $wpt){
	    $op.= '<rtept lat="'.$wpt->position["lat"].'" lon="'.$wpt->position["lon"].'">
	      <time></time>
	      <name>'.$wpt["id"].'</name>
	      <sym>diamond</sym>
	      <type>WPT</type>
	      <extensions>
	        <opencpn:guid></opencpn:guid>
	        <opencpn:auto_name>1</opencpn:auto_name>
	        <opencpn:arrival_radius>0.050</opencpn:arrival_radius>
	        <opencpn:waypoint_range_rings visible="false" number="0" step="1" units="0" colour="#FF0000" />
	        <opencpn:scale_min_max UseScale="false" ScaleMin="2147483646" ScaleMax="0" />
	      </extensions>
	    </rtept>
	    ';
		}
	$op.= "    <trkseg>\n";
	foreach ($xml->waypoints->waypoint as $wpt){
		$op.= '      <trkpt lat="'.$wpt->position["lat"].'" lon="'.$wpt->position["lon"].'">
	        <time />'."\n";
		}

	$op.= '  </rte>
	</gpx>
	';

	$f_op=fopen($argv[2],'w');
	fwrite($f_op,$op);
	fclose($f_op);
	echo "Convert ".$argv[1]."\n";
	echo "Data save to ".$argv[2]."\n\n";
	echo "Use data as your own risk ^_^\n\n";
}

