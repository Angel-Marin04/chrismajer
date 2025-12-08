<?php
// Configurar headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Recibir datos del formulario
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

// Validar datos
$firstName = isset($data['firstName']) ? trim($data['firstName']) : '';
$lastName = isset($data['lastName']) ? trim($data['lastName']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$company = isset($data['company']) ? trim($data['company']) : '';
$eventDate = isset($data['eventDate']) ? trim($data['eventDate']) : '';
$eventSize = isset($data['eventSize']) ? trim($data['eventSize']) : '';
$programs = isset($data['programs']) ? trim($data['programs']) : '';
$message = isset($data['message']) ? trim($data['message']) : '';
$source = isset($data['source']) ? trim($data['source']) : 'Unknown';

// Validaciones básicas
if (!$firstName || !$lastName || !$email || !$phone || !$message) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

// Preparar datos para email
$to = 'tellmeangelmarin@gmail.com';
$subject = "New Booking Request from Chris Majer Website - $firstName $lastName";

$body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        h2 { color: #d41f26; border-bottom: 2px solid #d41f26; padding-bottom: 10px; }
        .field { margin: 15px 0; }
        .label { font-weight: bold; color: #1a1a2e; }
        .value { color: #555; margin-top: 5px; }
        .divider { border-top: 1px solid #ddd; margin: 20px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>New Booking Request</h2>
        
        <div class='field'>
            <div class='label'>Full Name:</div>
            <div class='value'>$firstName $lastName</div>
        </div>
        
        <div class='field'>
            <div class='label'>Email:</div>
            <div class='value'>$email</div>
        </div>
        
        <div class='field'>
            <div class='label'>Phone:</div>
            <div class='value'>$phone</div>
        </div>
        
        <div class='field'>
            <div class='label'>Company:</div>
            <div class='value'>$company</div>
        </div>
        
        <div class='field'>
            <div class='label'>Event Date:</div>
            <div class='value'>$eventDate</div>
        </div>
        
        <div class='field'>
            <div class='label'>Expected Audience Size:</div>
            <div class='value'>$eventSize</div>
        </div>
        
        <div class='field'>
            <div class='label'>Program/Service:</div>
            <div class='value'>$programs</div>
        </div>
        
        <div class='divider'></div>
        
        <div class='field'>
            <div class='label'>Message:</div>
            <div class='value'>".nl2br(htmlspecialchars($message))."</div>
        </div>
        
        <div class='field'>
            <div class='label'>Submitted from:</div>
            <div class='value'>$source</div>
        </div>
        
        <div class='field'>
            <div class='label'>Submission Time:</div>
            <div class='value'>".date('Y-m-d H:i:s')."</div>
        </div>
    </div>
</body>
</html>
";

// Headers para email HTML
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: <noreply@chrismajer.com>\r\n";
$headers .= "Reply-To: <$email>\r\n";
$headers .= "X-Mailer: PHP\r\n";

// Intentar enviar el email
$mailSent = false;
try {
    $mailSent = mail($to, $subject, $body, $headers);
} catch (Exception $e) {
    $mailSent = false;
}

// Guardar en log como respaldo
$logFile = __DIR__ . '/form_submissions.log';
$logEntry = date('Y-m-d H:i:s') . " | Name: $firstName $lastName | Email: $email | Phone: $phone | Message: " . substr($message, 0, 50) . "...\n";
@file_put_contents($logFile, $logEntry, FILE_APPEND);

// Si el email se envió, enviar confirmación al cliente
if ($mailSent) {
    $confirmationBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        h2 { color: #d41f26; }
        p { color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Thank you for your booking request!</h2>
        <p>Dear $firstName,</p>
        <p>We have received your booking request for Chris Majer. Our team will review your request and get back to you within 24 hours.</p>
        <p style='margin-top: 30px;'>Best regards,<br><strong>Chris Majer Team</strong></p>
    </div>
</body>
</html>
";
    
    mail($email, "We received your booking request - Chris Majer", $confirmationBody, $headers);
}

// Respuesta de éxito (independientemente de si se envió el email)
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Your request has been received! We will contact you within 24 hours.',
    'mailSent' => $mailSent
]);
?>
