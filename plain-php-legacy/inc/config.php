<?php
// DB接続情報など。※ 本来はソースに直書きすべきではないが、当時からこのまま運用されている。
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', 'member_management');
define('DB_USER', 'member');
define('DB_PASS', 'secret');

// 管理者への通知先（あちこちで使う）
define('ADMIN_MAIL', 'admin@example.com');
define('FROM_MAIL', 'noreply@example.com');

// 1ページの表示件数
define('PER_PAGE', 20);
