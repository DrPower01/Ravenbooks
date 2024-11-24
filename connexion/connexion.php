<?php
$serveur="localhost";
$utulisateur="root";
$passe="";
$basse="connexions";
$sum=mysqli_connect($serveur,$utulisateur,$passe,$basse);
$email =$_POST["email"];
$password=$_POST["password"];
$sql="SELECT * FROM Inscription WHERE email='$email' ";
if(mysqli_query($sum,$sql)){
    header("location:acceuil.html");
}

?> 