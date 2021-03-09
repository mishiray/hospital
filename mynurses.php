<?php
    require_once 'base.php';
    //nurses on duty
    $sql = "SELECT n.name as name, n.doctor_id as doctor_id, n.nurse_id as nurse_id, d.name as doc, n.email as email, n.address as address, n.phone as phone FROM `nurse` as n LEFT JOIN `doctor` as d on n.doctor_id = d.doctor_id WHERE n.doctor_id = '$userinfo->doctor_id' ORDER BY d.name ";
    $result = mysqli_query($conn, $sql);
    if(!empty($result)){
        $nurses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['triggers'] == 'unassign'){
        $sql = "UPDATE `nurse` SET `doctor_id` = '' WHERE `nurse_id` = '$_POST[nurse_id]' ";
        echo $sql;
        if( mysqli_query($conn, $sql)){
            $fail = "Nurse successfully Unassigned";
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
                                My Assigned Nurses
                            </div>
                        
                            <?php 
                            if(!empty($fail)){
                               echo '<div class="alert alert-info alert-dismissible" role="alert" style="position: absolute; z-index: 99999; vertical-align: middle; align-self: center; width: 50% !important; top: 140px;"><button type="button" class="close  mx-auto" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$fail.'</div>';
                            }
                        ?>
                            <div class="card-content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>SN</th>
                                                <th>ID</th>
                                                <th>NAME</th>
                                                <th>EMAIL</th>
                                                <th>PHONE</th>
                                                <th>ADDRESS</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            if(!empty($nurses)){
                                                $count = 1;
                                                foreach($nurses as $nurse){
                                        ?>       
                                            <tr class='odd gradeX'>
                                                <td><?php echo $count++ ?></td>
                                                <td><?php echo $nurse['nurse_id'] ?></td>
                                                <td><?php echo ucwords($nurse['name']) ?></td>
                                                <td><?php echo $nurse['email'] ?></td>
                                                <td><?php echo $nurse['phone'] ?></td>
                                                <td><?php echo $nurse['address'] ?></td>
                                                <td class="center">
                                                    <?php 

                                                        if(!empty($nurse['doctor_id'])){
                                                            echo "
                                                            <form action='mynurses.php' method='post'>
                                                                <input type='hidden' name='nurse_id' value='$nurse[nurse_id]' />
                                                                <button type='submit' name='triggers' value='unassign' class='btn waves-effect waves-light  btn-success btn-sm'>UnAssign</button>
                                                            </form>
                                                            ";
                                                        }

                                                    ?>
                                                </td>
                                            </tr>
                                                  
                                        <?php
                                                }
                                            }else{
                                                echo "<p style='margin-left:30px;margin-right:30px;' class='text-light bg-danger text-center'>No Nurses assigned</p>";
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
