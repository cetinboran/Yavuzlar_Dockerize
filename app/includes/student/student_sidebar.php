<?php
    $conn = $DTO->Get();

    // Burada giriş yapan kullanıcının Id sine göre class adını öğreniyorum ve classId sini öğreniyorum.
    $studentId = $_SESSION['userId'];
    $class = $conn->query("SELECT classes_students.student_id, classes_students.class_id, classes.class_name, classes.id
    FROM classes_students, classes
    WHERE classes_students.class_id = classes.id AND classes_students.student_id = $studentId;")
    ->fetch(PDO::FETCH_ASSOC);

    if($class){
        $_SESSION['classId'] = $class['id'];
        $classId = $class['id'];
    }else{
        $_SESSION['classId'] = -1;
        $classId = -1;
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
                if($classId != -1){
            ?>
                <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../student/myClass.php">My Class</a></li>
                <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../student/myLessons.php">My Lessons & Exams</a></li>
            <?php } else { echo "You are not registered any class."; }?>
        </ul>
    </div>
</div>