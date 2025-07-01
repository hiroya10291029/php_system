<?php
session_start();

// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$username_display = htmlspecialchars($_SESSION['username'] ?? '管理者');
$message_status = ''; // 処理結果メッセージ用
$is_edit_mode = false;
$salary_data = [
    'id' => '',
    'job_title' => '',
    'experience_level' => '',
    'min_salary' => '',
    'max_salary' => '',
    'average_salary' => '',
    'notes' => ''
];

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

// --- GETリクエストの場合：編集対象データの取得 ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        $is_edit_mode = true;
        $stmt = $conn->prepare("SELECT id, job_title, experience_level, min_salary, max_salary, average_salary, notes FROM salary_data WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $salary_data = $result->fetch_assoc();
        } else {
            $message_status = '<p class="error-message">指定されたデータが見つかりません。</p>';
            $is_edit_mode = false; // データが見つからなければ新規モードに戻す
        }
        $stmt->close();
    }
}

// --- POSTリクエストの場合：データの保存（追加または更新） ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $job_title = trim($_POST['job_title'] ?? '');
    $experience_level = trim($_POST['experience_level'] ?? '');
    $min_salary = intval($_POST['min_salary'] ?? 0);
    $max_salary = intval($_POST['max_salary'] ?? 0);
    $average_salary = intval($_POST['average_salary'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    // 入力値のバリデーション
    if (empty($job_title) || empty($experience_level) || $min_salary <= 0 || $max_salary <= 0) {
        $message_status = '<p class="error-message">職種、経験レベル、最低年収、最高年収は必須入力です。</p>';
        // エラー時はフォームに再入力された値を保持
        $salary_data = [
            'id' => $id,
            'job_title' => $job_title,
            'experience_level' => $experience_level,
            'min_salary' => $min_salary,
            'max_salary' => $max_salary,
            'average_salary' => $average_salary,
            'notes' => $notes
        ];
        $is_edit_mode = ($id > 0); // エラー時も編集モードを維持
    } else {
        if ($id > 0) { // 更新処理
            $stmt = $conn->prepare("UPDATE salary_data SET job_title = ?, experience_level = ?, min_salary = ?, max_salary = ?, average_salary = ?, notes = ? WHERE id = ?");
            $stmt->bind_param("ssiiisi", $job_title, $experience_level, $min_salary, $max_salary, $average_salary, $notes, $id);
            if ($stmt->execute()) {
                $message_status = '<p class="success-message">年収データが正常に更新されました。</p>';
                // 更新後のデータを再取得してフォームに表示 (オプション、必要であれば)
                // あるいは、管理画面にリダイレクト
                header('Location: salary_simulator_management.php?message=updated');
                exit();
            } else {
                $message_status = '<p class="error-message">データの更新に失敗しました: ' . $stmt->error . '</p>';
            }
        } else { // 新規追加処理
            $stmt = $conn->prepare("INSERT INTO salary_data (job_title, experience_level, min_salary, max_salary, average_salary, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiis", $job_title, $experience_level, $min_salary, $max_salary, $average_salary, $notes);
            if ($stmt->execute()) {
                $message_status = '<p class="success-message">新しい年収データが正常に追加されました。</p>';
                // 新規追加後はフォームをクリア
                $salary_data = [
                    'id' => '', 'job_title' => '', 'experience_level' => '',
                    'min_salary' => '', 'max_salary' => '', 'average_salary' => '', 'notes' => ''
                ];
                header('Location: salary_simulator_management.php?message=added');
                exit();
            } else {
                $message_status = '<p class="error-message">データの追加に失敗しました: ' . $stmt->error . '</p>';
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit_mode ? '年収データ編集' : '年収データ追加'; ?> - 管理者画面</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* user_edit.php と似たスタイルを想定 */
        .main-content {
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .page-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .edit-form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .edit-form-container label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .edit-form-container input[type="text"],
        .edit-form-container input[type="number"],
        .edit-form-container textarea {
            width: calc(100% - 22px); /* Padding and border */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .edit-form-container textarea {
            resize: vertical; /* 垂直方向にリサイズ可能 */
            min-height: 80px;
        }
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }
        .form-actions button,
        .form-actions a.back-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }
        .form-actions button[type="submit"] {
            background-color: #28a745; /* Green for submit */
            color: white;
            transition: background-color 0.3s ease;
        }
        .form-actions button[type="submit"]:hover {
            background-color: #218838;
        }
        .form-actions a.back-button {
            background-color: #6c757d; /* Gray for back */
            color: white;
            transition: background-color 0.3s ease;
        }
        .form-actions a.back-button:hover {
            background-color: #5a6268;
        }
        .message-status {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .required {
            color: red;
            font-size: 0.8em;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="admin_dashboard.php" class="logo">管理者ダッシュボード</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="user_management.php">ユーザー管理</a></li>
                    <li><a href="inquiry_management.php">お問い合わせ管理</a></li>
                    <li><a href="salary_simulator_management.php">年収シミュレーター管理</a></li>
                    <li class="user-info">ようこそ、<?php echo $username_display; ?>さん</li>
                    <li><a href="logout.php" class="action-button">ログアウト</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <h1 class="page-title"><?php echo $is_edit_mode ? '年収データ編集' : '年収データ追加'; ?></h1>

        <section class="edit-form-container">
            <?php if (!empty($message_status)): ?>
                <div class="message-status <?php echo (strpos($message_status, 'エラー') !== false) ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message_status; ?>
                </div>
            <?php endif; ?>

            <form action="salary_simulator_add_edit.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($salary_data['id']); ?>">

                <label for="job_title">職種 <span class="required">*</span></label>
                <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($salary_data['job_title']); ?>" required>

                <label for="experience_level">経験レベル <span class="required">*</span></label>
                <input type="text" id="experience_level" name="experience_level" value="<?php echo htmlspecialchars($salary_data['experience_level']); ?>" required>

                <label for="min_salary">最低年収 <span class="required">*</span></label>
                <input type="number" id="min_salary" name="min_salary" value="<?php echo htmlspecialchars($salary_data['min_salary']); ?>" required min="0">

                <label for="max_salary">最高年収 <span class="required">*</span></label>
                <input type="number" id="max_salary" name="max_salary" value="<?php echo htmlspecialchars($salary_data['max_salary']); ?>" required min="0">

                <label for="average_salary">平均年収</label>
                <input type="number" id="average_salary" name="average_salary" value="<?php echo htmlspecialchars($salary_data['average_salary']); ?>" min="0">

                <label for="notes">備考</label>
                <textarea id="notes" name="notes"><?php echo htmlspecialchars($salary_data['notes']); ?></textarea>

                <div class="form-actions">
                    <button type="submit"><?php echo $is_edit_mode ? '更新' : '追加'; ?></button>
                    <a href="salary_simulator_management.php" class="back-button">一覧に戻る</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>
</body>
</html>