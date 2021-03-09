<?php

            require_once 'base.php';

            if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'add_room'){
                
                        $sql = "INSERT INTO `room` (`type`) VALUES ('$_POST[type]')";

                        if( mysqli_query($conn, $sql)){
                            $fail = "New room has been added";
                        }else{
                            $fail = "Error, Try again";
                        }
                    
            }

    

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HT Dashbaord</title> 
    <?php include 'styles.php'; ?>
</head>
<body>
    <div id="wrapper">

        <!-- NAV TOP  -->
        <?php include 'topnav.php'; ?>
	    <!--/. NAV TOP  -->
       
        <!-- NAV SIDE  -->
        <?php include 'sidemenu.php'; ?>
        <!-- /. NAV SIDE  -->

        <!--Start main shi-->
		<div id="page-wrapper">
            <div id="page-inner"> 
                <div class="row">
                    <div class="col-md-12">
                        <!-- Advanced Tables -->
                        <div class="card">
                            <form action="" method="post">
                            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-action">
                            ADD NEW ROOM
                        </div>
                            <?php 
                            if(!empty($fail)){
                               echo '<div class="alert alert-info alert-dismissible" role="alert" style="position: absolute; z-index: 99999; vertical-align: middle; align-self: center; width: 50% !important; top: 140px;"><button type="button" class="close  mx-auto" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> '.$fail.'</div>';
                            }
                            ?>
                        <div class="card-content">
                         	<form action="" method="POST" class="col s12">
                            <div class="row">
                            <div class="input-field col s6">
                                <input id="type" name="type" required type="number" class="validate">
                                <label for="type" data-error="wrong" data-success="right">Room size</label>
                            </div>
                            </div>
                            <button type="submit" name="triggers" value="add_room" class="waves-effect waves-light btn">ADD</button>
                            </form>
                        </div>
                        <!--End Advanced Tables -->
                    </div>
                </div>
            </div>
                <!-- /. PAGE INNER  -->
        </div>
        <!--End main shi-->
        <!-- /. PAGE WRAPPER  -->
    </div>
    <!-- /. WRAPPER  -->
    <!-- JS Scripts-->
    <!-- jQuery Js -->
    <?php include 'scripts.php'; ?>
</body>
</html>
