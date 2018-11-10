<?php
require_once get_path("core","Model.php");

class UserItem extends Model
{
	public $id;
	public $email;
	public $nume;
	public $prenume;
	public $esteActiv;
	public $esteAdmin;
	public $numeComplet;
	public $numeCompletInvariant;
	public $CNP;
	public $CiSeries;
	public $CiNumber;
	public $CiIssuedBy;
	public $CiDate;
}
