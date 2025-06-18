<?php
session_start();

// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$username_display = htmlspecialchars($_SESSION['username'] ?? '管理者');
$message_status = ''; // 処理結果メッセージ用

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

$user_data = null; // 編集対象のユーザーデータを格納する変数
$target_username = $_GET['username'] ?? ''; // GETパラメータからユーザー名を取得（初回表示時や更新後の再表示用）

// --- 削除処理 ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delete_username = $_POST['original_username'] ?? '';

    if (!empty($delete_username)) {
        $stmt = $conn->prepare("DELETE FROM login_table WHERE user_name = ?");
        $stmt->bind_param("s", $delete_username);

        if ($stmt->execute()) {
            $message_status = "<p class='success-message'>ユーザー (".$delete_username.") が正常に削除されました。</p>";
            // 削除成功後は一覧画面へリダイレクト
            header('Location: user_management.php?status=deleted');
            exit();
        } else {
            $message_status = "<p class='error-message'>ユーザーの削除に失敗しました: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        $message_status = "<p class='error-message'>削除対象のユーザーが指定されていません。</p>";
    }
}

// --- 更新処理 ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $original_username = $_POST['original_username'] ?? ''; // 元のユーザー名（WHERE句用）
    $new_username = $_POST['user_name'] ?? '';
    $new_password = $_POST['pass_word'] ?? '';

    if (empty($new_username) || empty($new_password)) {
        $message_status = "<p class='error-message'>ユーザー名とパスワードは必須項目です。</p>";
    } else {
        // パスワードのハッシュ化（推奨）
        // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        // 現在は平文のままですが、本番環境ではハッシュ化してください
        $update_password = $new_password;

        // ユーザー情報を更新
        // user_nameも更新対象とする場合はUNIQUE制約に注意
        $stmt = $conn->prepare("UPDATE login_table SET user_name = ?, pass_word = ?, koshin_dt = CURRENT_TIMESTAMP WHERE user_name = ?");
        $stmt->bind_param("sss", $new_username, $update_password, $original_username);

        if ($stmt->execute()) {
            $message_status = "<p class='success-message'>ユーザー情報が更新されました！</p>";
            // 更新後、`$target_username` を更新後のユーザー名に設定し直し、最新のデータを再取得して表示を更新
            $target_username = $new_username; // これにより、フォームに更新後の値がロードされる
        } else {
            // エラーが発生した場合、user_nameのUNIQUE制約違反の可能性も考慮
            if ($conn->errno == 1062) { // MySQLエラーコード 1062 はUNIQUE制約違反
                $message_status = "<p class='error-message'>エラー: 指定されたユーザー名は既に存在します。</p>";
            } else {
                $message_status = "<p class='error-message'>ユーザー情報の更新に失敗しました: " . $stmt->error . "</p>";
            }
        }
        $stmt->close();
    }
}

// --- ユーザー情報の読み込み (GETリクエスト、またはPOST処理後の再読み込み) ---
// $target_username が設定されている場合にのみデータを読み込む
if (!empty($target_username)) {
    $stmt = $conn->prepare("SELECT user_name, pass_word, create_dt, koshin_dt FROM login_table WHERE user_name = ?");
    $stmt->bind_param("s", $target_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    } else {
        // GETで指定されたユーザーが見つからない、または更新後にユーザー名が変わったが再取得できなかった場合
        if (empty($message_status)) { // 既にエラーメッセージがなければ
            $message_status = "<p class='error-message'>指定されたユーザーが見つかりませんでした。</p>";
        }
    }
    $stmt->close();
} else {
    // GETパラメータにusernameがない場合
    $message_status = "<p class='error-message'>編集対象のユーザーが指定されていません。</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | ユーザー編集</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* user_edit.php 専用のスタイル調整 */
        .main-content {
            padding: 40px 20px;
            max-width: 600px; /* フォームの幅を調整 */
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .edit-form-section h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .edit-form label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block; /* ラベルをブロック要素にする */
        }

        .edit-form input[type="text"],
        .edit-form input[type="password"] {
            width: calc(100% - 22px); /* パディング分を考慮 */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .edit-form .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap; /* ボタンが複数になった場合に対応 */
            gap: 10px; /* ボタン間の隙間 */
        }

        .edit-form button[type="submit"],
        .edit-form .delete-button, /* 削除ボタンのスタイル */
        .edit-form .back-button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            flex-grow: 1; /* ボタンが均等に広がるように */
            min-width: 120px; /* ボタンの最小幅 */
            box-sizing: border-box; /* パディングとボーダーを幅に含める */
        }

        .edit-form button[type="submit"] {
            background-color: #28a745; /* 緑系 */
            color: white;
        }

        .edit-form button[type="submit"]:hover {
            background-color: #218838;
        }

        .edit-form .delete-button {
            background-color: #dc3545; /* 赤系 */
            color: white;
        }
        .edit-form .delete-button:hover {
            background-color: #c82333;
        }

        .edit-form .back-button {
            background-color: #6c757d; /* グレー系 */
            color: white;
            text-decoration: none;
            display: inline-flex; /* aタグをボタンのように見せるため */
            align-items: center; /* テキストを中央揃え */
            justify-content: center; /* テキストを中央揃え */
        }

        .edit-form .back-button:hover {
            background-color: #5a6268;
        }

        /* メッセージ表示スタイル */
        .message-area {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
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
            .edit-form .form-actions {
                flex-direction: column;
                gap: 10px;
            }
            .edit-form button[type="submit"],
            .edit-form .delete-button,
            .edit-form .back-button {
                width: 100%;
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
        <h1 class="page-title">ユーザー情報編集</h1>

        <section class="edit-form-section">
            <?php if (!empty($message_status)): ?>
                <div class="message-area <?php echo (strpos($message_status, 'エラー') !== false) ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message_status; ?>
                </div>
            <?php endif; ?>

            <?php if ($user_data): ?>
                <form action="user_edit.php" method="post" class="edit-form" id="userEditForm">
                    <input type="hidden" name="original_username" value="<?php echo htmlspecialchars($user_data['user_name']); ?>">
                    <input type="hidden" name="action" id="formAction" value="update">

                    <label for="user_name">ユーザー名:</label>
                    <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user_data['user_name']); ?>" required>

                    <label for="pass_word">パスワード:</label>
                    <input type="password" id="pass_word" name="pass_word" value="<?php echo htmlspecialchars($user_data['pass_word']); ?>" required>
                    <small>※セキュリティのため、パスワードはハッシュ化して保存することを強く推奨します。</small>

                    <label>作成日時:</label>
                    <input type="text" value="<?php echo htmlspecialchars($user_data['create_dt']); ?>" readonly>

                    <label>更新日時:</label>
                    <input type="text" value="<?php echo htmlspecialchars($user_data['koshin_dt']); ?>" readonly>

                    <div class="form-actions">
                        <button type="submit">更新</button>
                        <button type="button" class="delete-button" onclick="confirmDelete()">削除</button>
                        <a href="user_management.php" class="back-button">一覧に戻る</a>
                    </div>
                </form>
            <?php else: ?>
                <p>編集するユーザーデータが見つかりません。一覧に戻って再選択してください。</p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="user_management.php" class="action-button" style="background-color: #6c757d;">一覧に戻る</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>

    <script>
        function confirmDelete() {
            if (confirm('本当にこのユーザーを削除してもよろしいですか？この操作は元に戻せません。')) {
                // 削除アクションを設定し、フォームを送信
                document.getElementById('formAction').value = 'delete';
                document.getElementById('userEditForm').submit();
            }
        }
    </script>
</body>
</html>