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
	$common->appendChild("Countries", "countries", "app-modules/invoicing/countries/index.html");
	$common->appendChild("Cities", "cities", "app-modules/invoicing/cities/index.html");
	$common->appendChild("Companies", "companies", "app-modules/invoicing/companies/index.html");

	$items[] = $common;

	$owner = new MenuItem("Owner data");

	$owner->appendChild("Invoices", "invoices", "app-modules/invoicing/invoices/index.html");

	$items[] = $owner;
	return $items;
}
