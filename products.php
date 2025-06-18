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
    <title>株式会社テストシステム | 製品</title>
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
        <h1 class="page-title">当社の製品</h1>

        <div class="section-block">
            <h3>製品名A：クラウド型業務支援ツール</h3>
            <p>チームの生産性を最大化するオールインワンのクラウドソリューションです。プロジェクト管理、タスク追跡、コミュニケーション機能を統合。</p>
            <ul>
                <li>直感的なインターフェース</li>
                <li>豊富な連携機能</li>
                <li>手厚いサポート体制</li>
            </ul>
            <p><img src="img/picture01.png" alt="製品Aのイメージ" style="max-width:100%; height:auto; display:block; margin-top:15px; border-radius:5px;"></p>
        </div>

        <div class="section-block">
            <h3>製品名B：AI搭載データ分析プラットフォーム</h3>
            <p>複雑なデータを視覚化し、ビジネスの意思決定をサポートするAI駆動型分析プラットフォーム。専門知識不要で高度な分析が可能。</p>
            <ul>
                <li>AIによる自動インサイト発見</li>
                <li>リアルタイムダッシュボード</li>
                <li>カスタマイズ可能なレポート</li>
            </ul>
            <p><img src="img/picture02.png" alt="製品Bのイメージ" style="max-width:100%; height:auto; display:block; margin-top:15px; border-radius:5px;"></p>
        </div>

        <div class="section-block">
            <h3>製品名C：セキュリティ強化ソリューション</h3>
            <p>企業のデジタル資産を脅威から守るための包括的なセキュリティ対策。最新の脅威情報に基づいた予防と検知、対応を提供します。</p>
            <ul>
                <li>多要素認証対応</li>
                <li>リアルタイム脅威検知</li>
                <li>インシデントレスポンス</li>
            </ul>
            <p><img src="img/picture03.png" alt="製品Cのイメージ" style="max-width:100%; height:auto; display:block; margin-top:15px; border-radius:5px;"></p>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved.</p>
    </footer>
</body>
</html>