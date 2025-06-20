<?php
session_start();
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: login.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? ''); // ユーザー名がセッションにない場合を考慮
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | サービス</title>
    <link rel="stylesheet" href="style.css"> </head>
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
        <h1 class="page-title">当社のサービス</h1>

        <div class="section-block">
            <h3>Webサイト制作</h3>
            <p>お客様のビジネスに最適なWebサイトをデザインから開発まで一貫してサポートします。レスポンシブデザインに対応し、SEO対策も考慮した高品質なサイトを提供します。</p>
            <ul>
                <li>コーポレートサイト制作</li>
                <li>ECサイト構築</li>
                <li>ランディングページ制作</li>
            </ul>
        </div>

        <div class="section-block">
            <h3>システム開発</h3>
            <p>業務効率化や新規事業創出に貢献するカスタムシステムを開発します。クラウド対応や既存システムとの連携も可能です。</p>
            <ul>
                <li>業務管理システム</li>
                <li>顧客管理システム (CRM)</li>
                <li>データ分析システム</li>
            </ul>
        </div>

        <div class="section-block">
            <h3>デジタルマーケティング支援</h3>
            <p>Webサイトへの集客から売上向上まで、デジタルを活用したマーケティング戦略を立案・実行します。</p>
            <ul>
                <li>SEO/SEM対策</li>
                <li>SNSマーケティング</li>
                <li>コンテンツマーケティング</li>
            </ul>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved.</p>
    </footer>
    <a href="salary_simulator.php" class="salary-simulator-button">
        年収シミュレーター
    </a>
</body>
</html>