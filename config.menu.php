<?php

$roles = array("admin","user");

function addMenu($roleId)
{
	$items = array();
	$admin= new MenuItem("Administration");
	$admin->appendChild("Users", "users", "app-modules/invoicing/users/index.html");
	$admin->appendChild("Owners", "owners", "app-modules/invoicing/owners/index.html");
	$items[] = $admin;
	$common = new MenuItem("Common items");
	$common->appendChild("Projects", "projects", "app-modules/invoicing/countries/index.html");
	$common->appendChild("Tasks", "tasks", "app-modules/invoicing/cities/index.html");
	$common->appendChild("Documents", "documents", "app-modules/invoicing/cities/index.html");

	$items[] = $common;

	return $items;
}
