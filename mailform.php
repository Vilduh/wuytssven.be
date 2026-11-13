<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /contact");
    exit;
}

// 1. Block spam bots that fill out the hidden 'website' field
if (!empty($_POST["website"])) {
    header("Location: /contact?status=sent");
    exit;
}

// 2. Sanitize and validate inputs
$name    = strip_tags(trim($_POST["name"] ?? ""));
$email   = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$message = strip_tags(trim($_POST["message"] ?? ""));

if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: /contact?status=error");
    exit;
}

// 3. Configure email details (Combell requires the 'From' address to match your domain)
$to      = "sven@wuytssven.be";
$subject = "Portfolio Contact from " . $name;

$body  = "Name: " . $name . "\n";
$body .= "Email: " . $email . "\n\n";
$body .= "Message:\n" . $message . "\n";

$headers  = "From: Portfolio Bot <noreply@wuytssven.be>\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// 4. Send email and redirect back with terminal status
if (mail($to, $subject, $body, $headers)) {
    header("Location: /contact?status=sent");
} else {
    header("Location: /contact?status=error");
}
exit;