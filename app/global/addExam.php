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
    <title>Add Exam</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden">

    <?php
    if (!Auth()) {
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }

    if ($_SESSION['role'] != "admin" && $_SESSION['role'] != "teacher") {
        header("Location: ../includes/forbidden.php?");
        ob_end_flush();
        return;
    }

    
    if(isset($_REQUEST['sendedData'])){
        $classId = $_REQUEST['sendedData'];
    }


    if($classId == 0){
        header("Location: ../global/chooseClass.php?error=3");
        ob_end_flush();
        return;
    }
    
    $conn = $DTO->Get();

    $query = "SELECT users.id, users.name, users.surname
    FROM users
    INNER JOIN classes_students ON users.id = classes_students.student_id
    WHERE classes_students.class_id = :classId;";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":classId", $classId);
    $stmt->execute();

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT class_name, class_teacher_id FROM classes WHERE id = :classId");
    $stmt->bindParam(":classId", $classId);
    $stmt->execute();

    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    // Yukarıdan aldığım class teacher ile o teacher'ın verdiği dersleri listeliyorum.
    $query = "SELECT lessons.id, lessons.lesson_name FROM lessons
    INNER JOIN classes ON classes.class_teacher_id = lessons.teacher_user_id
    WHERE lessons.teacher_user_id = :teacherId";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":teacherId", $class['class_teacher_id']);
    $stmt->execute();

    $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);



    if (isset($_POST['sendedData']) && isset($_POST['Student'])) {
        $classId = trim($_POST['sendedData']);
        $student = trim($_POST['Student']);
        $lesson = trim($_POST['Lesson']);
        $value = trim($_POST['note']);
        $examDate = trim($_POST['examDate']);

        $errorId = addExamErrors($student, $lesson, $value, $examDate);
        if ($errorId != -1 ) {
            header("Location: addExam.php?error=$errorId&sendedData=$classId");
            ob_end_flush();
            return;
        }

        // Valid check
        if($_SESSION['role'] == "teacher") {
            $c = $_SESSION["class"];
            if ($c != -1){
                $check = "SELECT * FROM classes_students WHERE class_id = :classId AND student_id = :studentId";
                $stmt = $conn->prepare($check);
                $stmt->bindParam(":classId", $c);
                $stmt->bindParam(":studentId", $student);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if(count($result) == 0){
                    header("Location: addExam.php?sendedData=$c");
                    ob_end_flush();
                    return;
                }
            } 
        }
        

        $DTO->Insert("exams", ["student_id", "lesson_id", "class_id", "exam_score" , "exam_date"], [$student, $lesson, $classId, $value, $examDate]);
        header("Location: ../global/manageExams.php");
        ob_end_flush();
        return;
    }
    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./addExam.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full">
                    <div class="text-center font-bold text-wheat w-full"><?= $class['class_name'] ?></div>
                </div>
                <div class="flex w-full items-center justify-between gap-4">
                    <div class="text-wheat font-bold">Students</div>
                    <div>
                        <select name="Student">
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
                </div>
                <div class="flex w-full items-center justify-between gap-4">
                    <div class="text-wheat font-bold">Lessons</div>
                    <div>
                        <select name="Lesson">
                            <option value=0>Select Lesson</option>

                            <?php
                            foreach ($lessons as $lesson) {
                                $id = $lesson['id'];
                            ?>
                                <option value=<?= $id ?>><?= htmlspecialchars($lesson['lesson_name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="flex w-full items-center justify-between gap-4">
                    <div class="text-wheat font-bold">Note</div>
                    <div><input type="number" name="note" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="flex w-full items-center justify-between gap-4">
                    <div class="text-wheat font-bold">Date</div>
                    <div><input type="datetime-local" name="examDate" class="outline-none pr-2 pl-2 rounded-lg p-1"></div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addExamErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="sendedData" value="<?= $classId ?>" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Add Exam</button>
                    <a href="../global/manageExams.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>