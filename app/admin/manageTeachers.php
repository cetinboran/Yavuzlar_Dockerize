<?php
ob_start();
require_once("../dto/dto.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["deneme", "deneme.", "yavuzlar_obs"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<?php
if (!Auth()) {
    echo "<script>window.location.href ='../includes/forbidden.php'</script>";
    exit();
}

if ($_SESSION['role'] != "admin") {
    echo "<script>window.location.href ='../includes/forbidden.php'</script>";
    exit();
}

$conn = $DTO->Get();

$filterArr = $DTO->Select("classes", [], []);
$filter = isset($_POST['filter']) ? $_POST['filter'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$query = "SELECT users.id, users.name, users.surname, classes.class_name, GROUP_CONCAT(lessons.lesson_name) AS lesson_names
    FROM users
    LEFT JOIN lessons ON lessons.teacher_user_id = users.id
    LEFT JOIN classes ON classes.class_teacher_id = users.id
    WHERE users.role = 'teacher'
    GROUP BY users.id, users.name, users.surname, classes.class_name;";

$data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

if ($filter != 0) {
    $query = "SELECT users.id, users.name, users.surname, classes.class_name, GROUP_CONCAT(lessons.lesson_name) AS lesson_names
    FROM users
    LEFT JOIN lessons ON lessons.teacher_user_id = users.id
    LEFT JOIN classes ON classes.class_teacher_id = users.id
    WHERE users.role = 'teacher' AND classes.id = :filter
    GROUP BY users.id, users.name, users.surname, classes.class_name;";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":filter", $filter);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

list($pageCount, $start, $end) = Pages($data, $page);

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Normalde silmemen lazım ama sıkıntı çıktığı için siliyorsun
    // Normalde teacher silinince lessonun teacher'ı null olmalı sonradan atama felan yapılabilmeli

    $conn->exec("UPDATE lessons SET teacher_user_id = NULL WHERE teacher_user_id = $id");
    $conn->exec("UPDATE classes SET class_teacher_id = NULL WHERE class_teacher_id = $id");

    $stmt = $conn->prepare("DELETE FROM users WHERE role = 'teacher' AND id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("Location: manageTeachers.php");
    ob_end_flush();
    exit();
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
                        <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../adminSQL/addTeacher.php">Add Teacher</a></li>
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
                        <form action="manageTeachers.php" method="post">
                            <div class="flex gap-4 items-center justify-center">
                                <div class="flex gap-4 items-center justify-center">
                                    <div class="font-bold text-wheat">None</div>
                                    <div><input onclick="AutoSubmit(this)" type="radio" name="filter" value=0></div>
                                </div>
                                <?php
                                foreach ($filterArr as $filterData) {
                                ?>
                                    <div class="flex gap-4 items-center justify-center">
                                        <div class="font-bold text-wheat"><?= $filterData['class_name'] ?></div>
                                        <div><input onclick="AutoSubmit(this)" type="radio" name="filter" value=<?= $filterData['id'] ?>></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </form>
                        <div class="flex w-full justify-center gap-4 font-bold pb-2 border-solid border-b-2 border-wheat mt-4">
                            <div class="w-2/12">Name</div>
                            <div class="w-2/12">Surname</div>
                            <div class="w-2/12">Teacher's Class</div>
                            <div class="w-2/12">Teacher's Lessons</div>
                            <div class="w-2/12">Update</div>
                            <div class="w-2/12">Delete</div>
                        </div>
                        <?php
                        for ($i = $start; $i < $end; $i++) {
                            if (!isset($data[$i])) {
                                continue;
                            }
                            $id = $data[$i]['id'];
                        ?>
                            <div class="flex w-full justify-center gap-4 mt-4">
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['name']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['surname']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['class_name']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]["lesson_names"]) ?></div>
                                <div class="w-2/12"><a href="<?= "../adminSQL/updateTeacher.php?sendedData=$id" ?>" class="fa-solid fa-pen hover:text-green-600"></a></div>
                                <div class="w-2/12"><a href=<?= "manageTeachers.php?delete=$id" ?> class="fa-solid fa-trash hover:text-red-600"></a></div>
                            </div>
                        <?php } ?>
                        <div class="flex gap-4 items-center justify-center">
                            <?php
                            for ($i = 0; $i < $pageCount; $i++) {
                            ?>
                                <a href="<?= "manageTeachers.php?page=" . ($i + 1) ?>" class="p-2 mt-4 border-solid border-b-2 border-t-2 border-transparent rounded-xl hover:border-wheat"><?= $i + 1 ?></a>
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