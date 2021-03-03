<?php

session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: default.php");
    exit;
}

require_once 'php/config.php';

$username = $password = $rolw = "";
$username_err = $password_err = $role_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["login"])){

    // Check if username is empty
    $err = 0;
    $fail = "";

    if(empty(trim($_POST["username"]))){
        $fail .= "<p>Please enter username.</p>";
        $err++;
    } else{
        $username = strtolower(trim($_POST["username"]));
    }

    if(empty($_POST["role"])){
        $fail .= "<p>Please select role.</p>";
        $err++;
    } else{
        $role = $_POST["role"];
    }
    echo $rolw;
    // Check if password is empty
    if(empty($_POST["password"])){
        $fail .= "<p>Please enter your password.</p>";
        $err++;
    } else{
        $password = $_POST["password"];
    }
    
    // Validate credentials
    if($err == 0){
        // Prepare a select statement
        switch($role){
            case 1: 
                $table = 'doctor';
                $id = 'doctor_id';
                break;
            
            case 2:
                $table = 'receptionist';
                $id = 'receptionist_id';
                break;

            default:
                $table = 'receptionist';
                $id = 'receptionist_id';
                break;
        }

        $sql = "SELECT `password`,`$id`, `email` FROM `$table` WHERE (`$id`= '$username' OR `email`= '$username' )";
        //echo $sql;
        $result = mysqli_query($conn, $sql);

        if(!empty($result)){

            $data = mysqli_fetch_assoc($result);
            $data = $data ? (object)$data : null;
            if($data->password==base64_encode($password) ){

                //if ok
                session_start();
                
                //Store user info in session value
                $user = "SELECT * FROM `$table` WHERE (`$id`= '$username' OR `email`= '$username' )";
                $result = mysqli_query($conn, $user);
                
                $userinfo = mysqli_fetch_assoc($result);
                $userinfo = $userinfo ? (object)$userinfo : null;
                
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;  
                $_SESSION["email"] = $data->email;                            
                $_SESSION["userinfo"] = $userinfo;

                // Redirect user to welcome page
                header("location: default.php");

            }else{

                // Display an error message if password is not valid
                $fail .= "<p>The password you entered was not valid.</p>";
            }
        }else{
            // Display an error message if username doesn't exist
            $fail .= "<p>No account found with that username.</p>";
        }				
    }
    // Close connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hospital Management System</title> 
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Bootstrap Styles-->
    <!-- FontAwesome Styles-->
    <link rel="stylesheet" type="text/css" href="login/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/animate/animate.css">
    <!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="login/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/vendor/select2/select2.min.css">
    <!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="login/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/css/util.css">
    <!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="login/css/main.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>

<body>
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                        <?php 
                            if(!empty($fail)){
                               echo '<div class="alert alert-danger alert-dismissible" role="alert" style="position: absolute; z-index: 99999; vertical-align: middle; align-self: center; width: 50% !important; top: 140px;"><button type="button" class="close  mx-auto" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h3>Error Messages</h3> '.$fail.'</div>';
                            }
                        ?>
                    <form method="post" action="#" class="login100-form validate-form">
                        <div class="mx-auto logo">
                            <h1 class="logo-text">
                                HT
                            </h1>
                        </div>
                        <span class="text-primary login100-form-title p-b-43">
                            
                        </span>
                        <p class="text-primary">Welcome back,</p> 

                        <div class="wrap-input100">
                            <select class="input100" required id="role" style="min-height: 50px;" name="role">
                                <option selected disabled>Choose Role</option>
                                <option value ="2">Receptionist</option>
                                <option value ="1">Doctor</option>
                            </select>
                        </div>
                        
                        <div class="wrap-input100">
                            <input class="input100" required id="username" type="text" name="username">
                        </div>
                        
                        <div class="wrap-input100">
                            <input class="input100" type="password" id="dataPass" name="password">
                           
                        </div>
                        
                        <div class="container-login100-form-btn">
                            <button type="submit" name="login" class="login100-form-btn btn btn-light border-primary">
                                Login
                            </button>
                        </div>
                    </form>
                    
                    <div class="login100-more" style="background-image: url('login/images/doc.png');">
                    </div>
                </div>
            </div>
        </div>
    <!-- /. WRAPPER  -->
    
    <!-- JS Scripts-->
    <!-- jQuery Js -->
    <script src="login/vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
	<script src="login/vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
	<script src="login/vendor/bootstrap/js/popper.js"></script>
	<script src="login/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
	<script src="login/vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
	<script src="login/vendor/daterangepicker/moment.min.js"></script>
	<script src="login/vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
	<script src="login/vendor/countdowntime/countdowntime.js"></script>
    <!--===============================================================================================-->
	<script src="login/js/main.js"></script>
</body>

</html>