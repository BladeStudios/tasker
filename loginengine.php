<?php
    session_start();
    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require_once('lang/'.$_SESSION['lang'].'.php');
    require_once('database/Database.class.php');

    if(!isset($_POST['login']) || !isset($_POST['password']))
        exit(header('Location: login.php'));

    try
    {
        $database = new Database();
        $connection = $database->connect();
        if($connection==null)
        {
            throw new Exception("Cannot connect to the database.");
        }
        else
        {
            require_once('database/Users.class.php');
            $user = new User();

            $login = $_POST['login'];
            $password = $_POST['password'];
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $login = htmlentities($login, ENT_QUOTES, "UTF-8");

            $user_data = $user->getUserByLogin($login);
            $users_amount = count($user_data);

            if($users_amount==1)
            {
                //logowanie
                $row = $user_data[0];
                if(password_verify($password, $row['password']))
                {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['login'] = $row['login'];
                    $previous_ip = $row['cur_ip'];

                    require_once('src/Info.class.php');
                    $info = new Info();

                    $time = $info->getTime(); //timestamp
                    $ip = $info->getIp(); //ip
                    $user_os = $info->getOS(); //operating system
                    $user_browser = $info->getBrowser(); //web browser

                    if($user->updateUser($row['id'],$row['current_ip'],$ip,$time,$user_os,$user_browser))
                    {
                        unset($_SESSION['error']);
                        $database->disconnect();
                        exit(header('Location: index.php'));
                    }
                    else
                    {
                        $_SESSION['error']=$lang['e_login'];
                        $_SESSION['loggedin'] = false;
                        unset($_SESSION['id']);
                        unset($_SESSION['login']);
                        $database->disconnect();
                        exit(header('Location: login.php'));
                    }
                }
                else
                {
                    $_SESSION['error']=$lang['e_wrong_credentials'];
                    $database->disconnect();
                    exit(header('Location: login.php'));
                }
            }
            else if($users_amount==-1)
            {
                //error during query
                $_SESSION['error']=$lang['e_unknown'];
                $database->disconnect();
                exit(header('Location: login.php'));
            }
            else
            {
                //wrong login
                $_SESSION['error']=$lang['e_wrong_credentials'];
                $database->disconnect();
                exit(header('Location: login.php'));
            }
            

            $database->disconnect();
        }
    }
    catch (Exception $e)
    {
        $_SESSION['error']=$lang['e_exception'].$e;
        exit(header('Location: login.php'));
    }
?>