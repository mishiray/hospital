<?php 

function addRoom($roomid){
  global $conn;
  $sql = "SELECT * FROM `room` WHERE `room_id` = '$roomid'";
    $result = mysqli_query($conn, $sql);
    $room = [];
    $lat = false;
    if(!empty($result)){
        while ($entry = mysqli_fetch_object($result)) {
           $room = $entry;
        }
        $status = $room->status;
        $type = $room->type;
        if($type > $status){
            $status++;
            $sql = "UPDATE `room` SET `status` = '$status'  WHERE `room_id` = '$roomid' ";

            if(mysqli_query($conn, $sql)){
              $lat =  true;
            }
        }else{
          $lat = false;
        }
    }
    return $lat;
}