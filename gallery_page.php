<?php
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';

if (isset($_GET['id'])) {
    $manga_page_id = $_GET['id'];
    $jsonData = file_get_contents('mangapages_min.json');
    $mangaPages = json_decode($jsonData, true);

    // manga_page_idに対応するmangapages_min.jsonのオブジェクトを検索
    $mangaPage = null;
    foreach ($mangaPages as $page) {
        if ($page['id'] == $manga_page_id) {
            $mangaPage = $page;
            break;
        }
    }

    // manga_page_idに対応するオブジェクトが見つかった場合は表示
    if ($mangaPage) {
        $name = $mangaPage['name'];

        // すべての画像とキャプションを表示
        echo '<div class="gallery"><h1>' . $name . '</h1>';
        foreach ($mangaPage['manga'] as $index => $manga) {
            $caption = $mangaPage['caption'];
            echo '<img src="' . $manga . '" alt="' . $caption . '"><br>';
        }
        echo $caption;
        echo '</div>';
    } else {
        echo "ページが見つかりません。";
    }
} else {
    echo "無効なリクエストです。";
}

include __DIR__ . '/inc/footer.php';
?>