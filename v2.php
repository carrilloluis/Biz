<?php

/*
 * @author Luis Carrillo Gutiérrez
 * @date Jun/2014 - Abr/2021
 * @version	0.0.1
 */

require_once __DIR__ . '/vendor/autoload.php';
use Steampixel\Route;

function
getConnection():object
{
  try {
    $conn = new PDO ( DATABASE_NAME );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $error) {
    http_response_code(500);
    die(json_encode( array('error' => $error->getMessage() )));
  } catch(Exception $errorConnection) {
    http_response_code(500);
    die(json_encode( array('error' => $errorConnection->getMessage() )));
  }
  return $conn;
}

Route::add('/Biz/v2/', function()
{
	$db_connection = getConnection();
	try {
		$stmt = $db_connection->prepare("SELECT [i] AS id, [b] AS nm, [c] AS cd FROM [xy]");
		$stmt->execute();
		# $sizeResultSet = $stmt->rowcount();
	} catch(PDOException $errorQuery1) {
		http_response_code(500);
		die(json_encode([ 'error' => $errorQuery1->getMessage() ]));
	} catch(Exception $errorQuery2) {
		http_response_code(500);
		die(json_encode([ 'error' => $errorQuery2->getMessage() ]));
	}
	$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$db_connection = null;
	unset($db_connection);

	http_response_code(200);
	header(STANDARD_HEADER);
	echo json_encode( [
		'rs' => $rs, // => $sizeResultSet === 0 ? [] : $rs,
	] );
}, 'GET');

