<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


// ===================================================
// 0️⃣ REDIRECT FUNCTION (for SweetAlert popup)
// ===================================================
function goHome($status, $msg) {
    $msg = urlencode($msg);
    header("Location: https://rustomjee-mumbai.in/?status=$status&msg=$msg");
    exit;
}


// ===================================================
// 1️⃣ BLOCK DIRECT ACCESS
// ===================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    goHome("error", "Invalid request.");
}


// ===================================================
// 2️⃣ CLEAN INPUTS
// ===================================================
function clean_input($str) {
    return trim(strip_tags($str));
}

function clean_header_field($str) {
    return trim(strip_tags(str_replace(["\r", "\n"], "", $str)));
}

$name    = clean_header_field($_POST['name'] ?? '');
$email   = clean_header_field($_POST['email'] ?? '');
$dial_code = clean_header_field($_POST['dial_code'] ?? ''); 
$phone     = clean_header_field($_POST['phone'] ?? '');     
$subject = clean_header_field($_POST['subject'] ?? 'Property Enquiry form submitted');
$full_phone = trim(($dial_code !== '' ? '+' . $dial_code . ' ' : '') . $phone);
$message   = clean_input($_POST['message'] ?? '');  // allow newlines

$honeypot  = $_POST['website'] ?? '';  // Hidden field


// ===================================================
// 3️⃣ SECURITY VALIDATIONS
// ===================================================
if ($honeypot !== '') {
    goHome("error", "Bot detected.");
}

if ($name == '' || $email == '' || $full_phone == '') {
    goHome("error", "All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    goHome("error", "Invalid email format.");
}

$phone_digits = preg_replace('/\D+/', '', $phone); // remove +, spaces
if (!preg_match("/^[0-9]{7,15}$/", $phone_digits)) {
    goHome("error", "Invalid phone number.");
}


if (strlen($message) > 1000) {
    goHome("error", "Message too long.");
}

if (preg_match('/^[0-9]+$/', $name)) {
    goHome("error", "Invalid name.");
}


// ===================================================
// 4️⃣ VERIFY reCAPTCHA
// ===================================================
$secretKey = "6LckGQIsAAAAAMlCSkr6GD-dJVBFuDPQsuASoFzS";
$response  = $_POST['g-recaptcha-response'] ?? '';
$ip        = $_SERVER['REMOTE_ADDR'];

$verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$ip";
$verify    = json_decode(file_get_contents($verifyURL));

if (!$verify->success) {
    goHome("error", "Captcha verification failed.");
}


// ===================================================
// 5️⃣ SEND TO CRM
// ===================================================
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => 'https://sanjarcrm.com/api/leads/submit',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'name'        => $name,
        'contact'     => $full_phone,
        'message'     => $message,
        'email'       => $email,
        'extra'       => $subject,
        'table_alias' => 'brickstonerealtors_com',
        'api_key'     => '7b08cc35f620f4d7aa36d08866e105af',
    ]
]);

$crm_response = curl_exec($curl);
curl_close($curl);


// ===================================================
// 6️⃣ EMAIL TEMPLATE
// ===================================================
$html = '
<div style="background:#f0f3f8; padding:25px; font-family:Arial, sans-serif;">
    <div style="
        max-width:600px; 
        margin:auto; 
        background:#ffffff; 
        border-radius:10px; 
        padding:25px; 
        border:1px solid #ddd;
    ">

        <h2 style="color:#1a73e8; margin-bottom:5px; font-size:24px;">
            🔔 New Website Enquiry
        </h2>

        <p style="color:#333; margin-top:0; font-size:15px;">
            You received a new enquiry from <b>rustomjee-mumbai.in</b>.
        </p>

        <table style="width:100%; border-collapse:collapse; margin-top:20px;">
            <tr>
                <td style="padding:10px; background:#f9fafc; width:30%; font-weight:bold;">Name</td>
                <td style="padding:10px;">'.$name.'</td>
            </tr>
            <tr>
                <td style="padding:10px; background:#f9fafc; font-weight:bold;">Email</td>
                <td style="padding:10px;">'.$email.'</td>
            </tr>
            <tr>
                <td style="padding:10px; background:#f9fafc; font-weight:bold;">Phone</td>
                <td style="padding:10px;">'.$full_phone.'</td>
            </tr>
            <tr>
                <td style="padding:10px; background:#f9fafc; font-weight:bold;">Message</td>
                <td style="padding:10px;">'.$message.'</td>
            </tr>
        </table>

        <p style="margin-top:25px; font-size:12px; color:#777;">
            This email was automatically generated by the Rustomjee Mumbai website.
        </p>
    </div>
</div>
';


// ===================================================
// 7️⃣ SEND EMAIL USING PHPMailer
// ===================================================
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'mail.rustomjee-mumbai.in';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@rustomjee-mumbai.in';
    $mail->Password   = 'PH$lItBW?R&Z';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->addCustomHeader('X-Mailer: PHP/' . phpversion());
    $mail->addCustomHeader('MIME-Version: 1.0');

    $mail->setFrom('info@rustomjee-mumbai.in', 'Rustomjee');
    $mail->addAddress('siddiquimahfooz327@gmail.com', 'mahfooz');
    $mail->addBCC('supritdagade77@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "You Recieved New $subject";
    $mail->Body    = $html;
    $mail->AltBody = strip_tags($html);


    $mail->send();

    goHome("success", "Your message has been sent successfully!");

} catch (Exception $e) {
    goHome("error", "Email sending failed.");
}

?>
