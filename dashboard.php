
<?php
// ---------- DATABASE CONNECTION ----------
$host = 'localhost';
$db   = 'it30_lab_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// ---------- 1. KPI COUNTS ----------
$studentCount = $pdo->query(
    "SELECT COUNT(*) FROM students"
)->fetchColumn();

$bookCount = $pdo->query(
    "SELECT COUNT(*) FROM books"
)->fetchColumn();

$totalBorrows = $pdo->query(
    "SELECT COUNT(*) FROM borrow"
)->fetchColumn();

$activeBorrows = $pdo->query("
    SELECT COUNT(*)
    FROM borrow
    WHERE borrow_return_date IS NULL
")->fetchColumn();


// ---------- 2. BOOKS BY CATEGORY ----------
$catStmt = $pdo->query("
    SELECT book_category, COUNT(*) AS cnt
    FROM books
    GROUP BY book_category
    ORDER BY cnt DESC
");

$categories = [];
$categoryCounts = [];

while ($row = $catStmt->fetch()) {
    $categories[] = $row['book_category'];
    $categoryCounts[] = (int) $row['cnt'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Library · Dashboard</title>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Roboto Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        /* ---------- GENERAL RESET ---------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            width: 100%;
            min-height: 100%;
        }

        body {
            width: 100%;
            min-height: 100vh;
            background: #f5f5f5;
            font-family: 'Roboto', system-ui, sans-serif;
            color: #212121;
            padding: 24px;
        }

        /* ---------- MAIN DASHBOARD ---------- */
        .dashboard {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }

        .dashboard h1 {
            font-size: 2rem;
            font-weight: 400;
            margin-bottom: 24px;
        }

        /* ---------- CARDS ---------- */
        .stat-card,
        .chart-card,
        .recent-borrows {
            background: #ffffff;
            border-radius: 6px;
            box-shadow:
                0 2px 2px rgba(0, 0, 0, 0.14),
                0 3px 1px -2px rgba(0, 0, 0, 0.12),
                0 1px 5px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.3s ease;
        }

        .stat-card:hover,
        .chart-card:hover,
        .recent-borrows:hover {
            box-shadow:
                0 4px 8px rgba(0, 0, 0, 0.14),
                0 2px 10px rgba(0, 0, 0, 0.12);
        }

        /* ---------- KPI CARDS ---------- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            width: 100%;
            margin: 28px 0 32px;
        }

        .stat-card {
            width: 100%;
            min-width: 0;
            padding: 24px;
            display: flex;
            flex-direction: column;
            border-top: 3px solid #1976d2;
        }

        .stat-card:nth-child(2) {
            border-top-color: #4caf50;
        }

        .stat-card:nth-child(3) {
            border-top-color: #ff9800;
        }

        .stat-card:nth-child(4) {
            border-top-color: #f44336;
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            color: #757575;
        }

        .stat-header .material-icons {
            font-size: 1.8rem;
            color: #bdbdbd;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 400;
            color: #212121;
            line-height: 1.2;
        }

        .stat-sub {
            font-size: 0.85rem;
            color: #9e9e9e;
            margin-top: 6px;
        }

        /* ---------- CHARTS ---------- */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 420px), 1fr));
            gap: 24px;
            width: 100%;
            margin-bottom: 32px;
        }

        .chart-card {
            width: 100%;
            min-width: 0;
            padding: 24px;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212121;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
        }

        .chart-title span {
            font-weight: 500;
            color: #1976d2;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .chart-title span .material-icons {
            font-size: 1rem;
        }

        /* Chart sizing */
        canvas {
            display: block;
            width: 100% !important;
            max-width: 700px;
            height: 320px !important;
            margin: 0 auto;
        }

        /* ---------- RECENT BORROWS ---------- */
        .recent-borrows {
            width: 100%;
            padding: 24px;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
        }

        .recent-header h3 {
            font-weight: 500;
            font-size: 1.1rem;
            color: #212121;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .recent-header h3 .material-icons {
            color: #1976d2;
        }

        .recent-header a {
            font-size: 0.85rem;
            color: #1976d2;
            font-weight: 500;
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .recent-header a:hover {
            text-decoration: underline;
        }

        .borrow-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px solid #eeeeee;
            font-size: 0.95rem;
        }

        .borrow-row:last-child {
            border-bottom: none;
        }

        .borrow-student {
            font-weight: 500;
            color: #424242;
            width: 30%;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .borrow-book {
            color: #616161;
            width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .borrow-student .material-icons,
        .borrow-book .material-icons {
            font-size: 1.1rem;
            color: #9e9e9e;
        }

        /* ---------- BORROW STATUS ---------- */
        .borrow-status {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 16px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .borrow-status .material-icons {
            font-size: 0.9rem;
        }

        .borrow-status.returned {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .borrow-status.borrowed {
            background: #fff3e0;
            color: #ef6c00;
        }

        /* ---------- FOOTER ---------- */
        .footer-note {
            width: 100%;
            margin-top: 32px;
            padding-bottom: 12px;
            text-align: center;
            font-size: 0.8rem;
            color: #9e9e9e;
            letter-spacing: 0.5px;
        }

        /* ---------- TABLET RESPONSIVE ---------- */
        @media (max-width: 1000px) {
            body {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ---------- MOBILE RESPONSIVE ---------- */
        @media (max-width: 600px) {
            body {
                padding: 14px;
            }

            .dashboard h1 {
                font-size: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .stat-card {
                padding: 20px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .chart-card {
                padding: 16px;
            }

            .chart-title {
                font-size: 1rem;
                flex-wrap: wrap;
            }

            canvas {
                height: 280px !important;
            }

            .borrow-row {
                flex-wrap: wrap;
            }

            .borrow-student,
            .borrow-book {
                width: 100%;
            }

            .recent-header {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard">

        <h1>Welcome to Dashboard</h1>

        <!-- ---------- KPI STATISTICS ---------- -->
        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Students</span>
                    <span class="material-icons">school</span>
                </div>

                <div class="stat-number">
                    <?= (int) $studentCount ?>
                </div>

                <div class="stat-sub">enrolled</div>
            </div>


            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Books</span>
                    <span class="material-icons">auto_stories</span>
                </div>

                <div class="stat-number">
                    <?= (int) $bookCount ?>
                </div>

                <div class="stat-sub">in collection</div>
            </div>


            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Borrow records</span>
                    <span class="material-icons">history</span>
                </div>

                <div class="stat-number">
                    <?= (int) $totalBorrows ?>
                </div>

                <div class="stat-sub">all time</div>
            </div>


            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Active loans</span>
                    <span class="material-icons">pending_actions</span>
                </div>

                <div class="stat-number">
                    <?= (int) $activeBorrows ?>
                </div>

                <div class="stat-sub">not returned</div>
            </div>

        </div>


        <!-- ---------- CHARTS ---------- -->
        <div class="charts-grid">

            <!-- Doughnut Chart - Books by Category -->
            <div class="chart-card">

                <div class="chart-title">
                    Books by category

                    <span>
                        <span class="material-icons">category</span>
                        collection
                    </span>
                </div>

                <canvas id="categoryChart"></canvas>

            </div>

        </div>


        <!-- ---------- FOOTER ---------- -->
        <div class="footer-note">
            LIBRARY MANAGEMENT SYSTEM
        </div>

    </div>


    <!-- ---------- CHART.JS SCRIPT ---------- -->
    <script>
        (function () {

            const colors = {
                blue: '#1976d2',
                green: '#4caf50',
                orange: '#ff9800',
                red: '#f44336',
                purple: '#9c27b0',
                grey: '#9e9e9e'
            };

            // ---------- DOUGHNUT: BOOKS BY CATEGORY ----------
            const catCanvas = document.getElementById('categoryChart');

            if (catCanvas) {

                const catCtx = catCanvas.getContext('2d');

                new Chart(catCtx, {
                    type: 'doughnut',

                    data: {
                        labels: <?= json_encode($categories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,

                        datasets: [{
                            data: <?= json_encode($categoryCounts) ?>,

                            backgroundColor: [
                                colors.blue,
                                colors.green,
                                colors.orange,
                                colors.red,
                                colors.purple,
                                colors.grey
                            ],

                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',

                        plugins: {

                            legend: {
                                position: 'bottom',

                                labels: {
                                    boxWidth: 12,
                                    boxHeight: 12,
                                    usePointStyle: false,
                                    padding: 16,

                                    font: {
                                        size: 12,
                                        family: 'Roboto'
                                    },

                                    color: '#616161'
                                }
                            },

                            tooltip: {
                                backgroundColor: '#212121',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 10,
                                cornerRadius: 4,
                                displayColors: true,

                                callbacks: {
                                    label: function (context) {

                                        const label = context.label || '';
                                        const value = context.parsed || 0;

                                        const total = context.dataset.data.reduce(
                                            (a, b) => a + b, 0
                                        );

                                        const pct = total > 0
                                            ? ((value / total) * 100).toFixed(0)
                                            : 0;

                                        return ` ${label}: ${value} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

        })();
    </script>

</body>
</html>
