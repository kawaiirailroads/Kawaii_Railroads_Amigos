<?php
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';

// ログインユーザーのIDを取得する処理
$loggedInUserId = null; // 初期値はログアウト状態
if (isset($_SESSION['member_id'])) {
  $loggedInUserId = $_SESSION['member_id'];
}
?>

<div id="saveResult"></div>
<div id="xxx"></div>

<?php include __DIR__ . '/geo.php'; ?>

<?php include __DIR__ . '/inc/footer.php'; ?>