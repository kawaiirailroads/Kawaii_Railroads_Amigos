<?php
require_once __DIR__ . '/inc/functions.php';
header("Content-Type: application/json");
// データベースに接続
$dbh = db_open(); // データベースに接続

// POSTデータを取得
$data = json_decode(file_get_contents('php://input'), true);

// 既に同じmember_idとmanga_page_idの組み合わせがテーブルに存在するかチェック
$sql_check = "SELECT * FROM `gallery_table` WHERE `member_id` = :member_id AND `manga_page_id` = :manga_page_id";
$stmt_check = $dbh->prepare($sql_check);
$stmt_check->bindValue(":member_id", $data['member_id'], PDO::PARAM_INT);
$stmt_check->bindValue(":manga_page_id", $data['manga_index'], PDO::PARAM_INT);
$stmt_check->execute();

// 既に同じ組み合わせが存在する場合は保存を避ける
if ($stmt_check->fetch(PDO::FETCH_ASSOC)) {
    $response = array('status' => 'error', 'message' => '既にデータが存在するため、保存を中止しました');
    echo json_encode($response);
} else {
    // データベースに保存するためのINSERT文を作成
    $sql_insert = "INSERT INTO `gallery_table` (`member_id`, `manga_page_id`)
            VALUES (:member_id, :manga_page_id)";
    $stmt_insert = $dbh->prepare($sql_insert);

    // INSERT文に値をバインドして実行
    $stmt_insert->bindValue(":member_id", $data['member_id'], PDO::PARAM_INT);
    $stmt_insert->bindValue(":manga_page_id", $data['manga_index'], PDO::PARAM_INT);

    // INSERT文を実行し、エラーが発生した場合はエラーメッセージを表示
    if ($stmt_insert->execute()) {
        // データベースへの保存に成功した場合
        $response = array('status' => 'success', 'message' => 'データベースへの保存に成功しました');
        echo json_encode($response);
    } else {
        // データベースへの保存に失敗した場合
        $response = array('status' => 'error', 'message' => 'データベースへの保存に失敗しました');
        echo json_encode($response);
    }
}

// データベース接続を閉じる
$dbh = null;
?>