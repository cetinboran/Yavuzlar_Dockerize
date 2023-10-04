<div class="h-full p-4 bg-myDark text-wheat rounded-r-2xl">
    <div class="flex text-center items-center">
        <div class="flex flex-col w-full">
            <?= htmlspecialchars($_SESSION['username']); ?>
            <?= htmlspecialchars($_SESSION['role']); ?>
        </div>
    </div>
    <div class="flex h-full justify-center">
        <ul class="flex flex-col items-center justify-center gap-4">
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../admin/manageStudents.php">Manage Students</a></li>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../admin/manageTeachers.php">Manage Teachers</a></li>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../admin/manageClasses.php">Manage Classes</a></li>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../admin/manageLessons.php">Manage Lessons</a></li>
            <li class="w-full text-center"><a class="block p-2 border-solid border-b-2 border-transparent hover:border-wheat hover:transition-all hover:duration-100" href="../global/manageExams.php">Manage Exams</a></li>
        </ul>
    </div>
</div>