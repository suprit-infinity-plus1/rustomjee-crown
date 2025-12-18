$dial_code = $_POST['dial_code'] ?? '';
$phone = $_POST['phone'] ?? '';
$full_phone = '+' . $dial_code . ' ' . $phone;
    $message = $_POST['message'] ?? '';
    $subject = $_POST['subject'] ?? 'Property Enquiry form submited';

    $mail->Subject = "Contact form submission: " . $subject;
    
    // Debug (optional)
echo "<pre>";
print_r([
  'Name' => $name,
  'Email' => $email,
  'Code' => $dial_code,
  'Full Phone' => $full_phone,
  'Message' => $message
]);
die;

    // Prepare email content
    $html = "  
        <table style='width: 100%;border: 2px solid black;border-collapse: collapse;'>
        <tr style='width: 100%;border: 2px solid black;'>";
    if (!empty($name)) {
      $html .= "     <th style='width: 20%;border: 2px solid black;'>Enter your Name</th>";
    }
    if (!empty($email)) {
      $html .= "      <th style='width: 20%;border: 2px solid black;'>Enter your Email</th>";
    }
    if (!empty($phone)) {
      $html .= "      <th style='width: 20%;border: 2px solid black;'>Enter your Mobile Number</th>";
    }
    if (!empty($message)) {
      $html .= "      <th style='width: 20%;border: 2px solid black;'>Enter your Message</th>";
    }
    $html .= "    
        </tr>
        <tr style='width: 100%;border: 2px solid black;'>";
    if (!empty($name)) {
      $html .= "     <th style='width: 20%;border: 2px solid black;'>" . $name . "</th>";
    }
    if (!empty($email)) {
      $html .= "     <th style='width: 20%;border: 2px solid black;'>" . $email . "</th>";
    }
    if (!empty($phone)) {
      $html .= "     <th style='width: 20%;border: 2px solid black;'>" . $full_phone . "</th>";
    }
    if (!empty($message)) {
      $html .= "     <th style='width: 20%;border: 2px solid black;'>" . $message . "</th>";
    }
    $html .= "         </tr>
                </table>";

    // Send data to CRM
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => 'https://sanjarcrm.com/api/leads/submit',
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => array(
        'name' => $name,
        'contact' => $full_phone,
        'message' => $message,
        'email' => $email,
        'extra' => $subject,
        'table_alias' => 'brickstonerealtors_com',
        'api_key' => '7b08cc35f620f4d7aa36d08866e105af',
      )
    ));

     $resp = curl_exec($curl);
    curl_close($curl);


    // Prepare email message
    $mail->msgHTML($html);
    $mail->AltBody = 'This is a plain-text message body';

    if (!$mail->send()) {
        echo "
            <script type=\"text/javascript\">
                swal(
                    'Error',
                    'Your email could not be sent. Please try again later.',
                    'error'
                );
            </script>
        ";
    } else {
        echo "
            <script type=\"text/javascript\">
                swal(
                    'Success',
                    'Your email has been sent successfully!',
                    'success'
                );
            </script>
        ";
    }
  } else {
    echo "
        <script type=\"text/javascript\">
            swal(
                'Error',
                'reCAPTCHA verification failed. Please try again.',
                'error'
            );
        </script>
    ";
  }
} else {
  echo "
      <script type=\"text/javascript\">
          swal(
              'Error',
              'Please complete the reCAPTCHA.',
              'error'
          );
      </script>
  ";
}
?>


<script>
  $('body').click(function () {
    // window.location = "https://  .com/demo";
    window.location = "https://rustomjee.in.net/thankyou.html";
  });
</script>