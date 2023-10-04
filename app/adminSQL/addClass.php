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
    <title>Add Class</title>

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
    
    $conn = $DTO->Get();

    $teachers = $conn->query("SELECT users.id, users.name, users.surname FROM users
    LEFT JOIN classes ON classes.class_teacher_id = users.id
    WHERE classes.class_teacher_id IS NULL AND users.role = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);


    if (isset($_POST['AddClass'])) {
        $className = trim($_POST['className']);
        $teacherId = trim($_POST['teacher']);

        $errorId = addClassErrors($DTO, $className, $teacherId);
        if ($errorId != -1) {
            header("Location: addClass.php?error=$errorId");
            ob_end_flush();
            return;
        }

        $DTO->Insert("classes", ["class_name", "class_teacher_id"], [$className, $teacherId]);
        header("Location: ../admin/manageClasses.php");
        ob_end_flush();
        return;
    }
    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./addClass.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Class Name</div>
                    <div><input type="text" name="className" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Teacher's Full Name</div>
                    <div>
                        <select name="teacher">
                            <option value=0>Select Teacher</option>

                            <?php
                            foreach ($teachers as $teacher) {
                                $id = $teacher['id'];
                                $fullName = htmlspecialchars($teacher['name']) . " " . htmlspecialchars($teacher['surname']);
                            ?>
                                <option value=<?= $id ?>><?= $fullName ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addClassErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="AddClass" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Add Class</button>
                    <a href="../admin/manageClasses.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>

                </div>
            </div>
        </form>
    </div>


</body>

</html>