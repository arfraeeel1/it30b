<?php
// Database Connection
$host = "localhost";
$dbname = "it30_lab_db";
$username = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Session
session_start();

// Determine current section
$section = $_GET['section'] ?? 'students';

// Determine CRUD operation
$action = $_GET['action'] ?? '';


// ======================================================
// DELETE STUDENT
// ======================================================

if ($section === 'students' && $action === 'delete') {

    $studentId = (int) ($_GET['id'] ?? 0);

    if ($studentId > 0) {

        $stmt = $pdo->prepare("
            DELETE FROM students
            WHERE student_id = ?
        ");

        $stmt->execute([$studentId]);
    }

    header("Location: index.php?section=students");
    exit;
}


// ======================================================
// CREATE STUDENT
// ======================================================

if ($section === 'students' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['student_first_name'] ?? '');
        $lastName = trim($_POST['student_last_name'] ?? '');
        $course = trim($_POST['student_course'] ?? '');

        if ($firstName !== '' && $lastName !== '' && $course !== '') {

            $sql = "
                INSERT INTO students (
                    student_first_name,
                    student_last_name,
                    student_course
                )
                VALUES (?, ?, ?)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $firstName,
                $lastName,
                $course
            ]);

            header("Location: index.php?section=students");
            exit;
        }
    }
}


// ======================================================
// UPDATE STUDENT
// ======================================================

if ($section === 'students' && $action === 'update') {

    $studentId = (int) ($_GET['id'] ?? 0);

    if ($studentId <= 0) {
        die("Invalid Student ID");
    }

    // Update when form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['student_first_name'] ?? '');
        $lastName = trim($_POST['student_last_name'] ?? '');
        $course = trim($_POST['student_course'] ?? '');

        if ($firstName !== '' && $lastName !== '' && $course !== '') {

            $sql = "
                UPDATE students
                SET
                    student_first_name = ?,
                    student_last_name = ?,
                    student_course = ?
                WHERE student_id = ?
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $firstName,
                $lastName,
                $course,
                $studentId
            ]);

            header("Location: index.php?section=students");
            exit;
        }
    }

    // Retrieve student information
    $stmt = $pdo->prepare("
        SELECT *
        FROM students
        WHERE student_id = ?
    ");

    $stmt->execute([$studentId]);

    $student = $stmt->fetch();

    if (!$student) {
        die("Student Not Found");
    }
}


// ======================================================
// FETCH STUDENTS
// ======================================================

if ($section === 'students') {

    $stmt = $pdo->query("
        SELECT *
        FROM students
        ORDER BY student_id DESC
    ");

    $students = $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Simple Library System</title>
</head>

<body>

    <h1>Simple Library System</h1>

    <nav>
        <a href="index.php?section=students">Students</a> |
        <a href="index.php?section=books">Books</a> |
        <a href="index.php?section=borrow">Borrow</a>
    </nav>

    <hr>


    <!-- ==================================================
         STUDENTS
    =================================================== -->

    <?php if ($section === 'students'): ?>

        <h1>Students</h1>

        <p>
            <a href="index.php?section=students&action=create">
                Add Student
            </a>
        </p>


        <!-- CREATE STUDENT -->

        <?php if ($action === 'create'): ?>

            <h2>Create Student</h2>

            <form method="POST">

                <p>
                    <label>First Name:</label>
                    <br>

                    <input
                        type="text"
                        name="student_first_name"
                        required
                    >
                </p>

                <p>
                    <label>Last Name:</label>
                    <br>

                    <input
                        type="text"
                        name="student_last_name"
                        required
                    >
                </p>

                <p>
                    <label>Course:</label>
                    <br>

                    <input
                        type="text"
                        name="student_course"
                        required
                    >
                </p>

                <button type="submit">
                    Save
                </button>

                <a href="index.php?section=students">
                    Cancel
                </a>

            </form>


        <!-- UPDATE STUDENT -->

        <?php elseif ($action === 'update'): ?>

            <h2>Update Student Info</h2>

            <form method="POST">

                <p>
                    <label>First Name:</label>
                    <br>

                    <input
                        type="text"
                        name="student_first_name"
                        value="<?= htmlspecialchars($student['student_first_name']) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Last Name:</label>
                    <br>

                    <input
                        type="text"
                        name="student_last_name"
                        value="<?= htmlspecialchars($student['student_last_name']) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Course:</label>
                    <br>

                    <input
                        type="text"
                        name="student_course"
                        value="<?= htmlspecialchars($student['student_course']) ?>"
                        required
                    >
                </p>

                <button type="submit">
                    Update
                </button>

                <a href="index.php?section=students">
                    Cancel
                </a>

            </form>


        <!-- STUDENT LIST -->

        <?php else: ?>

            <table border="1" cellpadding="8">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Course</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($students)): ?>

                        <tr>
                            <td colspan="6">
                                No students found.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($students as $student): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($student['student_id']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($student['student_first_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($student['student_last_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($student['student_course']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($student['student_created_at']) ?>
                                </td>

                                <td>

                                    <a href="index.php?section=students&action=update&id=<?= $student['student_id'] ?>">
                                        Edit
                                    </a>

                                    |

                                    <a
                                        href="index.php?section=students&action=delete&id=<?= $student['student_id'] ?>"
                                        onclick="return confirm('Are you sure you want to delete this student?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        <?php endif; ?>

    <?php endif; ?>


    <?php if ($section === 'books'): ?>

        <h1>Books</h1>

        <p>
        </p>

    <?php endif; ?>


    <?php if ($section === 'borrow'): ?>

        <h1>Borrow</h1>

        <p>

        </p>

    <?php endif; ?>

</body>

</html>
