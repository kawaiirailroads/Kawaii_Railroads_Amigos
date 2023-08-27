<?php
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';

// ログインしていない場合はログインページにリダイレクト
if (!isset($_SESSION["member_id"])) {
    header("Location: login.php");
    exit;
}

// ログイン中のユーザー名
$member_id = $_SESSION["member_id"];

// データベース接続
try {
    $conn = db_open();
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}

// 会員IDが取得できない場合はエラーを表示
if (!$member_id) {
    echo "会員情報が見つかりません。";
    exit;
}

// mangapages_test.jsonを読み込む
$jsonData = file_get_contents('mangapages_min.json');
$mangaPages = json_decode($jsonData, true);
?>

<h1>ギャラリー</h1>
<div>今までに集めた漫画はここで読めるよ！</div>

<?php
// member_idに紐づくギャラリーを取得するためのクエリを作成
$query = "SELECT manga_page_id FROM gallery_table WHERE member_id = :member_id";
try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':member_id', $member_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ギャラリーのリンクを表示
    foreach ($result as $row) {
        $manga_page_id = $row['manga_page_id'];
        foreach ($mangaPages as $mangaPage) {
            if ($mangaPage['id'] == $manga_page_id) {
                $name = $mangaPage['name'];
                echo '<a href="gallery_page.php?id=' . $manga_page_id . '">' . str2html($name) . '</a><br>';
                break;
            }
        }
    }
} catch (PDOException $e) {
    echo "データベースエラー: " . str2html($e->getMessage());
    exit;
}
?>
<?php include __DIR__ . '/inc/footer.php'; ?>