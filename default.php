<?php
  session_start();
  require_once 'php/config.php';
  
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: index.php");
        exit;
    }

    //doc name
    $userinfo = $_SESSION["userinfo"];
    $name = ucwords($userinfo->name);

    //nurses on duty
    $sql = "SELECT * FROM `nurses` WHERE (`doctor_id`= '$userinfo->doctor_id')";
    
    $result = mysqli_query($conn, $sql);

        if(!empty($result)){

            $mynurses = mysqli_fetch_assoc($result);

            $nurses = $mynurses ? (object)$mynurses : null;

        }

    //num of new patients
    $today = date("Y/m/d");
    $sql = "SELECT * FROM `patient` WHERE ( DATE_FORMAT(`dateadded`, '%Y/%m/%d') = '$today')";
    
    $result = mysqli_query($conn, $sql);

        if(!empty($result)){

            $new_patients = mysqli_num_rows($result);

        }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Target Material Design Bootstrap Admin Template</title> 
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
                <div class="dashboard-cards">
                    <div>
                        <div class="col-lg-8">
                            <div class="row">
                            <h1 class="page-header">
                                Good Day, Dr. <?php echo "$name" ?>
                            </h1>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="card vertical cardIcon waves-effect waves-dark">
                                        <div class="card-stacked text-white blue">
                                            <div class="card-action text-left">
                                                <strong>Patients</strong>
                                            </div>
                                            <div class="card-content">
                                                <h3 class="dbase"> <?php echo $new_patients ?> <small class="pull-right dtext">Today</small></h3> 
                                                <h3 class="">84 <small class="pull-right dtext">All time</small></h3> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="card vertical cardIcon waves-effect waves-dark">
                                        <div class="card-stacked text-primary white">
                                        <h4>Profit</h4> 
                                        <div class="easypiechart" id="easypiechart-blue" data-percent="82" ><span class="percent">82%</span>
                                        </div> 
                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                        <h1 class="page-header">
                            Nurses
                        <p><small class="">On duty</small></p>
                        </h1>
                            <div class="row">
                                <div class="mx-auto col-3">
                                    <?php 
                                        if(!empty($nurses)){
                                            foreach($nurses as $nurse){
                                                echo "
                                                <div class='nurses'>
                                                <img src='https://tm-women.org/wp-content/uploads/2017/07/nurse-791x1024.jpg' alt='Nurse' class='img-circle logo'>
                                                    <p class='text-center'> $nurse->name</p>
                                                </div>
                                                ";
                                            }
                                        }else{
                                            echo "<p style='margin-left:30px;margin-right:30px;' class='text-light bg-danger text-center'>No Nurses assigned</p>";
                                        }
                                    ?>
                                    <div class="nurses">
                                        <img src="https://tm-women.org/wp-content/uploads/2017/07/nurse-791x1024.jpg" alt="Nurse" class="img-circle logo" srcset="">
                                        <p class="text-center"><?php ?> Name Surname</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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