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
    <title>Update Exam</title>

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
        header("Location: ../includes/forbidden.php");
        ob_end_flush();
        return;
    }


    if (!isset($_REQUEST['studentId']) || !isset($_REQUEST['classId']) || !isset($_REQUEST['lessonId']) || !isset($_REQUEST['examId'])) {
        header("Location: manageExams.php");
        ob_end_flush();
        return;
    } else {
        $conn = $DTO->Get();

        $query = "SELECT users.name, lessons.lesson_name, exams.exam_score
        FROM exams
        INNER JOIN users ON exams.student_id = users.id
        INNER JOIN lessons ON exams.lesson_id = lessons.id
        WHERE exams.student_id = :studentId AND exams.id = :examId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(":studentId", $_REQUEST['studentId']);
        $stmt->bindParam(":examId", $_REQUEST['examId']);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (isset($_POST['sendedData'])) {
        $studentId = $_POST['studentId'];
        $classId = $_POST['classId'];
        $lessonId = $_POST['lessonId'];
        $examId = $_POST['examId'];
        $value = trim($_POST['value']);

        $valueAsInt = intval($value);

        // Eğer $valueinti 0 geliyor ise ya string tir value yada 0 dır. Eğer 0 ise sorun yok string ise error'a yolluyoruz.
        if ($valueAsInt === 0 && $value !== '0') {
            header("Location: updateExam.php" . "?examId=$examId&studentId=$studentId&lessonId=$lessonId&classId=$classId&error=3");
            ob_end_flush();
            return;
        }

        // $errorId = updateExamErrors($conn, $studentId, $classId, $lessonId, $examId);

        if ($_SESSION['role'] == 'admin') {
            $query = "SELECT * FROM exams WHERE student_id = :studentId
            AND class_id = :classId AND lesson_id = :lessonId AND id = :examId";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(":studentId", $studentId);
            $stmt->bindParam(":classId", $classId);
            $stmt->bindParam(":lessonId", $lessonId);
            $stmt->bindParam(":examId", $examId);

            $stmt->execute();

            $check = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$check) {
                header("Location: manageExams.php?error=2");
                ob_end_flush();
                return;
            }
        } else if ($_SESSION['role'] == 'teacher') {
            // Eğer giren kişi teacher ise onun class'ını fixliyorum ki başka class'larda işlem yapamasın.
            $teacherId = $_SESSION['userId'];
            $class = $conn->query("SELECT id FROM classes WHERE class_teacher_id = $teacherId")->fetch(PDO::FETCH_ASSOC);

            // Eğer teacher'ın classı yok ise bu siteye girememeli
            if (count($class) > 0) {
                $classId = $class['id'];
                $query = "SELECT * FROM exams WHERE student_id = :studentId
                AND class_id = $classId AND lesson_id = :lessonId AND id = :examId";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(":studentId", $studentId);
                $stmt->bindParam(":lessonId", $lessonId);
                $stmt->bindParam(":examId", $examId);

                $stmt->execute();

                $check = $stmt->fetch(PDO::FETCH_ASSOC);

                // Eğer fetch başarısız ise false alıyor.
                if (!$check) {
                    header("Location: manageExams.php?error=2");
                    ob_end_flush();
                    return;
                }
            } else {
                header("Location: manageExams.php?error=1");
                ob_end_flush();
                return;
            }
        }

        if ($value < 0 || $value > 100) {
            header("Location: updateExam.php" . "?examId=$examId&studentId=$studentId&lessonId=$lessonId&classId=$classId&error=3");
            ob_end_flush();
            return;
        }


        $query = "UPDATE exams SET exam_score = :examScore WHERE student_id = :studentId AND class_id = :classId AND lesson_id = :lessonId AND id = :examId";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(":examScore", $value);
        $stmt->bindParam(":studentId", $studentId);
        $stmt->bindParam(":classId", $classId);
        $stmt->bindParam(":lessonId", $lessonId);
        $stmt->bindParam(":examId", $examId);
        $stmt->execute();

        header("Location: manageExams.php");
        ob_end_flush();
        return;
    }

    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./updateExam.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat fontbold w-full text-center font-bold"><?= htmlspecialchars($data['lesson_name']) ?></div>
                </div>
                <div class="flex w-full justify-between gap-4 border-solid border-b-2 border-wheat pb-2">
                    <div class="text-wheat fontbold w-full text-center font-bold"><?= htmlspecialchars($data['name']) ?></div>
                </div>
                <div class="flex items-center w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Note</div>
                    <div><input type="number" name="value" class="outline-none pr-2 pl-2 rounded-lg p-1" value="<?= htmlspecialchars($data['exam_score']) ?>"></div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addExamErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="hidden">
                    <input type="text" name="studentId" value=<?= $_REQUEST['studentId'] ?>>
                    <input type="text" name="classId" value=<?= $_REQUEST['classId'] ?>>
                    <input type="text" name="examId" value=<?= $_REQUEST['examId'] ?>>
                    <input type="text" name="lessonId" value=<?= $_REQUEST['lessonId'] ?>>
                </div>
                <div class="text-wheat">
                    <button name="sendedData" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Update Exam</button>
                    <a href="../global/manageExams.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>