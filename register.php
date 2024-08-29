<?php 

include 'partials/_dbconnect.php';

if(isset($_POST['signUp'])){
    $FirstName = $_POST["FirstName"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    
   

     $checkEmail="SELECT * From merapyareusers where email='$email'";
     $result=$conn->query($checkEmail);
     if($result->num_rows>0){
        echo "Email Address Already Exists !";
     }
     else{
        $insertQuery="INSERT INTO merapyareusers(FirstName,email,password)
                       VALUES ('$FirstName','$email','$password')";
            if($conn->query($insertQuery)==TRUE){
                header("location: _login.php");
            }
            else{
                echo "Error:".$conn->error;
            }
     }
   

}

if(isset($_POST['signIn'])){
   $email=$_POST['email'];
   $password=$_POST['password'];
  
   
   $sql="SELECT * FROM merapyareusers WHERE email='$email' and password='$password'";
   $result=$conn->query($sql);
   if($result->num_rows>0){
    session_start();
    $row=$result->fetch_assoc();
    $_SESSION['email']=$row['email'];
    header("Location: welcome.php");
    exit();
   }
   else{
    echo "Not Found, Incorrect Email or Password";
   }

}
?>