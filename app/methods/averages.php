<?php 
    $conn = new PDO("mysql:host=mysql;port=3306;dbname=yavuzlar_obs", "deneme", "deneme");

    // lesson id ve lessons_name leri çekiyorum
    $query = "SELECT distinct lessons.id, lessons.lesson_name
    FROM lessons
    INNER JOIN classes ON classes.class_teacher_id = lessons.teacher_user_id
    INNER JOIN classes_students ON classes_students.class_id = classes.id";

    $lessons = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);



    // Bütün student'leri buluyorum.
    $studentsQuery = "SELECT id, name FROM users WHERE role = 'student'";
    $students = $conn->query($studentsQuery)->fetchAll(PDO::FETCH_ASSOC);

   
    // Lesson Başarı ortalamaları
    $lessonAverage = [];
    foreach($students as $student){
        $studentId = $student['id'];

        foreach($lessons as $lesson){
            $lessonId = $lesson['id'];

            $avarageQuery = "SELECT student_id,
                SUM(exam_score) AS total_exam_score,
                COUNT(*) AS total_exam_count
                FROM exams
                WHERE student_id = $studentId AND lesson_id = $lessonId";
            
            $result = $conn->query($avarageQuery)->fetch(PDO::FETCH_ASSOC);

            

            if(isset($result['student_id'])){
                $avarage = $result['total_exam_score'] / $result['total_exam_count'];

                if (isset($lessonAverage[$studentId][$lessonId])) {
                    $lessonAverage[$studentId][$lessonId] = $avarage;
                } else {
                    $lessonAverage[$studentId][$lessonId] = $avarage;
                }
            }
            
        }
    }

    // Student Başarı ortalamaları
    $studentSuccessAverages = [];

    foreach ($lessonAverage as $userId => $averages) {
        $studentSuccessAverage = [];

        $totalAverages = 0;
        $lessonAveragesCount = 0;

        foreach ($averages as $lessonId => $average) {
            $totalAverages += $average;
            $lessonAveragesCount++;
        }

        $theAverage = $totalAverages / $lessonAveragesCount;
        $studentSuccessAverages[$userId] = $theAverage;
    }

    $classQuery = "SELECT student_id, class_id FROM classes_students";
    $classesStudents = $conn->query($classQuery)->fetchAll(PDO::FETCH_ASSOC);


    $classAndStudents = [];

foreach ($classesStudents as $students) {
    $classId = $students['class_id'];
    $studentId = $students['student_id'];

    if (!isset($classAndStudents[$classId])) {
        // Eğer sınıf kimliği ile ilişkilendirilmiş bir dizi yoksa, yeni bir dizi oluştur ve öğrenci kimliğini bu diziye ekle
        $classAndStudents[$classId] = [$studentId];
    } else {
        // Eğer sınıf kimliği ile ilişkilendirilmiş bir dizi zaten varsa, öğrenci kimliğini bu diziye eklemek için [] işareti kullanarak diziye yeni bir eleman ekleyin
        $classAndStudents[$classId][] = $studentId;
    }
}

    // Bu sınıfların genel oratalaması classId ye göre dönüyor
    $classSuccessAvarages = [];
    foreach($classAndStudents as $classId => $classes){

        $totalClassAvarages = 0;
        $avarageCount = 0;
        foreach($classes as $studentId){
            if(isset($studentSuccessAverages[$studentId])){
                $totalClassAvarages += $studentSuccessAverages[$studentId];
            }
            $avarageCount++;
        }
        $avarage = $totalClassAvarages / $avarageCount;

        if (!isset($classSuccessAvarages[$classId])) {
            // Eğer sınıf kimliği ile ilişkilendirilmiş bir dizi yoksa, yeni bir dizi oluştur ve öğrenci kimliğini bu diziye ekle
            $classSuccessAvarages[$classId] = [$avarage];
        } else {
            // Eğer sınıf kimliği ile ilişkilendirilmiş bir dizi zaten varsa, öğrenci kimliğini bu diziye eklemek için [] işareti kullanarak diziye yeni bir eleman ekleyin
            $classSuccessAvarages[$classId][] = $avarage;
        }
    }

    