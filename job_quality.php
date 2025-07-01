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
    <title>株式会社テストシステム | 会社概要</title>
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
        <h1 class="page-title">品質保証エンジニア(QA)</h1>

		<div class="question_Box">
		    <div class="answer_image"><img src="img/picuture_queston.png">
		        <p class="name">
		        コダック
		        </p>
		    </div>
		    <div class="arrow_answer">
		        コダコダコダコダコダコダコダ～～
		    </div>
		</div>
		<div class="question_Box">
		    <div class="question_image"><img src="img/picuture_queston0.png">
		        <p class="name">
		        ピカ様
		        </p>
		</div>
		    <div class="arrow_question">
		        ピカピカピカピカピカピカピカ～
		    </div>
		</div>						
        <div class="section-block">
            <h3>企業理念</h3>
            <ul>
                <li><strong>革新性:</strong> 常に新しい技術とアイデアを追求し、市場をリードします。</li>
                <li><strong>顧客志向:</strong> お客様の成功を第一に考え、最適なソリューションを提供します。</li>
                <li><strong>社会貢献:</strong> テクノロジーを通じて、持続可能な社会の発展に貢献します。</li>
            </ul>
        </div>

        <div class="section-block">
            <h3>沿革</h3>
            <ul>
                <p>1974年: 宮崎県で誕生。</p>
                <p>1995年頃: 芸人としての活動を開始。</p>
                <p>1997年頃: 「習字6段」というコンビで活動していた時期がある。</p>
                <p>2002年: 所属していたホリプロを退社し、フリー期間を経て、「永野おしり」や「四木ひろし」などの芸名で活動</p>
                <p>2010年5月: フラットファイヴに所属していたが、この時期までに退社。</p>
                <p>2014年: バルーン漫談師のカルーア啓子さんと結婚。</p>
                <p>2015年頃: 「ゴッホより、普通に、ラッセンが好き！」のネタでブレイクし、テレビでの露出が増加。</p>
                <p>不明（2010年5月以降）: 現在所属するグレープカンパニーに移籍。</p>
                <p>2023年: 第7回喚き-1グランプリで優勝。</p>
                <p>2025年: 30-1グランプリで優勝。初監督映画「MAD MASK」がプチョン国際ファンタスティック映画祭にノミネートされるなど、多岐にわたる活躍を見せている。</p>
            </ul>
        </div>

        <div class="section-block">
            <h3>会社情報</h3>
            <p><strong>会社名:</strong> 株式会社 ラッセン</p>
            <p><strong>設立:</strong> 2025年7月7日</p>
            <p><strong>所在地:</strong> 〒150-0043 東京都渋谷区道玄坂1丁目2-3</p>
            <p><strong>資本金:</strong>  777万円</p>
            <p><strong>事業内容:</strong> ゴッホ制作、ラッセン開発、はぁあい支援</p>
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