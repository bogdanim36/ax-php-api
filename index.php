<?php
require './core/utils.php';
require './core/Router.php';
ob_start();

$route = new Router();
$response = $route->submit();


$list = ob_get_contents(); // Store buffer in variable

ob_end_clean(); // End buffering and clean up
if ($list) {//that mean we have an echo response = uncatched error
	$response["status"] = false;
	$response["message"] = $list;
}
echo json_encode($response);


