<?php
session_start();

// --- RATE LIMITING ---
$now = time();
$limit_seconds = 60; // Cooldown period
$last_submit = $_SESSION['last_submit_time'] ?? 0;

if (($now - $last_submit) < $limit_seconds) {
    http_response_code(429); // Too Many Requests
    // Redirect back with a specific error code
    header("Location: {$back}?error=ratelimit");
    exit;
}
// --------------------

require_once __DIR__ . '/includes/config.php';
require_once '/var/www/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function csrf_ok(?string $t): bool {
    if (empty($_SESSION['public_csrf'])) return false;
    if ($t === null) return false;
    return hash_equals($_SESSION['public_csrf'], $t);
}

function clean_line(string $s): string {
    // Prevent header injection (strip CR/LF)
    return preg_replace("/[\r\n]+/", " ", $s);
}

function clean_email(string $s): string {
    $s = preg_replace("/[\r\n]+/", "", $s);
    return trim($s);
}

function sanitize_text(string $s, int $maxLen): string {
    $s = trim($s);
    if (strlen($s) > $maxLen) $s = substr($s, 0, $maxLen);
    return $s;
}

// Determine which page to redirect back to based on form_source
$source = $_POST['form_source'] ?? 'contact';
$back   = ($source === 'join') ? 'join.php' : 'contact.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $back);
    exit;
}

$token = $_POST['csrf_token'] ?? null;
if (!csrf_ok($token)) {
    http_response_code(400);
    echo "Invalid form session. Please go back and try again.";
    exit;
}

// Honeypot check — bots fill hidden fields, real users don't
if (!empty($_POST['website']) || !empty($_POST['phone_confirm'])) {
    // Silently reject but look like success to the bot
    header("Location: {$back}?success=1");
    exit;
}

$subject_in = sanitize_text((string)($_POST['subject'] ?? 'General Inquiry'), 100);
$message    = sanitize_text((string)($_POST['message'] ?? ''), 2000);
$first_name = sanitize_text((string)($_POST['first_name'] ?? ''), 40);
$last_name  = sanitize_text((string)($_POST['last_name'] ?? ''), 40);
$full_name  = trim($first_name . ' ' . $last_name);
$email      = sanitize_text((string)($_POST['email'] ?? ''), 120);

$safe_subject = clean_line($subject_in);
$safe_name  = clean_line($full_name);
$safe_email = clean_email($email);

$body = "Name: {$safe_name}\n" .
        "Email: {$safe_email}\n" .
        "Subject: {$safe_subject}\n\n" .
        "Message:\n{$message}\n";

$mail = new PHPMailer(true);

try {
    // 1. SMTP Server Settings
    $mail->isSMTP();
    $mail->Host       = 'mail.smtp2go.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER'); // From docker-compose
    $mail->Password   = getenv('SMTP_PASS'); // From docker-compose
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 2. Sender & Recipient Settings
    // NOTE: 'From' must be an email verified in your SMTP2GO dashboard
    $mail->setFrom('no-reply@ritformula.com', 'RIT Racing Website');
    $mail->addAddress(get_contact_email()); 
    $mail->addReplyTo($safe_email, $safe_name);

    // 3. Content
    $mail->isHTML(false); // Keep it plain text for security/simplicity
    $mail->Subject = "RIT Racing Contact: {$safe_subject}";
    $mail->Body    = $body;
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->send();
    
    // Rotate CSRF and redirect on success
    $_SESSION['public_csrf'] = bin2hex(random_bytes(32));
    $_SESSION['last_submit_time'] = time();
    header("Location: {$back}?success=1");
    exit;

} catch (Exception $e) {
    // Log the error for your eyes, but don't show the user the details
    error_log("Mailer Error: {$mail->ErrorInfo}");
    header("Location: {$back}?error=send");
    exit;
}
exit;
