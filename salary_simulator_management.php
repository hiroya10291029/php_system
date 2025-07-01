<?php
session_start();

// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: admin_login.php'); // 管理者ログインページへリダイレクト
    exit();
}

$username_display = htmlspecialchars($_SESSION['username'] ?? '管理者');

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

$salary_data = []; // 年収シミュレーターデータを格納する配列

// 年収シミュレーターデータを取得するクエリ
$sql = "SELECT id, job_title, experience_level, min_salary, max_salary, average_salary, notes, create_dt, update_dt FROM salary_data ORDER BY job_title ASC, experience_level ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $salary_data[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>年収シミュレーター管理 - 管理者画面</title>
    <link rel="stylesheet" href="style.css"> <style>
        /* ここにこのページ固有のスタイルを追加できます */
        .management-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .management-table th, .management-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .management-table th {
            background-color: #f2f2f2;
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .action-buttons .edit-button,
        .action-buttons .delete-button {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 0.9em;
        }
        .action-buttons .edit-button {
            background-color: #007bff;
        }
        .action-buttons .edit-button:hover {
            background-color: #0056b3;
        }
        .action-buttons .delete-button {
            background-color: #dc3545;
        }
        .action-buttons .delete-button:hover {
            background-color: #c82333;
        }
        .add-button-container {
            text-align: right;
            margin-bottom: 20px;
        }
                /* ヘッダーのユーザー情報表示スタイル */
        .header .user-info {
            color: #ffffff;
            font-weight: bold;
            margin-left: 20px;
            font-size: 1rem;
        }
        .header .user-info {
            margin-top: 10px;
            margin-left: 0;
            }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="admin_dashboard.php" class="logo">株式会社テストシステム (管理者)</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="user_management.php">ユーザー管理</a></li>
                    <li><a href="inquiry_management.php">お問い合わせ管理</a></li>
                    <li><a href="salary_simulator_management.php">年収シミュレーター管理</a></li>                    
                    <li><a href="logout.php" class="action-button">ログアウト</a></li>
                </ul>
            </nav>
            <?php if (!empty($username_display)): ?>
                <div class="user-info">
                    ようこそ、<?php echo $username_display; ?> さん！
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="main-content">
        <h1 class="page-title">年収シミュレーター管理</h1>

        <section class="data-section">
            <div class="add-button-container">
                <a href="salary_simulator_add_edit.php" class="action-button">新規データ追加</a>
            </div>
            <?php if (!empty($salary_data)): ?>
                <table class="management-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>職種</th>
                            <th>経験レベル</th>
                            <th>最低年収</th>
                            <th>最高年収</th>
                            <th>平均年収</th>
                            <th>備考</th>
                            <th>作成日時</th>
                            <th>更新日時</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salary_data as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['id']); ?></td>
                                <td><?php echo htmlspecialchars($data['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($data['experience_level']); ?></td>
                                <td><?php echo number_format(htmlspecialchars($data['min_salary'])); ?>円</td>
                                <td><?php echo number_format(htmlspecialchars($data['max_salary'])); ?>円</td>
                                <td><?php echo number_format(htmlspecialchars($data['average_salary'])); ?>円</td>
                                <td><?php echo nl2br(htmlspecialchars($data['notes'])); ?></td>
                                <td><?php echo htmlspecialchars($data['create_dt']); ?></td>
                                <td><?php echo htmlspecialchars($data['update_dt']); ?></td>
                                <td class="action-buttons">
                                    <a href="salary_simulator_add_edit.php?id=<?php echo htmlspecialchars($data['id']); ?>" class="edit-button">編集</a>
                                    <form action="salary_simulator_management.php" method="POST" style="display:inline;" onsubmit="return confirm('本当にこのデータを削除してもよろしいですか？');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">
                                        <button type="submit" class="delete-button">削除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>表示する年収シミュレーターデータがありません。</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>
</body>
</html>