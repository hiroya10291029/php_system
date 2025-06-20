<?php
session_start();
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: login.php');
    exit();
}
// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    // AjaxリクエストなのでJSONでエラーを返す
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
// データベース接続ファイルを読み込む
require_once 'db_connect.php'; // ここを追記

$error_message = '';

// レスポンスはJSON形式で返す
header('Content-Type: application/json');

// POSTリクエストの場合（ステータス更新）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        // ★ inquiry_id を key_id に変更 ★
        $key_id = $_POST['key_id'] ?? '';
        $status = $_POST['status'] ?? '';

        // 入力値の検証
        if (empty($key_id) || !in_array($status, [0, 1, 2, 3])) {
            echo json_encode(['success' => false, 'message' => '無効なパラメータです。']);
            $conn->close();
            exit();
        }

        // SQLインジェクション対策
        $key_id_escaped = $conn->real_escape_string($key_id);
        $status_escaped = $conn->real_escape_string($status);

        // ステータス更新クエリ
        // ★ WHERE key_id = ... に変更 ★
        $update_sql = "UPDATE inquiry_table SET status = '$status_escaped' WHERE key_id = '$key_id_escaped'";

        if ($conn->query($update_sql)) {
            echo json_encode(['success' => true, 'message' => 'ステータスが正常に更新されました。']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ステータス更新に失敗しました: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '無効なアクションです。']);
    }
}
// GETリクエストの場合（お問い合わせ詳細取得）
else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ★ inquiry_id を key_id に変更 ★
    $key_id = $_GET['key_id'] ?? '';

    if (empty($key_id)) {
        echo json_encode(['success' => false, 'message' => 'お問い合わせIDが指定されていません。']);
        $conn->close();
        exit();
    }

    // SQLインジェクション対策
    $key_id_escaped = $conn->real_escape_string($key_id);

    // お問い合わせ詳細取得クエリ
    // ★ SELECT 文にも key_id を追加し、WHERE key_id = ... に変更 ★
    $sql = "SELECT key_id, name, mail_address, inquiry_kind, subject, inquiry_details, created_dt, status FROM inquiry_table WHERE key_id = '$key_id_escaped'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $inquiry = $result->fetch_assoc();
        echo json_encode(['success' => true, 'inquiry' => $inquiry]);
    } else {
        echo json_encode(['success' => false, 'message' => '指定されたお問い合わせが見つかりません。']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'サポートされていないリクエストメソッドです。']);
}

$conn->close();
?>