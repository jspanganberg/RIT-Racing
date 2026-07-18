<?php
require_once __DIR__ . '/includes/auth.php';

// Redirect to setup if no users exist
if (!users_file_exists_and_has_users()) {
    header('Location: setup.php');
    exit;
}

// Already logged in?
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    if (login_attempt($username, $password)) {
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Log In — RIT Racing Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= @filemtime(dirname(__DIR__) . '/assets/css/admin.css') ?: time() ?>">
</head>
<body class="auth-page">
    <div class="auth-card auth-card-sm">
        <div class="auth-brand">RIT <span>Racing</span></div>
        <p class="auth-sub">Admin Panel</p>
        <h1>Sign <span>In</span></h1>

        <?php if ($error): ?>
        <div class="auth-error"><i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="on">
            <label>Username</label>
            <input name="username" required autofocus placeholder="admin">
            <label>Password</label>
            <div class="pw-wrap">
                <input type="password" name="password" id="pw" required placeholder="••••••••••">
                <button type="button" class="pw-toggle" onclick="var p=document.getElementById('pw');var t=p.type==='password'?'text':'password';p.type=t;this.innerHTML=t==='password'?'<i class=\'fa fa-eye\'></i>':'<i class=\'fa fa-eye-slash\'></i>';" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
            </div>
            <button type="submit"><i class="fa fa-right-to-bracket"></i> Sign In</button>
        </form>
    </div>
</body>
</html>
