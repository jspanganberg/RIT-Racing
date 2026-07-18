<?php
require_once __DIR__ . '/includes/auth.php';
auth_require(); if (!auth_is_admin()) { header('Location: index.php'); exit; } // admin-only page

// PASSWORD STORAGE NOTE (v39):
// New and edited users are always saved with key "password_hash" (bcrypt).
// The legacy "password" key is never written. On edit without a new password,
// the existing hash is carried forward using:
//   $users[$existing_idx]['password_hash'] ?? $users[$existing_idx]['password'] ?? ''
// This allows one-pass migration of any pre-v39 accounts.
// Accounts that only have "password" will show "(reset required)" and
// must have a new password set before they can log in.

$adminTitle     = 'User';
$adminTitleSpan = 'Management';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $users = users_load();

    if (isset($_POST['delete_username'])) {
        $del = $_POST['delete_username'];
        if ($del === auth_user()['username']) { $msg = ['type'=>'danger','text'=>'You cannot delete your own account.']; }
        else {
            $users = array_values(array_filter($users, fn($u) => $u['username'] !== $del));
            users_save($users);
            $msg = ['type'=>'success','text'=>'User deleted.'];
        }
    }
    elseif (isset($_POST['save_user'])) {
        $username = strtolower(trim($_POST['username'] ?? ''));
        $name     = trim($_POST['name']     ?? '');
        $role     = trim($_POST['role']     ?? 'editor');
        $password = $_POST['password']      ?? '';
        $edit_un  = $_POST['edit_username'] ?? '';

        if (!preg_match('/^[a-z0-9_]{3,20}$/', $username)) {
            $msg = ['type'=>'danger','text'=>'Username must be 3-20 lowercase letters/numbers/underscores.'];
        } elseif (empty($name)) {
            $msg = ['type'=>'danger','text'=>'Display name is required.'];
        } else {
            $existing_idx = null;
            foreach ($users as $i => $u) { if ($u['username'] === $edit_un) { $existing_idx = $i; break; } }

            if ($existing_idx === null && empty($password)) {
                $msg = ['type'=>'danger','text'=>'Password is required for new users.'];
            } else {
                $entry = [
                    'username' => $username,
                    'name'     => $name,
                    'role'     => $role,
                    'password_hash' => !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : ($users[$existing_idx]['password_hash'] ?? $users[$existing_idx]['password'] ?? ''),
                ];
                if ($existing_idx !== null) $users[$existing_idx] = $entry;
                else $users[] = $entry;
                users_save($users);
                $msg = ['type'=>'success','text'=> $existing_idx !== null ? 'User updated.' : 'User created.'];
            }
        }
    }
}

$edit_user = null;
if (isset($_GET['edit'])) {
    foreach (users_load() as $u) { if ($u['username'] === $_GET['edit']) { $edit_user = $u; break; } }
}

$users = users_load();
$topbarActions = '<a href="'.'users.php?new=1" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</a>';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['new']) || $edit_user): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $edit_user ? 'Edit User' : 'New User' ?></h2>
        <a href="users.php" class="btn btn-secondary btn-sm">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST">
                    <?= csrf_field() ?>
            <input type="hidden" name="save_user" value="1">
            <input type="hidden" name="edit_username" value="<?= htmlspecialchars($edit_user['username']??'') ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Username <span class="req">*</span></label>
                    <input type="text" name="username" required pattern="[a-z0-9_]{3,20}"
                           value="<?= htmlspecialchars($edit_user['username']??'') ?>"
                           <?= $edit_user ? 'readonly style="opacity:.5"' : '' ?>>
                    <span class="form-help">3-20 lowercase letters, numbers, underscores</span>
                </div>
                <div class="form-group">
                    <label>Display Name <span class="req">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit_user['name']??'') ?>" placeholder="e.g. Team Captain">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="editor" <?= ($edit_user['role']??'editor')==='editor'?'selected':'' ?>>Editor — can manage content</option>
                        <option value="admin"  <?= ($edit_user['role']??'')==='admin'?'selected':'' ?>>Admin — full access incl. users</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?= $edit_user ? 'New Password' : 'Password' ?> <?= !$edit_user ? '<span class="req">*</span>' : '' ?></label>
                    <div class="pw-wrap">
                        <input type="password" name="password" id="u-pw" <?= !$edit_user ? 'required' : '' ?>>
                        <button type="button" class="pw-toggle" onclick="var p=document.getElementById('u-pw');var t=p.type==='password'?'text':'password';p.type=t;this.innerHTML=t==='password'?'<i class=\'fa fa-eye\'></i>':'<i class=\'fa fa-eye-slash\'></i>';" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
                    </div>
                    <?php if ($edit_user): ?><span class="form-help">Leave blank to keep current password</span><?php endif; ?>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= $edit_user ? 'Save Changes' : 'Create User' ?></button>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header"><h2>All Users (<?= count($users) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th>Username</th><th>Display Name</th><th>Role</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td style="font-family:monospace;color:var(--orange);"><?= htmlspecialchars($u['username'] ?? '') ?></td>
                <td><?= htmlspecialchars($u['name'] ?? $u['username'] ?? '') ?></td>
                <td><span class="badge <?= ($u['role']??'')==='admin'?'badge-orange':'badge-gray' ?>"><?= htmlspecialchars($u['role'] ?? 'editor') ?></span></td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?= urlencode($u['username']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <?php if ($u['username'] !== auth_user()['username']): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete user <?= htmlspecialchars(addslashes($u['username'])) ?>?')">
                    <?= csrf_field() ?>
                            <input type="hidden" name="delete_username" value="<?= htmlspecialchars($u['username']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2>Password Hash Generator</h2></div>
    <div class="admin-card-body">
        <p style="color:var(--gray);font-size:.875rem;margin-bottom:1rem;">Use this to generate a bcrypt hash if editing users.json directly.</p>
        <div style="display:flex;gap:.75rem;">
            <input type="password" id="hash-input" placeholder="Type a password..." style="flex:1;background:var(--dark3);border:1px solid var(--border);color:var(--white);padding:.6rem .85rem;outline:none;font-family:'Outfit',sans-serif;">
            <button class="btn btn-secondary" onclick="generateHash()">Generate Hash</button>
        </div>
        <div id="hash-output" style="display:none;margin-top:.75rem;background:var(--dark3);border:1px solid var(--border);padding:.75rem;font-family:monospace;font-size:.8rem;color:var(--orange);word-break:break-all;"></div>
    </div>
</div>

<script>
async function generateHash() {
    const pw = document.getElementById('hash-input').value;
    if (!pw) return;
    const res = await fetch('hash.php?pw=' + encodeURIComponent(pw));
    const txt = await res.text();
    const out = document.getElementById('hash-output');
    out.style.display = 'block';
    out.textContent = txt;
}
</script>

<?php include 'includes/admin-footer.php'; ?>
