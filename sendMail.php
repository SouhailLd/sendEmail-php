<?php
header('Access-Control-Allow-Origin: *');
use PHPMailer\PHPMailer\PHPMailer;
switch($_SERVER['REQUEST_METHOD']){
    case("OPTIONS"): //Allow preflighting to take place.
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: content-type");
        exit;
    case("POST"): //Send the email;
        $json = file_get_contents('php://input');
        $params = json_decode($json);   

        $name = $params->name;
        $email = $params->email;
        $subject = $params->subject;
        $body = $params->message;   

        if (isset($name) && isset($email)) {    

            require_once "PHPMailer/PHPMailer.php";
            require_once "PHPMailer/SMTP.php";
            require_once "PHPMailer/Exception.php"; 

            $mail = new PHPMailer();    

            //SMTP Settings
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "<Mettre l'email de l'expéditeur>";
            $mail->Password = '<mot de passe d\'email>';
            $mail->Port = 465; //587
            $mail->SMTPSecure = "ssl"; //tls    

            //Email Settings
            $mail->isHTML(true);
            $mail->setFrom($email, $name);
            $mail->addAddress("<Email du destinataire>");
            $mail->Subject = $subject;
            $mail->Body = $body;    

            if ($mail->send()) {
                $status = "success";
                $response = "Email is sent!";
            } else {
                $status = "failed";
                $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
            }
            exit(json_encode(array("status" => $status, "response" => $response)));
        }
    break;
    default: //Reject any non POST or OPTIONS requests.
        header("Allow: POST", true, 405);
        exit;
}
?>