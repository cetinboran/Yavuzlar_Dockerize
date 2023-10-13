<?php
require_once("../dto/dto.php");
require_once("../methods/methods.php");

$DTO = new DTO("", "mysql", ["deneme", "deneme.", "yavuzlar_obs"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="stylesheet" href="../dist/output.css">
</head>

<?php
if (!Auth()) {
    echo "<script>window.location.href ='../includes/forbidden.php'</script>";
    exit();
}

$role = $_SESSION['role'];
?>

<body class="bg overflow-hidden">
    <div class="flex h-full">
        <div class="w-2/12">
            <?php
            switch ($role) {
                case "admin":
                    include_once("../includes/admin/admin_sidebar.php");
                    break;
                case "teacher":
                    include_once("../includes/teacher/teacher_sidebar.php");
                    break;
                case "student":
                    include_once("../includes/student/student_sidebar.php");
                    break;
            }
            ?>
        </div>
        <div class="flex flex-col w-full">
            <div class="w-full">
                <?php include_once("../includes/header.php"); ?>
            </div>
            <div class="h-full">
                <?php
                switch ($role) {
                    case "admin":
                        include_once("../admin/adminContent.php");
                        break;
                    case "teacher":
                        include_once("../teacher/teacherContent.php");
                        break;
                    case "student":
                        include_once("../student/studentContent.php");
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>