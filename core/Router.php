<?php
define("DIR_DATABASE", $_SERVER["DOCUMENT_ROOT"] . "/api/vendor/database/");
require_once get_path("", "config.routes.php");
require_once get_path("vendor", "Db.php");
require_once get_path("", "config.secrets.php");
require_once get_path("", "config.appinfo.php");
define("DB_DATABASE", $dbConfig["dbName"]);

class Router
{
	public $routes = array();
	static public $defaultActions = ["create", "delete", "getItem", "getList", "new", "update"];

	public function __construct()
	{
		global $routes;
		foreach ($routes as $route) {
			$this->add($route);
		}
	}

	private function addRoute($controller, $action, $file, $class, $authorized)
	{
		$route = array();
		$route["uri"] = implode("/", [$controller, $action]);
		$route["action"] = $action;
		$route["file"] = $file;
		$route["class"] = $class;
		$route["authorized"] = $authorized;
		$this->routes[] = $route;
	}

	public function authorizing($route, $dbConnection)
	{
		if (function_exists("routeAuthorizing")) return routeAuthorizing($route, $dbConnection);
		else return true;
	}

	public function add($routeDef)
	{
		$uri = trim($routeDef["uri"], '/');
		$controller = explode("/", $uri)[0];
		$action = explode("/", $uri)[1];
		$file = $routeDef["file"];
		$class = isset($routeDef["class"]) ? $routeDef["class"] : pathinfo($file, PATHINFO_FILENAME);
		$authorized = isset($routeDef["authorized"]) ? $routeDef["authorized"] : true;
		if ($action == "*") foreach (self::$defaultActions as $action) $this->addRoute($controller, $action, $file, $class, $authorized);
		else $this->addRoute($controller, $action, $file, $class, $authorized);
	}

	public function submit()
	{
		global $dbConnection;
		$uriGetParam = trim(isset($_GET[ROUTE_PARAM]) ? $_GET[ROUTE_PARAM] : '/', " / ");
		foreach ($this->routes as $route) {
			$routeUri = $route["uri"];
			if (preg_match("#^$routeUri$#", $uriGetParam)) {
				$action = $route["action"];
				$file = $route["file"];
				$class = $route["class"];
				if ($route["authorized"]) {
					if (!$this->authorizing($route, $dbConnection)) throw new Exception("User not authorized!");
				}
				$classFile = implode([$_SERVER["DOCUMENT_ROOT"], API_ROOT, $file], "/");
				require_once($classFile);
				$ctrlObj = new $class($dbConnection);
				return $ctrlObj->action($action);
			}
		}
		return array("message" => "route:" . $uriGetParam . " not found!", "status" => false);
	}
}

