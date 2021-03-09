<?php
    require_once 'base.php';
    //nurses on duty

    $id = (!empty($_GET["id"])) ? $_GET["id"] : '';

    $sql = "SELECT * FROM `admission` WHERE `id` = '$id'";
    $result = mysqli_query($conn, $sql);
    $admission = [];
    if(!empty($result)){
        while ($entry = mysqli_fetch_object($result)) {
           $admission = $entry;
        }
    }
    if(!empty($admission)){
            if(!empty($admission->patient_id)){
                $sql = "SELECT `name` FROM `patient` WHERE `patient_id` = '$admission->patient_id'";
                $result = mysqli_query($conn, $sql);
                $doctor = mysqli_fetch_object($result);
                $admission->patient = $doctor->name;
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'update_admission'){
                $sql = "UPDATE `admission` SET `report` = '$_POST[report]' ,`discharge_date` = '$_POST[discharge_date]'  WHERE `id` = '$id' ";
                echo $sql;
                if( mysqli_query($conn, $sql)){
                    $fail = "Admission Updated";
                }else{
                    $fail = "Try again";
                }
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
                            UPDATE ADMISSION DATA
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
									  <i class="material-icons prefix">account_circle</i>
									  <input id="icon_prefix" readonly value="<?php echo $admission->patient ?>" type="text" class="validate">
									  <label for="icon_prefix">Patient Name</label>
									</div>
								  </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                    <textarea id="textarea1" name="report" class="materialize-textarea"><?php echo $admission->report ?></textarea>
                                    <label for="textarea1">Report</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        Discharge Date
                                    <input type="date" id="discharde_date" value="<?php echo $admission->discharge_date ?>" name="discharge_date" class="materialize-input">
                                    </div>
                                </div>
                            <button type="submit" name="triggers" value="update_admission" class="waves-effect waves-light btn">UPDATE</button>
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
