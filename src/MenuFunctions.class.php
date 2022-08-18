<?php

class MenuFunctions
{
    public function logout($id)
    {
        require_once('database/Database.class.php');
        require_once('database/User.class.php');

        $user = new User();
        $result = $user->onLogout($id);

        $_SESSION['loggedin'] = false;
        unset($_SESSION['id']);
        unset($_SESSION['login']);
        
        return $result;
    }

    public function isLoggedIn()
    {
        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true) return true;
        else return false;
    }
}

?>