<?php

            require_once 'base.php';
            //nurses on duty

            //get doctors
            $sql = "SELECT * FROM `doctor` ORDER BY `specialization`";
            $result = mysqli_query($conn, $sql);
            $docs = [];
            if(!empty($result)){
                while ($entry = mysqli_fetch_object($result)) {
                $docs[] = $entry;
                }
            }
    
            //get patients
            $sql = "SELECT * FROM `patient` ORDER BY `name` ASC ";
            $result = mysqli_query($conn, $sql);
            $patients = [];
            if(!empty($result)){
                while ($entry = mysqli_fetch_object($result)) {
                $patients[] = $entry;
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'add_appointment'){
                
                        $sql = "INSERT INTO `appointment` (`receptionist_id`,`doctor_id`,`patient_id`, `report`, `app_date`, `next_app_date`) VALUES ('htr-169-115','$_POST[doctor]','$_POST[patient]','$_POST[report]','$_POST[app_date]','$_POST[next_app_date]')";

                        if( mysqli_query($conn, $sql)){
                            $fail = "New appointment has been booked";
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
                            ADD NEW APPOINTMENT
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
                                        Doctor
                                        <select style="display: block !important;" required name="doctor" id="doctor">
                                        <option selected disabled value=''>Choose Doctor</option>
                                        <?php 
                                            if(!empty($docs)){
                                                foreach($docs as $doc){
                                                  echo  "<option value='$doc->doctor_id'>$doc->name - ".ucfirst($doc->specialization)." </option>";
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
                                    
                                    <div class="input-field col s12">
                                        Appointment Date
                                    <input type="date" id="app_date" value="<?php echo date("Y/m/d") ?>" name="app_date" class="materialize-input">
                                    </div>
                                    <div class="input-field col s12">
                                        Next Appointment Date
                                    <input type="date" id="next_app_date" value="" name="next_app_date" class="materialize-input">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                    <textarea id="textarea1" name="report" required class="materialize-textarea"></textarea>
                                    <label for="textarea1">Report</label>
                                    </div>
                                </div>
                            <button type="submit" name="triggers" value="add_appointment" class="waves-effect waves-light btn">ADD</button>
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
