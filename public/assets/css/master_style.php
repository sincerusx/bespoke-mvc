<?php
// Enable caching
header('Cache-Control: public');

// Expire in one day
#header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

// Set the correct MIME type, because Apache won't set it for us
header("Content-type: text/css");

$styles = array(
	'bootstrap.css',
	'fonts.css',
	'bootstrap.css',
	'bootstrap.css',
	"style.css",
	'join.css',
	'liquid-slider.css',
	'style-slider.css',
	'animate.css',
	'fractionslider.css',
	'jquery.bxslider.css',
	'main.css',
	'jquery.fancybox.css',

);

$join_styles = array(
	'jquery.fancybox.css',
);

if($body == 'join'){
	foreach($join_styles as $join_style){
		array_push($styles, $join_style);
	}
}


foreach($styles as $style):
	$css .= file_get_contents($style);
endforeach;

// Remove comments
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

// Remove space after colons
$css = str_replace(': ', ':', $css);


// Remove whitespace
$css = str_replace(array(
					   "\r\n",
					   "\r",
					   "\n",
					   "\t",
					   '  ',
					   '    ',
					   '    ',
				   ), '', $css);


// Write everything out
echo($css);