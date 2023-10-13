<?php
ob_start();
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

    if ($_SESSION['role'] != "admin") {
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }


    if(!isset($_REQUEST['sendedData'])){
        header("Location: ../admin/manageStudents.php");
        ob_end_flush();
        return;
    }else{
        $data = $DTO->Select("users", ["id"], [$_REQUEST['sendedData']])[0];
    }

    if (isset($_POST['sendedData'])) {
        $id = $_POST['sendedData'];
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);

        $oldUser = $DTO->Select("users", ["id"], [$id])[0];

        if($password != $confirmPassword){
            header("Location: updateStudent.php?sendedData=$id&error=3");
            ob_end_flush();
            return;
        }

        $columns = [];
        $values = [];
        
        if($oldUser['name'] != $name){
            $columns[] = "name";
            $values[] = $name;
        }
        if($oldUser['surname'] != $surname){
            $columns[] = "surname";
            $values[] = $surname;
        }
        if($oldUser['username'] != $username){
            $columns[] = "username";
            $values[] = $username;
        }

        if($password != ""){
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
            
            $columns[] = "password";
            $values[] = $hashedPassword;
        }

        $columns[] = "id";
        $values[] = $id;

        // Eğer column boyutu 1 ise hiç bişi değişmemiştir.
        if(count($columns) == 1){
            header("Location: updateStudent.php?sendedData=$id");
            ob_end_flush();
            return;
        }

        $DTO->Update("users", $columns, $values);
        header("Location: ../admin/manageStudents.php");
        ob_end_flush();
        return;
    }

    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./updateStudent.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Name</div>
                    <div><input type="text" name="name" class="outline-none pr-2 pl-2 rounded-lg p-1" value="<?= htmlspecialchars($data['name']) ?>"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Surname</div>
                    <div><input type="text" name="surname" class="outline-none pr-2 pl-2 rounded-lg p-1" value="<?= htmlspecialchars($data['surname']) ?>"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Username</div>
                    <div><input type="text" name="username" class="outline-none pr-2 pl-2 rounded-lg p-1" value="<?= htmlspecialchars($data['username']) ?>"></div>
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
                    <button name="sendedData" value=<?= $_GET['sendedData'] ?> class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Update Student</button>
                    <a href="../admin/manageStudents.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>