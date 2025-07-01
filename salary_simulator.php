<?php
session_start();

// ユーザー認証の確認
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: login.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? '');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// データベース接続情報
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // ★重要★ あなたのXAMPPのMySQLパスワードが空欄でなければここに設定
$db_name = 'inquiry'; // ★重要★ データベース名が 'inquiry' であることを確認

// データベースに接続
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 接続エラーの確認
if ($conn->connect_error) {
    die("データベース接続エラー: " . $conn->connect_error);
}

// salary_dataテーブルからユニークな職種名のみを取得（プルダウン用）
$job_titles = [];
$sql_jobs = "SELECT DISTINCT job_title FROM salary_data ORDER BY job_title ASC";
$result_jobs = $conn->query($sql_jobs);

if ($result_jobs) {
    while ($row = $result_jobs->fetch_assoc()) {
        $job_titles[] = $row['job_title'];
    }
    $result_jobs->free(); // 結果セットを解放
} else {
    error_log("Error fetching job titles: " . $conn->error);
}

// 経験年数のカテゴリ定義 (プルダウン表示用)
$experience_categories = [
    0 => '初級 (0年～4年)',
    5 => '中級 (5年～9年)',
    10 => '上級 (10年以上)',
];

// 経験カテゴリの数値とデータベースに保存されている文字列のマッピング
$experience_level_map = [
    0 => '初級',
    5 => '中級',
    10 => '上級',
];

// 選択された職種と経験カテゴリの数値を取得
$selected_job = $_GET['job'] ?? '';
$selected_experience_category_value = isset($_GET['experience_category']) ? (int)$_GET['experience_category'] : null;

$display_salary = '職種と経験年数を選択してください。';
$display_experience_text = ''; // 表示用の経験年数テキスト

// 表示用の経験年数テキストを設定
if ($selected_experience_category_value !== null && array_key_exists($selected_experience_category_value, $experience_categories)) {
    $display_experience_text = $experience_categories[$selected_experience_category_value];
}

// 職種と経験カテゴリが両方選択されている場合のみ、データベースから直接年収を取得
if ($selected_job && $selected_experience_category_value !== null) {
    $selected_job_escaped = $conn->real_escape_string($selected_job);
    $experience_level_string = $experience_level_map[$selected_experience_category_value] ?? '';
    $experience_level_escaped = $conn->real_escape_string($experience_level_string);

    // データベースからmin_salaryとmax_salaryを直接取得
    $sql_specific_salary = "SELECT min_salary, max_salary FROM salary_data WHERE job_title = '$selected_job_escaped' AND experience_level = '$experience_level_escaped'";
    $result_specific_salary = $conn->query($sql_specific_salary);

    if ($result_specific_salary && $result_specific_salary->num_rows > 0) {
        $row_specific_salary = $result_specific_salary->fetch_assoc();
        $final_min_salary = (int)$row_specific_salary['min_salary'];
        $final_max_salary = (int)$row_specific_salary['max_salary'];
        $display_salary = "{$final_min_salary}万円～{$final_max_salary}万円";
    } else {
        $display_salary = '選択された条件に一致する年収データが見つかりません。';
    }
    $result_specific_salary->free();
}

$conn->close(); // データベース接続を閉じる
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | 年収シミュレーター</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* salary_simulator.php 専用のスタイル調整 */
        .simulator-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .simulator-container h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .simulator-container label {
            font-size: 1.1em;
            color: #555;
            margin-right: 15px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .simulator-container select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            margin-bottom: 20px;
            width: calc(100% - 150px);
            max-width: 250px;
        }
        .form-group {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }
        .form-group label, .form-group select {
            margin: 5px 10px;
        }
        .salary-display {
            margin-top: 30px;
            padding: 20px;
            background-color: #e0f2f7;
            border: 1px solid #a7d9ed;
            border-radius: 8px;
        }
        .salary-display p {
            font-size: 1.3em;
            color: #333;
            margin: 0;
        }
        .salary-display .amount {
            font-size: 1.8em;
            font-weight: bold;
            color: #007bff;
            margin-top: 10px;
        }
        /* レスポンシブ対応 */
        @media (max-width: 600px) {
            .simulator-container {
                margin: 20px auto;
                padding: 20px;
            }
            .form-group {
                flex-direction: column;
                align-items: stretch;
            }
            .form-group label {
                margin-right: 0;
                text-align: left;
                width: 100%;
                margin-bottom: 5px;
            }
            .form-group select {
                width: 100%;
                max-width: none;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo">株式会社テストシステム</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="services.php">サービス</a></li>
                    <li><a href="products.php">製品</a></li>
                    <li><a href="about.php">会社概要</a></li>
                    <li><a href="contact.php">お問い合わせ</a></li>
                    <li><a href="logout.php" class="action-button">ログアウト</a></li>
                </ul>
            </nav>
            <?php if (!empty($username)): ?>
                <div class="user-info"> ようこそ、<?php echo htmlspecialchars($username); ?> さん！
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="main-content">
        <div class="simulator-container">
            <h1>年収シミュレーター</h1>
            <p>ご希望の職種と経験年数を選択し、年収目安をご確認ください。</p>

            <form action="salary_simulator.php" method="GET">
                <div class="form-group">
                    <label for="job_select">職種：</label>
                    <select name="job" id="job_select" onchange="this.form.submit()">
                        <option value="">--選択してください--</option>
                        <?php foreach ($job_titles as $job_title): // 職種プルダウンは、ユニークな職種名で作成 ?>
                            <option value="<?php echo htmlspecialchars($job_title); ?>"
                                <?php echo ($selected_job === $job_title) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($job_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="experience_category_select">経験年数：</label>
                    <select name="experience_category" id="experience_category_select" onchange="this.form.submit()">
                        <option value="">--選択してください--</option>
                        <?php foreach ($experience_categories as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"
                                <?php echo ((string)$selected_experience_category_value === (string)$value) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <div class="salary-display">
                <?php if ($selected_job && $selected_experience_category_value !== null): ?>
                    <p><strong><?php echo htmlspecialchars($selected_job); ?> (<?php echo htmlspecialchars($display_experience_text); ?>)</strong> の年収目安:</p>
                    <p class="amount"><?php echo htmlspecialchars($display_salary); ?></p>
                <?php else: ?>
                    <p><?php echo htmlspecialchars($display_salary); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved.</p>
    </footer>

</body>
</html>