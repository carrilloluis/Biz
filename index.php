<?php

require_once __DIR__ . '/vendor/autoload.php';
use Steampixel\Route;
define('STANDARD_HEADER', 'Content-Type: application/json; charset="UTF-8"');
define('VERSION', '0.0.1.13');

Route::add('/Biz/v1/', function()
{
	$xml = simplexml_load_file("Bussiness_00.xml") or die("Error: Cannot create object");
	header(STANDARD_HEADER);
	echo json_encode([
		'nm' => @strval($xml->empresa->desc),
		'ein' => @strval($xml->empresa->ruc)
	]);
}, 'GET');

Route::add('/Biz/v1/__version__/', function()
{
	header(STANDARD_HEADER);
	echo json_encode([
		'project' => 'Proyecto NONAME',
		'service' => 'μservicio para la gestión de EMPRESAS',
		'version' => 'v' . VERSION,
	]);
}, 'GET');

Route::pathNotFound(function( $path )
{
	http_response_code(404);
	echo "Not found {$path} url";
});

Route::run('/', true, true);


/*

if ($xml === false) {
  echo "Failed loading XML: ";
  // iterate over the errors
  foreach(libxml_get_errors() as $error) {
    echo "<br>", $error->message;
  }
} else {
  print_r($xml);
}
*/

