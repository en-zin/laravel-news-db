<?php
$user = 'root';
$password = 'root';
$db = 'laravel_news';
$host = 'localhost';
$port = 3306;

// MySQL接続を開始する
$link = mysqli_init();

// 接続が確立されている場合はtrueを返す
$success = mysqli_real_connect(
    $link,
    $host,
    $user,
    $password,
    $db,
    $port
);

//エスケープ処理
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

//変数の準備
$text = ''; //入力テキスト
$DATA = []; //一回分の投稿の情報を入れる
$BOARD = []; //全ての投稿の情報を入れる
$error_message = [];

// MySQLからデータを取得
$query = "SELECT * FROM `articles`";

if ($success) {
    // MySQLに対してQueryを発行する
    $result = mysqli_query($link, $query);
        // データベース内のデータ分配列で受け取る
    while ($row = mysqli_fetch_array($result)) {
        // $BOARD[] = $row でも結果は同じだが記述が少ないよりも読んで理解できるコードの方が素敵
        $BOARD[] = [$row['id'], $row['title'], $row['text']];
    }
}

//$_SERVERは送信されたサーバーの情報を得る
//REQUEST_METHODはフォームからのリクエストのメソッドがPOSTかGETか判断する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //$_POSTはHTTPリクエストで渡された値を取得する
  //リクエストパラメーターが空でなければ
    if (!empty($_POST['txt']) && !empty($_POST['title'])) {
    //投稿ボタンが押された場合

    //$textに送信されたテキストを代入
        $title = $_POST['title'];
        $text = $_POST['txt'];

        // データー追加のためのQuery
        // VALUES以降の文で変数を{}で囲っているが囲う必要はない、囲わなくても変数と認識している。
        // 今回の処理ではデーターベースに保存する際特殊文字などを弾く処理を書く必要はない、ただしログイン機能などのある処理などでは
        // 記号('<' '='  などパスワードやユーザー名に必要のない文字はMySQLに発行する前に弾かなければいけない)
        $insert_query = "INSERT INTO `articles`(`title`, `text`) VALUES ('{$title}', '{$text}')";

        // MySQLに対してQueryを発行する
        mysqli_query($link, $insert_query);

        //header()で指定したページにリダイレクト
        //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        //プログラム終了
        exit;
    } else {
        if (empty($_POST['title'])) $error_message[] = 'タイトルは必須です。';
        if (empty($_POST['txt'])) $error_message[] = "記事は必須です。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="viewport" content="width=device-width, initial-scale= 1.0">
    <meta http-equiv="content-type" charset="utf-8">
    <link rel='stylesheet' href='./css/index.css' type="text/css">
    <title>Laravel news</title>
</head>

<body>
    <h1 class='title'>Laravel News</h1>

    <section class="main">
        <h2 class="subTitle">さぁ、最新のニュースをシェアしましょう</h2>

        <!-- Errorメッセージ -->
        <ul>
            <?php foreach ($error_message as $error) : ?>
            <li>
                <?php echo $error ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <!--投稿-->
        <form method="post" class="form" onsubmit="return submitCheckFunction()">
            <div class='titleContainer'>
                <p class='nameFlex'>title: </p>
                <input type='text' name='title' class="inputFlex">
            </div>
            <div class='articleContainer'>
                <p class='nameFlex'>記事: </p>
                <textarea rows="10" cols="60" name="txt" class="inputFlex articleInput"></textarea>
            </div>
            <div class="submitContainer">
                <input type="submit" value="投稿" class="submitStyle">
            </div>
        </form>

        <hr>

        <!-- content -->
        <div class='Container'>
            <?php foreach ($BOARD as $DATA) : ?>
            <div class="content">
                <p class="articleTitle">
                    <?php echo escape($DATA[1]); ?>
                </p>
                <p class="articleText">
                    <?php echo escape($DATA[2]); ?>
                </p>
                <p class='routingStyle'><a href='comment.php?id=<?php echo $DATA[0]; ?>'>記事全文・コメントを見る</a></p>
            </div>

            <?php endforeach; ?>
        </div>
    </section>

    <script type="text/javascript" src="./js/index.js"></script>
</body>

</html>