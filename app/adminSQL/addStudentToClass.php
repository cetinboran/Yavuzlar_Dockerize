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
    <title>Add Student To Class</title>

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
    $students = $conn->query("SELECT users.id, users.name, users.surname FROM users
    LEFT JOIN classes_students ON classes_students.student_id = users.id
    WHERE classes_students.student_id IS NULL AND users.role = 'student'")->fetchAll(PDO::FETCH_ASSOC);

    // $students = $DTO->Select("users", ["role"], ["student"]);
    $classes = $DTO->Select("classes", [], []);

    if (isset($_POST['AddClassStudent'])) {
        $student = trim($_POST['student']);
        $class = trim($_POST['class']);

        $errorId = addStudentToClassErrors($student, $class);
        if($errorId != -1){
            header("Location: addStudentToClass.php?error=$errorId");
            ob_end_flush();
            return;
        }


        $DTO->Insert("classes_students", ["student_id", "class_id"], [$student, $class]);
        header("Location: ../admin/manageClasses.php");
        ob_end_flush();
        return;
    }
    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./addStudentToClass.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Students</div>
                    <select name="student">
                        <option value=0>Select Student</option>
                        <?php
                        foreach ($students as $student) {
                            $id = $student['id'];
                            $fullName = htmlspecialchars($student['name']) . " " . htmlspecialchars($student['surname']);
                        ?>
                            <option value=<?= $id ?>><?= $fullName ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Classes</div>
                    <div>
                        <select name="class">
                            <option value=0>Select Class</option>

                            <?php
                            foreach ($classes as $class) {
                                $id = $class['id'];
                                $fullName = htmlspecialchars($class['class_name']);
                            ?>
                                <option value=<?= $id ?>><?= $fullName ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addStudentToClassErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="AddClassStudent" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Add Student To Class</button>
                    <a href="../admin/manageClasses.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>