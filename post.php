<!-- ここから変更 -->
<?php
include 'lib/secure.php';
include 'lib/connect.php';
include 'lib/queryArticle.php';
include 'lib/article.php';
include 'lib/queryCategory.php';

use PHPMailer\PHPMailerPHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// 設置した場所のパスを指定する
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

// 文字エンコードを指定
mb_language('uni');
mb_internal_encoding('UTF-8');

// インスタンスを生成（true指定で例外を有効化）
$mail = new PHPMailer(true);

// 文字エンコードを指定
$mail->CharSet = 'utf-8';

$title = ""; // タイトル
$body = ""; // 本文
$title_alert = ""; // タイトルのエラー文言
$body_alert = ""; // 本文のエラー文言

$queryCategory = new QueryCategory();
$categories = $queryCategory->findAll();
if ((!empty($_POST['title']) && !empty($_POST['body'])) || (!empty($_GET['title']) && !empty($_GET['body']))) {
    // titleとbodyがPOSTメソッドで送信されたとき
    $title = $_POST['title'];
    $body = $_POST['body'];

    //completeを受信したらメールを送信して処理を終了する
    if ($title == "complete" && $body == "complete") {
        try {
            // デバッグ設定
            // $mail->SMTPDebug = 2; // デバッグ出力を有効化（レベルを指定）
            // $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str<br>";};

            // SMTPサーバの設定
            $mail->isSMTP(); // SMTPの使用宣言
            $mail->Host = 'sv13004.xserver.jp'; // SMTPサーバーを指定
            $mail->SMTPAuth = true; // SMTP authenticationを有効化
            $mail->Username = 'mailtest@ikefukuro40.tech'; // SMTPサーバーのユーザ名
            $mail->Password = 'Manabu2010'; // SMTPサーバーのパスワード
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 暗号化を有効（tls or ssl）無効の場合はfalse
            $mail->Port = 465; // TCPポートを指定（tlsの場合は465や587）

            //$tgt=['apimaster2018@gmail.com','mtaketani37@gmail.com'];

            // 送受信先設定（第二引数は省略可）
            $mail->setFrom('mailtest@ikefukuro40.tech', '差出人名'); // 送信者
            $mail->addAddress('apimaster2018@gmail.com', '受信者名'); // 宛先
            // $mail->addAddress('mtaketani37@gmail.com', '受信者名'); // 宛先
            //$mail->addReplyTo('mailtest@ikefukuro40.tech', 'お問い合わせ'); // 返信先
            $mail->addCC('finfizz2000@yahoo.co.jp', '受信者名'); // CC宛先
            $mail->Sender = 'mailtest@ikefukuro40.tech'; // Return-path

            // 送信内容設定
            $mail->Subject = '画像投稿有り';
            $mail->Body = '画像投稿がされました。';

            // 送信
            $mail->send();

            header('Location: backend.php');
            return;
        } catch (Exception $e) {
            // エラーの場合
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        $article = new Article();
        $article->setTitle($title);
        $article->setBody($body);
        if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $article->setFile($_FILES['image']);
        }

        if (!empty($_POST['category'])) {
            $category = $queryCategory->find($_POST['category']);
            if ($category) {
                $article->setCategoryId($category->getId());
            }
        }

        $article->save();

        // ===== ここまで変更 =====
        header('Location: backend.php');
    }

} else if (!empty($_POST) || !empty($_GET)) {
    // POSTメソッドで送信されたが、titleかbodyが足りないとき
    // 存在するほうは変数へ、ない場合空文字にしてフォームのvalueに設定する
    if (!empty($_POST['title'])) {
        $title = $_POST['title'];
    } else {
        $title_alert = "タイトルを入力してください。";
    }

    if (!empty($_POST['body'])) {
        $body = $_POST['body'];
    } else {
        $body_alert = "本文を入力してください。";
    }
} else {
    // var_dump("debug4");
    // die();
}
?>
<!-- ここまで変更 -->
<!doctype html>
<html lang="ja">
  <head>

<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Backend</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        padding-top: 5rem;
      }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .bg-red {
        background-color: #ff6644 !important;
      }
    </style>

    <!-- Custom styles for this template -->
    <link href="./css/blog.css" rel="stylesheet">
  </head>
  <body>

<!-- ここから変更 -->
<?php include 'lib/nav.php';?>
<!-- ここまで変更 -->

<main class="container">
  <div class="row">
    <div class="col-md-12">


      <h1>記事の投稿</h1>

      <form action="post.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">タイトル</label>
          <?php echo !empty($title_alert) ? '<div class="alert alert-danger">' . $title_alert . '</div>' : '' ?>
          <input type="text" name="title" value="<?php echo $title; ?>" class="form-control">
        </div>

        <div class="mb-3">
          <?php echo !empty($body_alert) ? '<div class="alert alert-danger">' . $body_alert . '</div>' : '' ?>
          <textarea name="body" class="form-control" rows="10"><?php echo $body; ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">カテゴリー</label>
          <select name="category" class="form-control">
            <option value="0">なし</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c->getId() ?>"><?php echo $c->getName() ?></option>
            <?php endforeach?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">画像</label>
          <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
          <button type="submit" class="btn btn-primary">投稿する</button>
        </div>
      </form>



    </div>

  </div><!-- /.row -->

</main><!-- /.container -->

  </body>
</html>
