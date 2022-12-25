#!/usr/bin/php
<?php
/*------------------------------------------------------------------------------*/
/* 2022/12/25 (c) Phil353556 github.com	  				        */
/*  email: 51y9oj579@relay.firefox.com                                	        */
/* 										*/
/* This piece of software is as IS with no Warranty. Use it of your own risk!	*/
/* See LICENCE.md file in repository 						*/
/*------------------------------------------------------------------------------*/
/* list of functions in this script 						*/
/* function get_token($content_type,$key)					*/
/* function HandleHeaderLine( $curl, $header_line ) 				*/
/* function hex2rgb($hex) {							*/
/* function create_ecowatt($rgb,$j,$dayofweek,$md)				*/
/* function get_ecowatt($token,$web)						*/
/* function usage()								*/
/*------------------------------------------------------------------------------*/
/* copy paste in base64: id client and id secret information from the website   */
/* If you want to base64 encode yourself use the function base64_encode("....");*/
/* This is the ONLY change to do in this script                                 */
$key = "COPY PASTE IN BASE 64 HERE YOUR ID CLIENT AND ID SECRET"; 
/*------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------*/
$content_type = "application/x-www-form-urlencoded";
$timeout = 0;
$jours = array( "Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi");
/*------------------------------------------------------------------------------*/

/*------------------------------------------------------------------------------*/
/*  get token authorization 							*/
/*------------------------------------------------------------------------------*/
function get_token($content_type,$key)
{

$url = "https://digital.iservices.rte-france.com/token/oauth/";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "content-type: $content_type",
   "Authorization: Basic ".$key,
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($curl);
curl_close($curl);
$array = json_decode($result, true);

return [$array['access_token'],$array['token_type'],$array['expires_in']]; 
}

/*------------------------------------------------------------------------------------------------------------------------------*/
/* To find the retry time returned if more than one call per quarter								*/
/* Found here: Thanks!														*/
/* https://stackoverflow.com/questions/32653598/php-curl-how-to-retrieve-headers-when-using-proxy-curlopt-headerfunction 	*/
/*------------------------------------------------------------------------------------------------------------------------------*/
function HandleHeaderLine( $curl, $header_line ) 
{
	if (str_contains($header_line,'Retry-After'))
    	{
	    $content = explode(":", $header_line, 2);
	    global $timeout;
	    $timeout = $content[1];
    	}
return strlen($header_line);
}

/*------------------------------------------------------------------------------------------------------------------------------*/
/* To convert color values 													*/
/* Found here: Thanks!														*/
/* from : https://kodex.pierrelebedel.fr/php/conversion-hexadecimale-rgb-php/							*/
/*------------------------------------------------------------------------------------------------------------------------------*/
function hex2rgb($hex) {
	$hex = str_replace("#", "", $hex);
	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
	return $rgb;
}
/*-----------------------------------------------------------*/
/* create ecowatt logo in png format                         */
/* $rgb: color values in hex                                 */
/* $j: can be J or J+1 or J+2                                */
/* $dayofweek: day of week                                   */
/* $md: day formatted m/d                                    */
/*-----------------------------------------------------------*/
function create_ecowatt($rgb,$j,$dayofweek,$md)
{
   $h = 100;
   $l = 100;
   $imagepng = imagecreatetruecolor($h, $l);
   imagesavealpha($imagepng, true);

   $trans_colour = imagecolorallocatealpha($imagepng, 0, 0, 0, 127);
   imagefill($imagepng, 0, 0, $trans_colour);

   $rgb = hex2rgb($rgb);
   $color = imagecolorallocate($imagepng, $rgb[0],$rgb[1], $rgb[2] );
   imagefilledellipse($imagepng, $h/2, $l/2, $h*0.95, $l*0.95, $color);
   imagestring( $imagepng, 5,$h/2-10,$l/2-30, $j,4);
   imagestring( $imagepng, 5,$h/2-30,$l/2-10, $dayofweek,4);
   imagestring( $imagepng, 5,$h/2-20,$l/2+10, $md,4);

   imagepng($imagepng,$j.".png");
}

/*------------------------------------------------------------------------------*/
/* Get all information needed							*/
/*------------------------------------------------------------------------------*/
function get_ecowatt($token,$web)
{
global $jours;
global $url;
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Authorization: Bearer ".$token
);

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_HEADERFUNCTION, "HandleHeaderLine");
$result = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ( $httpCode == "200")
 {

 	$decoded_data = json_decode($result, true);
 	$i=0;
 	foreach( $decoded_data as $signal) 
 	{
		foreach( $signal as $newday) 
		{
		echo " ------------------\n";
		$day= $newday['jour'];
		$value= $newday['dvalue'];
		if ( $i == 0 ) { $j = "J"; }
		if ( $i == 1 ) { $j = "J+1"; }
		if ( $i == 2 ) { $j = "J+2"; }
		if ($value == "1" AND $i <3 ) 
			{
			$DAY=date('m-d', strtotime($day));
			$the_date = date('Y-m-d', strtotime($day));
			$name_of_the_day = date("w", strtotime($the_date));
			echo " ".$DAY." ".$jours[$name_of_the_day]." vert\n";
			create_ecowatt('#02f0c6',$j,$jours[$name_of_the_day],$DAY);
			}
		if ($value == "2" AND $i <3)
			{ 
			$DAY=date('m-d', strtotime($day));
			$the_date = date('Y-m-d', strtotime($day));
			$name_of_the_day = date('w', strtotime($the_date));
			echo " ".$DAY." ".$jours[$name_of_the_day]." orange\n";
			create_ecowatt('#f2790f',$j,$jours[$name_of_the_day],$DAY);
			}
		if ($value == "3" AND $i <3)
			{
			$DAY=date('m-d', strtotime($day));
			$the_date = date('Y-m-d', strtotime($day));
			$name_of_the_day = date('w', strtotime($the_date));
			echo " ".$DAY." ".$jours[$name_of_the_day]." rouge\n";
			create_ecowatt('#e63946',$j,$jours[$name_of_the_day],$DAY);
			}
		$i++;
		}
 	}
 }
 else
 {
	global $timeout;
	echo" Retry in ".intval($timeout)." seconds\n";
        echo " ----------------------------------------------------------------------       \n";
 }

}
/*------------------------------------------------------------------------------*/
/* Display the help for this script                                             */
/*------------------------------------------------------------------------------*/
function usage()
{
echo " ---------------------------------------------------------------------- \n";
echo " Usage:  php ecowatt.php [ prod | sandbox ] [ local | web ]             \n";
echo "                                                                        \n";
echo " first argument:                                                        \n";
echo " prod: url for production information will be used                      \n";
echo " sandbox: url for test information will be used                         \n";
echo "                                                                        \n";
echo " second argument:                                                       \n";
echo " local: information will be only displayed localy                       \n";
echo " web: information will be displayed localy, generation for web done     \n";
echo "                                                                        \n";
echo " otherwise usage is displayed                                         \n";
echo " ---------------------------------------------------------------------- \n";
}

/*------------------------------------------------------------------------------*/
/* Main Routine 								*/
/*------------------------------------------------------------------------------*/
$argc = count($argv);
$web = 0;

if ( $argc == 3 ) 
 {
    if ( !str_contains($argv[1],'prod') AND !str_contains($argv[1],'sandbox') )
    {
	    usage();
	    exit;
    }
    if ( !str_contains($argv[2],'local') AND !str_contains($argv[2],'web') )
    {
	    usage();
	    exit;
    }
    if ( $argv[1] == "prod" )
    {
            echo " --------------------------------------------------------------------------------\n";
	    echo " URL used will be the production one                                             \n";
	    /* url production /  maximun of one call per 15 minutes 				     */
  	    $url = "https://digital.iservices.rte-france.com/open_api/ecowatt/v4/signals";
    }
    if ( $argv[1] == "sandbox" )
    {
            echo " --------------------------------------------------------------------------------\n";
	    echo " URL used will be the sandbox one 					           \n";
	    // url sandbox
	    $url = "https://digital.iservices.rte-france.com/open_api/ecowatt/v4/sandbox/signals";
    }
    if ( $argv[2] == "local" )
    {
            echo " ECOWATT Information will be displayed localy  \n";
	    $web = 1;
    }
    if ( $argv[2] == "web" )
    {
            echo " ECOWATT Information will be displayed localy AND generation for web will be done\n";
	    $web = 1;
    }
    [ $access_token, $token_type, $expires_in ] = get_token($content_type,$key);
    get_ecowatt($access_token,$web);
 }
else 
 {
    usage();
 }

/*------------------------------------------------------------------------------*/
?>
