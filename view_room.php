<?php
    require_once 'base.php';


    $id = (!empty($_GET["id"])) ? $_GET["id"] : '';
    $sql = "SELECT * FROM `admission` WHERE `room_id` = '$id' ";
    $result = mysqli_query($conn, $sql);
    $admissions = [];
    if(!empty($result)){
        while ($entry = mysqli_fetch_object($result)) {
           $admissions[] = $entry;
        }
    }
    if(!empty($admissions)){
        foreach($admissions as $value){
            if(!empty($value->patient_id)){
                $sql = "SELECT `name` FROM `patient` WHERE `patient_id` = '$value->patient_id'";
                $result = mysqli_query($conn, $sql);
                $doctor = mysqli_fetch_object($result);
                $value->patient = $doctor->name;
            }
            $value->out = strtotime($value->discharge_date) < strtotime(date("Y-m-d H:i:s")) ? true : false ;
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
                                ROOMING
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
                                                <th>PATIENT</th>
                                                <th>REPORT</th>
                                                <th>ADMITTED DATE</th>
                                                <th>DISCHARGE DATE</th>
                                                <th>STATUS</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            if(!empty($admissions)){
                                                $count = 1;
                                                foreach($admissions as $app){
                                        ?>       
                                            <tr class='odd gradeX'>
                                                <td><?php echo $count++ ?></td>
                                                <td><?php echo $app->patient ?></td>    
                                                <td><?php echo $app->report ?></td>
                                                <td><?php echo $app->admitted_date ?></td>
                                                <td><?php echo $app->discharge_date ?></td>
                                                <td><?php echo ($app->out) ? "Discharged" : "In Admission" ?></td>
                                                <td class="center">
                                                    <?php 
                                                        
                                                        echo "<a href='update_admission.php?id=$app->id' class='btn btn-info'>Update Info<a>";
                                                        
                                                    ?>
                                                </td>
                                            </tr>
                                                  
                                        <?php
                                                }
                                            }else{
                                                echo "<p style='margin-left:30px;margin-right:30px;' class='text-light bg-danger text-center'>No admissions</p>";
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
