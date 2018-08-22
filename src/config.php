<?php
define("DIR_DATABASE", $_SERVER["DOCUMENT_ROOT"] . "/api/vendor/database/");
include_once $_SERVER["DOCUMENT_ROOT"] . "/api/vendor/Db.php";

$dbConfig = array(
	"dbName" => "myDb",
	"user" => "myDb",
	"password" => "---------------",
	"driver" => "Mysqli",
	"host" => "localhost");
define("DB_DATABASE", $dbConfig["dbName"]);

$dbConnection = new Db($dbConfig["driver"], $dbConfig["host"], $dbConfig["user"], $dbConfig["password"], $dbConfig["dbName"]);


$roles = array("guest");
$email = array(
	"Username" => "xxx",
	"Password" => "MyFancyPass",
	"SetFrom" => ["email" => "xxx@gmail.com", "name" => "My app"],
	"AddReplyTo" => ["email" => "replayTo@gmail.com", "name" => "Your name"],
	"Host" => "smtp.gmail.com",
	"Port" => 587,
	"SMTPSecure" => "tls",
	'Type' => "POP3",
);

define("ROUTE_PARAM", "_route_");
define("API_ROOT", "/api/module");
define("APP_VERSION", "1.0.0");

$routes = array();

function addMenu($roleId)
{
	$items = array();
	return $items;
}


function routeAuthorizing($route)
{
	if (true) return true;
	header('HTTP/1.1 401 Not Authorized', true, 401);
	exit('Not authorized');
	return false;
}
