<?php
ob_start();
require_once("../methods/methods.php");
require_once("../methods/errors.php");
require_once("../dto/dto.php");

$DTO = new DTO("", "mysql", ["root", "Boran123.", "yavuzlar_obs"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden">

    <?php
    if (!Auth()) {
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }

    $name = $_SESSION['name'];
    $surname = $_SESSION['surname'];
    $username = $_SESSION['username'];

    ?>

    <div class="flex items-center justify-center w-full h-full">
        <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg" >

            <div class="flex w-full justify-between gap-4">
                <div class="text-wheat font-bold">Name </div>
                <div class="text-wheat font-bold"><?= htmlspecialchars($name) ?></div>
            </div>
            <div class="flex w-full justify-between gap-4">
            <div class="text-wheat font-bold">Surname </div>
                <div class="text-wheat font-bold"><?= htmlspecialchars($surname) ?></div>
            </div>
            <div class="flex w-full justify-between gap-4">
            <div class="text-wheat font-bold">Username </div>
                <div class="text-wheat font-bold"><?= htmlspecialchars($username) ?></div>
            </div>
            <div class="text-wheat">
                <a href="./updateProfile.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Change Password</a>
                <a href="../src/home.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
            </div>
        </div>
    </div>


</body>

</html>