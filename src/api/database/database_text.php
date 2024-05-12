<?php

require_once("userService.php");

class TextDatabase implements UserService{
	private $users = array();
	private $userFilePath = './Users.txt';

	public function GetUsers(): array
	{
		return $this->users;
	}

	function CheckUser($name, $pass): ?UserEntry
	{		
		$this->CheckUsers();
		
		foreach ($this->users as $user)
		{
			if( $name == $user->userName && strtoupper(sha1(strtoupper(sha1($pass)).$user->userSalt1)) == strtoupper($user->userPass))
				return new UserEntry($user->userName, "", $user->userSalt1, "", "", "");
		}
		return NULL;
	}
	function ValidateUserHash($hash): bool
	{	
		$this->CheckUsers();
		foreach ($this->users as $user) 
		{
			if ($hash == $user->userHash) 
			{ 
				return true;
			} 
		}
		return false;
	}

	function SerialiseUsers() 
	{
		global $users;
		global $userFilePath;
		
		$this->CheckUsers();
		
		$file = serialize($users);
		
		file_put_contents($userFilePath, $file);
	}

	function DeSerialiseUsers() 
	{	
		global $users;
		global $userFilePath;
		
		if(file_exists($userFilePath))
		{
			$userFile = file_get_contents($userFilePath);
			$users = unserialize($userFile);
		}
		else
		{
			$users = array();
		}
	}

	function CheckUsers()
	{		
		if(count($this->users) <= 0)
			$this->DeSerialiseUsers();
	}
}