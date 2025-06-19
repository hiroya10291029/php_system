-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-06-19 04:32:00
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `inquiry`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `admin_login_table`
--

CREATE TABLE `admin_login_table` (
  `key_id` int(20) NOT NULL COMMENT '主キー',
  `user_name` varchar(20) NOT NULL COMMENT 'ユーザーネーム',
  `pass_word` varchar(20) NOT NULL COMMENT 'パスワード',
  `create_dt` date NOT NULL DEFAULT current_timestamp(),
  `koshin_dt` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `admin_login_table`
--

INSERT INTO `admin_login_table` (`key_id`, `user_name`, `pass_word`, `create_dt`, `koshin_dt`) VALUES
(193, 'ImadaMio', 'imada_pass', '2025-06-17', NULL),
(194, 'Hashimotokanna', 'hashimoto_pass', '2025-06-17', NULL),
(195, 'HamabeMinami', 'hamabe_pass', '2025-06-17', NULL),
(196, 'NaganoMei', 'nagano_pass', '2025-06-17', NULL),
(197, 'HiroseSuzu', 'hirose_pass', '2025-06-17', NULL),
(198, 'KawaguchiHaruna', 'kawaguchi_pass', '2025-06-17', NULL),
(199, 'YoshiokaRiho', 'yoshioka_pass', '2025-06-17', NULL),
(200, 'MoriNana', 'mori_pass', '2025-06-17', NULL),
(201, 'KoshibaFuka', 'koshiba_pass', '2025-06-17', NULL),
(202, 'SeinoNana', 'seino_pass', '2025-06-17', NULL),
(203, 'FukuharaHaruka', 'fukuhara_pass', '2025-06-17', NULL),
(204, 'AshidaMana', 'ashida_pass', '2025-06-17', NULL),
(205, 'IkedaElaiza', 'ikeda_pass', '2025-06-17', NULL),
(206, 'KuroshimaYuina', 'kuroshima_pass', '2025-06-17', NULL),
(207, 'YoshineKyoko', 'yoshine_pass', '2025-06-17', NULL),
(208, 'YoshikawaAi', 'yoshikawa_pass', '2025-06-17', NULL),
(209, 'HottaMayu', 'hotta_pass', '2025-06-17', NULL),
(210, 'MatsumotoHonoka', 'matsumoto_pass', '2025-06-17', NULL),
(211, 'AoiWakana', 'aoi_pass', '2025-06-17', NULL),
(212, 'OkazakiSae', 'okazaki_pass', '2025-06-17', NULL),
(213, 'BabaFumika', 'baba_pass', '2025-06-17', NULL),
(214, 'YamamotoMaika', 'yamamoto_pass', '2025-06-17', NULL),
(215, 'IitoyoMarie', 'iitoyo_pass', '2025-06-17', NULL),
(216, 'ArakiYuko', 'araki_pass', '2025-06-17', NULL),
(217, 'TakahataMitsuki', 'takahata_pass', '2025-06-17', NULL),
(218, 'Honda Tsubasa', 'honda_pass', '2025-06-17', NULL),
(219, 'Shuri', 'shuri_pass', '2025-06-17', NULL),
(220, 'Matsumoto Wakana', 'matsumoto_w_pass', '2025-06-17', NULL),
(221, 'Inoue Mao', 'inoue_pass', '2025-06-17', NULL),
(222, 'Higa Manami', 'higa_pass', '2025-06-17', NULL),
(223, 'Eikura Nana', 'eikura_pass', '2025-06-17', NULL),
(224, 'Amami Yuki', 'amami_pass', '2025-06-17', NULL),
(225, 'Eguchi Noriko', 'eguchi_pass', '2025-06-17', NULL),
(226, 'Kurashina Kana', 'kurashina_pass', '2025-06-17', NULL),
(227, 'Ueto Aya', 'ueto_pass', '2025-06-17', NULL),
(228, 'Kimura Fumino', 'kimura_f_pass', '2025-06-17', NULL),
(229, 'Kichise Michiko', 'kichise_pass', '2025-06-17', NULL),
(230, 'Aoi Yu', 'aoi_y_pass', '2025-06-17', NULL),
(231, 'Ishida Yuriko', 'ishida_y_pass', '2025-06-17', NULL),
(232, 'Yoshida Yo', 'yoshida_pass', '2025-06-17', NULL),
(233, 'Yonekura Ryoko', 'yonekura_pass', '2025-06-17', NULL),
(234, 'Shiraishi Mai', 'shiraishi_pass', '2025-06-17', NULL),
(235, 'Nishino Nanase', 'nishino_pass', '2025-06-17', NULL),
(236, 'Saito Asuka', 'saito_pass', '2025-06-17', NULL),
(237, 'Yoda Yuki', 'yoda_pass', '2025-06-17', NULL),
(238, 'Yamashita Mizuki', 'yamashita_pass', '2025-06-17', NULL),
(239, 'Endo Sakura', 'endo_pass', '2025-06-17', NULL),
(240, 'Kaki Haruka', 'kaki_pass', '2025-06-17', NULL),
(241, 'Morita Hikaru', 'morita_pass', '2025-06-17', NULL),
(242, 'Tamura Hono', 'tamura_pass', '2025-06-17', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `inquiry_table`
--

CREATE TABLE `inquiry_table` (
  `key_id` int(20) NOT NULL COMMENT '主キー(自動採番)',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '名前\r\n',
  `mail_address` varchar(40) NOT NULL DEFAULT '' COMMENT 'メールアドレス',
  `inquiry_kind` varchar(20) NOT NULL DEFAULT '' COMMENT '問いあ合わせ種類',
  `subject` varchar(20) NOT NULL DEFAULT '' COMMENT '件名',
  `inquiry_details` varchar(800) NOT NULL COMMENT '問い合わせ内容',
  `status` varchar(20) NOT NULL DEFAULT '0' COMMENT '0:未対応、1:一次回答済、2:完了、3:クローズ',
  `created_dt` date NOT NULL DEFAULT current_timestamp() COMMENT 'TIIMESTAMP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `inquiry_table`
--

INSERT INTO `inquiry_table` (`key_id`, `name`, `mail_address`, `inquiry_kind`, `subject`, `inquiry_details`, `status`, `created_dt`) VALUES
(1, '齋藤飛鳥', 'saito.a@example.com', 'その他のお問い合わせ', '新機能について', '新機能の詳細を知りたいです。', '0', '0000-00-00'),
(2, '白石麻衣', 'shiraishi.m@example.com', '採用に関するお問い合わせ', '採用についてのお問い合わせ', '採用に関する一般的な質問です。', '0', '0000-00-00'),
(3, '西野七瀬', 'nishino.n@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', 'ソフトウェアエンジニア職に応募します。', '0', '0000-00-00'),
(4, '生田絵梨花', 'ikuta.e@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', 'データサイエンティスト職に応募します。', '0', '0000-00-00'),
(5, '山下美月', 'yamashita.m@example.com', 'プロジェクトマネージャー', '採用応募：プロジェクトマネージャー', 'プロジェクトマネージャー職に応募します。', '0', '0000-00-00'),
(6, '与田祐希', 'yoda.y@example.com', 'UI/UXデザイナー', '採用応募：UI/UXデザイナー', 'UI/UXデザイナー職に応募します。', '0', '0000-00-00'),
(7, '梅澤美波', 'umezawa.m@example.com', 'インフラエンジニア', '採用応募：インフラエンジニア', 'インフラエンジニア職に応募します。', '0', '0000-00-00'),
(8, '賀喜遥香', 'kaki.h@example.com', '品質保証エンジニア', '採用応募：品質保証エンジニア', '品質保証エンジニア職に応募します。', '1', '0000-00-00'),
(9, '遠藤さくら', 'endo.s@example.com', 'その他のお問い合わせ', '不具合報告', '〇〇機能に不具合を発見しました。', '0', '0000-00-00'),
(10, '筒井あやめ', 'tsutsui.a@example.com', '採用に関するお問い合わせ', '採用に関する一般的なお問い合わせ', 'キャリアパスについて質問です。', '0', '0000-00-00'),
(11, '久保史緒里', 'kubo.s@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', 'ソフトウェアエンジニア職の経験について。', '0', '0000-00-00'),
(12, '田村真佑', 'tamura.m@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', 'データサイエンスのプロジェクトについて質問。', '0', '0000-00-00'),
(13, '金川紗耶', 'kanagawa.s@example.com', 'プロジェクトマネージャー', '採用応募：プロジェクトマネージャー', 'プロジェクトマネジメント経験の詳細。', '0', '0000-00-00'),
(14, '清宮レイ', 'seimiya.r@example.com', 'UI/UXデザイナー', '採用応募：UI/UXデザイナー', 'ポートフォリオの確認をお願いします。', '3', '0000-00-00'),
(15, '星野みなみ', 'hoshino.m@example.com', 'インフラエンジニア', '採用応募：インフラエンジニア', 'インフラ構築経験について。', '0', '0000-00-00'),
(16, '松村沙友理', 'matsumura.s@example.com', '品質保証エンジニア', '採用応募：品質保証エンジニア', 'テスト自動化の経験について。', '0', '0000-00-00'),
(17, '秋元真夏', 'akimono.m@example.com', 'その他のお問い合わせ', '機能要望', '〇〇のような機能を追加してほしいです。', '0', '0000-00-00'),
(18, '高山一実', 'takayama.k@example.com', '採用に関するお問い合わせ', '福利厚生について', '福利厚生について教えてください。', '0', '0000-00-00'),
(19, '桜井玲香', 'sakurai.r@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', '開発環境について質問です。', '0', '0000-00-00'),
(20, '深川麻衣', 'fukagawa.m@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', '貴社でのデータ分析について。', '0', '0000-00-00'),
(21, '早川聖来', 'hayakawa.s@example.com', 'その他のお問い合わせ', 'データ分析について', '最近のデータ分析トレンドについて質問です。', '0', '0000-00-00'),
(22, '柴田柚菜', 'shibata.y@example.com', '採用に関するお問い合わせ', 'マネージャー職について', 'マネージャー職の応募要件を教えてください。', '0', '0000-00-00'),
(23, '佐藤璃果', 'sato.r@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', '新しい開発言語の経験について。', '2', '0000-00-00'),
(24, '林瑠奈', 'hayashi.r@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', 'データモデルの構築経験について。', '0', '0000-00-00'),
(25, '弓木奈於', 'yumiki.n@example.com', 'プロジェクトマネージャー', '採用応募：プロジェクトマネージャー', '大規模プロジェクトの経験について。', '0', '0000-00-00'),
(26, '北川悠理', 'kitagawa.y@example.com', 'UI/UXデザイナー', '採用応募：UI/UXデザイナー', 'デザインツールの使用経験について。', '0', '0000-00-00'),
(27, '黒見明香', 'kuromi.h@example.com', 'インフラエンジニア', '採用応募：インフラエンジニア', 'クラウド環境の構築経験について。', '2', '0000-00-00'),
(28, '向井葉月', 'mukai.h@example.com', '品質保証エンジニア', '採用応募：品質保証エンジニア', 'QAプロセスの改善提案について。', '0', '0000-00-00'),
(29, '吉田綾乃クリスティー', 'yoshida.a@example.com', 'その他のお問い合わせ', 'パートナーシップについて', '新規ビジネスパートナーシップの可能性を探っています。', '0', '0000-00-00'),
(30, '阪口珠美', 'sakaguchi.t@example.com', '採用に関するお問い合わせ', '企業文化について', '貴社の企業文化について詳しく知りたいです。', '0', '0000-00-00'),
(31, '佐藤楓', 'sato.kaede@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', 'バックエンド開発の経験について質問です。', '0', '0000-00-00'),
(32, '中村麗乃', 'nakamura.re@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', '機械学習モデルのデプロイ経験について。', '0', '0000-00-00'),
(33, '伊藤理々杏', 'ito.riri@example.com', 'プロジェクトマネージャー', '採用応募：プロジェクトマネージャー', 'アジャイル開発の経験について質問です。', '0', '0000-00-00'),
(34, '岩本蓮加', 'iwamoto.re@example.com', 'UI/UXデザイナー', '採用応募：UI/UXデザイナー', 'ユーザーインタビューの経験について。', '0', '0000-00-00'),
(35, '大園桃子', 'ozono.mo@example.com', 'インフラエンジニア', '採用応募：インフラエンジニア', 'コンテナ技術（Docker/Kubernetes）の経験について。', '0', '0000-00-00'),
(36, '理々杏', 'riri.a@example.com', '品質保証エンジニア', '採用応募：品質保証エンジニア', 'E2Eテストフレームワークの経験について。', '0', '0000-00-00'),
(37, '北野日奈子', 'kitano.ki@example.com', 'その他のお問い合わせ', 'プライバシーポリシーについて', 'プライバシーポリシーの改定点について確認です。', '0', '0000-00-00'),
(38, '新内眞衣', 'shinuchi.m@example.com', '採用に関するお問い合わせ', 'インターンシップについて', 'インターンシップの募集はありますか？', '0', '0000-00-00'),
(39, '若月佑美', 'wakatsuki.y@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', 'フロントエンド開発の経験について。', '0', '0000-00-00'),
(40, '衛藤美彩', 'eto.m@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', 'ビッグデータの処理経験について。', '0', '0000-00-00'),
(41, '堀未央奈', 'hori.m@example.com', 'その他のお問い合わせ', 'イベント開催について', '次回のイベント開催予定について教えてください。', '0', '0000-00-00'),
(42, '鈴木絢音', 'suzuki.a@example.com', '採用に関するお問い合わせ', '中途採用について', '現在募集している中途採用の職種はありますか？', '3', '0000-00-00'),
(43, '渡辺みり愛', 'watanabe.m@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', 'モバイルアプリ開発の経験について。', '3', '0000-00-00'),
(44, '山崎怜奈', 'yamazaki.r@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', 'A/Bテストの設計・分析経験について。', '1', '0000-00-00'),
(45, '伊藤かりん', 'ito.ka@example.com', 'プロジェクトマネージャー', '採用応募：プロジェクトマネージャー', 'スクラムマスターとしての経験について。', '0', '0000-00-00'),
(46, '樋口日奈', 'higuchi.h@example.com', 'UI/UXデザイナー', '採用応募：UI/UXデザイナー', 'アクセシビリティデザインの知見について。', '0', '0000-00-00'),
(47, '和田まあや', 'wada.m@example.com', 'インフラエンジニア', '採用応募：インフラエンジニア', 'オンプレミス環境の運用経験について。', '0', '0000-00-00'),
(48, '能條愛未', 'noujou.a@example.com', '品質保証エンジニア', '採用応募：品質保証エンジニア', 'パフォーマンス・負荷テストの経験について。', '0', '0000-00-00'),
(49, '斉藤優里', 'saito.yu@example.com', 'その他のお問い合わせ', 'メディア出演について', 'メディア出演に関するご依頼です。', '0', '0000-00-00'),
(50, '川後陽菜', 'kawago.h@example.com', '採用に関するお問い合わせ', '新卒採用について', '来年度の新卒採用のスケジュールについて。', '0', '0000-00-00'),
(51, '相楽伊織', 'sagara.i@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', '分散システム開発の経験について。', '0', '0000-00-00'),
(52, '寺田蘭世', 'terada.r@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', '自然言語処理（NLP）の経験について。', '0', '0000-00-00'),
(53, '中元日芽香', 'nakamoto.h@example.com', 'プロジェクトマネージャー', '採用応募：プロジェクトマネージャー', 'リスク管理の経験について。', '0', '0000-00-00'),
(54, '永島聖羅', 'nagashima.s@example.com', 'UI/UXデザイナー', '採用応募：UI/UXデザイナー', 'ユーザーリサーチの手法について。', '0', '0000-00-00'),
(55, '深川麻衣', 'fukagawa.m2@example.com', 'インフラエンジニア', '採用応募：インフラエンジニア', 'ネットワークセキュリティの知識について。', '0', '0000-00-00'),
(56, '橋本奈々未', 'hashimoto.n@example.com', '品質保証エンジニア', '採用応募：品質保証エンジニア', 'セキュリティテストの経験について。', '0', '0000-00-00'),
(57, '生駒里奈', 'ikoma.r@example.com', 'その他のお問い合わせ', '広報協力について', '貴社サービスに関する広報協力のご相談です。', '3', '0000-00-00'),
(58, '桜井玲香', 'sakurai.r2@example.com', '採用に関するお問い合わせ', '福利厚生の詳細について', '詳細な福利厚生制度について教えてください。', '0', '0000-00-00'),
(59, '若月佑美', 'wakatsuki.y2@example.com', 'ソフトウェアエンジニア', '採用応募：ソフトウェアエンジニア', 'クラウドネイティブな開発経験について。', '0', '0000-00-00'),
(60, '衛藤美彩', 'eto.m2@example.com', 'データサイエンティスト', '採用応募：データサイエンティスト', 'レコメンデーションシステムの構築経験について。', '1', '0000-00-00'),
(61, '森尋哉', 'hiroya1029hiroya1029@yahoo.co.jp', 'その他のお問い合わせ', 'その他のお問い合わせ', 'あ', '0', '2025-06-17'),
(122, 'HIROYA MORI', 'aaa@aa', 'その他のお問い合わせ', 'その他のお問い合わせ', 'fhd', '2', '2025-06-17'),
(123, '森尋哉', 'aaa@aa', 'インフラエンジニア', '採用応募：インフラエンジニア', 'konｓｘ', '0', '2025-06-17'),
(124, 'HIROYA MORI', 'hiroya1029hiroya1029@yahoo.co.jp', 'その他のお問い合わせ', 'その他のお問い合わせ', 'a', '0', '2025-06-17'),
(125, '森尋哉', 'ooooooo@oooo', '品質保証エンジニア', '採用応募：品質保証エンジニア', 'ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ', '0', '2025-06-17'),
(126, 'テスト', 'test@test.jp', 'その他のお問い合わせ', 'その他のお問い合わせ', 'ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ', '2', '2025-06-18'),
(127, 'a', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'その他のお問い合わせ', 'その他のお問い合わせ', 'aa', '3', '2025-06-18'),
(128, '瀬戸環奈', 'kanna_seto@yahoo.ne.jp', 'データサイエンティスト', '採用応募：データサイエンティスト', 'データサイエンティストへの応募です。よろしくお願いいたします。', '1', '2025-06-18');

-- --------------------------------------------------------

--
-- テーブルの構造 `login_table`
--

CREATE TABLE `login_table` (
  `key_id` int(20) NOT NULL COMMENT '主キー',
  `user_name` varchar(40) NOT NULL COMMENT 'ユーザーネーム',
  `pass_word` varchar(40) NOT NULL COMMENT 'パスワード',
  `create_dt` date NOT NULL DEFAULT current_timestamp(),
  `koshin_dt` date DEFAULT NULL COMMENT '更新日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `login_table`
--

INSERT INTO `login_table` (`key_id`, `user_name`, `pass_word`, `create_dt`, `koshin_dt`) VALUES
(101, 'asuka_saito', 'saito_pass1', '2025-06-16', '2025-06-16'),
(102, 'mizuki_yamashita', 'yamashita_pass2', '2025-06-17', '2025-06-12'),
(103, 'yuki_yoda', 'yoda_pass3', '2025-06-17', NULL),
(104, 'sakura_endo', 'endo_pass4', '2025-06-17', NULL),
(105, 'haruka_kaki', 'kaki_pass5', '2025-06-17', NULL),
(106, 'rena_kubo', 'kubo_pass6', '2025-06-17', NULL),
(107, 'shiori_kubo', 'kubo_pass7', '2025-06-17', '2025-06-18'),
(108, 'ayame_tsutsui', 'tsutsui_pass8', '2025-06-17', NULL),
(109, 'saya_kanagawa', 'kanagawa_pass9', '2025-06-17', NULL),
(110, 'nagi_inoue', 'inoue_pass10', '2025-06-17', NULL),
(111, 'hina_kawago', 'kawago_pass11', '2025-06-17', NULL),
(112, 'mao_goto', 'goto_pass12', '2025-06-17', '2025-06-18'),
(113, 'miku_kono', 'kono_pass13', '2025-06-17', NULL),
(114, 'rui_saito', 'saito_pass14', '2025-06-17', NULL),
(115, 'rika_sato', 'sato_pass15', '2025-06-17', NULL),
(116, 'seira_sato', 'sato_pass16', '2025-06-17', NULL),
(118, 'satsuki_sugawara', 'sugawara_pass18', '2025-06-17', '2025-06-18'),
(119, 'risa_watanabe', 'nakamura_pass19', '2025-06-17', '2025-06-17'),
(120, 'yua_mikami', 'okamoto_pass20', '2025-06-17', '2025-06-17'),
(121, 'あああああああああ', '$2y$10$x2NsWIbFEYD0iTIWqZBFE.dscf52swOyo', '2025-06-18', '2025-06-18'),
(122, 'bbb', '$2y$10$bxVB766CkkaCKkKv5AUkJeViy9V9.9vDU', '2025-06-18', '2025-06-18'),
(123, 'xxx', 'xxx', '2025-06-18', '2025-06-18');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `admin_login_table`
--
ALTER TABLE `admin_login_table`
  ADD PRIMARY KEY (`key_id`);

--
-- テーブルのインデックス `inquiry_table`
--
ALTER TABLE `inquiry_table`
  ADD PRIMARY KEY (`key_id`);

--
-- テーブルのインデックス `login_table`
--
ALTER TABLE `login_table`
  ADD PRIMARY KEY (`key_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `admin_login_table`
--
ALTER TABLE `admin_login_table`
  MODIFY `key_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主キー', AUTO_INCREMENT=243;

--
-- テーブルの AUTO_INCREMENT `inquiry_table`
--
ALTER TABLE `inquiry_table`
  MODIFY `key_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主キー(自動採番)', AUTO_INCREMENT=129;

--
-- テーブルの AUTO_INCREMENT `login_table`
--
ALTER TABLE `login_table`
  MODIFY `key_id` int(20) NOT NULL AUTO_INCREMENT COMMENT '主キー', AUTO_INCREMENT=124;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
