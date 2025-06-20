<?php
// データベース接続情報
$db_host = 'localhost';
$db_user = 'root';
// ★重要★ あなたのXAMPPのMySQLパスワードが空欄でなければここに設定
// デフォルトではXAMPPのrootユーザーのパスワードは空欄です。
$db_pass = '';
$db_name = 'inquiry'; // ★重要★ データベース名が 'inquiry' であることを確認

// データベースに接続
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 接続エラーの確認
if ($conn->connect_error) {
    // 開発中はエラー詳細を表示しても良いが、本番環境では非推奨
    // die("データベース接続エラー: " . $conn->connect_error);

    // 本番環境を意識したエラーハンドリング（例: エラーログへの記録と一般的なメッセージ表示）
    error_log("Database Connection Error: " . $conn->connect_error);
    http_response_code(500); // サーバー内部エラー
    echo "システムエラーが発生しました。時間をおいて再度お試しください。";
    exit();
}
// 文字コードをUTF-8に設定
$conn->set_charset("utf8mb4");
?>