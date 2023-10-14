<?php
session_start();
function Auth()
{
    if (!isset($_COOKIE['PHPSESSID'])) {
        return false;
    }

    if (!isset($_SESSION['userId']) && !isset($_SESSION['username']) && !isset($_SESSION['password']) && !isset($_SESSION['role']) && !isset($_SESSION['name']) && !isset($_SESSION['surname'])) {
        return false;
    }

    return true;
}

function Login($dto, $username, $password)
{
    // Girilen username e göre user'ı çeçktim.
    $user = $dto->Select("users", ["username"], [$username]);

    // Eğer boş değil ise password doğru mu diye baktım.
    // Doğru ise sessionları kaydettim.
    // Değil ise error döndürüyorum.

    // eğer $user boyutu 1 den fazla ise aynı username den fazla var.
    if ($user != null) {
        if (password_verify($password, $user[0]['password'])) {
            $_SESSION['userId'] = $user[0]['id'];
            $_SESSION['name'] = $user[0]['name'];
            $_SESSION['surname'] = $user[0]['surname'];
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $user[0]['password'];
            $_SESSION['role'] = $user[0]['role'];

            echo "<script>window.location.href ='../src/home.php'</script>";
            exit();
        }
    }

    echo "<script>window.location.href ='../src/login.php?error=1'</script>";
    exit();
  
}

function CalculateLessonAvarages($pdo, $users)
{
    $examsArr = [];
    foreach ($users as $user) {
        $id = $user['id'];

        $exams = $pdo->query("SELECT student_id,
            SUM(exam_score) AS total_exam_score,
            COUNT(*) AS total_exam_count
            FROM exams
            WHERE student_id = $id;")->fetch(PDO::FETCH_ASSOC);

        if ($exams['total_exam_count'] == 0) {
            $avarage = [$id => "Unknown"];
        } else {
            $avarage = [$id => ceil($exams['total_exam_score'] / $exams['total_exam_count'])];
        }

        $examsArr[] = $avarage;
    }

    return $examsArr;
}

function CalculateLessonAvarage($pdo, $id, $lessonId)
{
    $exams = $pdo->query("SELECT student_id,
    SUM(exam_score) AS total_exam_score,
    COUNT(*) AS total_exam_count
    FROM exams
    WHERE student_id = $id AND lesson_id = $lessonId")->fetch(PDO::FETCH_ASSOC);

    $avarage = ceil($exams['total_exam_score'] / $exams['total_exam_count']);

    return $avarage;
}

function Pages($arr, $currentPage)
{
    $currentPage = intval($currentPage);
    if($currentPage == 0){
        return [0,0,0];
    }
    
    $maxList = 8;
    $length = count($arr);

    $pageCount = ceil($length / $maxList);

    $start = ($currentPage - 1) * $maxList;
    $end = (($currentPage - 1) * $maxList) + $maxList;

    return [$pageCount, $start, $end];
}
?>