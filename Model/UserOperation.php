<?php
include "../Config/Db.php";
$database = new Db();
$conn = $database->connection();

function register($tablename,$name,$email,$pass,$role,$dob,$bg,$phone, $isActive )
{    
    global $conn; 
    $hash=password_hash($pass,PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO $tablename 
    (user_name, user_email, user_password, user_role, user_dob, user_bg, user_phone, user_is_active) 
    VALUES (?,?,?,?,?,?,?,?)";
    $statement=$conn->prepare($sql);
     $statement->bind_param("sssssssi",$name,$email,$hash,$role,$dob,$bg,$phone,$isActive);
    $result = $statement->execute();
    return $result;
}


function login ($tablename,$email,$pass)
{    
    global $conn; 
   $sql = "SELECT * FROM $tablename 
    WHERE user_email= ? ";

  $statement=$conn->prepare($sql);
     $statement->bind_param("s",$email);
     $statement->execute();
    $result = $statement->get_result();
    $user=$result->fetch_assoc();

    if(!$user)return false;
 if($user['user_is_active']==0)return 'Deactivated';

        if(!password_verify($pass,$user['user_password']))
         {return false;} 
        
   
     
      return $user;  

}
  
function updateProfile ($tablename,$id, $name,$phone,$dob)
{    
    global $conn; 
    $sql = "UPDATE $tablename 
    SET user_name= ?, user_phone=?, user_dob=? WHERE id=?";
    $statement=$conn->prepare($sql);
    $statement->bind_param("sssi",$name,$phone,$dob,$id);
    $result=$statement->execute();
    return $result;
}

function  findById($tablename,$id)
{    global $conn;
    $sql="SELECT * FROM $tablename WHERE id=?";
    $statement=$conn->prepare($sql);
    $statement->bind_param("i",$id);
    $statement->execute();
    $result=$statement->get_result();

    return $result->fetch_assoc();
}

function  changePassword($tablename,$id,$email,$currentpass,$newpass)
{    global $conn;
    $sql="SELECT * FROM $tablename WHERE id=?";
    $statement=$conn->prepare($sql);
    $statement->bind_param("i",$id);
    $statement->execute();
    $result=$statement->get_result();
    $user=$result->fetch_assoc();

    if(!$user)return false;

    if(!password_verify($currentpass,$user['user_password']))return false;
    $hash=password_hash($newpass, PASSWORD_DEFAULT);
    $sql = "UPDATE  $tablename 
    SET user_password= ?WHERE user_email=?";
    $statement=$conn->prepare($sql);
    $statement->bind_param("ss",$hash,$email);
    return $statement->execute();
}

function getUpcomingCount($patientId){

global $conn ;
$sql="SELECT COUNT(*)AS TOTAL FROM appointments
       WHERE patient_id=? AND status IN('Pending','Confirmed')
       AND appointment_date >=CURDATE()";
$statement=$conn->prepare($sql);
$statement->bind_param("i",$patientId);
$statement->execute();
$result=$statement->get_result();
$row=$result->fetch_assoc();
return $row['TOTAL'];        
}


function getAllUsers($tablename){
    global $conn;
    $sql="SELECT * FROM $tablename ORDER BY created_at DESC";
    $result=$conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function toggleActive($tablename, $id){
    global $conn;
    $sql="UPDATE $tablename 
    SET user_is_active= 1-user_is_active WHERE id=?";  
    $statement=$conn->prepare($sql);
    $statement->bind_param("i",$id);
    $statement->execute();
    $user=findById($tablename,$id);
    return $user['user_is_active'];              

}


?>