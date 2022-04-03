<?php
include 'lib/connect.php';
include 'lib/queryArticle.php';
include 'lib/article.php';

$limit = 20;
$page = 1;
$month = null;
$title = "";

//keyword検索
if (!empty($_POST['keyword'])) {
    $queryArticle = new QueryArticle();
    $pager = $queryArticle->searchArticle($_POST['keyword']);
    $monthly = $queryArticle->getMonthlyArchiveMenu();
} else {
// ページ数の決定
    if (!empty($_GET['page']) && intval($_GET['page']) > 0) {
        $page = intval($_GET['page']);
    }
// 月指定 アーカイブ欄で指定した時にmonthが渡される模様
    if (!empty($_GET['month'])) {
        $month = $_GET['month'];
        $title = $month . 'の投稿一覧';
    }
    $queryArticle = new QueryArticle();
    $pager = $queryArticle->getPager($page, $limit, $month);
    $monthly = $queryArticle->getMonthlyArchiveMenu();
}

?>
<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        padding-top: 6rem;
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
    </style>

    <!-- Custom styles for this template -->
    <link href="./css/blog.css" rel="stylesheet">
  </head>
  <body>

<!-- <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="/blog/">My Blog</a>
  </div>
</nav> -->

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
      <a class="navbar-brand" href="/blog/">My Blog</a>
      <form class="d-flex" action="index.php" method="post">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="keyword">
        <button class="btn btn-outline-success" type="submit">検索</button>
      </form>
  </div>
</nav>

<main class="container">
  <div class="row">
    <div class="col-md-8">
        <!-- ↓$titleの表示処理を追加 ここから↓ -->
        <?php if (!empty($title)): ?>
            <h2><?php echo $title ?></h2>
        <?php endif?>
        <!-- ↑$titleの表示処理を追加 ここまで↑ -->
        <?php if ($pager['articles']): ?>
            <?php foreach ($pager['articles'] as $article): ?>
                    <article class="blog-post">
                        <h2 class="blog-post-title">
                        <a href="view.php?id=<?php echo $article->getId() ?>">
                            <?php echo $article->getTitle() ?>
                        </a>
                        </h2>
                        <p class="blog-post-meta"><?php echo $article->getCreatedAt() ?></p>
                        <?php echo nl2br($article->getBody()) ?>
                    </article>
            <?php endforeach?>
        <?php else: ?>
            <div class="alert alert-success">
                <p>記事はありません。</p>
            </div>
        <?php endif?>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php for ($i = 1; $i <= ceil($pager['total'] / $limit); $i++): ?>
                        <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $i ?><?php echo $month ? '&month=' . $month : '' ?>"><?php echo $i ?></a></li>
                <?php endfor?>
            </ul>
        </nav>
    </div>

    <div class="col-md-4">
      <div class="p-4 mb-3 bg-light rounded">
        <h4>ブログについて</h4>
        <p class="mb-0">毎日のなんてことない日常を書いていきます。</p>
      </div>

      <div class="p-4">
        <h4>アーカイブ</h4>
        <ol class="list-unstyled mb-0">
        <?php foreach ($monthly as $m): ?>
          <li><a href="index.php?month=<?php echo $m['month'] ?>"><?php echo $m['month'] ?> (<?php echo $m['count'] ?>)</a></li>
        <?php endforeach?>
        </ol>
      </div>

    </div>

  </div><!-- /.row -->

</main><!-- /.container -->

  </body>
</html>
