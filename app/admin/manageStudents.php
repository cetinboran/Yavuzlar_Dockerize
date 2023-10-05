<?php
require_once("../dto/dto.php");
require_once("../methods/averages.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["root", "Boran123.", "yavuzlar_obs"]);
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

$query = "SELECT users.id, users.name, users.surname, classes.class_name
    FROM users
    LEFT JOIN classes_students ON users.id = classes_students.student_id
    LEFT JOIN classes ON classes.id = classes_students.class_id
    WHERE users.role = 'student'";

$data = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

if ($filter != 0) {
    $query = "SELECT users.id, users.name, users.surname, classes.class_name
        FROM users
        LEFT JOIN classes_students ON users.id = classes_students.student_id
        LEFT JOIN classes ON classes.id = classes_students.class_id
        WHERE users.role = 'student' AND classes.id = :filter";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(":filter", $filter);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$avarage = CalculateLessonAvarages($conn, $data);
list($pageCount, $start, $end) = Pages($data, $page);

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $DTO->Delete("classes_students", ["student_id"], [$id]); // Böyle öğrenci bir sınıfta ise onu da siliyoruz.
    $DTO->Delete("exams", ["student_id"],[$id]); // kayıtlı sınavı varsa onu da siliyoruz.
    $DTO->Delete("users", ["role", "id"], ["student", $id]);

    echo "<script>window.location.href ='manageStudents.php'</script>";
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
                        <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../adminSQL/addStudent.php">Add Student</a></li>
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
                        <form action="manageStudents.php" method="post">
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
                            <div class="w-3/12">Class</div>
                            <div class="w-2/12">Overoll Success Avarage</div>
                            <div class="w-1/12">Update</div>
                            <div class="w-1/12">Delete</div>
                            <div class="w-1/12">View</div>
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
                                <div class="w-2/12"><?= htmlspecialchars($data[$i]['surname'] )?></div>
                                <div class="w-3/12"><?= htmlspecialchars($data[$i]['class_name']) ?></div>
                                <div class="w-2/12"><?= isset($studentSuccessAverages[$id]) ? $studentSuccessAverages[$id]:"No Exam" ?></div>
                                <div class="w-1/12"><a href=<?= "../adminSQL/updateStudent.php?sendedData=$id" ?> class="fa-solid fa-pen hover:text-green-600"></a></div>
                                <div class="w-1/12"><a href=<?= "manageStudents.php?delete=$id" ?> class="fa-solid fa-trash hover:text-red-600"></a></div>
                                <div class="w-1/12"><a href=<?= "../adminSQL/viewStudent.php?sendedData=$id" ?> class="fa-solid fa-eye hover:text-blue-600"></a></div>
                            </div>
                        <?php } ?>
                        <div class="flex gap-4 items-center justify-center">
                            <?php
                            for ($i = 0; $i < $pageCount; $i++) {
                            ?>
                                <a href="<?= "manageStudents.php?page=" .($i + 1) ?>" class="p-2 mt-4 border-solid border-b-2 border-t-2 border-transparent rounded-xl hover:border-wheat"><?= $i + 1 ?></a>
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