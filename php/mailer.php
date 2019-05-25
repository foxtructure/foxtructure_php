<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__."/autoload.php";

function send_mail($config)
{

    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = 2;                                       // Enable verbose debug output

        $mail->CharSet = "UTF-8";
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = "host name smtp";                     // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = "username smtp";                     // SMTP username
        $mail->Password   = "Haslo smtp do poczty";                               // SMTP password
        $mail->SMTPSecure = "tls";                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to
    
        //Recipients
        $mail->setFrom("biuro@adresstrony.pl", "Formularz kontaktowy");
        $mail->addAddress("biuro@adresstrony.pl", "Nazwa nadawcy");     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        $mail->addReplyTo($config->from_email, $config->from_name);
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
    
        // Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    
        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $config->mail_subject;
        $mail->Body    = $config->mail_body;
        $html = new \Html2Text\Html2Text($mail->Body);
        $mail->AltBody = $html->getText();
    
        $mail->send();
        //echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

}

if (isset($_POST["messagecontent"])) {

    $captcha = "";

    if (isset($_POST["g-recaptcha-response"])) {
        $captcha = $_POST["g-recaptcha-response"];
    }

    $secret = "insert secret key here";
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".$_SERVER["REMOTE_ADDR"]), true);

    var_dump($response);

    $config = (object) [
        "from_name" => $_REQUEST["username"],
        "from_email" => $_REQUEST["useremail"],
        "mail_subject" => $_REQUEST["messagetopic"],
        "mail_body" => "Wiadomość od:<br/><br/>" . $_REQUEST["username"] . "<br/><br/>" . "Treść wiadomości:<br/><br/>" . $_REQUEST["messagecontent"]
    ];
    
    if ($response["success"] != false) {
        send_mail($config);
    }
}

?>
