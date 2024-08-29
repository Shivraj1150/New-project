<?php
$showAlert = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){

include 'partials/_dbconnect.php';
$FirstName = $_POST["FirstName"];
$email = $_POST["email"];
$password = $_POST["password"];
$email = $_POST["Emails"];
$password = $_POST["Passwords"];
$exists=false;

if($exists==false){
    $sql = "INSERT INTO `merapyareusers` (`FirstName`, `email`, `password`) VALUES ('$FirstName', '$email', '$password')";
    $result = mysqli_query($conn, $sql);
    if ($result){
        $showAlert = true;
    }
}

}


?>

<?php
$showAlert = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){

include 'partials/_dbconnect.php';

$email = $_POST["Emails"];
$password = $_POST["Passwords"];



    $sql = "Select * from merapyareusers where email='$Emails' AND passwords='$Passwords'";
    $result = mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);
    if ($num == 1){
        $login = true;

    }
    else{
        $showError = "Invalid Credentials";
    }
}




?>


<?php
if($login){
echo '
<div class="alert alert-success" role="alert">
  <h4 class="alert-heading">Well done!</h4>
  <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
  <hr>
  <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
</div>

';
}
if($showError){
echo '
<div class="alert alert-denger" role="alert">
  <h4 class="alert-heading">Well done!</h4>
  <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p>
  <hr>
  <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and tidy.</p>
</div>

';
}
?>