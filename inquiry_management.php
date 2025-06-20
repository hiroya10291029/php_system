<?php
session_start();

// 認証されていない場合は管理者ログインページへリダイレクト
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: admin_login.php');
    exit();
}

$username_display = htmlspecialchars($_SESSION['username'] ?? '管理者');

// データベース接続ファイルを読み込む
require_once 'db_connect.php'; // ここを追記

$error_message = '';

// ===============================================
// ★ここから追加・修正するPHPコード★
// ===============================================

// お問い合わせ種別の選択肢を定義 (contact.php と同期)
$inquiry_types = [
    '' => '全てのお問い合わせ種別', // 検索用に追加
    '採用に関するお問い合わせ' => '採用に関するお問い合わせ',
    'その他のお問い合わせ' => 'その他のお問い合わせ',
    'ソフトウェアエンジニア' => 'ソフトウェアエンジニア',
    'データサイエンティスト' => 'データサイエンティスト',
    'プロジェクトマネージャー' => 'プロジェクトマネージャー',
    'UI/UXデザイナー' => 'UI/UXデザイナー',
    'インフラエンジニア' => 'インフラエンジニア',
    '品質保証エンジニア' => '品質保証エンジニア'
];

// ステータスの選択肢を定義
$status_options = [
    '' => '全てのステータス',
    '0' => '未対応',
    '1' => '一次回答済',
    '2' => '完了',
    '3' => 'クローズ'
];


// 検索条件の取得
$search_keyword = $_GET['search_keyword'] ?? '';
$search_inquiry_kind = $_GET['search_inquiry_kind'] ?? ''; // ★新規: お問い合わせ種別検索パラメータ
$search_status = $_GET['search_status'] ?? ''; // ★新規: ステータス検索パラメータ

// SQLインジェクション対策としてエスケープし、部分一致検索用にワイルドカードを追加
$search_keyword_escaped = '%' . $conn->real_escape_string($search_keyword) . '%';

// ソート条件の取得
// デフォルトはお問い合わせ日時 (created_dt) の降順
$sort_column = $_GET['sort_column'] ?? 'created_dt';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// ソート可能なカラムをホワイトリスト化 (SQLカラム名)
$allowed_sort_columns = ['name', 'mail_address', 'inquiry_kind', 'subject', 'created_dt', 'status'];
if (!in_array($sort_column, $allowed_sort_columns)) {
    $sort_column = 'created_dt'; // 不正な場合はデフォルトに戻す
}
// ソート順もホワイトリスト化
if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
    $sort_order = 'DESC'; // 不正な場合はデフォルトに戻す
}

// SQLクエリの構築
$sql = "SELECT key_id, name, mail_address, inquiry_kind, subject, inquiry_details, created_dt, status FROM inquiry_table";
$where_clauses = [];

if (!empty($search_keyword)) {
    // 複数のカラムに対してOR検索を適用 (検索対象も物理カラム名)
    $where_clauses[] = "(name LIKE '$search_keyword_escaped' OR
                         mail_address LIKE '$search_keyword_escaped' OR
                         subject LIKE '$search_keyword_escaped' OR
                         inquiry_details LIKE '$search_keyword_escaped')";
}

// ★修正: お問い合わせ種別によるフィルタリングを修正
if (!empty($search_inquiry_kind)) {
    $search_inquiry_kind_escaped = $conn->real_escape_string($search_inquiry_kind);
    $where_clauses[] = "inquiry_kind = '$search_inquiry_kind_escaped'";
}

// ★追加: ステータスによるフィルタリング
// search_statusが空文字列でない、かつ '0', '1', '2', '3' のいずれかである場合にフィルタリングを適用
if ($search_status !== '' && in_array($search_status, array_keys($status_options))) {
    $search_status_escaped = $conn->real_escape_string($search_status);
    $where_clauses[] = "status = '$search_status_escaped'";
}


if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses); // AND で結合することで、両方の条件を満たすものを検索
}

// ソート条件を追加 (物理カラム名)
$sql .= " ORDER BY " . $sort_column . " " . $sort_order;

$result = $conn->query($sql);

$inquiries = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inquiries[] = $row;
    }
}

$conn->close();

// ステータス表示用のヘルパー関数
function getStatusText($status_code) {
    switch ($status_code) {
        case 0: return '未対応';
        case 1: return '一次回答済';
        case 2: return '完了';
        case 3: return 'クローズ';
        default: return '不明';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | お問い合わせ管理</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* inquiry_management.php 専用のスタイル調整 */
        .main-content {
            padding: 40px 20px;
            max-width: 1200px; /* 画面幅に合わせて調整 */
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .inquiry-management-section h1 { /* page-title から h1 に変更 */
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        /* 検索フォームのスタイル */
        .search-form {
            display: flex;
            flex-wrap: wrap; /* 要素が収まらない場合に折り返す */
            gap: 10px;
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f9f9f9;
            align-items: center;
        }

        .search-group {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap; /* ラベルと入力フィールドも折り返す */
        }

        .search-form label {
            font-weight: bold;
            color: #333;
            white-space: nowrap; /* ラベルが途中で改行されないように */
        }

        .search-form input[type="text"],
        .search-form select {
            flex-grow: 1;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            max-width: 400px; /* 検索フィールドの最大幅 */
        }

        .search-form button[type="submit"] { /* type="submit" を明示 */
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .search-form button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .search-form .action-button {
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block; /* ボタンのように見せる */
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .search-form .action-button:hover {
            background-color: #5a6268;
        }


        /* お問い合わせテーブルのスタイル */
        .inquiry-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .inquiry-table th,
        .inquiry-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: top; /* 内容が長い場合でも上揃え */
        }

        .inquiry-table th {
            background-color: #eef7ff; /* ヘッダーの背景色 */
            color: #333;
            font-weight: bold;
            cursor: pointer; /* ソート可能を示す */
            position: relative;
        }

        .inquiry-table th:hover {
            background-color: #e0f0ff;
        }

        .inquiry-table th .sort-indicator {
            position: absolute;
            right: 10px;
            font-size: 0.8em;
            color: #666;
        }

        .inquiry-table tr:nth-child(even) {
            background-color: #f9f9f9; /* 偶数行の背景色 */
        }

        .inquiry-table tr:hover {
            background-color: #f1f1f1;
            cursor: pointer; /* カーソルをポインターにしてクリック可能であることを示す */
        }

        /* 詳細カラムの幅調整と内容の省略 */
        .inquiry-table .col-subject {
            max-width: 250px; /* 件名の最大幅 */
            white-space: nowrap; /* 折り返しをしない */
            overflow: hidden; /* はみ出た部分を非表示 */
            text-overflow: ellipsis; /* はみ出た部分を...で表示 */
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
            .search-form input[type="text"],
            .search-form select {
                max-width: 100%;
            }
             .inquiry-table th, .inquiry-table td {
                padding: 8px; /* モバイルでパディングを減らす */
                font-size: 0.9em;
            }
        }

        /* モーダルウィンドウのスタイル */
        .modal {
            display: none; /* 初期状態では非表示 */
            position: fixed; /* 画面に固定 */
            z-index: 1000; /* 最前面に表示 */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* コンテンツがはみ出た場合にスクロール */
            background-color: rgba(0,0,0,0.4); /* 半透明の黒い背景 */
            padding-top: 60px; /* 上部に少しスペース */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 上下中央、左右中央 */
            padding: 30px;
            border: 1px solid #888;
            width: 80%; /* 幅 */
            max-width: 700px; /* 最大幅 */
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative; /* クローズボタンの配置用 */
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-body p {
            margin-bottom: 10px;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .modal-body strong {
            display: inline-block;
            width: 120px; /* ラベルの幅を固定 */
            color: #555;
        }

        .modal-body pre {
            background-color: #eef7ff;
            border: 1px solid #d0e0ed;
            padding: 15px;
            border-radius: 5px;
            white-space: pre-wrap; /* 自動改行 */
            word-wrap: break-word; /* 長い単語でも改行 */
            max-height: 200px; /* お問い合わせ内容の最大高さ */
            overflow-y: auto; /* 内容が長い場合にスクロールバー */
        }

        .status-update-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: right; /* ドロップダウンとボタンを右寄せ */
        }
        .status-update-section label {
            font-weight: bold;
            margin-right: 10px;
        }
        .status-update-section select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }
        .status-update-section button {
            padding: 8px 15px;
            background-color: #28a745; /* 緑色のボタン */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .status-update-section button:hover {
            background-color: #218838;
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
                    <li><a href="inquiry_management.php">お問い合わせ管理</a></li>
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
        <h1 class="page-title">お問い合わせ内容確認</h1>

        <section class="inquiry-management-section">
            <form action="inquiry_management.php" method="get" class="search-form">
                <div class="search-group">
                    <label for="search_keyword">キーワード検索:</label>
                    <input type="text" id="search_keyword" name="search_keyword"
                           value="<?php echo htmlspecialchars($search_keyword); ?>"
                           placeholder="名前、メール、件名、内容">
                </div>
                <div class="search-group">
                    <label for="search_inquiry_kind">お問い合わせ種別:</label>
                    <select id="search_inquiry_kind" name="search_inquiry_kind">
                        <?php foreach ($inquiry_types as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"
                                <?php echo ($search_inquiry_kind === $value) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="search-group">
                    <label for="search_status">ステータス:</label>
                    <select id="search_status" name="search_status">
                        <?php foreach ($status_options as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"
                                <?php echo (string)$search_status === (string)$value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="sort_column" value="<?php echo htmlspecialchars($sort_column); ?>">
                <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">
                <button type="submit">検索</button>
                <?php if (!empty($search_keyword) || !empty($search_inquiry_kind) || $search_status !== ''): ?>
                    <a href="inquiry_management.php" class="action-button" style="background-color: #6c757d; margin-left: 10px;">検索クリア</a>
                <?php endif; ?>
            </form>

            <?php if (!empty($search_inquiry_kind) || $search_status !== ''): ?>
                <div style="padding: 10px; margin-bottom: 10px; background-color: #f0f8ff; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9em;">
                    <strong>検索中のお問い合わせ種別:</strong> <?php echo htmlspecialchars($search_inquiry_kind); ?><br>
                    <strong>検索中のステータス:</strong> <?php echo htmlspecialchars($status_options[$search_status] ?? '全て'); ?><br>
                    <strong>該当件数:</strong> <?php echo count($inquiries); ?>件<br>
                </div>
            <?php endif; ?>

            <table class="inquiry-table">
                <thead>
                    <tr>
                        <th onclick="sortTable('name')">
                            名前
                            <?php if ($sort_column === 'name'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('mail_address')">
                            メールアドレス
                            <?php if ($sort_column === 'mail_address'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('inquiry_kind')">
                            お問い合わせ種別
                            <?php if ($sort_column === 'inquiry_kind'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('subject')" class="col-subject">
                            件名
                            <?php if ($sort_column === 'subject'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('created_dt')">
                            お問い合わせ日時
                            <?php if ($sort_column === 'created_dt'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                        <th onclick="sortTable('status')">
                            ステータス
                            <?php if ($sort_column === 'status'): ?>
                                <span class="sort-indicator"><?php echo ($sort_order === 'ASC' ? '▲' : '▼'); ?></span>
                            <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($inquiries) > 0): ?>
                        <?php foreach ($inquiries as $inquiry): ?>
                            <tr onclick="openInquiryDetailModal(<?php echo htmlspecialchars(json_encode($inquiry['key_id'])); ?>)">
                                <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['mail_address']); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['inquiry_kind']); ?></td>
                                <td class="col-subject"><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['created_dt']); ?></td>
                                <td><?php echo getStatusText($inquiry['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">表示するお問い合わせがありません。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>

    <div id="inquiryDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeInquiryDetailModal()">&times;</span>
            <h2>お問い合わせ詳細</h2>
            <div class="modal-body">
                <p><strong>名前:</strong> <span id="modalName"></span></p>
                <p><strong>メールアドレス:</strong> <span id="modalEmail"></span></p>
                <p><strong>お問い合わせ種別:</strong> <span id="modalInquiryKind"></span></p>
                <p><strong>件名:</strong> <span id="modalSubject"></span></p>
                <p><strong>お問い合わせ日時:</strong> <span id="modalCreatedDt"></span></p>
                <p><strong>お問い合わせ内容:</strong></p>
                <pre id="modalInquiryDetails"></pre>
                <p><strong>現在のステータス:</strong> <span id="modalStatusText"></span></p>
            </div>
            <div class="status-update-section">
                <label for="statusSelect">ステータス変更:</label>
                <select id="statusSelect">
                    <option value="0">未対応</option>
                    <option value="1">一次回答済</option>
                    <option value="2">完了</option>
                    <option value="3">クローズ</option>
                </select>
                <button onclick="updateInquiryStatus()">ステータス更新</button>
                <input type="hidden" id="currentInquiryKeyId">
            </div>
        </div>
    </div>

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
            const searchKeyword = currentUrl.searchParams.get('search_keyword');
            if (searchKeyword) {
                 currentUrl.searchParams.set('search_keyword', searchKeyword);
            } else {
                 currentUrl.searchParams.delete('search_keyword');
            }

            // ★修正: お問い合わせ種別の検索条件を保持
            const searchInquiryKind = currentUrl.searchParams.get('search_inquiry_kind');
            if (searchInquiryKind) {
                currentUrl.searchParams.set('search_inquiry_kind', searchInquiryKind);
            } else {
                currentUrl.searchParams.delete('search_inquiry_kind');
            }

            // ★追加: ステータスの検索条件を保持
            const searchStatus = currentUrl.searchParams.get('search_status');
            if (searchStatus !== null) { // nullチェックで空文字列も考慮
                currentUrl.searchParams.set('search_status', searchStatus);
            } else {
                currentUrl.searchParams.delete('search_status');
            }


            window.location.href = currentUrl.toString();
        }

        // モーダル表示関数
        // ★ inquiryId を keyId に変更 ★
        function openInquiryDetailModal(keyId) {
            const modal = document.getElementById('inquiryDetailModal');
            const currentInquiryKeyIdField = document.getElementById('currentInquiryKeyId');
            currentInquiryKeyIdField.value = keyId; // 選択されたお問い合わせkey_idを隠しフィールドに設定

            // 選択されたお問い合わせのデータをAjaxで取得
            // ★ inquiry_id パラメータを key_id に変更 ★
            fetch('inquiry_detail_modal.php?key_id=' + encodeURIComponent(keyId))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalName').textContent = data.inquiry.name;
                        document.getElementById('modalEmail').textContent = data.inquiry.mail_address;
                        document.getElementById('modalInquiryKind').textContent = data.inquiry.inquiry_kind;
                        document.getElementById('modalSubject').textContent = data.inquiry.subject;
                        document.getElementById('modalCreatedDt').textContent = data.inquiry.created_dt;
                        document.getElementById('modalInquiryDetails').textContent = data.inquiry.inquiry_details;
                        document.getElementById('modalStatusText').textContent = getStatusText(data.inquiry.status);

                        // ステータス選択ドロップダウンの初期値を設定
                        const statusSelect = document.getElementById('statusSelect');
                        statusSelect.value = data.inquiry.status;

                        modal.style.display = 'block'; // モーダルを表示
                    } else {
                        alert('お問い合わせ詳細の取得に失敗しました: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching inquiry details:', error);
                    alert('お問い合わせ詳細の取得中にエラーが発生しました。');
                });
        }

        // モーダルを閉じる関数
        function closeInquiryDetailModal() {
            const modal = document.getElementById('inquiryDetailModal');
            modal.style.display = 'none';
        }

        // モーダル外クリックで閉じる
        window.onclick = function(event) {
            const modal = document.getElementById('inquiryDetailModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

       // ステータス更新関数
        function updateInquiryStatus() {
            // ★ currentInquiryId を currentInquiryKeyId に変更 ★
            const keyId = document.getElementById('currentInquiryKeyId').value;
            const newStatus = document.getElementById('statusSelect').value;

            if (!confirm('ステータスを更新してもよろしいですか？')) {
                return;
            }

            fetch('inquiry_detail_modal.php', { // 同じPHPファイルを使って更新も行います
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                // ★ inquiry_id パラメータを key_id に変更 ★
                body: 'action=update_status&key_id=' + encodeURIComponent(keyId) + '&status=' + encodeURIComponent(newStatus)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('ステータスが更新されました。');
                    closeInquiryDetailModal();
                    location.reload(); // ページをリロードして一覧を更新
                } else {
                    alert('ステータス更新に失敗しました: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                alert('ステータス更新中にエラーが発生しました。');
            });
        }

        // PHPのgetStatusText関数と同期するJavaScript版
        function getStatusText(statusCode) {
            switch (parseInt(statusCode)) {
                case 0: return '未対応';
                case 1: return '一次回答済';
                case 2: return '完了';
                case 3: return 'クローズ';
                default: return '不明';
            }
        }
    </script>
</body>
</html>