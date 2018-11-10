<?php
require_once get_path("core", "Controller.php");
require_once get_path("pm", "UserService.php");

class UserController extends BaseController
{
	public $tableName = "users";
	public $model = "User";
	public $service = "UserService";

}
