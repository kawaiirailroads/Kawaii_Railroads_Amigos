<?php
session_start();
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';
?>
<form method='post' action='login.php'>
    <p>
        <label for="user_name">ユーザ名：</label>
        <input type='text' name='user_name'>
    </p>
    <p>
        <label for="amigos_pass">パスワード：</label>
        <input type='password' name='amigos_pass'>
    </p>
    <input type='submit' value='ログイン'>
</form>

<?php
if (!empty($_SESSION['login'])) {
    echo "ログイン済です<br>";
    echo "<a href=index.php>トップに戻る</a>";
    exit;
}

if (empty($_POST['user_name']) || empty($_POST['amigos_pass'])) {
    echo "ユーザ名、パスワードを入力してください<br><br><a href='register.php'>会員登録(サブスク会員様限定)</a>";
    exit;
}

try {
    $dbh = db_open();
    $sql = "SELECT member_id, amigos_pass FROM `members_tbl` WHERE user_name = :user_name";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":user_name", $_POST['user_name'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        echo "ログインに失敗しました。";
        exit;
    }

    if(password_verify($_POST['amigos_pass'], $result['amigos_pass'])) {
        session_regenerate_id(true);
        $_SESSION['login'] = true;
        $_SESSION['member_id'] = $result['member_id'];
        header("Location: index.php");
        exit;
    } else {
        echo 'ログインに失敗しました。(2)';
    }

} catch (PDOException $e) {
    echo "エラー！：" . str2html($e->getMessage());
    exit;
}
include __DIR__ . '/inc/footer.php'; ?> 