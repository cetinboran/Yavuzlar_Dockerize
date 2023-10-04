<?php
require_once("../dto/dto.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["root", "Boran123.", "yavuzlar_obs"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg">
    
    <?php
    if (isset($_POST['Login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        Login($DTO, $username, $password);
    }
    ?>
    <div class="h-full flex items-center justify-center ">
        <form action="./login.php" method="POST">
            <div class="flex flex-col items-center bg-myDark p-8 rounded-xl gap-4">
                <div class="flex items-center gap-4">
                    <div class="text-wheat font-bold">Username: </div>
                    <div><input class="p-1 pr-2 pl-2 rounded-xl box-border outline-none" type="text" name="username"></div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-wheat font-bold">Password: </div>
                    <div><input class="p-1 pr-2 pl-2 rounded-xl box-border outline-none" type="text" name="password"></div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-red-600 font-bold">
                        <?= isset($_GET['error']) ? "Invalid Credentials" : "" ?>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4">
                    <div><input name="Login" type="submit" value="Login" class="text-wheat p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat"></div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>