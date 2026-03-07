<?php

/*
 * @author Luis Carrillo Gutiérrez
 * @date Jun/2014 - Abr/2021
 * @version	0.0.1
 */

require_once __DIR__ . '/vendor/autoload.php';
use Steampixel\Route;
define('STANDARD_HEADER', 'Content-Type: application/json; charset="UTF-8"');
define('FILEPATH', '/tmp/Biz.xml');
define('VERSION', '0.0.1.13');

function
uuid():string
{
	return sprintf('%08x-%04x-%04x-%02x%02x-%012x',
		mt_rand(),
		mt_rand(0, 65535),
		bindec(substr_replace(
			sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
		),
		bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
		mt_rand(0, 255),
		mt_rand()
	);
}

Route::add('/Biz/v1/', function()
{
	$tmp = [];
	$xml = simplexml_load_file(FILEPATH) or die('Error: Cannot connect datasource');
	header(STANDARD_HEADER);
	foreach($xml->children() as $empresa) {
		$tmp[] = ['id' => strtoupper(@strval($empresa['id'])), 'nm' => @strval($empresa->desc), 'ein' => @strval($empresa->ruc)];
	}
	echo json_encode($tmp);
}, 'GET');


Route::add('/Biz/v1/([0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12})/', function($id)
{
	$xml = simplexml_load_file(FILEPATH) or die('Error: Cannot create object');
	$strQuery = '//empresa[@id="' . $id . '"]';
	$result = $xml->xpath($strQuery);
	header(STANDARD_HEADER);
	echo json_encode([
		'nm' => @strval($result[0]->desc),
		'ein' => @strval($result[0]->ruc)
	]);
}, 'GET');

Route::add('/Biz/v1/', function()
{
	$receivedData = json_decode(file_get_contents('php://input'), true);
	if (json_last_error() != JSON_ERROR_NONE)
	{
		http_response_code(403);
		header(STANDARD_HEADER);
		die(json_encode([ 'error' => 'Error on new ITEM' ]));
	}

	if (!(
		!empty($receivedData) && count($receivedData) >= 2 && isset($receivedData['cd_']) && isset($receivedData['nm_'])
	))
	{
		http_response_code(403);
		header(STANDARD_HEADER);
		die( json_encode([ 'error' => 'parameters onto ITEM [ERROR]' ]));
	}
	$id_ = uuid();
	$xml = simplexml_load_file(FILEPATH) or die('Error: Cannot create object');
	
	$m = $xml->addChild('empresa');
	$m->addAttribute('id', $id_);
	$m->addChild('desc', $receivedData['nm_']);
	$m->addChild('ruc', $receivedData['cd_']);
	$xml->asXML(FILEPATH);
	http_response_code(201);
}, 'POST');


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

