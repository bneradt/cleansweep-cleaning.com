<?php
/**
 * Clean Sweep Cleaning Services - Contact Form Handler
 * Sends contact form submissions to the business email
 */

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Configuration
$recipient_email = 'pdixon701@gmail.com';
$email_subject_prefix = '[Clean Sweep Website]';

// Get and sanitize form data
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Validate required fields
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
}

// Return errors if validation failed
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Service type labels
$service_labels = [
    'residential' => 'Residential Cleaning',
    'commercial' => 'Commercial Cleaning',
    'deep-cleaning' => 'Deep Cleaning',
    'window' => 'Window Cleaning',
    'move-in-out' => 'Move-In/Move-Out Cleaning',
    'turnover' => 'Turnover Cleaning',
    'maid' => 'Maid Services',
    'other' => 'Other'
];

$service_text = isset($service_labels[$service]) ? $service_labels[$service] : 'Not specified';

// Build email content
$email_subject = $email_subject_prefix . ' New Contact Form Submission';

$email_body = "New contact form submission from Clean Sweep Cleaning Services website:\n\n";
$email_body .= "----------------------------------------\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Phone: $phone\n";
$email_body .= "Service Requested: $service_text\n";
$email_body .= "----------------------------------------\n\n";
$email_body .= "Message:\n";
$email_body .= !empty($message) ? $message : "(No message provided)";
$email_body .= "\n\n----------------------------------------\n";
$email_body .= "Submitted: " . date('Y-m-d H:i:s') . "\n";

// Email headers
$headers = [
    'From: Clean Sweep Website <noreply@cleansweep-cleaning.brianneradt.com>',
    'Reply-To: ' . $name . ' <' . $email . '>',
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

// Send email
$mail_sent = mail($recipient_email, $email_subject, $email_body, implode("\r\n", $headers));

if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'There was an error sending your message. Please call us directly at (217) 714-7408.'
    ]);
}
