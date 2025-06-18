<?php
session_start();

// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: admin_login.php');
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

// 検索条件の取得
$search_username = $_GET['search_username'] ?? '';
$search_username_escaped = '%' . $conn->real_escape_string($search_username) . '%';

// ソート条件の取得
$sort_column = $_GET['sort_column'] ?? 'create_dt'; // デフォルトはcreate_dtでソート
$sort_order = $_GET['sort_order'] ?? 'DESC'; // デフォルトは降順

// ソート可能なカラムをホワイトリスト化
$allowed_sort_columns = ['user_name', 'pass_word', 'create_dt', 'koshin_dt'];
if (!in_array($sort_column, $allowed_sort_columns)) {
    $sort_column = 'create_dt'; // 不正な場合はデフォルトに戻す
}
if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
    $sort_order = 'DESC'; // 不正な場合はデフォルトに戻す
}

// SQLクエリの構築
$sql = "SELECT user_name, pass_word, create_dt, koshin_dt FROM login_table";
$where_clauses = [];

if (!empty($search_username)) {
    $where_clauses[] = "user_name LIKE '$search_username_escaped'";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

// ソート条件を追加
$sql .= " ORDER BY " . $sort_column . " " . $sort_order;

$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | ユーザー管理</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* user_management.php 専用のスタイル調整 */
        .main-content {
            padding: 40px 20px;
            max-width: 1000px; /* 画面幅に合わせて調整 */
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .user-management-section h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        /* 検索フォームのスタイル */
        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f9f9f9;
            align-items: center;
        }

        .search-form label {
            font-weight: bold;
            color: #333;
        }

        .search-form input[type="text"] {
            flex-grow: 1;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            max-width: 300px; /* 検索フィールドの最大幅 */
        }

        .search-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        /* ユーザーテーブルのスタイル */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-table th,
        .user-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .user-table th {
            background-color: #eef7ff; /* ヘッダーの背景色 */
            color: #333;
            font-weight: bold;
            cursor: pointer; /* ソート可能を示す */
            position: relative;
        }

        .user-table th:hover {
            background-color: #e0f0ff;
        }

        .user-table th .sort-indicator {
            position: absolute;
            right: 10px;
            font-size: 0.8em;
            color: #666;
        }

        .user-table tr {
            cursor: pointer; /* 行がクリック可能であることを示す */
        }

        .user-table tr:nth-child(even) {
            background-color: #f9f9f9; /* 偶数行の背景色 */
        }

        .user-table tr:hover {
            background-color: #f1f1f1; /* ホバー時の背景色 */
        }

        /* ヘッダーのユーザー情報表示スタイル */
        .header .user-info {
            color: #ffffff;
            font-weight: bold;
            margin-left: 20px;
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            .main-nav ul {
                flex-direction: column;
                width: 100%;
                margin-top: 10px;
            }
            .main-nav li {
                margin: 5px 0;
            }
            .header .user-info {
                margin-top: 10px;
                margin-left: 0;
            }
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            .search-form input[type="text"] {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="admin_dashboard.php" class="logo">株式会社テストシステム (管理者)</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin_dashboard.php">ダッシュボード</a></li>
                    <li><a href="user_management.php">ユーザー管理</a></li>
                    <li><a href="inquiry_management.php">お問い合わせ</a></li>
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
        <h1 class="page-title">ユーザー管理</h1>

        <section class="user-management-section">
            <div class="search-form">
                <form action="user_management.php" method="get">
                    <label for="search_username">ユーザー名で検索:</label>
                    <input type="text" id="search_username" name="search_username" 
                           value="<?php echo htmlspecialchars($search_username); ?>" 
                           placeholder="ユーザー名を入力">
                    <input type="hidden" name="sort_column" value="<?php echo htmlspecialchars($sort_column); ?>">
                    <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">
                    <button type="submit">検索</button>
                    <?php if (!empty($search_username)): ?>
                        <a href="user_management.php" class="action-button" style="background-color: #6c757d; margin-left: 10px;">検索クリア</a>
                    <?php endif; ?>
                </form>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th onclick="sortTable('user_name')">
                            ユーザー名
                            <?php if ($sort_column === 'user_name'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('pass_word')">
                            パスワード
                            <?php if ($sort_column === 'pass_word'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('create_dt')">
                            作成日時
                            <?php if ($sort_column === 'create_dt'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('koshin_dt')">
                            更新日時
                            <?php if ($sort_column === 'koshin_dt'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr onclick="location.href='user_edit.php?username=<?php echo urlencode($user['user_name']); ?>'">
                                <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['pass_word']); ?></td>
                                <td><?php echo htmlspecialchars($user['create_dt']); ?></td>
                                <td><?php echo htmlspecialchars($user['koshin_dt']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">表示するユーザーがいません。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>

    <script>
        function sortTable(column) {
            const currentUrl = new URL(window.location.href);
            const currentSortColumn = currentUrl.searchParams.get('sort_column');
            const currentSortOrder = currentUrl.searchParams.get('sort_order');
            let newSortOrder = 'ASC';

            if (currentSortColumn === column) {
                newSortOrder = (currentSortOrder === 'ASC' ? 'DESC' : 'ASC');
            }

            currentUrl.searchParams.set('sort_column', column);
            currentUrl.searchParams.set('sort_order', newSortOrder);
            
            // 検索条件を保持したままソートを実行
            const searchUsername = currentUrl.searchParams.get('search_username');
            if (searchUsername) {
                 currentUrl.searchParams.set('search_username', searchUsername);
            } else {
                 currentUrl.searchParams.delete('search_username');
            }

            window.location.href = currentUrl.toString();
        }
    </script>
</body>
</html>