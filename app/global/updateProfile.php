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
    <title>Update Student</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden">

    <?php
    if (!Auth()) {
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }
    // Eğer auth'dan geçtiyse gerekli veriler zaten session'da vardır

    $userId = $_SESSION['userId'];
    $oldPasswordHashed = $_SESSION['password'];

    if (isset($_POST['sendedData'])) {
        $oldPassword = trim($_POST['oldPassword']);
        $newPassword = trim($_POST['newPassword']);
        $confirmPassword = trim($_POST['confirmPassword']);

        $errorId = profileUpdateError($oldPassword, $oldPasswordHashed, $newPassword, $confirmPassword);
        if($errorId != -1){
            header("Location: updateProfile.php?error=$errorId");
            ob_end_flush();
            return;
        }

        $hashedNewPassword = password_hash($newPassword, PASSWORD_ARGON2ID);

        $DTO->Update("users", ['password','id'], [$hashedNewPassword,$userId]);
        header("Location: ../src/index.php?logout=true");
        ob_end_flush();
        return;
    }

    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./updateProfile.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Old Password</div>
                    <div><input type="text" name="oldPassword" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">New Password</div>
                    <div><input type="text" name="newPassword" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Confirm Password</div>
                    <div><input type="text" name="confirmPassword" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? profileUpdateErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="sendedData" value=<?= $userId ?> class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Update Profile</button>
                    <a href="../global/profile.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>