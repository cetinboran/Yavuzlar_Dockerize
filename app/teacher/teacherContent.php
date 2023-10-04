<?php
require_once("../methods/averages.php");

$pdo = $DTO->Get();
$teacherId = $_SESSION['userId'];
$data = $conn->query("SELECT id, class_name FROM classes WHERE class_teacher_id = $teacherId")->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $classId = $data['id'];
    $studentCount = $pdo->query("SELECT COUNT(*) AS user_count
        FROM classes_students
        WHERE class_id = $classId")->fetch(PDO::FETCH_ASSOC);
} else {
    $studentCount = ["user_count" => "You dont have class"];
}
?>

<div class="flex h-full items-center justify-center gap-4 text-wheat">
    <div class="bg-myDark p-4 text-center rounded-lg font-bold">
        <div class="font-bold text-center flex flex-col gap-2">
            <div>Student Count</div>
            <div><?= $studentCount['user_count'] ?></div>
        </div>

    </div>
    <div class="bg-myDark p-4 text-center rounded-lg font-bold">
        <div class="font-bold text-center flex flex-col gap-2">
            <div>Overall Class Success Average</div>
            <div><?= $classSuccessAvarages[$classId][0] ?></div>
        </div>
    </div>
</div>