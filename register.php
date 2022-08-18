<?php
    session_start();

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
        exit(header('Location: index.php'));

    if(!isset($_SESSION['lang']))
        $_SESSION['lang'] = 'pl_PL';

    require_once('lang/'.$_SESSION['lang'].'.php');
?>

<html>
<head>
    <title>Register - Tasker (Powered by BladeStudios (C) 2022)</title>
    <link rel="stylesheet" href="libs/bootstrap-4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php 
    require('header.php');
    if(isset($_POST['email']))
    {
        //czy udana walidacja
        $ok = true;

        //walidacja nickname
        $login = $_POST['nick'];

        if(mb_strlen($login)<3 || mb_strlen($login)>32)
        {
            $ok = false;
            $_SESSION['e_nick'] = $lang['e_nick_length'];
        }
        if(ctype_alnum($login)==false)
        {
            $ok = false;
            $_SESSION['e_nick'] = $lang['e_nick_letters'];
        }

        //walidacja e-maila
        $email = $_POST['email'];
        $email2 = filter_var($email,FILTER_SANITIZE_EMAIL);

        if(filter_var($email2,FILTER_VALIDATE_EMAIL)==false || $email2!=$email)
        {
            $ok = false;
            $_SESSION['e_email'] = $lang['e_email_incorrect'];
        }

        //walidacja hasla
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];

        if(strlen($password1)<8 || strlen($password1)>32)
        {
            $ok = false;
            $_SESSION['e_password'] = $lang['e_password_length'];
        }

        if($password1 != $password2)
        {
            $ok = false;
            $_SESSION['e_password'] = $lang['e_password_identical'];
        }

        $password_hash = password_hash($password1, PASSWORD_DEFAULT);

        //miejsce na inne walidacje (regulamin, recaptcha)

        //zapamietanie wprowadzonych danych
        $_SESSION['temp_nick'] = $login;
        $_SESSION['temp_email'] = $email;

        require_once('database/Database.class.php');

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

                //czy istnieje taki sam e-mail w bazie danych
                $how_many_emails = count($user->getIdByEmail($email));
                if($how_many_emails==-1)
                {
                    $_SESSION['error_registration']=$lang['e_registration'];
                    $database->disconnect();
                    exit(header('Location: register.php'));
                }

                if($how_many_emails>0)
                {
                    $ok = false;
                    $_SESSION['e_email'] = $lang['e_email_exists'];
                }

                $how_many_nicks = count($user->getIdByLogin($login));
                if($how_many_nicks==-1)
                {
                    $_SESSION['error_registration']=$lang['e_registration'];
                    $database->disconnect();
                    exit(header('Location: register.php'));
                }

                if($how_many_nicks>0)
                {
                    $ok = false;
                    $_SESSION['e_nick'] = $lang['e_nick_exists'];
                }

                if($ok==true)
                {
                    require_once('src/Info.class.php');
                    $info = new Info();

                    $time = $info->getTime(); //timestamp
                    $ip = $info->getIp(); //ip
                    $country = $info->getCountry(); //country
                    $language = $info->getLanguage(); //language
                    $user_os = $info->getOS(); //operating system
                    $user_browser = $info->getBrowser(); //web browser

                    if($user->addUser($login,$password_hash,$email,$ip,$time,$user_browser,$user_os))
                    {
                        $_SESSION['registered'] = true;
                        $database->disconnect();
                        exit(header('Location: login.php'));
                    }
                    else
                    {
                        throw new Exception("Cannot add user.");
                    }
                }

                $database->disconnect();
            }
        }
        catch (Exception $e)
        {
            echo '<span style="color: red;">Registration failed. Please contact administrator.</span>';
            echo '<br/>Error info: '.$e;
        }
    }
?>

<div id="container">
    <br>
    <div id="title"><?php echo $lang['title_register']; ?></div>
    <br>
    <div id="registerform">
        <?php
			if (isset($_SESSION['error_registration']))
			{
				echo '<div class="error">'.$_SESSION['e_registration'].'</div>';
				unset($_SESSION['e_registration']);
			}
		?>
        <form method="post">
            <?php echo $lang["login"] ?><br/><input type="text" value="<?php
            if(isset($_SESSION['temp_nick']))
            {
                echo $_SESSION['temp_nick'];
                unset ($_SESSION['temp_nick']);
            }?>" name="nick"/><br/>
            <?php
			if (isset($_SESSION['e_nick']))
			{
				echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
				unset($_SESSION['e_nick']);
			}
		    ?>
            <?php echo $lang["password"] ?><br/><input type="password" name="password1"/><br/>
            <?php
			if (isset($_SESSION['e_password']))
			{
				echo '<div class="error">'.$_SESSION['e_password'].'</div>';
				unset($_SESSION['e_password']);
			}
		    ?>
            <?php echo $lang["passwordrepeat"] ?><br/><input type="password" name="password2"/><br/>
            <?php echo $lang["email"] ?><br/><input type="text" value="<?php
            if(isset($_SESSION['temp_email']))
            {
                echo $_SESSION['temp_email'];
                unset ($_SESSION['temp_email']);
            }?>" name="email"/><br/>
            <?php
			if (isset($_SESSION['e_email']))
			{
				echo '<div class="error">'.$_SESSION['e_email'].'</div>';
				unset($_SESSION['e_email']);
			}
		    ?>
            </br><input type="submit" class="btn btn-success center-in-div" value="<?php echo $lang["registerbutton"] ?>"/>
        </form>
    </div>
</div>
<?php require('footer.php'); ?>

</body>
</html>