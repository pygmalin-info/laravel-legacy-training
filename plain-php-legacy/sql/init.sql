-- 会員管理システム（素PHP版）初期化SQL
-- Docker の MySQL コンテナ起動時に自動実行されます。
-- ※ DB設計自体はおおむね妥当。テーブル定義の修正は課題の対象外とします。

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(191) NOT NULL,
  password VARCHAR(191) NOT NULL,  -- ※当時の実装で md5 ハッシュを保存している
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS members (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  name_kana VARCHAR(100) NULL,
  email VARCHAR(191) NOT NULL,
  phone VARCHAR(20) NULL,
  gender TINYINT UNSIGNED NOT NULL DEFAULT 3,   -- 1=男性 2=女性 3=その他
  birthday DATE NULL,
  postal_code VARCHAR(8) NULL,
  prefecture VARCHAR(20) NULL,
  address VARCHAR(191) NULL,
  `rank` TINYINT UNSIGNED NOT NULL DEFAULT 1,   -- 1=通常 2=ゴールド 3=プラチナ
  status TINYINT UNSIGNED NOT NULL DEFAULT 1,   -- 0=仮登録 1=有効 2=退会
  memo TEXT NULL,
  avatar VARCHAR(191) NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_members_email (email)
  -- email の一意制約は「アプリ側で担保する」当時の方針であえて付けていない
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ログイン用アカウント（パスワードは md5('password')）
INSERT INTO users (name, email, password) VALUES
  ('管理者', 'admin@example.com', MD5('password'));

-- 動作確認用の会員データ
INSERT INTO members (name, name_kana, email, phone, gender, birthday, postal_code, prefecture, address, `rank`, status, memo) VALUES
  ('教材 太郎', 'キョウザイ タロウ', 'taro@example.com', '090-1111-2222', 1, '1990-04-01', '100-0001', '東京都', '千代田1-1-1', 3, 1, '優良会員。<b>太字メモ</b>'),
  ('テスト 花子', 'テスト ハナコ', 'hanako@example.com', '080-3333-4444', 2, '1988-12-24', '150-0001', '東京都', '渋谷2-2-2', 1, 1, '<script>alert("memo")</script>クレーム対応履歴あり'),
  ('退会 三郎', 'タイカイ サブロウ', 'saburo@example.com', '070-5555-6666', 1, '1975-07-07', '060-0001', '北海道', '北1-1', 1, 2, ''),
  ('佐藤 一郎', 'サトウ イチロウ', 's1@example.com', '090-0000-0001', 1, '1992-01-15', '231-0001', '神奈川県', '中央1-1', 1, 1, ''),
  ('鈴木 二郎', 'スズキ ジロウ', 's2@example.com', '090-0000-0002', 1, '1985-05-05', '460-0001', '愛知県', '中1-2', 2, 1, ''),
  ('高橋 三子', 'タカハシ ミツコ', 's3@example.com', '090-0000-0003', 2, '1998-08-08', '810-0001', '福岡県', '中央2-3', 1, 0, ''),
  ('田中 四郎', 'タナカ シロウ', 's4@example.com', '090-0000-0004', 1, '1979-11-30', '530-0001', '大阪府', '北3-4', 3, 1, ''),
  ('伊藤 五子', 'イトウ イツコ', 's5@example.com', '090-0000-0005', 2, '2001-03-03', '330-0001', '埼玉県', '大宮4-5', 1, 1, ''),
  ('渡辺 六郎', 'ワタナベ ロクロウ', 's6@example.com', '090-0000-0006', 1, '1983-07-21', '260-0001', '千葉県', '中央5-6', 1, 1, ''),
  ('山本 七子', 'ヤマモト ナナコ', 's7@example.com', '090-0000-0007', 2, '1995-09-09', '100-0002', '東京都', '丸の内6-7', 2, 1, ''),
  ('中村 八郎', 'ナカムラ ハチロウ', 's8@example.com', '090-0000-0008', 1, '1972-02-02', '231-0002', '神奈川県', '山下7-8', 1, 1, ''),
  ('小林 九子', 'コバヤシ クコ', 's9@example.com', '090-0000-0009', 2, '1990-10-10', '460-0002', '愛知県', '栄8-9', 1, 1, ''),
  ('加藤 十郎', 'カトウ ジュウロウ', 's10@example.com', '090-0000-0010', 1, '1987-06-06', '810-0002', '福岡県', '天神9-10', 3, 1, ''),
  ('佐々木 千夏', 'ササキ チナツ', 's11@example.com', '090-0000-0011', 2, '1993-04-18', '530-0002', '大阪府', '梅田10-11', 1, 1, ''),
  ('山田 大輔', 'ヤマダ ダイスケ', 's12@example.com', '090-0000-0012', 1, '1981-12-01', '330-0002', '埼玉県', '浦和11-12', 2, 1, ''),
  ('松本 美咲', 'マツモト ミサキ', 's13@example.com', '090-0000-0013', 2, '1999-05-25', '260-0002', '千葉県', '幕張12-13', 1, 0, ''),
  ('井上 翔', 'イノウエ ショウ', 's14@example.com', '090-0000-0014', 1, '1976-08-14', '100-0003', '東京都', '大手町13-14', 1, 1, ''),
  ('木村 さくら', 'キムラ サクラ', 's15@example.com', '090-0000-0015', 2, '2000-01-01', '231-0003', '神奈川県', '関内14-15', 1, 1, ''),
  ('林 健', 'ハヤシ ケン', 's16@example.com', '090-0000-0016', 1, '1984-03-27', '460-0003', '愛知県', '伏見15-16', 3, 1, ''),
  ('清水 愛', 'シミズ アイ', 's17@example.com', '090-0000-0017', 2, '1991-07-07', '810-0003', '福岡県', '博多16-17', 1, 1, ''),
  ('山口 大和', 'ヤマグチ ヤマト', 's18@example.com', '090-0000-0018', 1, '1978-09-13', '530-0003', '大阪府', '難波17-18', 1, 1, ''),
  ('森 陽菜', 'モリ ヒナ', 's19@example.com', '090-0000-0019', 2, '1997-11-11', '330-0003', '埼玉県', '川口18-19', 2, 1, ''),
  ('池田 蓮', 'イケダ レン', 's20@example.com', '090-0000-0020', 1, '1986-02-22', '260-0003', '千葉県', '船橋19-20', 1, 1, ''),
  ('橋本 結衣', 'ハシモト ユイ', 's21@example.com', '090-0000-0021', 2, '1994-06-16', '100-0004', '東京都', '有楽町20-21', 1, 1, ''),
  ('阿部 颯太', 'アベ ソウタ', 's22@example.com', '090-0000-0022', 1, '1982-10-30', '231-0004', '神奈川県', '桜木町21-22', 1, 1, ''),
  ('石川 芽依', 'イシカワ メイ', 's23@example.com', '090-0000-0023', 2, '2002-04-04', '460-0004', '愛知県', '金山22-23', 1, 0, ''),
  ('前田 湊', 'マエダ ミナト', 's24@example.com', '090-0000-0024', 1, '1974-12-12', '810-0004', '福岡県', '中洲23-24', 3, 1, '');
