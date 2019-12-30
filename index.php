<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type:text/html; charset=utf-8");
    date_default_timezone_set("Asia/Taipei");

    require_once 'connectMySQL.php';
    $conn = mysqli_connect($serverName, $userName, $password,$databaseName);

    if($conn->connect_error){
        die($conn->connect_error);
    }
    mysqli_query($conn, 'SET NAMES utf8'); //避免中文字亂碼 

    $input = $_POST;

    switch($input["act"]){
        case "logIn":
            doLogin();
            break;
        case "creatAccount":
            doCreate();
            break;

        case "Setting":
            doSetting();
            break;

        case "insertLog":
            doInsert();
            break;

        case "showLog":
            doShow();
            break;
    }

    function doLogin(){
        global $input,$conn;
        $sql="SELECT `id`,`username`,`showSeat`,`over`,`alarm` FROM `userdetail` WHERE `account`='".$input['account']."' AND `password`='".$input['password']."'";
        $result=$conn->query($sql);
        if(!$result){
            die($conn->error);
        }

        if($result->num_rows <= 0){
            $rtn = array();
            $rtn["statue"] = false;
            $rtn["errorCode"] = "找不到會員";
            $rtn["data"] = "";
        }
        else{
            $row=$result->fetch_row();
            $rtn = array();
            $rtn["statue"] = true;
            $rtn["errorCode"] = "";
            $rtn["data"] =$row;
        }
        echo json_encode($rtn);
    }
    function doCreate()
    {
        global $input,$conn;
        $sql="SELECT `id` FROM `userdetail` WHERE `account`='".$input['account']."'";
        $result=$conn->query($sql);
        if(!$result){
            die($conn->error);
        }

        if($result->num_rows > 0){
            $rtn = array();
            $rtn["statue"] = false;
            $rtn["errorCode"] = "帳號已被使用";
            $rtn["data"] = "create fail....";
        }
        else{
            $new="INSERT INTO  `userdetail`(`id`,`username`,`account`,`password`,`showSeat`,`over`,`alarm`) VALUES(0,'".$input['username']."','".$input['account']."','".$input['password']."',5,0,0)";
            $resultNEW=$conn->query($new);
            if(!$resultNEW){
                die($conn->error);
            }
            $sql="SELECT `id` FROM `userdetail` WHERE `account`='".$input['account']."' AND `password`='".$input['password']."'";
            $result=$conn->query($sql);
            if(!$result){
                die($conn->error);
            }
            $row=$result->fetch_row();
            $rtn = array();
            $rtn["statue"] = true;
            $rtn["errorCode"] = "";
            $rtn["data"] = $new[0];
        }
        echo json_encode($rtn);
    }
    function doInsert(){
        global $input,$conn;
        $new="INSERT INTO  `userhistory`(`userid`,`keyword`,`time`) VALUES(".$input['userId'].",'".$input['keyword']."','".date("Y-m-d H:i:s")."')";
        $result=$conn->query($new);
        $rtn = array();
        if(!$result){
            $rtn["statue"] = false;
            $rtn["errorCode"] = "創建記錄失敗";
            $rtn["data"] = "fail....";
            echo json_encode($rtn);
            die($conn->error);
        }
        $rtn["statue"] = true;
        $rtn["errorCode"] = "";
        $rtn["data"] = $result;
        echo json_encode($rtn);
    }
    function doShow(){
        global $input,$conn;
        $sql="SELECT `id`,`keyword`,`time` FROM `userhistory` WHERE `userid`='".$input['userId']."'";
        $result=$conn->query($sql);
        if(!$result){
            die($conn->error);
        }
        if($result->num_rows <= 0){
            $rtn = array();
            $rtn["statue"] = false;
            $rtn["errorCode"] = "沒有歷史紀錄";
            $rtn["data"] = "";
        }
        else{
            $arr=array();
            for($i=0;$i<$result->num_rows;$i++){
                $row=$result->fetch_row();
                $log=array("id"=>"$row[0]","keyword"=>"$row[1]","time"=>"$row[2]");
                $arr[$i]=$log;
            }
            $rtn = array();
            $rtn["statue"] = true;
            $rtn["errorCode"] = "";
            $rtn["data"] =$arr;
        }
        echo json_encode($rtn);
    }
    function doSetting(){
        global $input,$conn;
        $update="UPDATE `userdetail` SET `username`='".$input['username']."',`showSeat`='".$input['showSeat']."',`over`='".$input['over']."',`alarm`='".$input['alarm']."' WHERE `id`='".$input['userId']."'";
        $result=$conn->query($update);
        $rtn = array();
        if(!$result){
            $rtn["statue"] = false;
            $rtn["errorCode"] = "更新失敗";
            $rtn["data"] = "fail....";
            echo json_encode($rtn);
            die($conn->error);
        }
        $rtn["statue"] = true;
        $rtn["errorCode"] = "";
        $rtn["data"] = $result;
        echo json_encode($rtn);
    }
?>
