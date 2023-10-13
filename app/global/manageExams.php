<?php
ob_start();
require_once("../methods/averages.php");
require_once("../dto/dto.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["deneme", "deneme.", "yavuzlar_obs"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

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

$conn = $DTO->Get();

$filterArr = $DTO->Select("classes", [], []);
$filter = isset($_POST['filter']) ? $_POST['filter'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;


//  sınav tarihi sınıf adı, öğrenci ismi, öğrenci soy ismi, ders adı, ders ortalaması
$query = "SELECT exams.id, users.id AS user_id, exams.exam_date, exams.exam_score,classes.id AS class_id ,classes.class_name, users.name, users.surname, lessons.id AS lesson_id, lessons.lesson_name
FROM users 
INNER JOIN exams ON exams.student_id = users.id
INNER JOIN classes ON exams.class_id = classes.id
INNER JOIN lessons ON exams.lesson_id = lessons.id
WHERE users.role = 'student'
ORDER BY classes.class_name;";

$data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

if ($filter != 0 && $_SESSION['role'] == "admin") {
    $query = "SELECT exams.id, users.id AS user_id, exams.exam_date, exams.exam_score,classes.id AS class_id ,classes.class_name, users.name, users.surname, lessons.id AS lesson_id, lessons.lesson_name
    FROM users 
    INNER JOIN exams ON exams.student_id = users.id
    INNER JOIN classes ON exams.class_id = classes.id
    INNER JOIN lessons ON exams.lesson_id = lessons.id
    WHERE users.role = 'student' AND classes.id = :filter
    ORDER BY classes.class_name;";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":filter", $filter);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

list($pageCount, $start, $end) = Pages($data, $page);

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $DTO->Delete("exams", ["id"], [$id]);
    header("Location: manageExams.php");
    ob_end_flush();
    return;
}

// Eğer öğretmense sadece onun class'ları ekrana gelsin.
if ($_SESSION['role'] == 'teacher') {
    $userId = $_SESSION['userId'];

    $class = $DTO->Select("classes", ["class_teacher_id"], [$userId]);

    // Teacher ise lesson name'i id yi alttaki gibi kolaylıkla çekebilirim
    // burada teacher a göre filter ayarlıyorum. Filter ekrana yazarken class_name olarak yaptığım için AS class_name ekledim.
    $query = "SELECT id, lesson_name AS class_name FROM lessons WHERE teacher_user_id = :userId";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $filterArr = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($class) > 0) {
        $classId = $class[0]['id'];

        $query = "SELECT exams.id, users.id AS user_id, exams.exam_date, exams.exam_score, classes.id AS class_id, classes.class_name, users.name, users.surname, lessons.id AS lesson_id, lessons.lesson_name
        FROM users 
        INNER JOIN exams ON exams.student_id = users.id
        INNER JOIN classes ON exams.class_id = classes.id
        INNER JOIN lessons ON exams.lesson_id = lessons.id
        WHERE users.role = 'student' AND classes.id = :classId
        ORDER BY classes.class_name";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);



        if ($filter != 0) {
            $query = "SELECT exams.id, users.id AS user_id, exams.exam_date, exams.exam_score, classes.id AS class_id, classes.class_name, users.name, users.surname, lessons.id AS lesson_id, lessons.lesson_name
            FROM users 
            INNER JOIN exams ON exams.student_id = users.id
            INNER JOIN classes ON exams.class_id = classes.id
            INNER JOIN lessons ON exams.lesson_id = lessons.id
            WHERE users.role = 'student' AND classes.id = :classId AND lessons.id = :filter
            ORDER BY classes.class_name";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':classId', $classId);
            $stmt->bindParam(':filter', $filter);
            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        list($pageCount, $start, $end) = Pages($data, $page);
    } else {
        // Eğer buraya girerse bu öğretmenin bir sınıfı yok dolayısıyla görecek exam'ı da yok.
        $data = [];
        $pageCount = 1;
    }
}

?>

<body class="bg overflow-hidden">
    <div class="flex h-full">
        <div class="w-2/12">
            <div class="h-full p-4 bg-myDark text-wheat rounded-r-2xl">
                <div class="flex text-center items-center">
                    <div class="flex flex-col w-full">
                        <?= htmlspecialchars($_SESSION['username']); ?>
                        <?= htmlspecialchars($_SESSION['role']); ?>
                    </div>
                </div>
                <div class="flex h-full justify-center">
                    <ul class="flex flex-col items-center justify-center gap-4">
                        <?php
                        if ($_SESSION['role'] == 'admin') {
                        ?>
                            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../global/chooseClass.php">Add Exam</a></li>
                            <?php
                        } else {
                            if (count($class) > 0) {
                                $id = $class[0]['id'];
                            ?>
                                <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href=<?= "../global/addExam.php?sendedData=$id" ?>>Add Exam</a></li>

                        <?php }
                        } ?>
                        <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../src/home.php">Back</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="flex flex-col w-full">
            <div class="w-full">
                <?php include_once("../includes/header.php"); ?>
            </div>
            <div class="h-full mt-4">
                <div class="flex flex-col items-center justify-center h-full p-4 text-wheat text-center">
                    <div class="bg-myDark p-4 w-full rounded-xl">
                        <?php
                        if ($_SESSION['role'] == 'teacher' && $_SESSION['class'] == -1) {
                        ?>
                            <div class="font-bold text-red-600 pb-2">You don't have a class</div>
                        <?php } ?>
                        <form action="manageExams.php" method="post">
                            <div class="flex gap-4 items-center justify-center">
                                <div class="flex gap-4 items-center justify-center">
                                    <div class="font-bold text-wheat">None</div>
                                    <div><input onclick="AutoSubmit(this)" type="radio" name="filter" value=0></div>
                                </div>
                                <?php
                                foreach ($filterArr as $filterData) {
                                ?>
                                    <div class="flex gap-4 items-center justify-center">
                                        <div class="font-bold text-wheat"><?= htmlspecialchars($filterData['class_name']) ?></div>
                                        <div><input onclick="AutoSubmit(this)" type="radio" name="filter" value=<?= $filterData['id'] ?>></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </form>
                        <div class="flex w-full justify-center gap-4 font-bold pb-2 border-solid border-b-2 border-wheat mt-4">
                            <div class="w-2/12">Exam Date</div>
                            <div class="w-1/12">Class Name</div>
                            <div class="w-1/12">Student Name</div>
                            <div class="w-2/12">Student Surname</div>
                            <div class="w-2/12">Lesson Name</div>
                            <div class="w-1/12">Exam Socre</div>
                            <div class="w-1/12">Success Avarage</div>
                            <div class="w-1/12">Update</div>
                            <div class="w-1/12">Delete</div>
                        </div>
                        <?php
                        for ($i = $start; $i < $end; $i++) {
                            if (!isset($data[$i])) {
                                continue;
                            }
                            $id = $data[$i]['id'];
                            $userId = $data[$i]['user_id'];
                            $lessonId = $data[$i]['lesson_id'];
                            $classId = $data[$i]['class_id'];

                            $examScore = htmlspecialchars($data[$i]["exam_score"]);
                        ?>
                            <div class="flex w-full justify-center gap-4 mt-4">
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['exam_date']) ?></div>
                                <div class="w-1/12"><?= htmlspecialchars($data[$i]['class_name']) ?></div>
                                <div class="w-1/12"><?= htmlspecialchars($data[$i]['name']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]["surname"]) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]["lesson_name"]) ?></div>
                                <div class="w-1/12"><?= $examScore == -1 ? "Coming" : $examScore  ?></div>
                                <div class="w-1/12"><?= $examScore == -1 ? "Coming" : $lessonAverage[$userId][$lessonId] ?></div>
                                <div class="w-1/12"><a href="<?= "../global/updateExam.php?examId=$id&studentId=$userId&lessonId=$lessonId&classId=$classId" ?>" class="fa-solid fa-pen hover:text-green-600"></a></div>
                                <div class="w-1/12"><a href=<?= "manageExams.php?delete=$id" ?> class="fa-solid fa-trash hover:text-red-600"></a></div>
                            </div>
                        <?php } ?>
                        <div class="flex gap-4 items-center justify-center">
                            <?php
                            for ($i = 0; $i < $pageCount; $i++) {
                            ?>
                                <a href="<?= "manageExams.php?page=" . ($i + 1) ?>" class="p-2 mt-4 border-solid border-b-2 border-t-2 border-transparent rounded-xl hover:border-wheat"><?= $i + 1 ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const AutoSubmit = (e) => {
            e.parentNode.parentNode.parentNode.parentNode.submit()
        }
    </script>

    <script src="https://kit.fontawesome.com/4f303a59a9.js" crossorigin="anonymous"></script>
</body>

</html>