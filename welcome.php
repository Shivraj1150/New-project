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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .welcome-header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .welcome-message {
            font-size: 24px;
            margin: 20px 0;
        }
        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-block;
            width: 280px;
        }
        .card h3 {
            margin: 0 0 10px;
        }
        .card p {
            font-size: 16px;
            color: #666;
        }
        .logout-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
        }
        .logout-button:hover {
            background-color: #d32f2f;
        }
    </style>
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
       <div class="container">
        <div class="welcome-header">
            <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
        </div>
        <div class="welcome-message">
            <p>We're glad to have you back. Here are some things you might want to check out:</p>
        </div>
        <div class="card">
            <h3>Your Profile</h3>
            <p>Update your profile details, add a profile picture, and more.</p>
            <a href="profile.php">Go to Profile</a>
        </div>
        <div class="card">
            <h3>Dashboard</h3>
            <p>View your latest activities, manage your settings, and track your progress.</p>
            <a href="dashboard.php">Go to Dashboard</a>
        </div>
        <div class="card">
            <h3>Support</h3>
            <p>Need help? Check out our support resources or contact us for assistance.</p>
            <a href="support.php">Get Support</a>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
       :)
      </p>
      <a href="logout.php">Logout</a>
    </div>
</body>
</html>