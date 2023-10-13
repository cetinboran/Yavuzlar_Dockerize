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
    <title>Choose Class</title>

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

    $classes = $DTO->Select("classes", [], []);
    ?>


    <div class="flex items-center justify-center w-full h-full">
        <form action="./addExam.php" method="POST">
            <div class="flex items-center flex-col gap-4 bg-myDark p-8 text-center rounded-lg">
                <div class="flex w-full justify-between gap-4">
                    <div class="text-wheat font-bold">Class</div>
                    <div>
                        <select name="sendedData">
                            <option value=0>Select Class</option>
                            <?php
                            foreach ($classes as $class) {
                                $id = $class['id'];
                            ?>
                                <option value=<?= $id ?> "><?= htmlspecialchars($class['class_name']) ?></option>
                                <?php } ?>
                        </select>
                    </div>
                </div>
                <div class=" flex flex-col w-full justify-between gap-4">
                    <div class="text-red-600 font-bold">
                        <?= isset($_GET['error']) ? "Please Choose Valid Class" : "" ?>
                    </div>
                    <div class="text-wheat">
                        <button class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat" type="submit">Go to Exam</button>
                        <a href="../global/manageExams.php" class="p-4 border-solid border-b-2 border-t-2 border-transparent rounded-full hover:border-wheat">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>