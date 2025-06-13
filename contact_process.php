<?php
/**
 * Ted Rubin Project Management
 * Contact Form Processing with Geo Restrictions
 */

// Include geo restrictions - allow contact form processing from blocked countries
define('SKIP_GEO_CHECK', true);

// Start session for CSRF protection
session_start();

// Configuration
$config = [
    'email_to' => 'ted@example.com', // Replace with your actual email
    'email_from_name' => 'Ted Rubin Project Management',
    'email_subject_prefix' => '[Project Inquiry] ',
    'redirect_success' => 'index.php?status=success',
    'redirect_error' => 'index.php?status=error',
    'allowed_methods' => ['POST'],
    'rate_limit' => 3, // Max submissions per hour per IP
    'enable_spam_protection' => true
];

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Only allow POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], $config['allowed_methods'])) {
    http_response_code(405);
    die('Method not allowed');
}

// Rate limiting
if (!checkRateLimit($_SERVER['REMOTE_ADDR'], $config['rate_limit'])) {
    http_response_code(429);
    redirect($config['redirect_error'] . '&reason=rate_limit');
}

try {
    // Include database configuration
    require_once 'config/database.php';
    
    // Validate and sanitize input
    $formData = validateAndSanitizeInput($_POST);
    
    // Spam protection
    if ($config['enable_spam_protection'] && isSpam($formData)) {
        error_log("Spam detected from IP: " . $_SERVER['REMOTE_ADDR']);
        redirect($config['redirect_error'] . '&reason=spam');
    }
    
    // Save to database
    $contactManager = new ContactManager();
    $submissionId = $contactManager->saveSubmission($formData);
    
    // Send email
    if (sendEmail($formData, $config)) {
        // Log successful submission
        logSubmission($formData, true);
        redirect($config['redirect_success']);
    } else {
        throw new Exception('Failed to send email');
    }
    
} catch (Exception $e) {
    // Log error
    error_log("Contact form error: " . $e->getMessage());
    logSubmission($_POST, false, $e->getMessage());
    redirect($config['redirect_error'] . '&reason=server_error');
}

/**
 * Validate and sanitize form input
 */
function validateAndSanitizeInput($input) {
    $errors = [];
    $data = [];
    
    // Required fields
    $required = ['name', 'email', 'subject', 'message'];
    
    foreach ($required as $field) {
        if (empty($input[$field])) {
            $errors[] = "Field '{$field}' is required";
        }
    }
    
    if (!empty($errors)) {
        throw new Exception('Validation failed: ' . implode(', ', $errors));
    }
    
    // Sanitize and validate
    $data['name'] = sanitizeString($input['name']);
    $data['email'] = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
    $data['subject'] = sanitizeString($input['subject']);
    $data['message'] = sanitizeString($input['message']);
    
    // Additional validation
    if (!$data['email']) {
        throw new Exception('Invalid email address');
    }
    
    if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
        throw new Exception('Name must be between 2 and 100 characters');
    }
    
    if (strlen($data['message']) < 10 || strlen($data['message']) > 2000) {
        throw new Exception('Message must be between 10 and 2000 characters');
    }
    
    return $data;
}

/**
 * Sanitize string input
 */
function sanitizeString($string) {
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    return $string;
}

/**
 * Basic spam detection
 */
function isSpam($data) {
    $spamKeywords = [
        'viagra', 'cialis', 'pharmacy', 'casino', 'poker', 'loan', 'bitcoin',
        'cryptocurrency', 'investment', 'profit', 'money back', 'guarantee',
        'click here', 'buy now', 'limited time'
    ];
    
    $text = strtolower($data['name'] . ' ' . $data['message']);
    
    foreach ($spamKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return true;
        }
    }
    
    // Check for excessive links
    if (preg_match_all('/https?:\/\//', $data['message']) > 2) {
        return true;
    }
    
    // Check for suspicious patterns
    if (preg_match('/[^\x20-\x7E]/', $data['message'])) {
        // Contains non-printable ASCII characters
        return true;
    }
    
    return false;
}

/**
 * Send email
 */
function sendEmail($data, $config) {
    $to = $config['email_to'];
    $subject = $config['email_subject_prefix'] . ucfirst($data['subject']);
    
    // Create email body
    $body = createEmailBody($data);
    
    // Email headers
    $headers = [
        'From: ' . $config['email_from_name'] . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>',
        'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>',
        'Content-Type: text/html; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion(),
        'X-Priority: 3',
        'X-MSMail-Priority: Normal'
    ];
    
    $headerString = implode("\r\n", $headers);
    
    // Send email
    $success = mail($to, $subject, $body, $headerString);
    
    if ($success) {
        // Send auto-reply to customer
        sendAutoReply($data, $config);
    }
    
    return $success;
}

/**
 * Create email body
 */
function createEmailBody($data) {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>New Project Inquiry</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2f55d4; color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 30px; }
            .field { margin-bottom: 20px; }
            .label { font-weight: bold; color: #2f55d4; }
            .value { margin-top: 5px; padding: 10px; background: white; border-left: 3px solid #2f55d4; }
            .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Project Inquiry</h1>
                <p>Someone wants to transform their chaos into art!</p>
            </div>
            
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>{$data['name']}</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>{$data['email']}</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Project Type:</div>
                    <div class='value'>" . formatProjectType($data['subject']) . "</div>
                </div>
                
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>" . nl2br($data['message']) . "</div>
                </div>
            </div>
            
            <div class='footer'>
                <p>Submitted on: {$timestamp}</p>
                <p>IP Address: {$ip}</p>
                <p>User Agent: " . htmlspecialchars($userAgent) . "</p>
            </div>
        </div>
    </body>
    </html>";
    
    return $body;
}

/**
 * Format project type for display
 */
function formatProjectType($type) {
    $types = [
        'new-project' => 'New Project Launch',
        'process-improvement' => 'Process Improvement',
        'team-coordination' => 'Team Coordination',
        'consultation' => 'General Consultation'
    ];
    
    return $types[$type] ?? ucfirst(str_replace('-', ' ', $type));
}

/**
 * Send auto-reply to customer
 */
function sendAutoReply($data, $config) {
    $subject = "Thank you for your project inquiry, {$data['name']}!";
    
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2f55d4; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; }
            .signature { margin-top: 30px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Thank You!</h1>
                <p>Your message has been received</p>
            </div>
            
            <div class='content'>
                <p>Hi {$data['name']},</p>
                
                <p>Thank you for reaching out about your project! I received your inquiry about <strong>" . formatProjectType($data['subject']) . "</strong> and I'm excited to learn more about how I can help transform your chaos into a work of art.</p>
                
                <p>I typically respond to all inquiries within 24 hours. In the meantime, feel free to browse my services and success stories on my website.</p>
                
                <p>I look forward to discussing your project and exploring how we can choreograph your success together!</p>
                
                <div class='signature'>
                    <p>Best regards,<br>
                    <strong>Ted Rubin</strong><br>
                    Project Management Excellence<br>
                    <em>\"I turn your chaos into a work of art\"</em></p>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = [
        'From: Ted Rubin <' . $config['email_to'] . '>',
        'Content-Type: text/html; charset=UTF-8',
        'X-Auto-Response-Suppress: OOF, DR, RN, NRN, AutoReply'
    ];
    
    mail($data['email'], $subject, $body, implode("\r\n", $headers));
}

/**
 * Rate limiting
 */
function checkRateLimit($ip, $maxRequests) {
    $cacheFile = sys_get_temp_dir() . '/contact_rate_' . md5($ip);
    $now = time();
    $window = 3600; // 1 hour
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        $data = array_filter($data, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
    } else {
        $data = [];
    }
    
    if (count($data) >= $maxRequests) {
        return false;
    }
    
    $data[] = $now;
    file_put_contents($cacheFile, json_encode($data));
    
    return true;
}

/**
 * Log submission
 */
function logSubmission($data, $success, $error = null) {
    $logFile = 'logs/contact_submissions.log';
    
    // Create logs directory if it doesn't exist
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'name' => $data['name'] ?? 'Unknown',
        'email' => $data['email'] ?? 'Unknown',
        'subject' => $data['subject'] ?? 'Unknown',
        'success' => $success,
        'error' => $error,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Redirect helper
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}
?>