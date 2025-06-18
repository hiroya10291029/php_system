<?php
session_start(); // セッションを開始

$error_message = '';

// 既にログインしている場合はダッシュボードへリダイレクト
if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
    header('Location: dashboard.php');
    exit();
}

// フォームが送信された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 入力値が空でないかチェック
    if (empty($username) || empty($password)) {
        $error_message = 'ユーザー名とパスワードを入力してください。';
    } else {
        // --- データベース接続情報 ---
        $db_host = 'localhost';
        $db_user = 'root';
        // ★重要★ あなたのXAMPPのMySQLパスワードが空欄でなければここに設定
        // デフォルトではXAMPPのrootユーザーのパスワードは空欄です。
        $db_pass = '';
        // ★ここを「inquiry」に設定
        $db_name = 'inquiry';

        // データベースに接続
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        // 接続エラーの確認
        if ($conn->connect_error) {
            $error_message = "データベース接続エラー: " . $conn->connect_error;
        } else {
            // SQLインジェクションを防ぐためにプリペアドステートメントを使用
            // ★セキュリティ向上のため、パスワードはハッシュ化して保存し、password_verify()で照合することを強く推奨します。
            //   ここでは、指示されたテーブル構造に合わせてプレーンテキストで照合します。
            $sql = "SELECT * FROM login_table WHERE user_name = ? AND pass_word = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                // パラメータをバインド
                $stmt->bind_param("ss", $username, $password); // "ss"は2つの文字列パラメータを示す

                // クエリを実行
                $stmt->execute();

                // 結果を取得
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // ログイン成功
                    $_SESSION['user_authenticated'] = true; // 認証済みフラグをセッションに設定
                    $_SESSION['username'] = $username;     // ユーザー名をセッションに保存
                    header('Location: dashboard.php');     // ログイン後のページへリダイレクト
                    exit();
                } else {
                    // ログイン失敗
                    $error_message = 'ユーザー名またはパスワードが間違っています。';
                }

                // ステートメントを閉じる
                $stmt->close();
            } else {
                $error_message = "プリペアドステートメントの準備に失敗しました: " . $conn->error;
            }

            // データベース接続を閉じる
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            position: relative; /* 管理者ログインリンクの配置のため */
        }
        .admin-login-link {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 14px;
        }
        .admin-login-link a {
            color: #007bff;
            text-decoration: none;
            padding: 8px 12px;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .admin-login-link a:hover {
            background-color: #007bff;
            color: white;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container p.error {
            color: red;
            margin-bottom: 15px;
        }
        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #555;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .login-container input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="admin-login-link">
        <a href="admin_login.php">管理者ログインへ</a>
    </div>
    <div class="login-container">
        <h2>ログイン画面！！！</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <label for="username">ユーザー名:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="ログイン">
        </form>
    </div>
</body>
</html>