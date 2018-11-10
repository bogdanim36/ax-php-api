<?php
require_once get_path("pm", 'users/UserService.php');
define("API_ROOT", "api/pm");
define("ROUTE_PARAM", "_route_");

$routes = array(
	array("uri" => "account/getUserInfo", "file" => "accounts/AccountController.php", "authorized" => false),
	array("uri" => "account/login", "file" => "Account.php", "authorized" => false),
	array("uri" => "account/logoff", "file" => "Account.php", "authorized" => true),
	array("uri" => "account/resetPassword", "file" => "Account.php", "authorized" => false),
	array("uri" => "users/*", "file" => "User.php", "class" => "User"),
	array("uri" => "countries/*", "file" => "Country.php", "class" => "Country"),
	array("uri" => "cities/*", "file" => "City.php"),
	array("uri" => "owners/*", "file" => "Owner.php"),
	array("uri" => "companies/*", "file" => "Company.php"),
	array("uri" => "companies/getItemDetails", "file" => "Company.php"),
	array("uri" => "companies-accounts/*", "file" => "CompanyAccount.php"),
	array("uri" => "companies-addresses/*", "file" => "CompanyAddress.php"),
	array("uri" => "companies-emails/*", "file" => "CompanyEmail.php"),
	array("uri" => "companies-phones/*", "file" => "CompanyPhone.php"),
	array("uri" => "invoices/*", "file" => "Invoice.php"),
	array("uri" => "invoices/getInvoiceNumber", "file" => "Invoice.php"),
	array("uri" => "invoices-details/*", "file" => "InvoiceDetail.php"),
	array("uri" => "invoices-details/getDescriptions", "file" => "InvoiceDetail.php"),
	array("uri" => "export/pdf", "file" => "Export.php"),
	array("uri" => "export/email", "file" => "Export.php"),
	array("uri" => "cursValutar/getCurs", "file" => "CursValutar.php", "authorized" => false),
);


function routeAuthorizing($route, $dbConnection)
{

	if (isset($_COOKIE["user-id"])) {
		$userClass = new User($dbConnection);
		$user = $userClass->getItem(array("email" => $_COOKIE["user-id"]));
		if ($user["status"] && $user["data"]["esteActiv"] === "1") return true;
		return true;
	}
	header('HTTP/1.1 401 Not Authorized', true, 401);
	exit('Not authorized');
	return false;
}