<?php
$serveur="localhost";
$utulisateur="root";
$passe="";
$basse="connexions";
$sum=mysqli_connect($serveur,$utulisateur,$passe,$basse);
$email=$_POST["email"];
$password=$_POST["password"];
$sql="INSERT INTO inscription VALUES ('$email','$password')";
if(mysqli_query($sum,$sql)){
    echo header("location:connexion.html");
}

?> 