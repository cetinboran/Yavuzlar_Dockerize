<?php
    require_once("../methods/averages.php");

    $pdo = $DTO->Get();

    $studentId = $_SESSION['userId'];
    $examCount = $pdo->query("SELECT COUNT(*) AS exam_count
    FROM exams
    WHERE student_id = $studentId")
    ->fetch(PDO::FETCH_ASSOC);
?>

<div class="flex h-full items-center justify-center gap-6 text-wheat">
    <div class="flex flex-col gap-2 bg-myDark p-4 text-center rounded-lg font-bold">
        <div>Exam Count</div>
        <div><?= $examCount['exam_count'] ?></div>
    </div>
    <div class="flex flex-col gap-2 bg-myDark p-4 text-center rounded-lg font-bold">
        <div>Overall success average</div>
        <div><?= $studentSuccessAverages[$studentId] ?></div>
    </div>

</div>