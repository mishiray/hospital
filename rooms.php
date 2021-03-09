<?php
    require_once 'base.php';
    //nurses on duty
    $sql = "SELECT* FROM `room` ORDER BY `room_id`";
    $result = mysqli_query($conn, $sql);
    if(!empty($result)){
        $rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'delete'){
        $sql = "DELETE FROM `nurse` WHERE `nurse_id` = '$_POST[nurse_id]' ";
        //echo $sql;
        if( mysqli_query($conn, $sql)){
            $fail = "Nurse data has been deleted!";
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
                                                <th>SN</th>
                                                <th>ROOM NUM</th>
                                                <th>SIZE</th>
                                                <th>PATIENTS AVAILABLE</th>
                                                <th>DATE ADDED</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            if(!empty($rooms)){
                                                $count = 1;
                                                foreach($rooms as $room){
                                        ?>       
                                            <tr class='odd gradeX'>
                                                <td><?php echo $count++ ?></td>
                                                <td><?php echo $room['room_id'] ?></td>
                                                <td><?php echo $room['type'] ?></td>
                                                <td><?php echo $room['status'] ?></td>
                                                <td><?php echo $room['dateadded'] ?></td>
                                                <td class="center">
                                                    <?php 
                                                        echo "<a href='update_rooms.php?id=$room[room_id]' style='margin-right:4px;' class='btn btn-info'>Update Info<a>"; 
                                                        echo "<a href='view_room.php?id=$room[room_id]' class='btn btn-success'>View Room<a>";
                                                        
                                                    ?>
                                                </td>
                                            </tr>
                                                  
                                        <?php
                                                }
                                            }else{
                                                echo "<p style='margin-left:30px;margin-right:30px;' class='text-light bg-danger text-center'>No rooms found</p>";
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
