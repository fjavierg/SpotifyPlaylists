<?php

/*
* Copyright 2013 Javier Gómez
* Version 1. 
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
* Extract length, title and artist from
* a Spotify URI.
*/
function extractSpotify($uri,$outputFile=NULL){

  $id = substr(strrchr($uri, ":"), 1);

  $url = "http://ws.spotify.com/lookup/1/.json?uri=" . $uri;
  $json = file_get_contents($url);
  $data = json_decode($json, FALSE);

  $line = "EXTINF:" . $data->track->length ."," . $data->track->artists[0]->name . " - " . $data->track->name . "\n";
  $line2 = "http://open.spotify.com/track/" . $id . "\n";
  
  if($outputFile === NULL){  
  	echo $line;
	echo $line2;
  } else {
		file_put_contents($outputFile,$line,FILE_APPEND);
		file_put_contents($outputFile,$line2,FILE_APPEND);
	}
}


switch ($argc) {
	case 2:
		$outputFile = NULL;
		break;
	case 3:
		$outputFile = $argv[2];
		if(file_exists($outputFile)){
			echo "File $outputFile already exists!";
			exit(1);
		}
		break;
	default:
		echo "Usage: php -f $argv[0] INPUT_FILE [OUTPUT_FILE]\n";
		exit(1);
		break;
}

$spotifyURIList = file_get_contents($argv[1]);

if ($spotifyURIList === FALSE) {
	echo "INPUT_FILE Not found.\n";
	exit(1);
	break;
} else {
/**
* Write File header
*/
  if($outputFile === NULL){  
  	echo "#EXTM3U\n";
  } else {
		file_put_contents($outputFile,"#EXTM3U\n",FILE_APPEND);
	}

	foreach(explode("\n",$spotifyURIList) as $spotifyURI){
		extractSpotify(trim($spotifyURI),$outputFile);
	}
}
