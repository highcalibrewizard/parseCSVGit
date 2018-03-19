<?php

///HOW TO USE GENERAL
/*
Simple CSV formatting program. Use excel to insert/replace the correct column namnes, and use this to format (add appropriate quotes etc)
*/

///SETTINGS. CHANGE THESE MANUALLY
$file = 'konstverk_test.csv';  //file name to read. put it in current folder
$delimiter = ';';			//delimiter to read in
$delimiterToWrite = ',';	//delimiter to use when writing
$fileToWrite = 'output.csv';	//output. will be overwritten if exists
define('ENCLOSURE','"');		//if quotes should enclose each element. If they are missing a word like: Blå, vas cant be imported. Leave as empty string if you dont need.
define('PRINT_ARRAY',false);	//debug
define('PRINT_ROWS', false);	//debug
define('OUTPUT_ROW', false);		//debug

///LOGICS
$file = fopen($file, "r");  //open file
$fileToWrite = fopen($fileToWrite, 'w');	//create file to write
$header = array();		//header
$content = array();		//body, i.e remaining rows

echo "IMPORTANT! The things printed out here are unformated, and wont show up in written file<br>";

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

/*
Usage: Run as is (only set file). KEEP AS IS FOR FUTURE USE
This function reads in a CSV-file where the format is: see below, and makes the vocab data list column readable by Sofie 8 (will add '\n' after each word).
It will skip inserting data into anything other than data. It will also skip the first line (since that is column data)
Column
Word1
Word2
...
echoes string of words, separated by \n, enclosed in quotes. You will need to add in appropriate header (vocab:namn,vocab:beskrivning,vocab:data,vocab:links)
as well as namn and possibly links and beskrivning
*/
function makeVocabData() {
	global $file;
	fgets($file);	//skip header
	$list = array();	//empty array to fill
	while(!feof($file)) {		//read in all remaining lines
		$word = trim(fgets($file));	//trims
		$list[] = $word;		
	}
	
	$wordlist = implode('\n',$list);
	echo quoteEncloseElement($wordlist);		//copy and paste tthis into your file...
}


//reads each row, except header and returns them as separat array, exploding with delimiter
function readLines($file) {
	global $content, $delimiter, $header;
	while($row = fgetcsv($file,0,$delimiter)) {
		$content[] = $row;
		
		if(PRINT_ROWS) {
			foreach($row as $element) {		//can get error here if empty... ignore
				echo $element." ";
			}
			echo "<br>";
		}
		
		echo "READ ROW LENGTH ".count($row)."<br>";
		if (count($header) !== count($row)) {
			echo "THE ROW COUNT DOES NOT MATCH HEADER! BREAKING!<br>";
			break;
		}
	}
	
	echo "TOTAL NUM ROWS ".count($content)."<br>";
	
	if(PRINT_ARRAY)
		echo var_export($content,true);
}

/*
Always run this on each element to enclose it with double quotes. KEEP! DO. NOT. DELETE.
*/
function quoteEncloseElement($element) {
	if(empty(ENCLOSURE))
		return $element;
	$element = ENCLOSURE.$element.ENCLOSURE;
	return $element;
}

//writes the old header. no enclosure is needed here, but you can add it in you you need
function writeHeader() {
	global $fileToWrite, $delimiterToWrite, $header;
	fputcsv($fileToWrite, $header, $delimiterToWrite);
		
}

/*
Writes all lines (except header that should be called first) from array data to FILE
*/
function writeLines($data) {
	global $fileToWrite, $delimiterToWrite;
	$counter = 1;
	foreach($data as $row) {
		if (OUTPUT_ROW)
			echo "Writing: ".implode($delimiterToWrite,$row)."<br>";
		$result = fputcsv($fileToWrite, $row, $delimiterToWrite, ENCLOSURE);		//writing CSV. might get error if empty ignore.
		
		if(!$result) {
			echo "CRITICAL ERROR! CAN'T WRITE ROW NUM (row count starts with 1, after header): ".$counter;
			break;
		}
		$counter++;
	}
	
	echo "WROTE A TOTAL OF (excluding header). An empty line will be written at end: ". $counter;

}

/////////////////////////HERE YOU ENABLE DISABLE WHAT TO RUN

	//SIMPLE VOCAB. Will not make output file
//makeVocabData();

	//REFORMAT ENYTHING ELSE
readHeader($file);
readLines($file);
writeHeader($header);
writeLines($content);
	
	//Closes file handlers. Leave these
fclose($file);
fclose($fileToWrite);
?>