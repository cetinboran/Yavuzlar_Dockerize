<?php

    function addUsersError($DTO ,$name, $surname, $username, $password, $confirmPassword){
        if($name == "" || $surname == "" || $username == "" || $password == ""){
            return 1;
        }

        $users = $DTO->Select("users", ["username"], [$username]);
        if(count($users) != 0){
            return 2;
        }

        if($password != $confirmPassword){
            return 3;
        }

        return -1;
    }

    function addUsersErrorWrite($errorId){
        switch($errorId){
            case "1":
                return "Fields cannot be empty";
            case "2":
                return "This username already being used";
            case "3":
                return "Passwords do not match";
        }
    }

    function addClassErrors($DTO, $className, $teacherId){
        if($className == ""){
            return 1;
        }

        $classes = $DTO->Select("classes", ["class_name"], [$className]);
        if(count($classes) != 0){
            return 2;
        }

        if($teacherId == 0){
            return 3;
        }

        return -1;
    }

    function addClassErrorWrite($errorId){
        switch($errorId){
            case "1":
                return "Class name cannot be empty";
            case "2":
                return "This class name already being used";
            case "3":
                return "Choose Teacher";
        }
    }

    function addLessonErrors($DTO, $lessonName, $teacherId){
        if($lessonName == ""){
            return 1;
        }

        $lessons = $DTO->Select("lessons", ["lesson_name"], [$lessonName]);
        if(count($lessons) != 0){
            return 2;
        }

        if($teacherId == 0){
            return 3;
        }

        return -1;
    }

    function addLessonErrorWrite($errorId){
        switch($errorId){
            case "1":
                return "Lesson name cannot be empty";
            case "2":
                return "This lesson name already being used";
            case "3":
                return "Choose Teacher";
        }
    }

    function addStudentToClassErrors($studentId, $classId){
        if($studentId == 0){
            return 1;
        }

        if($classId == 0){
            return 2;
        }

        return -1;
    }

    function addStudentToClassErrorWrite($errorId){
        switch($errorId){
            case "1":
                return "Choose Student";
            case "2":
                return "Choose Class";
        }
    }

    function addExamErrors($student, $lesson, &$value , $examDate){
        if($student == 0){
            return 1;
        }

        if($lesson == 0){
            return 2;
        }

        // Eğer boş girerse demmeki sınav ilerde olucak.
        if($value == ""){
            $value = -1;
            return -1;
        }
        if($value < 0 || $value > 100){
            return 3;
        }
            
        if($examDate == ""){
            return 4;
        }

        return -1;
    }

    function addExamErrorWrite($errorId){
        switch($errorId){
            case "1":
                return "Choose Student";
            case "2":
                return "Choose Lesson";
            case "3":
                return "Invalid Exam Score";
            case "4":
                return "Invalid Exam Date";
        }
    }

    function profileUpdateError($oldPassword,$oldPasswordHashed, $newPassword,$confirmPassword){
        // Kullanıcıdan girilen şifre ile veritabanındaki eski şifreyi kıyaslıyoruz
        // yanlış ise invalid credsdir izin vermicez.
        if (!password_verify($oldPassword, $oldPasswordHashed)){
            return 1;
        }

        if($newPassword != $confirmPassword){
            return 2;
        }

        return -1;
    }

    function profileUpdateErrorWrite($errorId){
        switch($errorId){
            case "1":
                return "Old password do not match";
            case "2":
                return "New Password do not match";
        }
    }