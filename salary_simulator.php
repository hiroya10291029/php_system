<?php
session_start();

// ユーザー認証の確認（必要であれば、一般ユーザー向けのアクセス制御も考慮）

if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
    header('Location: login.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? '');
error_reporting(E_ALL);
ini_set('display_errors', 1);


// 各職種の年収目安を定義 (下限, 上限)
// 単位は万円
$salaries_base = [
    'ソフトウェアエンジニア' => ['min' => 450, 'max' => 900],
    'データサイエンティスト' => ['min' => 500, 'max' => 1000],
    'プロジェクトマネージャー' => ['min' => 600, 'max' => 1200],
    'UI/UXデザイナー' => ['min' => 400, 'max' => 800],
    'インフラエンジニア' => ['min' => 450, 'max' => 900],
    '品質保証エンジニア (QA)' => ['min' => 400, 'max' => 850],
];

// 選択された職種と経験年数を取得
$selected_job = $_GET['job'] ?? '';
$experience_years = isset($_GET['experience']) ? (int)$_GET['experience'] : 0;

$display_salary = '';

if (isset($salaries_base[$selected_job])) {
    $job_min_salary = $salaries_base[$selected_job]['min'];
    $job_max_salary = $salaries_base[$selected_job]['max'];

    // 基本年収（下限と上限の中間値）
    $base_salary = ($job_min_salary + $job_max_salary) / 2;

    // 経験年数による加算を計算 (1年につき基本年収の5%を加算、最大10年分)
    $experience_multiplier = min($experience_years, 10) * 0.05; // 経験年数が10年を超えても50%まで
    $additional_salary = $base_salary * $experience_multiplier;

    // 経験年数を考慮した推定年収
    $estimated_salary = $base_salary + $additional_salary;

    // 算出された年収が元のレンジを大きく超えないように調整（任意）
    // 例えば、上限値の1.2倍まで、といった上限を設けることも可能
    $adjusted_max_salary = $job_max_salary * 1.2; // 上限値の20%増しを最大とする例
    $adjusted_min_salary = $job_min_salary * 0.8; // 下限値の20%減を最小とする例 (経験0年の場合などを考慮)

    $final_salary_lower = max($job_min_salary, floor($estimated_salary * 0.9)); // 推定値の-10%をレンジ下限
    $final_salary_upper = min($job_max_salary, ceil($estimated_salary * 1.1));  // 推定値の+10%をレンジ上限

    // もし経験年数でレンジが広がった場合、元の上限を超過する分を調整
    // 例: 経験年数によって算出した結果が元の上限を超えた場合、元の最大値か、調整された上限値でキャップする
    $final_salary_lower = max($final_salary_lower, $job_min_salary);
    $final_salary_upper = min($final_salary_upper, $adjusted_max_salary);

    // 経験年数によるレンジ幅の調整をもう少し動的にする
    // 例: 基本レンジ幅の20%を経験年数で分配する
    $range_spread = ($job_max_salary - $job_min_salary) * (1 + min($experience_years, 10) * 0.02); // 経験1年で2%レンジが広がる

    $calculated_min = $estimated_salary - ($range_spread / 2);
    $calculated_max = $estimated_salary + ($range_spread / 2);

    // 職種ごとの元の最低・最高年収を下回らないように、また大きく逸脱しないように補正
    $final_min_salary = max($job_min_salary, floor($calculated_min));
    $final_max_salary = min($job_max_salary * 1.2, ceil($calculated_max)); // 元の上限の1.2倍までを許容

    // もし経験年数に応じて計算された下限が上限を超えてしまうようなら調整
    if ($final_min_salary > $final_max_salary) {
        $final_min_salary = $final_max_salary;
    }


    $display_salary = "{$final_min_salary}万円～{$final_max_salary}万円";

} else {
    $display_salary = '職種と経験年数を選択してください。';
}

// 経験年数の選択肢を生成
$experience_options = [];
for ($i = 0; $i <= 10; $i++) {
    if ($i == 10) {
    $experience_options[$i] = $i . '年以上';
    }else{
    $experience_options[$i] = $i . '年';
}
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>株式会社テストシステム | 年収シミュレーター</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* salary_simulator.php 専用のスタイル調整 */
        .simulator-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .simulator-container h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .simulator-container label {
            font-size: 1.1em;
            color: #555;
            margin-right: 15px;
            display: inline-block; /* ラベルとセレクトボックスが同じ行になるように */
            margin-bottom: 10px; /* 縦方向のスペースを確保 */
        }
        .simulator-container select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            margin-bottom: 20px;
            width: calc(100% - 150px); /* ラベルの幅を考慮 */
            max-width: 250px; /* 少し小さめに調整 */
        }
        .form-group {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap; /* レスポンシブ対応で折り返す */
        }
        .form-group label, .form-group select {
            margin: 5px 10px; /* 要素間のスペース */
        }
        .salary-display {
            margin-top: 30px;
            padding: 20px;
            background-color: #e0f2f7;
            border: 1px solid #a7d9ed;
            border-radius: 8px;
        }
        .salary-display p {
            font-size: 1.3em;
            color: #333;
            margin: 0;
        }
        .salary-display .amount {
            font-size: 1.8em;
            font-weight: bold;
            color: #007bff; /* ブランドカラーの青 */
            margin-top: 10px;
        }
        /* レスポンシブ対応 */
        @media (max-width: 600px) {
            .simulator-container {
                margin: 20px auto;
                padding: 20px;
            }
            .form-group {
                flex-direction: column;
                align-items: stretch;
            }
            .form-group label {
                margin-right: 0;
                text-align: left;
                width: 100%;
                margin-bottom: 5px;
            }
            .form-group select {
                width: 100%;
                max-width: none;
                margin-bottom: 15px;
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
                <div class="user-info">  ようこそ、<?php echo htmlspecialchars($username); ?> さん！
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="main-content">
        <div class="simulator-container">
            <h1>年収シミュレーター</h1>
            <p>ご希望の職種と経験年数を選択し、年収目安をご確認ください。</p>

            <form action="salary_simulator.php" method="GET">
                <div class="form-group">
                    <label for="job_select">職種：</label>
                    <select name="job" id="job_select" onchange="this.form.submit()">
                        <option value="">--選択してください--</option>
                        <?php foreach ($salaries_base as $job_title => $range): ?>
                            <option value="<?php echo htmlspecialchars($job_title); ?>"
                                <?php echo ($selected_job === $job_title) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($job_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="experience_select">経験年数：</label>
                    <select name="experience" id="experience_select" onchange="this.form.submit()">
                        <?php foreach ($experience_options as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"
                                <?php echo ((string)$experience_years === (string)$value) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <div class="salary-display">
                <?php if ($selected_job && $experience_years !== null): // 経験年数が選択されていれば表示 ?>
                    <p><strong><?php echo htmlspecialchars($selected_job); ?> (経験<?php echo htmlspecialchars($experience_years); ?>年)</strong> の年収目安:</p>
                    <p class="amount"><?php echo htmlspecialchars($display_salary); ?></p>
                <?php else: ?>
                    <p><?php echo htmlspecialchars($display_salary); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2023 株式会社テストシステム. All rights reserved.</p>
    </footer>

</body>
</html>