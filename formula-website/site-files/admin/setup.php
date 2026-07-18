<?php
require_once __DIR__ . '/includes/auth.php';

if (users_file_exists_and_has_users()) {
    header('Location: login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;
    if (!csrf_verify($token)) {
        $errors[] = 'Invalid session token. Refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm  = (string)($_POST['confirm_password'] ?? '');

        if (!validate_username($username)) $errors[] = 'Username must be 3–32 chars (letters/numbers/._- only).';
        if (!validate_password($password))  $errors[] = 'Password must be at least 10 characters.';
        if ($password !== $confirm)         $errors[] = 'Passwords do not match.';

        if (!$errors) {
            $users = [[
                'username'      => $username,
                'password_hash' => password_hash_safe($password),
                'role'          => 'admin',
                'created_at'    => date('c'),
            ]];
            if (!users_save($users)) {
                $errors[] = 'Could not write data/users.json. Check permissions on the data/ folder.';
            } else {
                $_SESSION['user'] = ['username' => $username, 'role' => 'admin'];
                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Setup — RIT Racing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="/assets/images/site/favicon.png">
  <link rel="stylesheet" href="/assets/css/admin.css?v=<?= @filemtime(dirname(__DIR__) . '/assets/css/admin.css') ?: time() ?>">
</head>
<body class="auth-page">
  <div class="auth-card">
    <div class="auth-brand">RIT <span>Racing</span></div>
    <p class="auth-sub">Admin Panel</p>
    <h1>First-Time <span>Setup</span></h1>
    <p class="auth-intro">Create the first admin account. This page disables itself once an account exists.</p>

    <?php if ($errors): ?>
      <div class="auth-errors">
        <ul><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <label>Username</label>
      <input name="username" required placeholder="e.g. admin">
      <label>Password (min 10 characters)</label>
      <div class="pw-wrap">
        <input type="password" name="password" id="pw1" required placeholder="••••••••••">
        <button type="button" class="pw-toggle" onclick="var p=document.getElementById('pw1');var t=p.type==='password'?'text':'password';p.type=t;this.innerHTML=t==='password'?'<i class=\'fa fa-eye\'></i>':'<i class=\'fa fa-eye-slash\'></i>';" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
      </div>
      <label>Confirm Password</label>
      <div class="pw-wrap">
        <input type="password" name="confirm_password" id="pw2" required placeholder="••••••••••">
        <button type="button" class="pw-toggle" onclick="var p=document.getElementById('pw2');var t=p.type==='password'?'text':'password';p.type=t;this.innerHTML=t==='password'?'<i class=\'fa fa-eye\'></i>':'<i class=\'fa fa-eye-slash\'></i>';" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
      </div>
      <button type="submit">Create Admin Account</button>
    </form>
    <p class="auth-hint">You'll be logged in automatically after setup.</p>
  </div>
</body>
</html>
