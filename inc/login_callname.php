<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/functions.php';
try {
    if (empty($_SESSION['login'])) {
        echo "<div class='callname'>無料でプレイ中！ <a href='login.php'>ログイン</a></div><div class='menu'><a href='geolocation.php'>探検</a> <a href='rule.php'>あそびかた</a> <a href='index.php'>トップ</a> <a href='https://example.com' target='_blank'>他リンク</a></div>";
    } else {
        $dbh = db_open();
        $sql = "SELECT amigos_handle FROM `members_tbl` WHERE member_id = :member_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":member_id", $_SESSION['member_id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<div class='callname'>こんにちは、" . str2html($result['amigos_handle']) . "さん！</div><div class='menu'><a href='geolocation.php'>探検</a> <a href='gallery.php'>ギャラリー</a> <a href='index.php'>トップ</a> <a href='logout.php'>ログアウト</a></div>";
        } else {
            echo "<div class='callname'>ハンドルネームの取得に失敗しました。<br>お手数ですが、<a href='logout.php'>会員様限定機能をご利用の場合こちらでログアウト後、再度ログインをお願いします。</a></div><div class='menu'><a href='geolocation.php'>探検</a> <a href='gallery.php'>ギャラリー</a> <a href='index.php'>トップ</a> </div>";
        }
    }
} catch (PDOException $e) {
    echo "エラー！：" . str2html($e->getMessage());
    exit;
}
?>