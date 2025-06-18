<?php
session_start();

// フォーム送信後の処理
$message_status = ''; // メッセージ表示用の変数

// フォームがPOSTメソッドで送信された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力値の取得とHTML特殊文字のエスケープ
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $inquiry_type = htmlspecialchars($_POST['inquiry_type'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // --- データベース挿入処理 ---

    // データベース接続情報
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = ''; // ★重要★ あなたのXAMPPのMySQLパスワードが空欄でなければここに設定
    $db_name = 'inquiry'; // ★重要★ データベース名が 'inquiry' であることを確認

    // データベースに接続
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // 接続エラーの確認
    if ($conn->connect_error) {
        // 接続に失敗した場合、エラーメッセージを表示して処理を終了
        $message_status = "<p style='color: red; font-weight: bold;'>データベース接続エラー: " . $conn->connect_error . "</p>";
        // 開発中はエラーログにも記録すると良い
        error_log("Database Connection Error: " . $conn->connect_error);
    } else {
        // SQLクエリの準備 (テーブル構造に合わせて修正)
        // テーブルのカラム名: name, mail_address, inquiry_kind, subject, inquiry_details
        $sql = "INSERT INTO inquiry_table (name, mail_address, inquiry_kind, subject, inquiry_details) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // prepare() が成功したかを確認
        if ($stmt === false) {
            // SQLクエリに構文エラーがある場合や、テーブル/カラム名が間違っている場合にここに来る
            $message_status = "<p style='color: red; font-weight: bold;'>データベースクエリの準備中にエラーが発生しました。<br>エラー: " . $conn->error . "</p>";
            error_log("SQL Prepare Error: " . $conn->error . " Query: " . $sql);
        } else {
            // データのバインド (変数の型を指定: sssssはすべて文字列)
            // 順序: name, mail_address, inquiry_kind, subject, inquiry_details
            $stmt->bind_param("sssss", $name, $email, $inquiry_type, $subject, $message);

            // SQLを実行
            if ($stmt->execute()) {
                // 挿入成功
                $message_status = "<p style='color: green; font-weight: bold;'>お問い合わせありがとうございます。<br>内容を確認し、後ほどご連絡いたします。</p>";
                // フォーム送信成功後、入力フィールドをクリアする
                $name = '';
                $email = '';
                $subject = 'その他のお問い合わせ'; // 初期値に戻す
                $inquiry_type = 'その他のお問い合わせ'; // 初期値に戻す
                $message = '';
            } else {
                // 挿入失敗
                $message_status = "<p style='color: red; font-weight: bold;'>お問い合わせの送信に失敗しました。<br>エラー: " . $stmt->error . "</p>";
                error_log("SQL Execute Error: " . $stmt->error);
            }

            // ステートメントを閉じる
            $stmt->close();
        }
        // データベース接続を閉じる
        $conn->close();
    }
    // --- データベース挿入処理 終了 ---
}
// ユーザー名を取得 (ログインしている場合)
$username = htmlspecialchars($_SESSION['username'] ?? '');

// PHP側での初期値設定（JavaScriptで動的に件名を変更するための基盤）
$recruitment_job_types = [
    'ソフトウェアエンジニア',
    'データサイエンティスト',
    'プロジェクトマネージャー',
    'UI/UXデザイナー',
    'インフラエンジニア',
    '品質保証エンジニア'
];

$selected_inquiry_type = $_GET['job'] ?? 'その他のお問い合わせ';
if (!in_array($selected_inquiry_type, $recruitment_job_types) && $selected_inquiry_type !== '採用に関するお問い合わせ') {
    $selected_inquiry_type = 'その他のお問い合わせ';
}

$initial_subject = 'その他のお問い合わせ';
if (in_array($selected_inquiry_type, $recruitment_job_types)) {
    $initial_subject = '採用応募：' . $selected_inquiry_type;
} else if ($selected_inquiry_type === '採用に関するお問い合わせ') {
    $initial_subject = '採用に関するお問い合わせ';
}

// フォーム送信失敗時にユーザーが入力した内容を保持、成功時はクリア
$name_value = $name ?? '';
$email_value = $email ?? '';
$message_value = $message ?? '';

// 件名の表示値の最終決定
// フォーム送信成功時は$initial_subject（クリア後のデフォルト値）を表示
// フォーム送信失敗時はユーザーが入力した$subjectを保持
// 初回アクセス時（GET）は$initial_subjectを表示
$subject_for_display = $initial_subject; // デフォルトは初期件名
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($message_status, '失敗しました') !== false) {
    // 送信失敗時のみ、ユーザーが入力した件名を保持
    $subject_for_display = $_POST['subject'] ?? $initial_subject;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | お問い合わせ</title>
    <link rel="stylesheet" href="style.css">
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
                <div class="user-info">
                    ようこそ、<?php echo $username; ?> さん！
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="main-content">
        <h1 class="page-title">お問い合わせ</h1>
        <div class="contact-form-container">
            <?php echo $message_status; // 送信結果メッセージを表示 ?>

            <form action="contact.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">お名前 <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required maxlength="50" value="<?php echo htmlspecialchars($name_value); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required maxlength="50" value="<?php echo htmlspecialchars($email_value); ?>" required>
                </div>
                <div class="form-group">
                    <label for="inquiry_type">お問い合わせ種別 <span class="required">*</span></label>
                    <select id="inquiry_type" name="inquiry_type" required>
                        <option value="その他のお問い合わせ" <?php echo ($selected_inquiry_type == 'その他のお問い合わせ' ? 'selected' : ''); ?>>その他のお問い合わせ</option>
                        <option value="製品に関するお問い合わせ" <?php echo ($selected_inquiry_type == '製品に関するお問い合わせ' ? 'selected' : ''); ?>>製品に関するお問い合わせ</option>
                        <option value="サービスに関するお問い合わせ" <?php echo ($selected_inquiry_type == 'サービスに関するお問い合わせ' ? 'selected' : ''); ?>>サービスに関するお問い合わせ</option>
                        <option value="採用に関するお問い合わせ" <?php echo ($selected_inquiry_type == '採用に関するお問い合わせ' ? 'selected' : ''); ?>>採用に関するお問い合わせ</option>
                        <option value="ソフトウェアエンジニア" <?php echo ($selected_inquiry_type == 'ソフトウェアエンジニア' ? 'selected' : ''); ?>>採用応募：ソフトウェアエンジニア</option>
                        <option value="データサイエンティスト" <?php echo ($selected_inquiry_type == 'データサイエンティスト' ? 'selected' : ''); ?>>採用応募：データサイエンティスト</option>
                        <option value="プロジェクトマネージャー" <?php echo ($selected_inquiry_type == 'プロジェクトマネージャー' ? 'selected' : ''); ?>>採用応募：プロジェクトマネージャー</option>
                        <option value="UI/UXデザイナー" <?php echo ($selected_inquiry_type == 'UI/UXデザイナー' ? 'selected' : ''); ?>>採用応募：UI/UXデザイナー</option>
                        <option value="インフラエンジニア" <?php echo ($selected_inquiry_type == 'インフラエンジニア' ? 'selected' : ''); ?>>採用応募：インフラエンジニア</option>
                        <option value="品質保証エンジニア" <?php echo ($selected_inquiry_type == '品質保証エンジニア' ? 'selected' : ''); ?>>採用応募：品質保証エンジニア</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject">件名 <span class="required">*</span></label>
                    <input type="text" id="subject" name="subject"required maxlength="40" value="<?php echo htmlspecialchars($subject_for_display); ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">お問い合わせ内容 <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="10" required maxlength="400" placeholder="400文字以内での記入をお願いいたします。採用応募に関しましては、空白で構いません。こちらからメールにて折り返しご連絡差し上げます。"><?php echo htmlspecialchars($message_value); ?></textarea>
                </div>
                <button type="submit">送信</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inquiryTypeSelect = document.getElementById('inquiry_type');
            const subjectInput = document.getElementById('subject');

            function updateSubject() {
                const selectedValue = inquiryTypeSelect.value;
                let newSubject = '';

                // 募集職種を判定するためのリスト (PHPの$recruitment_job_typesと同期させる)
                const jobTitles = [
                    'ソフトウェアエンジニア',
                    'データサイエンティスト',
                    'プロジェクトマネージャー',
                    'UI/UXデザイナー',
                    'インフラエンジニア',
                    '品質保証エンジニア'
                ];

                if (jobTitles.includes(selectedValue)) {
                    newSubject = '採用応募：' + selectedValue;
                } else if (selectedValue === '採用に関するお問い合わせ') {
                    newSubject = '採用に関するお問い合わせ';
                } else {
                    newSubject = 'その他のお問い合わせ';
                }
                subjectInput.value = newSubject;
            }

            // プルダウンが変更されたら件名を更新
            inquiryTypeSelect.addEventListener('change', updateSubject);

            // ページ読み込み時にも件名を初期設定 (URLパラメータなどに対応するため)
            updateSubject();
        });
    </script>
</body>
</html>