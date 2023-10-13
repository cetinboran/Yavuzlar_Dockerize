<?php
session_start();
require_once("../dto/dto.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["deneme", "deneme.", "yavuzlar_obs"]);

if(isset($_GET['logout'])){
    setcookie("PHPSESSID", "", time() + (86400 * 30), "/");
    header("Location: index.php");
    return;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anasayfa</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden p-4">

    <div class="flex justify-end font-bold ">
        <a class="rounded-xl pr-4 pl-4 p-2 hover:bg-myDark hover:text-wheat hover:transition-all hover:duration-100" href="./login.php">Login</a>
    </div>
    <div class="h-full flex items-center justify-center text-wheat">
        <div class="flex flex-col items-center bg-myDark p-4 rounded-xl gap-4">
            <div class="author">Çetin Boran Mesüm</div>
            <div class="datetime">06.10.2023</div>
        </div>
    </div>

</body>

</html>