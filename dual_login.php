<?php
session_start();

if(isset($_SESSION["dual_loggedin"]) && $_SESSION["dual_loggedin"] === true)
{
    header("location: mainhomepage.php");
    exit;
}

require_once "dual_config.php";

$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["username"])))
	{
        $username_err = "Please enter your username.";
    } 
	
	else
	{
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["password"])))
	{
        $password_err = "Please enter your password.";
    }
	
	else
	{
        $password = trim($_POST["password"]);
    }

    if(empty($username_err) && empty($password_err))
	{
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql))
		{
            mysqli_stmt_bind_param($stmt, "s", $p_user);

            $p_user = $username;

            if(mysqli_stmt_execute($stmt))
			{
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1)
				{
                    mysqli_stmt_bind_result($stmt, $id, $username, $hash_pass);
					
                    if(mysqli_stmt_fetch($stmt))
					{
                        if(password_verify($password, $hash_pass))
						{
                            session_start();

                            $_SESSION["dual_loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            header("location: mainhomepage.php");
                        }
						
						else
						{
                            $password_err = "The password you entered is incorrect.";
                        }
                    }
                }
				
				else
				{
                    $username_err = "That user is not currently registered.";
                }
            }
			
			else
			{
                echo "An error has ocurred, please try again.";
            }

            mysqli_stmt_close($stmt);
        }
    }
	
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .textbox{ width: 400px; padding: 25px; align-items: center;}
    </style>
</head>
<body>
    <div class="textbox">
        <h2>Login</h2>
        <p>Please fill in your credentials to login to your dual account.</p>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
