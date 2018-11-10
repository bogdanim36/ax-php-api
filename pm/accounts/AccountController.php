<?php
require_once get_path("core", "Controller.php");

class AccountController extends Controller
{
	public function getUserInfo()
	{
		require_once get_path("core", "MenuRoles.php");
		require_once get_path("pm", "users/UserService.php");
		$response = array();
		$response["status"] = false;
		try {
			if (!isset($_COOKIE["user-id"]) && !isset($this->postData->email)) throw new Exception("Login user");
			$email = isset($_COOKIE["user-id"]) ? $_COOKIE["user-id"] : $this->postData->email;
			$userClass = new UserService();
			$users = $userClass->getAll(array("email" => array("operator" => "equal", "value" => $email)));
			if (count($users) == 0) throw new Exception("No user found for email: " . $email);
			$user = $users[0];
			if ($user["disabledAccess"] == 1) throw new Exception("Access is disabled for email: " . $email);


			$response["data"] = $user;
			$expired = 365 * 86400 + time();
			setcookie("user-id", $email, $expired, "/");
			$roles = array(array("id" => "user", "name" => "user"));
			$menus = MenuRoles::getMenuList($roles);
			$response["menus"] = $menus;
			$response["status"] = true;
			$response["extra"]["version"] = APP_VERSION;
			return $response;
		} catch (Exception  $error) {
			$response["errors"] = $error->getMessage();
			return $response;
		}
	}

	public function login()
	{
		require_once $_SERVER["DOCUMENT_ROOT"] . "/api/module/User.php";
		$response = array();
		try {
			if (!$this->postData->email) throw new Exception("No email provided");
			if (!$this->postData->parola) throw new Exception("Nu e parolaaaaa");
			$userObj = new User($this->db, null);
			$users = $userObj->getUserByEmailAndPassword($this->postData->email, $this->postData->parola);
			if (!$users["status"] || count($users["data"]) != 1) throw new Exception("No user found for email and password provided!");
			$expired = 365 * 86400 + time();
			setcookie("user-id", $this->postData->email, $expired, "/");
			return $this->getUserInfoAction();
		} catch (Exception  $error) {
			$response["errors"] = $error->getMessage();
			$response["status"] = false;
		}
		$response["extra"]["version"] = APP_VERSION;
		return $response;
	}

	public function logoff()
	{
		require_once $_SERVER["DOCUMENT_ROOT"] . "/api/module/User.php";
		$response = array();
		try {
			setcookie("user-id", null, 1 + time(), "/");
			unset($_COOKIE["user-id"]);
			return array("status" => true);
		} catch (Exception  $error) {
			$response["errors"] = $error->getMessage();
			$response["status"] = false;
		}
		$response["extra"]["version"] = APP_VERSION;
		return $response;
	}

	private function sendResetPassEmail($userEmail, $userName, $guid)
	{
		global $email;
		$email["Subject"] = "Resetare parola Invoicng app pt. " . $userName;
		$email["To"] = array("email" => $userEmail, "name" => $userName);
		$email["MsgHTML"] = "";
		$email["MsgHTML"] .= "<br>Utilizeaza acest link (numai cu Chrome browser pt. desktop) ptr. resetare parola:<br> ";
		$email["MsgHTML"] .= "{$_SERVER['HTTP_REFERER']}#!/resetare-parola?id=$guid";
		$email["MsgHTML"] .= "<br>Atentie: linkul este valabil o singura data!";
		$email["MsgHTML"] .= "<br>Daca ai probleme cu logarea (sau aplicatia), contacteaza-ma la adresa de email bogdanim36@gmail.com, sau prin telefon la 0730740392.";

		return sendMail();

	}

	public function resetPassword()
	{
		require_once $_SERVER["DOCUMENT_ROOT"] . "/api/module/User.php";
		require_once $_SERVER["DOCUMENT_ROOT"] . "/api/send-email.php";
		$response = array();
		try {
			if (!isset($this->postData->{"email"})) throw new Exception("No email provided");
			$userEmail = $this->postData->email;
			$guid = guidv4();
			$userObj = new User($this->db);
			$user = $userObj->getItemAction(array("email" => $userEmail));
			if ($user["status"] && count($user["data"])) {
				$user["data"]["resetLink"] = $guid;
				$updateResponse = $userObj->updateAction(false, $user["data"]);
				if (!$updateResponse["status"]) return $updateResponse;
				$userName = $updateResponse["data"]["numeComplet"];
				$response = $this->sendResetPassEmail($userEmail, $userName, $guid);
				if (!$response["status"]) return $response;
				$response = array("status" => true);
			} else {
				throw new Exception("No user found for " . $userEmail);
			}
		} catch (Exception  $error) {
			$response["errors"] = $error->getMessage();
			$response["status"] = false;
		}
		return $response;
	}

	public function savePassword()
	{
		require_once $_SERVER["DOCUMENT_ROOT"] . "/api/module/User.php";
		try {
			if (!isset($this->postData->id)) throw new Exception("No id provided");
			if (!isset($this->postData->parola)) throw new Exception("No password provided");
			$userObj = new User($this->db);
			$response = $userObj->savePassword($this->postData->id, $this->postData->parola);
			if (!$response["status"]) {
				return array("status" => false, "errors" => "Link isn't valid one. Reset again your password");
			}
			$this->postData->email = $response["data"]["email"];
			$this->postData->parola = $response["data"]["parola"];
			return $this->loginAction();
		} catch (Exception  $error) {
			$response = array();
			$response["errors"] = $error->getMessage();
			$response["status"] = false;
		}
		return $response;
	}
}