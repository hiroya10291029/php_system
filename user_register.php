<?php
session_start();

// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$username_display = htmlspecialchars($_SESSION['username'] ?? '管理者');
$message_status = ''; // 処理結果メッセージ用
$input_username = '';
$input_password = ''; // 新規登録なので初期は空

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

// --- 新規登録処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $input_username = trim($_POST['username'] ?? '');
        $input_password = $_POST['password'] ?? '';

        // 入力値のバリデーション (ユーザー名とパスワードは必須)
        if (empty($input_username) || empty($input_password)) {
            $message_status = "<p class='error-message'>ユーザー名とパスワードは必須です。</p>";
        } else {
            // ユーザー名が既に存在するかチェック
            $check_sql = "SELECT COUNT(*) FROM login_table WHERE user_name = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $input_username);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count > 0) {
                $message_status = "<p class='error-message'>このユーザー名は既に存在します。</p>";
            } else {

                // INSERT文の準備 
                $insert_sql = "INSERT INTO login_table (user_name, pass_word, create_dt, koshin_dt) VALUES (?, ?, NOW(), NOW())";
                $stmt = $conn->prepare($insert_sql);

                if ($stmt) {
                    // bind_paramの引数を調整 (mail_addressとdepartmentを削除)
                    $stmt->bind_param("ss", $input_username, $input_password);
                    if ($stmt->execute()) {
                        $message_status = "<p class='success-message'>ユーザー「" . htmlspecialchars($input_username) . "」を新規登録しました。</p>";
                        // 登録成功後、フォームをクリア
                        $input_username = '';
                        $input_password = '';
                        $input_authority = 'user';
                    } else {
                        $message_status = "<p class='error-message'>ユーザー登録に失敗しました: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    $message_status = "<p class='error-message'>SQLステートメントの準備に失敗しました: " . $conn->error . "</p>";
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | ユーザー新規登録</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* user_edit.php から引き継ぐか、適宜調整 */
        .edit-form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        .edit-form-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 2px solid #5a80a2;
            padding-bottom: 15px;
        }
        .edit-form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .edit-form-container input[type="text"],
        .edit-form-container input[type="email"],
        .edit-form-container input[type="password"],
        .edit-form-container select {
            width: calc(100% - 24px); /* Padding adjustment */
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box; /* Include padding in width */
        }
        .edit-form-container input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .edit-form-container .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        .edit-form-container button,
        .edit-form-container .back-button { /* 戻るボタンもスタイルを統一 */
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0 10px;
            display: inline-block; /* ボタンが横並びになるように */
            text-decoration: none; /* aタグの場合のデフォルト下線を消す */
            color: white; /* 文字色を白に */
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .edit-form-container button[type="submit"] {
            background-color: #28a745; /* 登録ボタンは緑色 */
        }
        .edit-form-container button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }
        .edit-form-container .delete-button { /* 削除ボタンは今回は使わないが、念のためスタイル */
            background-color: #dc3545;
        }
        .edit-form-container .delete-button:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }
        .edit-form-container .back-button {
            background-color: #6c757d; /* 戻るボタンは灰色 */
        }
        .edit-form-container .back-button:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }
        .message-status {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
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
            margin-left: 5px;
            font-size: 0.8em;
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
                    <li><a href="inquiry_management.php">お問い合わせ管理</a></li>
                    <li><a href="user_management.php">ユーザー管理</a></li>
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
        <h1 class="page-title">ユーザー新規登録</h1>

        <section class="edit-form-container">
            <?php if (!empty($message_status)): ?>
                <div class="message-status">
                    <?php echo $message_status; ?>
                </div>
            <?php endif; ?>

            <form action="user_register.php" method="POST">
                <input type="hidden" name="action" value="register">

                <label for="username">ユーザー名 <span class="required">*</span></label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($input_username); ?>" required>

                <label for="password">パスワード <span class="required">*</span></label>
                <input type="password" id="password" name="password" value="" required placeholder="パスワードを入力してください">

                <label for="authority">権限</label>
                <input type="text" id="authority" name="authority" value="一般ユーザー" readonly style="background-color: #e9ecef;">
		<p>※管理者ユーザーの登録は情報システム部までお問い合わせください</p>
                    <button type="submit">登録</button>
                    <a href="user_management.php" class="back-button">一覧に戻る</a>
                </div>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>
</body>
</html>