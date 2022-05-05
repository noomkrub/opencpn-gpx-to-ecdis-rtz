# opencpn-gpx-to-ecdis-rtz
# Path converter

php cli utility to convert openbpn gpx route to furuno ecdis rtz format and vice versa

This program is to be called via commandline

Usage: from gpx to trz

  	php /path/to/script/path.php input.gpx output.rtz step width

option: step , width

	step: minimum distant required between each waypoint (nm)
  
	width: passage width

Usage: from rtz to gpx

  	php /path/to/script/path.php input.rtz output.gpx

# Boundary converter
This script to convert openCPN draw boundary to ecdis userchart.

Require 2 argument, input file name and output file name

Usage: php /path/to/script/boundary.php input.gpx output.rtz
