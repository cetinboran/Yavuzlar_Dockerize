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
    <title>Add Lesson</title>

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

    // user bilgilerini ve class_name i çektim.
    // Hangi class'a lesson eklediğini de anlasın.
    $query = "SELECT users.id, users.name, users.surname, classes.class_name 
    FROM users
    INNER JOIN classes ON classes.class_teacher_id = users.id";
    
    $teachers = $DTO->Select("users", ['role'],['teacher']);

    // $teachers = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['AddLesson'])) {
        $lessonName = trim($_POST['lessonName']);
        $teacherId = trim($_POST['teacher']);

        $errorId = addLessonErrors($DTO, $lessonName, $teacherId);
        if ($errorId != -1) {
            header("Location: addLesson.php?error=$errorId");
            ob_end_flush();
            return;
        }

        $DTO->Insert("lessons", ["lesson_name", "teacher_user_id"], [$lessonName, $teacherId]);
        header("Location: ../admin/manageLessons.php");
        ob_end_flush();
        return;
    }
    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./addLesson.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Lesson Name</div>
                    <div><input type="text" name="lessonName" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Teachers</div>
                    <div>
                        <select name="teacher">
                            <option value=0>Select Teacher</option>

                            <?php
                            
                            foreach ($teachers as $teacher) {
                                $className = "";
                                $id = $teacher['id'];
                                $fullName = htmlspecialchars($teacher['name']) . " " . htmlspecialchars($teacher['surname']);

                                $teacherClass = $DTO->Select("classes", ["class_teacher_id"], [$id]);
                                if($teacherClass){
                                    $className = $teacherClass[0]['class_name'];
                                }
                            ?>
                                <option value=<?= $id ?>><?= $fullName . " | " . htmlspecialchars($className) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addLessonErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="AddLesson" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Add Lesson</button>
                    <a href="../admin/manageLessons.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>