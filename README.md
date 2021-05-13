# opencpn-gpx-to-ecdis-rtz
php cli utility to convert openbpn gpx route to furuno ecdis rtz format and vice versa

This program is to be called via commandline

Usage: from gpx to trz

  	php /path/to/script/convert.php input.gpx output.rtz step width

option: step , width

	step: minimum distant required between each waypoint (nm)
  
	width: passage width

Usage: from rtz to gpx

  	php /path/to/script/convert.php input.rtz output.gpx
