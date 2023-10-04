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
    <title>View</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden">

    <?php
    if (!Auth()) {
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }

    if ($_SESSION['role'] != "teacher") {
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }

    if (!isset($_REQUEST['sendedData'])) {
        header("Location: ../teacher/viewStudents.php");
        ob_end_flush();
        return;
    }
    $id = $_REQUEST['sendedData'];

    $conn = $DTO->Get();

    $classId = $_SESSION['class'];

    $query = "SELECT users.name, users.surname, users.username, classes.class_name 
    FROM users
    LEFT JOIN classes_students ON classes_students.student_id = users.id
    LEFT JOIN classes ON classes_students.class_id = classes.id
    WHERE users.id = :userId AND users.role = 'student' AND classes.id = $classId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":userId", $id);
    $stmt->execute();
    $dataUser = $stmt->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT lessons.lesson_name, exams.exam_score, exams.exam_date FROM lessons
    LEFT JOIN exams ON exams.lesson_id = lessons.id
    WHERE exams.student_id = :userId";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":userId", $id);
    $stmt->execute();
    $dataExams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <div class="flex items-center justify-center w-full h-full gap-4">
        <div class="flex flex-col gap-4 text-center bg-myDark text-wheat p-4 rounded-lg">
            <div class="flex gap-2 font-bold w-full justify-between">
                <div>Name</div>
                <div><?= htmlspecialchars($dataUser['name']) ?></div>
            </div>
            <div class="flex gap-2 font-bold w-full justify-between">
                <div>Surname</div>
                <div><?= htmlspecialchars($dataUser['surname']) ?></div>
            </div>
            <div class="flex gap-2 font-bold w-full justify-between">
                <div>Username</div>
                <div><?= htmlspecialchars($dataUser['username']) ?></div>
            </div>
            <div class="flex gap-2 font-bold w-full justify-between">
                <div>Class</div>
                <div><?= htmlspecialchars($dataUser['class_name']) ?></div>
            </div>
        </div>
        <div class="flex flex-col gap-4 text-center bg-myDark text-wheat p-4 rounded-lg">
            <?php
                foreach($dataExams as $exam){
            ?>
                <div class="flex flex-col border-solid border-wheat border-b-2 mb-4 p-2 gap-4">
                    <div class="flex gap-2 font-bold w-full justify-between">
                        <div>Lesson Name</div>
                        <div><?= htmlspecialchars($exam['lesson_name']) ?></div>
                    </div>
                    <div class="flex gap-2 font-bold w-full justify-between">
                        <div>Exam Score</div>
                        <div><?= htmlspecialchars($exam['exam_score']) ?></div>
                    </div>
                    <div class="flex gap-2 font-bold w-full justify-between">
                        <div>Exam Date</div>
                        <div><?= htmlspecialchars($exam['exam_date']) ?></div>
                    </div>
                </div>
            <?php } ?>
            <a href="../teacherSQL/viewStudents.php" class="p-1 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
        </div>
    </div>


</body>

</html>