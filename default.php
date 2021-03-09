<?php
    require_once 'base.php';
    //nurses on duty
    $sql = "SELECT * FROM `nurse` WHERE `doctor_id` = '$userinfo->doctor_id'";
    
    $result = mysqli_query($conn, $sql);

    if(!empty($result)){
        $nurses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    //num of new patients
    $today = date("Y/m/d"); 
    $sql = "SELECT * FROM patient WHERE ( DATE_FORMAT(dateadded, '%Y/%m/%d') = '$today')";
    $result = mysqli_query($conn, $sql);
    if(!empty($result)){
        $new_patients = mysqli_num_rows($result);
    }
    //num of total patients
    $total_patients = mysqli_query($conn, "SELECT * FROM `patient` WHERE `status` = 1");
    $total_patients = mysqli_num_rows($total_patients);
    
    //appointments ratio
    $sql = "SELECT COUNT(*) as alltime, COUNT(case doctor_id when '$userinfo->doctor_id' then 1 else null end) as mine FROM `appointment`";
    $result = mysqli_query($conn, $sql);
    $appointments = [];
    if(!empty($result)){
        while ($entry = mysqli_fetch_object($result)) {
           $appointments = $entry;
        }

        $mine = $appointments->mine;
        $alltime = $appointments->alltime;

        $per = ($mine/$alltime) * 100;
        $per = number_format($per,0);
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
                                                <h3 class=""><?php echo $total_patients ?> <small class="pull-right dtext">All time</small></h3> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="card vertical cardIcon waves-effect waves-dark">
                                        <div class="card-stacked text-primary white">
                                        <h4>Appointments</h4> 
                                        <div class="easypiechart" id="easypiechart-blue" data-percent="<?php echo $per; ?>" ><span class="percent"><?php echo $per; ?>%</span>
                                        </div> 
                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                        <h1 class="page-header text-center">
                            Nurses <a href="nurses.php" class="btn-floating btn-primary"><i class="material-icons">add</i></a>
                        <p><small class="">On duty</small></p>
                        </h1>
                            <div class="row ">
                                <div class="d-flex justify-content-center align-items-center mx-auto col-3">
                                    <?php 
                                        if(!empty($nurses)){
                                            foreach($nurses as $nurse){
                                                echo "
                                                <div class='nurses'>
                                                <img src='https://tm-women.org/wp-content/uploads/2017/07/nurse-791x1024.jpg' alt='Nurse' class='img-circle logo'>
                                                    <p class='text-center'> $nurse[name] </p>
                                                </div>
                                                ";
                                            }
                                        }else{
                                            echo "<p style='margin-left:30px;margin-right:30px;' class='text-light bg-danger text-center'>No Nurses assigned</p>";
                                        }
                                    ?>
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