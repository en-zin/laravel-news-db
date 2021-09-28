<?php

$user = 'root';
$password = 'root';
$db = 'laravel_news';
$host = 'localhost';
$port = 3306;

$link = mysqli_init();
$success = mysqli_real_connect(
  $link,
  $host,
  $user,
  $password,
  $db,
  $port
);

$id = $_GET['id'];
$page_data = [];

$text = '';
$comments = []; //追加するデータ

$error_message = [];

// エスケープ処理
function escape($str) {
  return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

// MySQLからページの情報を取得
// WHERE分を使用して取得する  パラメーターとarticlesテーブルのidが同じやつを持ってくる
$query = "SELECT * FROM `articles` WHERE `id` = '${id}'";
if ($success) {
  $result = mysqli_query($link, $query);
  // mysqli_fetch_array 連想配列でデータの取得を行う
  while ($row = mysqli_fetch_array($result)) {
    $page_data = [$row['id'], $row['title'], $row['text']];
  }
}

// コメントの情報をココで取得
// WHERE文 パラメータのidと同じやつを取得してくる
$comment_query = "SELECT * FROM `comments` WHERE `article_id` = '${id}'";
if ($success) {
  $result = mysqli_query($link, $comment_query);
  while ($row = mysqli_fetch_array($result)) {
    $comments[] = [$row['id'], $row['article_id'], $row['comment']];
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //$_POSTはHTTPリクエストで渡された値を取得する
  //リクエストパラメーターが空でなければ
  if (!empty($_POST['txt'])) {
    //投稿ボタンが押された場合
    if (mb_strlen($_POST['txt']) > 50) {
      $error_message[] = "コメント数は50文字以内でお願いします。";
    } else {

    //$textに送信されたテキストを代入
    $text = $_POST['txt'];

    $insert_query = "INSERT INTO `comments`( `article_id`, `comment`) VALUES ('{$id}', '{$text}')";
    mysqli_query($link, $insert_query);

    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['REQUEST_URI']);
    //プログラム終了
    exit;
    }
  } else if (isset($_POST['del'])) {
    $delete_query = "DELETE FROM `comments` WHERE `id` = '{$_POST['del']}'";
    mysqli_query($link, $delete_query);

    //header()で指定したページにリダイレクト
    // //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['REQUEST_URI']);
    // //プログラム終了
    exit;
  } else if (empty($_POST['txt'])) {
    $error_message[] = "コメントは必須です。";
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta name="viewport" content="width=device-width, initial-scale= 1.0">
    <meta http-equiv="content-type" charset="utf-8">
    <link rel='stylesheet' href='./css/article.css' type="text/css">
    <title>Laravel news</title>
</head>

<body>
    <h1 class='title link'><a href='/'>Laravel News</a></h1>

    <section class="main">
        <div class='content'>
            <h2 class="subTitle"><?php echo escape($page_data[1]); ?></h2>
            <p class='article'><?php echo escape($page_data[2]); ?></p>
        </div>
        <!-- エラーメッセージ -->
        <ul>
            <?php foreach ($error_message as $error) : ?>
            <li>
                <?php echo $error ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class='commentContainer'>
            <!-- コメント表示部分 -->
            <form method="post" class="commentForm">
                <textarea name="txt" class="inputFlex commentInput"></textarea>
                <input type="submit" value="コメントを書く" class="commnetSubmitStyle">
            </form>


            <?php foreach ($comments as $comment) : ?>
            <div class="commentContent">
                <p>
                    <?php echo $comment[2] ?>
                </p>
                <div>
                    <form method="post">
                        <input type="hidden" name="del" value="<?php echo escape($comment[0]); ?>">
                        <input type="submit" value="コメントを消す" class="deleteComment">
                    </form>
                </div>
            </div>

            <?php endforeach; ?>
        </div>
    </section>
</body>

</html>