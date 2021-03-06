<?php
    require_once 'base.php';
    //nurses on duty
    $today = date("Y/m/d"); 
    $sql = "SELECT * FROM `appointment` WHERE `doctor_id` = '$userinfo->doctor_id' AND ( DATE_FORMAT(app_date, '%Y/%m/%d') = '$today')  ORDER BY `app_date` ASC ";
    $result = mysqli_query($conn, $sql);
    $appointments = [];
    if(!empty($result)){
        while ($entry = mysqli_fetch_object($result)) {
           $appointments[] = $entry;
        }
    }
    if(!empty($appointments)){
        foreach($appointments as $value){
            if(!empty($value->receptionist_id)){
                $sql = "SELECT `name` FROM `receptionist` WHERE `receptionist_id` = '$value->receptionist_id'";
                $result = mysqli_query($conn, $sql);
                $doctor = mysqli_fetch_object($result);
                $value->receptionist = $doctor->name;
            }
            if(!empty($value->patient_id)){
                $sql = "SELECT `name` FROM `patient` WHERE `patient_id` = '$value->patient_id'";
                $result = mysqli_query($conn, $sql);
                $doctor = mysqli_fetch_object($result);
                $value->patient = $doctor->name;
            }
        }
    }



    if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'assign'){
        $sql = "UPDATE `nurse` SET `doctor_id` = '$userinfo->doctor_id' WHERE `nurse_id` = '$_POST[nurse_id]' ";
        echo $sql;
        if( mysqli_query($conn, $sql)){
            $fail = "Nurse assigned!";
        }else{
            
            $fail = "Try again";
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
                            <div class="card-action">
                                My Appointments For Today
                            </div>
                        
                            <?php 
                            if(!empty($fail)){
                               echo '<div class="alert alert-info alert-dismissible" role="alert" style="position: absolute; z-index: 99999; vertical-align: middle; align-self: center; width: 50% !important; top: 140px;"><button type="button" class="close  mx-auto" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> '.$fail.'</div>';
                            }
                            ?>
                            <div class="card-content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>RECORDED BY</th>
                                                <th>PATIENT</th>
                                                <th>REPORT</th>
                                                <th>APP DATE</th>
                                                <th>NEXT APP DATE</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            if(!empty($appointments)){
                                                $count = 1;
                                                foreach($appointments as $app){
                                        ?>       
                                            <tr class='odd gradeX'>
                                                <td><?php echo $count++ ?></td>
                                                <td><?php echo $app->receptionist ?></td>
                                                <td><?php echo $app->patient ?></td>
                                                <td><?php echo $app->report ?></td>
                                                <td><?php echo $app->app_date ?></td>
                                                <td><?php echo $app->next_app_date ?></td>
                                                <td class="center">
                                                    <?php 

                                                        echo "<a href='update_appointments.php?id=$app->id' class='btn btn-info'>Update Info<a>";
                                                        
                                                    ?>
                                                </td>
                                            </tr>
                                                  
                                        <?php
                                                }
                                            }else{
                                                echo "<p style='margin-left:30px;margin-right:30px;' class='text-light bg-danger text-center'>No appointments</p>";
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
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
