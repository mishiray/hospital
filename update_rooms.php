<?php
    require_once 'base.php';
    //nurses on duty

    $id = (!empty($_GET["id"])) ? $_GET["id"] : '';

    $sql = "SELECT * FROM `room` WHERE `room_id` = '$id'";
    $result = mysqli_query($conn, $sql);
    $room = [];
    if(!empty($result)){
        while ($entry = mysqli_fetch_object($result)) {
           $room = $entry;
        }
    }
    if(!empty($room)){

            if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'update_room'){
                if($_POST['type'] < $_POST['status']){
                    $fail = "Room size cannot accomodate this number";
                }else{
                    $sql = "UPDATE `room` SET `type` = '$_POST[type]' ,`status` = '$_POST[status]'  WHERE `room_id` = '$id' ";
                    if( mysqli_query($conn, $sql)){
                        $fail = "Room Updated";
                    }else{
                        $fail = "Try again";
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
                            UPDATE ROOM DATA
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
									  <i class="material-icons prefix"></i>
									  <input id="icon_prefix" value="<?php echo $room->room_id ?>" type="number" readonly class="validate">
									  <label for="icon_prefix">Room Number</label>
									</div>
								  </div>
								  <div class="row">
									<div class="input-field col s6">
									  <i class="material-icons prefix"></i>
									  <input id="icon_prefix" value="<?php echo $room->type ?>" type="number" name="type" class="validate">
									  <label for="icon_prefix">Room Size</label>
									</div>
								  </div>
								  <div class="row">
									<div class="input-field col s6">
									  <i class="material-icons prefix"></i>
									  <input id="icon_prefix" value="<?php echo $room->status ?>" type="number" name="status" class="validate">
									  <label for="icon_prefix">No of Patients in Room</label>
									</div>
								  </div>
                            <button type="submit" name="triggers" value="update_room" class="waves-effect waves-light btn">UPDATE</button>
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
