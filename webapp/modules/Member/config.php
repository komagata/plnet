<?php
// error
// account
define('ERROR_ACCOUNT_REQUIRED', 'アカウント名は必須です');
define('ERROR_ACCOUNT_TAKEN', 'アカウント名が既に使われています');
define('ERROR_ACCOUNT_BANNED', 'アカウント名が使用できません');
define('ERROR_ACCOUNT_CHARACTER', 'アカウント名に使用できない文字列が含まれています');
define('PATTERN_ACCOUNT', "/^[0-9a-zA-Z-_]+$/");

// password
define('ERROR_PASSWORD_WRONG', '再入力のパスワードと一致しません');

// email
define('ERROR_EMAIL_REQUIRED', 'E-mailアドレスは必須です');
define('ERROR_EMAIL_WRONG', 'E-mailアドレスが間違っています');

// confirmaiton mail
define('EMAIL_SUBJECT', 'plnetよりメンバー登録のお知らせ');
define('EMAIL_BODY', "plnetへのメンバー登録ありがとうございます。\n\n" .
                     "下記のURLをクリックして、登録を完了させてください。\n" .
                     SCRIPT_PATH . "activate/%s");
define('EMAIL_FROM', 'info@plnet.jp');
?>
