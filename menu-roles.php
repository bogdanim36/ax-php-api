<?php
include_once "config.php";

class MenuRoles
{
	static public $userId;

	public static function GetMenuList($roles, $userId = 0)
	{
		self::$userId = $userId;
		$menus = array();
		foreach ($roles as $role) {
			$menus[] = array(
				"RoleId" => $role["id"],
				"RoleName" => $role["name"],
				"Menu" => self:: addMenu($role["id"]));
		}
		return $menus;
	}


	private static function addMenu($roleId)
	{
		return addMenu($roleId);
	}
}

class MenuItem
{
	public $title;
	public $route;
	public $templateUrl;
	public $cssClass;
	public $items;
	public $showItems = true;

	public function __construct($title, $route = "", $templateUrl = "", $items = array(), $cssClass = "", $showItems = true)
	{
		$this->title = $title;
		$this->route = $route;
		$this->templateUrl = $templateUrl;
		$this->cssClass = $cssClass;
		$this->showItems = $showItems;
		if ($items) $this->items = $items;
	}

	public function appendChild($title, $route = "", $templateUrl = "", $cssClass = "", $showItems = true)
	{
		$item = new MenuItem($title, $route, $templateUrl, array(), $cssClass, $showItems );
		if (!$this->items) $this->items = [];
		$this->items[] = $item;
		return $item;
	}
}