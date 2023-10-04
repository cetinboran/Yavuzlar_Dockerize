<?php
$pdo = $DTO->Get();

    $studentCount = $pdo->query("SELECT COUNT(*) AS user_count
        FROM users
        WHERE role = 'student'")->fetch(PDO::FETCH_ASSOC);

    $teacherCount = $pdo->query("SELECT COUNT(*) AS user_count
    FROM users
    WHERE role = 'teacher'")->fetch(PDO::FETCH_ASSOC);

    $classCount = $pdo->query("SELECT COUNT(*) AS class_count
    FROM classes")->fetch(PDO::FETCH_ASSOC);

?>

<div class="flex h-full items-center justify-center gap-4 text-wheat">
    <div class="bg-myDark p-4 text-center rounded-lg">
        <div>Student Count</div>
        <div><?= $studentCount['user_count'] ?></div>
    </div>
    <div class="bg-myDark p-4 text-center rounded-lg">
        <div>Teacher Count</div>
        <div><?= $teacherCount['user_count'] ?></div>
    </div>
    <div class="bg-myDark p-4 text-center rounded-lg">
        <div>Class Count</div>
        <div><?= $classCount['class_count'] ?></div>
    </div>

</div>