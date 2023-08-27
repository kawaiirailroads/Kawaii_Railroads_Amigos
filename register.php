<?php
session_start();
$token = bin2hex(random_bytes(20));
$_SESSION['token'] = $token;
?>
<?php include_once __DIR__ . '/inc/header.php'; ?>
<?php
    // ユーザー名やメールアドレスが既に登録されているかをチェック
    // ログイン済みの場合
    if (isset($_SESSION['member_id'])) {
        $loggedInMemberId = $_SESSION['member_id'];
    
        // ユーザーのamigos_handleをデータベースから取得
        $sql = "SELECT amigos_handle FROM members_tbl WHERE member_id = :member_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":member_id", $loggedInMemberId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result && isset($result['amigos_handle'])) {
            echo "既に会員登録済みのユーザーです。";
            // 他の処理を追加
            exit;
        }
    } else {
        // 通常の登録フォームを表示
    ?>
<h1>登録フォーム</h1>
<p>ブログの記事閲覧時に使用されているログインIDと、登録されているメールアドレスとで照合し、<br>
アプリ用ハンドルネーム(日本語可)とアプリ用パスワード(半角英数字)を<br>
アカウントに追加登録できます。</p>


        <form action="register_add.php" method="post">
            <p>
                <label for="user_name">ファンクラブのログインID：</label>
                <input type="text" name="user_name">
            </p>
            <p>
                <label for="email">メールアドレス：</label>
                <input type="text" name="email">
            </p>
            <p>
                <label for="amigos_handle">アプリで使うハンドルネーム：</label>
                <input type="text" name="amigos_handle">
            </p>
            <p>
                <label for="amigos_pass">アプリで使うパスワード：</label>
                <input type="text" name="amigos_pass">
            </p>
            <p class="button">
                <input type="hidden" name="token" value="<?php echo $token ?>">
                <input type="submit" value="アカウントに情報を追加">
            </p>
        </form>
        <?php
    }
    ?>

<?php include_once __DIR__ . '/inc/footer.php'; ?>