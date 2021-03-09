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
   
            if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'add_nurse'){
                
                $id = $nur_id.rand(100,999).'-'.rand(100,999);
                echo $id;
                $sql = "SELECT `nurse_id` from `nurse` WHERE `nurse_id`  = '$id'";
                if($result = mysqli_query($conn, $sql)){
                    if(mysqli_num_rows($result)==0){
                        $sql = "INSERT INTO `nurse` (`nurse_id`, `doctor_id`, `name`, `email`, `phone`, `address`) VALUES ('$id','$_POST[doctor]','$_POST[name]','$_POST[email]','$_POST[phone]','$_POST[address]')";
                        if( mysqli_query($conn, $sql)){
                            $fail = "New Nurse has been added";
                        }else{
                            $fail = "Error, Try again";
                        }
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
                            ADD NEW NURSE
                        </div>
                            <?php 
                            if(!empty($fail)){
                               echo '<div class="alert alert-info alert-dismissible" role="alert" style="position: absolute; z-index: 99999; vertical-align: middle; align-self: center; width: 50% !important; top: 140px;"><button type="button" class="close  mx-auto" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> '.$fail.'</div>';
                            }
                            ?>
                        <div class="card-content">
                         	<form action="" method="POST" class="col s12">
								  <div class="row">
									<div class="input-field col s12">
									  <i class="material-icons prefix">account_circle</i>
									  <input id="icon_prefix" required name="name" value="" type="text" class="validate">
									  <label for="icon_prefix">Full Name</label>
									</div>
								  </div>
                                <div class="row">
                                    <div class="col s12">
                                        Assign Doctor
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
                                        <div class="input-field col s6">
                                        <i class="material-icons prefix">phone</i>
                                        <input id="icon_telephone" required name="phone" type="tel" class="validate">
                                        <label for="icon_telephone">Telephone</label>
                                        </div>
                                    
                                        <div class="input-field col s6">
                                        <input id="email" name="email" required type="email" class="validate">
                                        <label for="email" data-error="wrong" data-success="right">Email</label>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                    <textarea id="textarea1" name="address" required class="materialize-textarea"></textarea>
                                    <label for="textarea1">Address</label>
                                    </div>
                                </div>
                            <button type="submit" name="triggers" value="add_nurse" class="waves-effect waves-light btn">ADD</button>
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
