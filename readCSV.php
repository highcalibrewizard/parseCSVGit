<?php

///HOW TO USE GENERAL
/*

*/

///SETTINGS. CHANGE THESE MANUALLY
$file = 'fileToRead.csv';  //file name. put it in current folder
$delimiter = ',';			//delimiter
$typeToUse = "vocab";		//the current datatype, see below
$fileToWrite = 'output.csv';

///DATA TYPES
$vocabHead = array("vocab:namn","vocab:beskrivning","vocab:links");		//vocab links has underfield {...}

///LOGICS
$file = fopen($file, "r");  //open file
$header = array();		//header
$content = array();		//body, i.e remaining rows

//fgets returns 1 line and keeps track internally. So next fgets after this one will return the next line!
function readHeader($file) {
	global $header, $delimiter;
	
	$header = fgetcsv($file,0,$delimiter);
	foreach($header as $head) {
		echo $head;
	}
	echo "<br>";
	
	echo "READ HEADER LENGTH ".count($header)."<br>";
}

//reads each row, except header and returns them as separat array, exploding with delimiter
function readLines($file) {
	global $content, $delimiter;
	while(!feof($file)) {	//checking EOF
		$row = fgetcsv($file,0,$delimiter);
		$content[] = $row;
		
		foreach($row as $element) {
			echo $element;
		}
		echo "<br>";
		
		echo "READ ROW LENGTH ".count($row)."<br>";
	}
}

/*
Writes all lines from array data to FILE
*/
function writeLines($data) {
	global $fileToWrite, $delimiter;
	
	$file = fopen($fileToWrite, 'w');				//making file. Will rewrite existing
	foreach($data as $row)
		fputcsv($file, $row, $delimiter);		//writing CSV

}

readHeader($file);
readLines($file);
writeLines($content);
?>