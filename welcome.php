<?php
session_start();
include 'partials/_dbconnect.php';


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <img src="WMNS+AIR+JORDAN+1+LOW.jpeg" alt="">
    <div style="text-align:center; padding:15%;">
      <p  style="font-size:50px; font-weight:bold;">
       Hello  <?php 
       if(isset($_SESSION['email'])){
        $email=$_SESSION['email'];
        $query=mysqli_query($conn, "SELECT merapyareusers.* FROM `merapyareusers` WHERE merapyareusers.email='$email'");
        while($row=mysqli_fetch_array($query)){
            echo $row['FirstName'].' '.$row['email'];
        }
       }
       ?>
       :)
      </p>
      <a href="logout.php">Logout</a>
    </div>
</body>
</html>