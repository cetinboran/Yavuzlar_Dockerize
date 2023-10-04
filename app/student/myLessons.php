<?php
ob_start();
require_once("../dto/dto.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["root", "Boran123.", "yavuzlar_obs"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Lessons</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<?php
if (!Auth()) {
    header("Location: ../includes/forbidden.php");
    ob_end_flush();
    return;
}

if ($_SESSION['role'] != "student") {
    header("Location: ../includes/forbidden.php");
    ob_end_flush();
    return;
}

$conn = $DTO->Get();

if($_SESSION['classId'] != -1){
    $userId = $_SESSION['userId'];
    $classId = $_SESSION['classId'];
    $query = "SELECT lessons.id, lessons.lesson_name, exams.id AS exam_id, exams.exam_score, exams.exam_date
    FROM exams
    INNER JOIN lessons ON lessons.id = exams.lesson_id
    WHERE exams.student_id = :userId";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

}
$page = isset($_GET['page']) ? $_GET['page'] : 1;

list($pageCount, $start, $end) = Pages($data, $page);

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
                <div class="flex items-center justify-center h-full p-4">
                    <div class="bg-myDark p-4 text-wheat text-center w-full rounded-xl">
                        <div class="flex w-full justify-center gap-4 font-bold pb-2 border-solid border-b-2 border-wheat mt-4">
                            <div class="w-2/12">Lesson Name</div>
                            <div class="w-2/12">Exam Score</div>
                            <div class="w-2/12">Exam Date</div>
                        </div>
                        <?php
                        $count = 1;
                        for ($i = $start; $i < $end; $i++) {
                            if (!isset($data[$i])) {
                                continue;
                            }
                        ?>
                            <div class="flex w-full justify-center gap-4 mt-4">
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['lesson_name']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['exam_score']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['exam_date']) ?></div>
                            </div>
                        <?php $count++; } ?>
                        <div class="flex gap-4 items-center justify-center">
                            <?php
                            for ($i = 0; $i < $pageCount; $i++) {
                            ?>
                                <a href="<?= "myLessons.php?page=" . ($i + 1) ?>" class="p-2 mt-4 border-solid border-b-2 border-t-2 border-transparent rounded-xl hover:border-wheat"><?= $i + 1 ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/4f303a59a9.js" crossorigin="anonymous"></script>
</body>

</html>