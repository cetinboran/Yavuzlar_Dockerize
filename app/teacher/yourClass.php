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
    <title>Your Class</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

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

if(isset($_SESSION['class'])){
    if($_SESSION['class'] == -1){
        header("Location: ../src/home.php");
        ob_end_flush();
        return;
    }
}

$conn = $DTO->Get();

$teacherId = $_SESSION['userId'];
$data = $conn->query("SELECT id, class_name FROM classes WHERE class_teacher_id = $teacherId")->fetch(PDO::FETCH_ASSOC);

$classId = $data['id'];

$studentCount = $conn->query("SELECT COUNT(*) AS count FROM classes_students WHERE  class_id = $classId")->fetch(PDO::FETCH_ASSOC);

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
                        <div class="flex flex-col gap-4 font-bold">
                            <div><?= $data['class_name'] ?></div>
                            <div>Student Count: <?= $studentCount['count'] ?></div>
                            <div><a href=<?= "../teacherSQL/viewStudents.php?sendedData=$classId" ?> class="fa-solid fa-eye hover:text-blue-600"></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/4f303a59a9.js" crossorigin="anonymous"></script>
</body>

</html>