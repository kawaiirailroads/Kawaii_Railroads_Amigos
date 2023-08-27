<?php
require_once 'functions.php';
$errors = [];

if ((empty($_POST['user_name'])) || (empty($_POST['email'])) || (empty($_POST['amigos_handle'])) || (empty($_POST['amigos_pass']))) {
    $errors[] = "項目はすべてご入力ください。";
}
if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $_POST['user_name'])) {
    $errors[] = "ファンクラブのログインIDに使用できない文字が含まれています。";
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "正しいメールアドレスを入力してください。";
}
if (!preg_match('/^[\p{L}a-zA-Z0-9_.-]+$/u', $_POST['amigos_handle'])) {
    $errors[] = "アプリで使うハンドルネームは半角英数字と日本語のみを使用してください。";
}
if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d]{8,}$/u', $_POST['amigos_pass'])) {
    $errors[] = "パスワードは少なくとも1つの英字と1つの数字を含む8文字以上で入力してください。";
}

if (!empty($errors)) {
    include_once 'header.php';  
    foreach ($errors as $error) {
        echo "<div class='error-message'>$error</div>";
    }
    include_once 'footer.php';  
    exit;
} else {
    // エラーメッセージ変数を初期化
    $errors = [];
}
?>
