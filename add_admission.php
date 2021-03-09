<?php

            require_once 'base.php';
            require_once 'functions.php';

            //get patients
            $sql = "SELECT * FROM `patient` ORDER BY `name` ASC ";
            $result = mysqli_query($conn, $sql);
            $patients = [];
            if(!empty($result)){
                while ($entry = mysqli_fetch_object($result)) {
                $patients[] = $entry;
                }
            }
            
            //get rooms
            $sql = "SELECT * FROM `room` WHERE `type` != `status` ";
            $result = mysqli_query($conn, $sql);
            $rooms = [];
            if(!empty($result)){
                while ($entry = mysqli_fetch_object($result)) {
                $rooms[] = $entry;
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'add_admission'){
                        
                        $sql = "SELECT * FROM `admission` WHERE `patient_id` = '$_POST[patient]' ORDER BY `dateadded` DESC LIMIT 1";
                        $result = mysqli_query($conn, $sql);
                        $admissions = [];
                        if(!empty($result)){
                                $admissions = mysqli_fetch_object($result);

                                if(!empty($admissions)){
                                    $check = strtotime($admissions->discharge_date) > strtotime(date("Y-m-d H:i:s")) ? true : false ;
                                    if($check){
                                        $fail = "Patient already has ongoing admission";
                                    }else{
                                        
                                        if(addRoom($_POST['room'])){
                                            $sql = "INSERT INTO `admission` (`patient_id`,`room_id`, `report`, `admitted_date`, `discharge_date`) VALUES ('$_POST[patient]','$_POST[room]','$_POST[report]','$_POST[admitted_date]','$_POST[discharge_date]')";
                                            if( mysqli_query($conn, $sql)){
                                                $fail = "New admission has been booked";
                                                //echo "New admission has been booked";
                                            }else{
                                                $fail = "Error, Try again";
                                            }
                                        }else{
                                            $fail = "Error, Choose another room please";
                                            //echo "Error, Choose another room please";
                                        }
                                    }
                                }
                        }else{
                            
                            if(addRoom($_POST['room'])){
                                $sql = "INSERT INTO `admission` (`patient_id`,`room_id`, `report`, `admitted_date`, `discharge_date`) VALUES ('$_POST[patient]','$_POST[room]','$_POST[report]','$_POST[admitted_date]','$_POST[discharge_date]')";
                                if( mysqli_query($conn, $sql)){
                                    $fail = "New admission has been booked";
                                    //echo "New admission has been booked";
                                }else{
                                    $fail = "Error, Try again";
                                }
                            }else{
                                $fail = "Error, Choose another room please";
                                //echo "Error, Choose another room please";
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
                            ADD NEW ADMISSION
                        </div>
                            <?php 
                            if(!empty($fail)){
                               echo '<div class="alert alert-info alert-dismissible" role="alert" style="position: absolute; z-index: 99999; vertical-align: middle; align-self: center; width: 50% !important; top: 140px;"><button type="button" class="close  mx-auto" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> '.$fail.'</div>';
                            }
                            ?>
                        <div class="card-content">
                         	<form action="" method="POST" class="col s12">
                                <div class="row">
                                    <div class="col s12">
                                        Patient
                                        <select style="display: block !important;" required name="patient" id="patient">
                                        <option selected disabled value=''>Choose Patient</option>
                                        <?php 
                                            if(!empty($patients)){
                                                foreach($patients as $patient){
                                                  echo  "<option value='$patient->patient_id'>$patient->name</option>";
                                                }
                                            }else{
                                                echo  "<option value=''>No docs</option>";
                                            }   
                                        ?>      
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12">
                                        Room
                                        <select style="display: block !important;" required name="room" id="room">
                                        <option selected disabled value=''>Choose Room</option>
                                        <?php 
                                            if(!empty($rooms)){
                                                foreach($rooms as $doc){
                                                  echo  "<option value='$doc->room_id'>Room $doc->room_id - Size: $doc->type heads</option>";
                                                }
                                            }else{
                                                echo  "<option value=''>No rooms available</option>";
                                            }   
                                        ?>      
                                        </select>
                                    </div>
                                </div>
								<div class="row">
                                    <div class="input-field col s12">
                                        Admitted Date
                                    <input type="date" id="admitted_date" value="" name="admitted_date" class="materialize-input">
                                    </div>
                                    <div class="input-field col s12">
                                        Discharged Date
                                    <input type="date" id="discharge_date" value="" name="discharge_date" class="materialize-input">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                    <textarea id="textarea1" name="report" required class="materialize-textarea"></textarea>
                                    <label for="textarea1">Report</label>
                                    </div>
                                </div>
                            <button type="submit" name="triggers" value="add_admission" class="waves-effect waves-light btn">ADD</button>
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
