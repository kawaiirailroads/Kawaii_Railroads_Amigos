<?php
require_once __DIR__ . '/token_check.php';
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/account_check.php';
include_once __DIR__ . '/inc/header.php';

try {
    // ファンクラブのログインIDとメールアドレスを取得
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];

    $dbh = db_open();

    // ファンクラブのログインIDとメールアドレスを元に特定の行を取得
    // 登録権があるのはサブスク会員のみなので必要な情報をテーブルから拾ってきています
    // membership_levelは3以上でサブスク会員    
    $sql = "SELECT membership_level, account_state, amigos_handle FROM members_tbl WHERE user_name = :user_name AND email = :email";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 特定の行が存在するかチェック
    if (!$result) {
        echo "指定されたファンクラブのログインIDとメールアドレスが見つかりません。";
        exit;
    }

    if ($result['amigos_handle'] !== null && $result['amigos_handle'] !== '') {
        echo "既に会員登録済みのユーザーです。";
    // membership_levelが3以上でかつaccount_stateがactiveでない場合は登録を受け付けない
    } else if ($result['membership_level'] >= 3 && $result['account_state'] === 'active') {
        // ユーザーが入力したパスワードをハッシュ化して変数に代入
        $hashedPassword = password_hash($_POST['amigos_pass'], PASSWORD_DEFAULT);

        // ハンドルネームとアプリ用パスワードをテーブルに挿入
        $sql = "UPDATE members_tbl SET amigos_pass = :amigos_pass, amigos_handle = :amigos_handle WHERE user_name = :user_name AND email = :email";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":amigos_handle", $_POST['amigos_handle'], PDO::PARAM_STR);
        $stmt->bindParam(":amigos_pass", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        echo "会員登録が完了しました。";
        echo "<a href='login.php'>ログイン画面へ</a>";
    } else {
        echo "会員登録が許可されていません。";
    }
} catch (PDOException $e) {
    echo "エラー！：" . str2html($e->getMessage()) . "<br>";
    exit;
}
include_once __DIR__ . '/inc/footer.php';
?>