<?php
session_start();

// 管理者として認証されているか確認
// user_authenticated セッションが存在し、かつ true であることを確認
// 個人開発のため、is_admin フラグの確認は省略
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    // 認証されていない場合は管理者ログインページへリダイレクト
    header('Location: admin_login.php');
    exit();
}

// セッションからユーザー名を取得
$username = htmlspecialchars($_SESSION['username'] ?? '管理者'); // ユーザー名がセッションにない場合は「管理者」と表示

// エラーレポートを有効にする（開発時のみ）
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | 管理者ダッシュボード</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* admin_dashboard.php 専用のスタイル調整 */
        .main-content {
            padding: 80px;
            max-width: 900px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .admin-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .dashboard-card {
            background-color: #f0f8ff; /* 淡い青の背景 */
            border: 1px solid #cce7ff; /* 少し濃い青のボーダー */
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card h3 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .dashboard-card p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .dashboard-card .action-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .dashboard-card .action-button:hover {
            background-color: #0056b3;
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
            <?php if (!empty($username)): ?>
                <div class="user-info">
                    ようこそ、<?php echo $username; ?> さん！
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="main-content">
        <h1 class="page-title">管理者ダッシュボード</h1>

        <div class="admin-dashboard-grid">
            <div class="dashboard-card">
                <h3>ユーザー管理</h3>
                <p>登録されている一般ユーザーのアカウント情報を管理します。新規ユーザーの追加、既存ユーザーの編集、削除が行えます。</p>
                <a href="user_management.php" class="action-button">ユーザー管理</a>
            </div>

            <div class="dashboard-card">
                <h3>お問い合わせ内容確認</h3>
                <p>ユーザーからのお問い合わせ内容を一覧で確認し、対応状況を管理します。返信状況の更新や詳細情報の閲覧が可能です。</p>
                <a href="inquiry_management.php" class="action-button">お問い合わせ確認</a>
            </div>

            <div class="dashboard-card">
                <h3>システム設定</h3>
                <p>どんな画面を実装しようか検討中です。どんな画面を実装しようか検討中です。</p>
                <a href="#" class="action-button" style="background-color: #6c757d;">????</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved. (管理者)</p>
    </footer>
</body>
</html>