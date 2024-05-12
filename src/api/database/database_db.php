<?php

require_once("userService.php");
require_once(dirname(__FILE__)."/../problemDetail.php");
require_once(dirname(__FILE__)."/../settings.php");

class MySqlDatabase implements UserService {
    //Database info
    private string $db_charset = 'utf8mb4';
    private mysqli $db_handle;

    function __construct()
    {
        global $db_host, $db_user, $db_pass, $db_dbase;
        $this->db_handle = new mysqli($db_host, $db_user, $db_pass, $db_dbase);
        $this->db_handle->set_charset($this->db_charset);
    }

    function CheckConnection() : bool
    {	
        return true;
    }

    public function GetUsers(): array
    {
        $users = [];

        if($this->CheckConnection() == false)
        {
            mysqli_close($this->db_handle);
            return array();
        }
        
        $stmt = $this->db_handle->query("SELECT Username, PassSalt1, PassSalt2, UserHash, UserType from Users");
        while($user = $stmt->fetch_object())
        {
            if($user == NULL || empty($user))
                return array();
                
            array_push($users, new UserEntry($user->Username, "", $user->PassSalt1, $user->PassSalt2, $user->UserHash, UserType::from($user->UserType)));
        }
        
        return $users;
    }
    function CheckUser($name, $pass): ?UserEntry
    {
        if($this->CheckConnection() == false)
            return NULL;
        
        global $db_ENC_KEY;
        $name = strtolower($name);
        $pass = strtolower(hash( 'sha256' ,(base64_encode(strtolower($pass).$db_ENC_KEY))));

        $stmt = $this->db_handle->prepare('select Username, PassSalt1, PassSalt2, UserHash, UserType from Users where Username=? and Password = LOWER(sha2(CONCAT(PassSalt1,sha2(?,512),PassSalt2),512));');
        //there is currently an issue with OVH/Mysql in which calling the function takes FOREVER and kills the connection afterwards. hence the query gets executed directly...
        //$stmt = $pdo->prepare('CALL CheckUser(:username,:pass);');
        $stmt->bind_param("ss", $name,$pass);
        $stmt->execute();
        $stmt->bind_result($username, $salt1, $salt2, $userhash, $usertype);
        $stmt->fetch();

        if(empty($username))
            return NULL;

        return new UserEntry($username, "", $salt1, $salt2, $userhash, UserType::from($usertype));
    }

    function ValidateUserHash($hash): bool
    {
        global $db_handle;
	
        //echo "checking connection...<br>";
        if($this->CheckConnection() == false)
            return false;
        
        $hash = strtolower($hash);
        
        //see GetUserHash for notes on the query
        $stmt = $this->db_handle->prepare('SELECT Username from Users where UserHash=:hash;');
        $stmt->execute(['hash' => $hash]);
        $stmt->bind_result($result);
        $result = $stmt->fetch();
        
        return !empty($result);
    }
}