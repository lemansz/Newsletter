<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();



$email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

function previous_page(){
    $referer = $_SERVER['HTTP_REFERER'];
    if ($referer){
        header('Location:' .$referer);
    }
}

$host = $_ENV["DB_HOST"];
$db_name = $_ENV["DB_NAME"];
$username = $_ENV["DB_USER_NAME"];
$password = $_ENV["DB_PASSWORD"];



$conn = mysqli_connect(hostname: $host, username: $username, database: $db_name, password: $password);

if (mysqli_connect_errno()){
    die ("Connect error: " . mysqli_connect_errno());
} 

$sql = "INSERT INTO emails (subscriber_email) VALUE (?)";

$stmt = mysqli_stmt_init($conn);

if ( ! mysqli_stmt_prepare($stmt, $sql)){
    die (mysqli_error($conn));
};

mysqli_stmt_bind_param($stmt, "s", $email);

mysqli_stmt_execute($stmt);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST["submit"])){
    $mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV["EMAIL_SENDER"];
    $mail->Password = $_ENV["EMAIL_PASSWORD"];
    $mail->SMTPSecure = 'ssl';
    $mail->Port = $_ENV["EMAIL_PORT"];

    $mail->setFrom($_ENV["EMAIL_SENDER"]);

    $mail->addAddress($_POST["email"]);

    $mail->isHTML(true);

    $mail->Subject = "Newsletter Signup Successful";

    $mail->Body = '<table width="100%" cellspacing="0" cellpadding="0" border="0"
    <tr>
    <td align = "center" valign="middle">
    <table width="200" height="40" cellspacing="0" cellpadding="0" border"0" style="background-color:rgba(23, 30, 56, 0.899); border-radius: 0.5rem;">
    <tr><td align="center" valign="middle" style="Roboto, sans-serif; font-size: 1rem; color: #ffffff;"><a href="index.html"style="text-decoration: none; color: #ffffff;"> Confirm!</a>
    </td>
    </tr>
    </table>
    </td>
    </tr>
    </table>'
    ;
    $mail->send();
} catch (Exception $e) {
   die(previous_page());
}}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Complete!</title>
    <link rel="icon" href="favicon-32x32.png">
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

        body{
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(22, 29, 54, 0.899);
            padding-top: 10em;
        }

        .parent{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            border: none;
            border-radius: 18px;
            padding-right: 2em;
            padding-left: 2em;
            height: 23em;
            width: 22em;
        }

        .tick{
            height: 20%;
            min-width: 100%;
            background: url(icon-success.svg);
            background-repeat: no-repeat;
            align-self: flex-start;
            position: relative;
        }

        .text-area{
            min-height: 70%;
        }

        .btn{
            padding: 1.3em;
            min-width: 100%;
            border: none;
            border-radius: 6px;
            color: white;
            background-color: hsla(227, 42%, 11%, 0.899);
            font-family: "Roboto", sans-serif;
            font-style:normal;
            font-weight: 500;
            
            
        }

        .btn:hover{
            cursor: pointer;
            cursor: pointer;
            background: linear-gradient(to right , hsl(340, 75%, 63%), hsl(370, 100%, 65%));
            box-shadow: 1px 8px 18px hsl(370, 100%, 85%);
        }

        .appreciation{
            font-family: "Roboto", sans-serif;
            font-weight: 700;
            font-style:normal;
           
           
        }

        .message{
            font-size: "Roboto", sans-serif;
            font-weight: 300;
            font-style: normal;
            font-family: 'Times New Roman', Times, serif;
        }
        @media screen and (max-width: 40rem){
            body{
                padding: 0;
                margin: 0;
                overflow: hidden;
               
            }

            .parent{
                height: 100vh;
                border-radius: 0;
                padding-bottom: 5em;
                width: 100%;
            }
            .tick{
                margin-top: 8em; 
            }
            .appreciation{
                margin-bottom: 0rem;
                margin-top: 0;
            }
            .text-area{
                margin-top: 0rem;
            }
            .message{
                max-height: 3.43rem;
                margin-bottom: 14rem;
            }
        }
    </style>
</head>
<body>
    <div class="parent">
        <div class="tick"></div>
        <div class="text-area">
            <h1 class="appreciation">Thanks for <br> subcribing!</h1>

            <p class="message">A confirmation email as been sent to <?php echo "<b>$email</b>" ?>. 
            Please open it and click the button inside to confirm your subscription</p>
            <button class="btn" onclick="history.back()">Dismiss message</button>
        </div>
    </div>
</body>
</html>

