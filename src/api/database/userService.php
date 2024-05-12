<?php

interface UserService {
  public function GetUsers(): array;
  function CheckUser($name, $pass): ?UserEntry;
  function ValidateUserHash($hash): bool;
}

//Admin and users can upload, guests can not
enum UserType: string
{
	  case Unknown = "";
    case Admin = "Admin";
    case User = "User";
    case Guest = "Guest";
}

class UserEntry {
  public string $userName = "";
  public string $userPass = "";
  public $userSalt1 = "";
  public $userSalt2 = "";
  public string $userHash = "";

  public UserType $type;

  function __construct($user = "", $userPass = "", $userSalt1 = null, $userSalt2 = null, $userHash = "", $type = UserType::Guest)
  {
    $this->userName = $user;
    $this->userPass = $userPass;
    $this->userSalt1 = $userSalt1 ?? uniqid(mt_rand(), true);
    $this->userSalt2 = $userSalt2 ?? uniqid(mt_rand(), true);
    $this->userHash = $userHash;
    $this->type = $type;
  }
}