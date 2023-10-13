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
    <title>Update Lesson</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<body class="bg overflow-hidden">

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


    if(!isset($_REQUEST['sendedData'])){
        header("Location: ../admin/manageLessons.php");
        ob_end_flush();
        return;
    }else{
        $data = $DTO->Select("lessons", ["id"], [$_REQUEST['sendedData']])[0];
    }

    $conn = $DTO->Get();
    $teachers = $conn->query("SELECT users.id, users.name, users.surname FROM users WHERE role = 'teacher'")
    ->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['sendedData'])) {
        $id = $_POST['sendedData'];
        $lessonName = trim($_POST['lessonName']);
        $teacher = trim($_POST['teacher']);

        $old = $DTO->Select("lessons", ["id"], [$id])[0];

        $columns = [];
        $values = [];
        
        if($old['lesson_name'] != $lessonName){
            $columns[] = "lesson_name";
            $values[] = $lessonName;
        }
        if($old['teacher_user_id'] != $teacher && $teacher != 0){ // eğer teacher 0 geldiyse değiştirmiyordur.
            $columns[] = "teacher_user_id";
            $values[] = $teacher;
        }

        $columns[] = "id";
        $values[] = $id;

        // Eğer column boyutu 1 ise hiç bişi değişmemiştir.
        if(count($columns) == 1){
            header("Location: updateLesson.php?sendedData=$id");
            ob_end_flush();
            return;
        }


        $DTO->Update("lessons", $columns, $values);
        header("Location: ../admin/manageLessons.php");
        ob_end_flush();
        return;
    }

    ?>

    <div class="flex items-center justify-center w-full h-full">
        <form action="./updateLesson.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">

                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Lesson Name</div>
                    <div><input type="text" name="lessonName" class="outline-none pr-2 pl-2 rounded-lg p-1" value="<?= htmlspecialchars($data['lesson_name']) ?>"></div>
                </div>
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Teachers</div>
                    <div>
                        <select name="teacher">
                            <option value=0>Select Teacher</option>

                            <?php
                            foreach ($teachers as $teacher) {
                                $id = $teacher['id'];
                                $fullName = htmlspecialchars($teacher['name']) . " " . htmlspecialchars($teacher['surname']);
                            ?>
                                <option value=<?= $id ?>><?= $fullName ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="text-red-600 font-bold">
                    <?= isset($_GET['error']) ? addClassErrorWrite($_GET['error']) : "" ?>
                </div>
                <div class="text-wheat">
                    <button name="sendedData" value=<?= $_GET['sendedData'] ?> class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Update Lesson</button>
                    <a href="../admin/manageLessons.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
                </div>
            </div>
        </form>
    </div>


</body>

</html>