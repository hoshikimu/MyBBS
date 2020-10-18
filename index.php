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

    $user = ($user === '') ? 'ななしさん' : $user;

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
        投稿者: <input type="text" name="user"><br>
        本　文: <textarea type="text" name="message"></textarea><br>
        <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
        <input type="submit" value="投稿" class="btn">
      </form>
      <h2>投稿一覧（<?php echo count($posts); ?>件）</h2>
      <ul>
        <?php if (count($posts)) : ?>
          <?php foreach ($posts as $post) : ?>
          <?php list($message, $user, $postedAt) = explode("\t", $post); ?>
            <li><?php echo h($user); ?> / <?php echo h($postedAt); ?></li>
            <li><?php echo h($message); ?></li>
          <?php endforeach; ?>
        <?php else : ?>
          <li>まだ投稿はありません。</li>
        <?php endif; ?>
      </ul>
  </main>
  </div>
</body>
</html>