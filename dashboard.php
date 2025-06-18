<?php
session_start();
if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: login.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? '');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | ダッシュボード</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* careers.php (採用ページ) 専用のスタイル調整 */

        /* メインコンテンツの調整 */
        .main-content {
            padding-top: 20px;
        }

        /* ヒーローセクション (ページの顔となる部分) */
        .hero-section {
            text-align: center;
            padding: 80px 20px;
            /* ここを修正：背景色を暗めの薄い青系に */
            background-color: #dbe9f4; /* 落ち着いた青系 */
            color: #2c3e50;
            margin-bottom: 50px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
  　　　　　box-sizing: border-box;
        }

        .hero-section h1 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .hero-section p {
            font-size: 20px;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-section .action-button {
            /* style.css の .action-button に任せるため、個別のスタイルは削除推奨 */
            /* 必要であればここで上書き */
            /* background-color: #f39c12; */ /* オレンジ系 */
            /* color: white; */
            /* padding: 15px 30px; */
            /* font-size: 20px; */
            /* border-radius: 8px; */
            /* text-decoration: none; */
            /* transition: background-color 0.3s ease, transform 0.2s ease; */
            /* display: inline-block; */
        }

        .hero-section .action-button:hover {
            /* style.css の .action-button:hover に任せるため、個別のスタイルは削除推奨 */
            /* background-color: #e67e22; */
            /* transform: translateY(-2px); */
        }

        /* 募集職種セクション */
        .job-listings {
            margin-top: 50px;
            text-align: center;
        }

        .job-listings h2 {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 41px;
            /* ここを修正：ボーダー色を暗めの青系に */
            border-bottom: 2px solid #5a80a2;
            padding-bottom: 16px;
            display: inline-block;
        }

        .job-cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .job-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 30px;
            width: calc(33% - 30px);
            box-sizing: border-box;
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .job-card h3 {
            font-size: 24px;
            color: #34495e;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .job-card p {
            font-size: 16px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .job-card .button-group {
            text-align: right;
        }

        .job-card .action-button {
            /* style.css の .action-button に任せるため、個別のスタイルは削除推奨 */
            /* background-color: #28a745; */ /* 緑系 */
            /* color: white; */
            /* padding: 10px 20px; */
            /* font-size: 16px; */
            /* border-radius: 5px; */
            /* text-decoration: none; */
            /* transition: background-color 0.3s ease; */
        }

        .job-card .action-button:hover {
            /* style.css の .action-button:hover に任せるため、個別のスタイルは削除推奨 */
            /* background-color: #218838; */
        }

        /* 会社文化セクション */
        .culture-section {
            /* ここを修正：背景色を薄いグレー系に（緑からの変更） */
            background-color: #f0f4f7; /* 明るい背景はそのまま維持、青系のトーンと合わせる */
            padding: 50px 20px;
            text-align: center;
            border-radius: 8px;
            margin-top: 50px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.03);
        }

        .culture-section h2 {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .culture-section .quote {
            font-size: 22px;
            font-style: italic;
            color: #555;
            max-width: 900px;
            margin: 0 auto 20px auto;
            line-height: 1.6;
        }

        .culture-section .author {
            font-size: 16px;
            color: #777;
            margin-bottom: 30px;
        }

        /* CTAセクション */
        .cta-section {
            text-align: center;
            padding: 50px 20px;
            /* ここを修正：背景色を暗めの青系に */
            background-color: #5a80a2; /* 暗めの青系 */
            color: white;
            border-radius: 8px;
            margin-top: 50px;
        }

        .cta-section h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .cta-section .action-button {
            /* style.css の .action-button に任せるため、個別のスタイルは削除推奨 */
            /* background-color: #f39c12; */ /* オレンジ系 */
            /* color: white; */
            /* padding: 15px 30px; */
            /* font-size: 20px; */
            /* border-radius: 8px; */
            /* text-decoration: none; */
            /* transition: background-color 0.3s ease, transform 0.2s ease; */
            /* display: inline-block; */
        }

        .cta-section .action-button:hover {
            /* style.css の .action-button:hover に任せるため、個別のスタイルは削除推奨 */
            /* background-color: #e67e22; */
            /* transform: translateY(-2px); */
        }

        /* レスポンシブ対応 (dashboard.php特有) */
        @media (max-width: 1024px) {
            .job-card {
                width: calc(50% - 30px);
            }
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 36px;
            }

            .hero-section p {
                font-size: 18px;
            }

            .job-card {
                width: 90%;
                margin-left: auto;
                margin-right: auto;
            }

            .job-listings h2, .culture-section h2, .cta-section h2 {
                font-size: 28px;
            }

            .culture-section .quote {
                font-size: 18px;
            }
        }
    </style>
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
        <section class="hero-section">
            <h1>あなたのキャリアを、社会の未来に。</h1>
            <p>革新的な技術で社会課題を解決する仲間を募集しています。</p>
            <a href="#job-listings" class="action-button0">募集職種を見る</a>
        </section>

        <section id="job-listings" class="job-listings">
            <h2>募集職種</h2>
            <div class="job-cards-container">
                <div class="job-card">
                    <h3>ソフトウェアエンジニア</h3>
                    <p>Webアプリケーション、モバイルアプリ、バックエンドシステムなど、多岐にわたる開発を担当します。最新技術を積極的に取り入れ、高品質なコードを生み出します。</p>
                    <div class="button-group">
                        <a href="contact.php?job=ソフトウェアエンジニア" class="action-button">応募する</a>
                    </div>
                </div>
                <div class="job-card">
                    <h3>データサイエンティスト</h3>
                    <p>大量のデータから価値あるインサイトを抽出し、ビジネス戦略の立案をサポートします。機械学習モデルの開発やデータ可視化ツールの構築も行います。</p>
                    <div class="button-group">
                        <a href="contact.php?job=データサイエンティスト" class="action-button">応募する</a>
                    </div>
                </div>
                <div class="job-card">
                    <h3>プロジェクトマネージャー</h3>
                    <p>ソフトウェア開発プロジェクトの計画、実行、監視、完了までを一貫して管理します。チームと顧客の橋渡し役となり、プロジェクトを成功に導きます。</p>
                    <div class="button-group">
                        <a href="contact.php?job=プロジェクトマネージャー" class="action-button">応募する</a>
                    </div>
                </div>
                <div class="job-card">
                    <h3>UI/UXデザイナー</h3>
                    <p>ユーザーが直感的で快適に操作できるインターフェースを設計し、ユーザー体験を最適化します。デザイン思考に基づき、プロダクトの魅力を最大限に引き出します。</p>
                    <div class="button-group">
                        <a href="contact.php?job=UI/UXデザイナー" class="action-button">応募する</a>
                    </div>
                </div>
                <div class="job-card">
                    <h3>インフラエンジニア</h3>
                    <p>システムの安定稼働を支える基盤（サーバー、ネットワーク、データベースなど）の設計、構築、運用保守を担当します。クラウド技術を活用し、堅牢なインフラを構築します。</p>
                    <div class="button-group">
                        <a href="contact.php?job=インフラエンジニア" class="action-button">応募する</a>
                    </div>
                </div>
                <div class="job-card">
                    <h3>品質保証エンジニア (QA)</h3>
                    <p>開発されたソフトウェアやシステムの品質を確保するためのテスト計画、実行、改善を担当します。ユーザーに最高の体験を提供するため、品質向上に貢献します。</p>
                    <div class="button-group">
                        <a href="contact.php?job=品質保証エンジニア" class="action-button">応募する</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="culture-section">
            <h2>私たちの働く環境</h2>
            <p class="quote">「株式会社テストシステムでは、個々の多様性を尊重し、自由に意見を交わせるオープンな文化を大切にしています。技術への探究心と、社会に貢献したいという強い想いがあれば、私たちは全力でサポートします。約束するよチャンカパーナ」</p>
            <p class="author"><a href="https://www.nogizaka46.com/">— 人事部長 秋元 康 —</a></p>
            <p><a href="about.php" class="action-button" style="background-color:#555; margin-top:20px;">会社文化について詳しく見る</a></p>
        </section>

        <section class="cta-section">
            <h2>私たちと一緒に働きませんか？</h2>
            <p>ご興味をお持ちいただけましたら、ぜひお気軽にお問い合わせください。あなたの挑戦をお待ちしております。</p>
            <a href="contact.php?inquiry_type=採用に関するお問い合わせ" class="action-button">今すぐ応募・問い合わせる</a>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved.</p>
    </footer>
</body>
</html>