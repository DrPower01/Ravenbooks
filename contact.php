<?php

$server="localhost";
$names="root";
$password="";
$base="contacts";
$co=mysqli_connect($server,$names,$password,$base);
$name=$_POST["name"];
$email=$_POST["email"];
$message=$_POST["message"];
$sql="INSERT INTO messages VALUES('$name','$email','$message')";
if(mysqli_query($co,$sql)){
    echo "merci pour votre message";
}
?>