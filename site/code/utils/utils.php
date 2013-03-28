<?php

function debug($var)
{
	if(!DEBUG)
		return;
	
	$trace = debug_backtrace(false);
	$file = $trace[0]['file'];
	$line = $trace[0]['line'];
	
	echo "<pre>";
	
	if($file)
		echo "<em>file:</em> <span style='color: red;'>" . $file . "</span> ";
		
	if($line)
		echo "<em>line:</em> <span style='color: red;'>" . $line . "</span>";

	if($file || $line)
		echo "<br />";
	
	print_r($var);
	
	echo "</pre>";
}

function generateTag($tag, $data)
{
	global $system;
	
	$output = '<'.$tag.' ';
	foreach($data as $key => $value)
	{
		if ($key == 'href')
			$value = $system->base_url.$value;
		
		$output .= $key.'="'.$value.'" ';
	}
	$output .= '/>';
	
	return $output;
}
