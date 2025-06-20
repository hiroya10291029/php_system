<?php
session_start(); // セッションを開始

$error_message = '';

// 既にログインしている場合はダッシュボードへリダイレクト
if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
    header('Location: admin_dashboard.php');
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
       // データベース接続ファイルを読み込む
       require_once 'db_connect.php'; // ここを追記

        $error_message = '';

        // 接続エラーの確認
        if ($conn->connect_error) {
            $error_message = "データベース接続エラー: " . $conn->connect_error;
        } else {
            // SQLインジェクションを防ぐためにプリペアドステートメントを使用
            // ★セキュリティ向上のため、パスワードはハッシュ化して保存し、password_verify()で照合することを強く推奨します。
            //   ここでは、指示されたテーブル構造に合わせてプレーンテキストで照合します。
            $sql = "SELECT * FROM admin_login_table WHERE user_name = ? AND pass_word = ?";
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
                    header('Location: admin_dashboard.php');     // ログイン後のページへリダイレクト
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
    <title>管理者ログイン</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e6edf2; /* 全体的に青みがかった背景 */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            position: relative; /* ユーザーログインリンクの配置のため */
        }
        .user-login-link {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 14px;
        }
        .user-login-link a {
            color: #2c3e50; /* ヘッダーのネイビーに合わせた色 */
            text-decoration: none;
            padding: 8px 12px;
            border: 1px solid #2c3e50;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .user-login-link a:hover {
            background-color: #2c3e50;
            color: white;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* 影を少し強調 */
            width: 500px;
            max-width: 90%; /* レスポンシブ対応 */
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 25px; /* マージンを増やす */
            color: #34495e; /* ヘッダーのダークグレーに近い色 */
            font-size: 28px; /* フォントサイズを少し大きく */
        }
        .login-container p.error {
            color: red;
            margin-bottom: 20px; /* マージンを増やす */
            font-weight: bold;
        }
        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 8px; /* マージンを増やす */
            color: #555;
            font-weight: bold; /* ラベルを太字に */
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 22px); /* パディング考慮 */
            padding: 12px; /* パディングを増やす */
            margin-bottom: 20px; /* マージンを増やす */
            border: 1px solid #a4c6e0; /* 暗めの薄い青系のボーダー */
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box; /* パディングとボーダーを幅に含める */
        }
        .login-container input[type="submit"] {
            background-color: #5a80a2; /* 暗めの青系 */
            color: white;
            padding: 12px 15px; /* パディングを増やす */
            border: none;
            border-radius: 5px; /* 角丸を少し大きく */
            cursor: pointer;
            font-size: 18px; /* フォントサイズを大きく */
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .login-container input[type="submit"]:hover {
            background-color: #4a6d8f; /* ホバーでより濃い青に */
            transform: translateY(-1px); /* 少し浮き上がる効果 */
        }

        /* レスポンシブ対応 */
        @media (max-width: 600px) {
            .login-container {
                width: 95%;
                padding: 20px;
            }
            .login-container h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="user-login-link">
        <a href="login.php">一般ユーザーログインへ</a>
    </div>

    <div class="login-container">
        <h2>管理者ログイン</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="admin_login.php" method="post">
            <label for="username">管理者ユーザー名:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="ログイン">
        </form>
    </div>
</body>
</html>