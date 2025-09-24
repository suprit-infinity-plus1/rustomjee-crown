<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>

<style>
  body {
    font-family: sans-serif;
    text-align: center;
  }

  button {
    background-color: cadetblue;
    color: whitesmoke;
    border: 0;
    font-size: 18px;
    font-weight: 500;
    border-radius: 7px;
    padding: 15px 35px;
    cursor: pointer;
    margin: 10px;
  }

  img {
    width: 200px;
  }

  input[type="text"] {
    padding: 12px 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
  }

  h1 {
    border-bottom: solid 2px grey;
  }

  #success {
    background: green;
  }

  #error {
    background: red;
  }

  #warning {
    background: coral;
  }

  #info {
    background: cornflowerblue;
  }

  #question {
    background: grey;
  }
</style>

<button style="display:none" id="success">Success</button>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// Start session
// session_start();  // Only if needed elsewhere

// Initialize PHPMailer
$mail = new PHPMailer();
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = "mail.rustomjee.in.net";
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Username = 'info_crown@rustomjee.in.net'; // Update if required
$mail->Password = 'Ld^R{Xw9KAH=';  // Replace with actual password
$mail->IsHTML(true);
$mail->setFrom('info_crown@rustomjee.in.net', 'Rustomjee Crown');

// Add recipients
$mail->addBCC('sanjaresolutions@gmail.com', 'sanjaresolutions');
// $mail->addBCC('nathuramvarande@gmail.com', 'nathuram');
// $mail->addBCC('mirzafaizan1931@gmail.com', 'Faizan Mirza');
$mail->addAddress('supritdagade77@gmail.com', 'suprit');
$mail->Subject = 'Contact form submitted data.';

// Gather form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$message = $_POST['message'] ?? '';

// Prepare email content
$html = "<table style='width: 100%;border: 2px solid black;border-collapse: collapse;'><tr>";
if (!empty($name)) $html .= "<th style='border: 2px solid black;'>Enter your Name</th>";
if (!empty($email)) $html .= "<th style='border: 2px solid black;'>Enter your Email</th>";
if (!empty($phone)) $html .= "<th style='border: 2px solid black;'>Enter your Mobile Number</th>";
if (!empty($message)) $html .= "<th style='border: 2px solid black;'>Enter your Message</th>";
$html .= "</tr><tr>";
if (!empty($name)) $html .= "<td style='border: 2px solid black;'>$name</td>";
if (!empty($email)) $html .= "<td style='border: 2px solid black;'>$email</td>";
if (!empty($phone)) $html .= "<td style='border: 2px solid black;'>$phone</td>";
if (!empty($message)) $html .= "<td style='border: 2px solid black;'>$message</td>";
$html .= "</tr></table>";

// Send data to CRM
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://sanjarcrm.com/api/leads/submit',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        'name' => $name,
        'contact' => $phone,
        'message' => $message,
        'email' => $email,
        'extra' => 'bloomivf.in.net',
        'table_alias' => 'bloomivfgroup_in',
        'api_key' => '1eca48c1d63b9ddcbb555ef9a0b6c602',
    )
));
$resp = curl_exec($curl);
curl_close($curl);

// Send email
$mail->msgHTML($html);
$mail->AltBody = 'This is a plain-text message body';

if (!$mail->send()) {
    echo "
    <script type=\"text/javascript\">
        swal('Failed', 'Your mail could not be sent. <b style=color:red>Try Again</b>', 'error');
    </script>";
} else {
    echo "
    <script type=\"text/javascript\">
        swal('Success', 'Your mail has been sent <b style=color:green>Successfully</b>', 'success');
    </script>";
}
?>

<script>
  $('body').click(function() {
    window.location = "http://bloomivf.in/";
  });
</script>
