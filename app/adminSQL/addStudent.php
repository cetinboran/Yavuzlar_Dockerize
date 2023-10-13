<?php
require_once("../methods/methods.php");
require_once("../methods/errors.php");
require_once("../dto/dto.php");

$DTO = new DTO("", "mysql", ["deneme", "deneme.", "yavuzlar_obs"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden">

    <?php
    if (!Auth()) {
        echo "<script>window.location.href ='../includes/forbidden.php'</script>";
        exit();
    }

    if ($_SESSION['role'] != "admin") {
        echo "<script>window.location.href ='../includes/forbidden.php'</script>";
        exit();
    }

    if (isset($_POST['AddUsers'])) {
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);

        $errorId = addUsersError($DTO, $name, $surname, $username, $password, $confirmPassword);
        if ($errorId != -1) {
            echo "<script>window.location.href ='addStudent.php?error=$errorId'</script>";
            exit();
        }

        // Created At zamanını alıyorum.
        $time = date("Y-m-d H:i:s");

        // Hashliyorum gelen şifreyi
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        $DTO->Insert("users", ["name", "surname", "username", "password", "role", "created_at"], [$name, $surname, $username, $hashedPassword, "student", $time]);
        echo "<script>window.location.href ='../admin/manageStudents.php'</script>";
        exit();
    }

    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./addStudent.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Name</div>
                    <div><input type="text" name="name" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Surname</div>
                    <div><input type="text" name="surname" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Username</div>
                    <div><input type="text" name="username" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Password</div>
                    <div><input type="password" name="password" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Confirm Password</div>
                    <div><input type="password" name="confirmPassword" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addUsersErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="AddUsers" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Add Student</button>
                    <a href="../admin/manageStudents.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>