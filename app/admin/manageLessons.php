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
    <title>Manage Lessons</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

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

// distinct ile aynı olmayanları döndürüyor sadece.
$filterArr = $conn->query("SELECT distinct users.id, users.name FROM users
INNER JOIN lessons ON users.id = lessons.teacher_user_id");

$filter = isset($_POST['filter']) ? $_POST['filter'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

$query = "SELECT lessons.id ,lessons.lesson_name, users.name, users.surname, classes.class_name
FROM lessons
LEFT JOIN users ON lessons.teacher_user_id = users.id
LEFT JOIN classes ON lessons.teacher_user_id = classes.class_teacher_id
WHERE lessons.lesson_name IS NOT NULL;";

$data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

if ($filter != 0) {
    $query = "SELECT lessons.id ,lessons.lesson_name, users.name, users.surname, classes.class_name
    FROM lessons
    LEFT JOIN users ON lessons.teacher_user_id = users.id
    LEFT JOIN classes ON lessons.teacher_user_id = classes.class_teacher_id
    WHERE lessons.lesson_name IS NOT NULL AND users.id = :filter";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":filter", $filter);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

list($pageCount, $start, $end) = Pages($data, $page);

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $DTO->Delete("lessons", ["id"], [$id]);
    header("Location: manageLessons.php");
    ob_end_flush();
    return;
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
                        <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../adminSQL/addLesson.php">Add Lesson</a></li>
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
                <div class="flex flex-col justify-center items-centerp-4 text-wheat text-center  w-full h-full p-4">
                    <div class="bg-myDark p-4 rounded-xl">
                        <form action="manageLessons.php" method="post">
                            <div class="flex gap-4 items-center justify-center">
                                <div class="flex gap-4 items-center justify-center">
                                    <div class="font-bold text-wheat">None</div>
                                    <div><input onclick="AutoSubmit(this)" type="radio" name="filter" value=0></div>
                                </div>
                                <?php
                                foreach ($filterArr as $filterData) {
                                ?>
                                    <div class="flex gap-4 items-center justify-center">
                                        <div class="font-bold text-wheat"><?= $filterData['name'] ?></div>
                                        <div><input onclick="AutoSubmit(this)" type="radio" name="filter" value=<?= $filterData['id'] ?>></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </form>
                        <div class="flex w-full justify-center gap-4 font-bold pb-2 border-solid border-b-2 border-wheat mt-4">
                            <div class="w-4/12">Lesson Name</div>
                            <div class="w-3/12">Lesson Teacher</div>
                            <div class="w-3/12">Class Name</div>
                            <div class="w-1/12">Update</div>
                            <div class="w-1/12">Delete</div>
                        </div>
                        <?php
                        for ($i = $start; $i < $end; $i++) {
                            if (!isset($data[$i])) {
                                continue;
                            }
                            $id = $data[$i]['id'];
                        ?>
                            <div class="flex w-full justify-center gap-4 mt-4">
                                <div class="w-4/12"><?= htmlspecialchars($data[$i]['lesson_name']) ?></div>
                                <div class="w-3/12"><?= htmlspecialchars($data[$i]['name']) . " " . htmlspecialchars($data[$i]['surname']) ?></div>
                                <div class="w-3/12"><?= htmlspecialchars($data[$i]['class_name']) ?></div>
                                <div class="w-1/12"><a href=<?= "../adminSQL/updateLesson.php?sendedData=$id" ?> class="fa-solid fa-pen hover:text-green-600"></a></div>
                                <div class="w-1/12"><a href=<?= "manageLessons.php?delete=$id" ?> class="fa-solid fa-trash hover:text-red-600"></a></div>
                            </div>
                        <?php } ?>
                        <div class="flex gap-4 items-center justify-center">
                            <?php
                            for ($i = 0; $i < $pageCount; $i++) {
                            ?>
                                <a href="<?= "manageLessons.php?page=" . ($i + 1) ?>" class="p-2 mt-4 border-solid border-b-2 border-t-2 border-transparent rounded-xl hover:border-wheat"><?= $i + 1 ?></a>
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