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
    <title>Manage Classes</title>

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

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$query = "SELECT classes.id,classes.class_teacher_id, classes.class_name, users.name, users.surname,
    (SELECT COUNT(*) FROM classes_students WHERE classes_students.class_id = classes.id) AS student_count
FROM classes
LEFT JOIN users ON users.id = classes.class_teacher_id";

$data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

list($pageCount, $start, $end) = Pages($data, $page);

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $DTO->Delete("classes", ["id"], [$id]);
    header("Location: manageClasses.php");
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
                        <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../adminSQL/addClass.php">Add Class</a></li>
                        <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../adminSQL/addStudentToClass.php">Add Student To a Class</a></li>
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
                <div class="flex flex-col items-center justify-center w-full h-full p-4 text-wheat text-center">
                    <div class="bg-myDark p-4 w-full rounded-xl">
                        <div class="flex w-full justify-center gap-4 font-bold pb-2 border-solid border-b-2 border-wheat mt-4">
                            <div class="w-2/12">Class Name</div>
                            <div class="w-2/12">Class Teacher</div>
                            <div class="w-2/12">Class Success Avarage</div>
                            <div class="w-2/12">Student Count</div>
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
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['class_name']) ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['name']) . " " . htmlspecialchars($data[$i]['surname']) ?></div>
                                <div class="w-2/12"> <?= isset($classSuccessAvarages[$id][0]) ? $classSuccessAvarages[$id][0]: 0 ?></div>
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['student_count']) ?></div>
                                <div class="w-2/12"><a href=<?= "../adminSQL/updateClass.php?sendedData=$id" ?> class="fa-solid fa-pen hover:text-green-600"></a></div>
                                <div class="w-2/12"><a href=<?= "manageClasses.php?delete=$id" ?> class="fa-solid fa-trash hover:text-red-600"></a></div>
                            </div>
                        <?php } ?>
                        <div class="flex gap-4 items-center justify-center">
                            <?php
                            for ($i = 0; $i < $pageCount; $i++) {
                            ?>
                                <a href="<?= "manageClasses.php?page=" . ($i + 1) ?>" class="p-2 mt-4 border-solid border-b-2 border-t-2 border-transparent rounded-xl hover:border-wheat"><?= $i + 1 ?></a>
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