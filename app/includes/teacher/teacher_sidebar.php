<?php
    // Eğer class'ı yok sa teacher'ın your class'a gidemicek.

    $conn = $DTO->Get();
    $teacherId = $_SESSION['userId'];
    $data = $conn->query("SELECT id, class_name FROM classes WHERE class_teacher_id = $teacherId")->fetch(PDO::FETCH_ASSOC);

    // Eğer class'ı yok ise session'a class'ı -1 atıyorum.
    if(!$data){
        $_SESSION['class'] = -1;
    }else{
        $_SESSION['class'] = $data['id'];
    }
?>

<div class="h-full p-4 bg-myDark text-wheat rounded-r-2xl">
    <div class="flex text-center items-center">
        <div class="flex flex-col w-full">
            <?= htmlspecialchars($_SESSION['username']); ?>
            <?= htmlspecialchars($_SESSION['role']); ?>
        </div>
    </div>
    <div class="flex h-full justify-center">
        <ul class="flex flex-col items-center justify-center gap-4">
            <?php 
                if($data){
            ?>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../teacher/yourClass.php">Your Class</a></li>
            <?php } ?>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../teacher/yourLessons.php">Your Lessons</a></li>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../global/manageExams.php">Manage Exams</a></li>
        </ul>
    </div>
</div>