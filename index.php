<?php

$dataFile = 'bbs.dat';

session_start();

function setToken() {
  $token = sha1(uniqid(mt_rand(), true));
  $_SESSION['token'] = $token;
}

function checkToken() {
  if (empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])) {
    echo "不正な投稿が行われました！";
    exit;
  }
}

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
  isset($_POST['message']) &&
  isset($_POST['user'])) {

  checkToken();

  $message = trim($_POST['message']);
  $user = trim($_POST['user']);

  if ($message !== '') {

    $user = ($user === '') ? '名無しさん' : $user;

    $message = str_replace("\t", ' ', $message);
    $user = str_replace("\t", ' ', $user);

    $postedAt = date('Y-m-d H:i:s');

    $newData = $message . "\t" . $user . "\t" . $postedAt . "\n";

    $fp = fopen($dataFile, 'a');
    fwrite($fp, $newData);
    fclose($fp);
  }

  header('Location: http://localhost/MyBBS/');
  exit;

} else {
  setToken();
}

$posts = file($dataFile, FILE_IGNORE_NEW_LINES);
$posts = array_reverse($posts);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>MyBBS</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <header>
    <nav>
      <p class="h-logo">MyBBS</p>
    </nav>
  </header>
  <main>
    <div class="container">
      <form action="" method="post" class="form-container">
        <span class="form-title">投稿者</span><input type="text" name="user"><br>
        <span class="form-title">本　文</span><textarea type="text" name="message"></textarea><br>
        <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
        <input type="submit" value="投稿" class="btn">
      </form>
      <div class="posts-container">
        <h3>投稿一覧（全<?php echo count($posts); ?>件）</h3>
        <table>
          <?php if (count($posts)) : ?>
            <tr><th class="post-user">投稿者</th><th class="post-at">投稿日時</th><th class="post-message">本文</th></tr>
            <?php foreach ($posts as $post) : ?>
            <?php list($message, $user, $postedAt) = explode("\t", $post); ?>
              <tr><td><?php echo h($user); ?></td><td><?php echo h($postedAt); ?></td><td><?php echo h($message); ?></td></tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr><td>まだ投稿はありません。</td></tr>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </main>
  </div>
</body>
</html>