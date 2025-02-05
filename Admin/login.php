<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('../php/db.php');
// Ensure the user is not already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_logs.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Check if the username and password match a user in the database
    $result = $conn->query("SELECT * FROM admin WHERE username = '$username' AND password = '$password'");
   
    $data = $result->fetch_assoc();
    if($data['admin_id']){
        $_SESSION['admin_id'] = $data['admin_id'];
        $sql = "insert into admin_logs (admin_id, action_type, table_name, affected_row_id, action_description) values ('$_SESSION[admin_id]', 'LOGIN', 'admin', null, 'Logged in successfully')";
        $conn->query($sql);
        header("Location: admin_logs.php");
    }

    // if ($result->num_rows > 0) {
    //     // If a user is found, set the admin_id session variable
    //     $data = $result->fetch_assoc();
    //     $_SESSION['admin_id'] = $data['admin_id'];
        
    //     $mail = new PHPMailer(true);

    //     try {
    //         //Server settings
    //         $mail->isSMTP();
    //         $mail->Host = $hostServer; // Set the SMTP server to send through
    //         $mail->SMTPAuth = true;
    //         $mail->Username = $hostPartnerEmail; // SMTP username
    //         $mail->Password = $hostPartnerPass; // SMTP password
    //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //         $mail->Port = 587;
    
    //         //Recipients
    //         $mail->setFrom($hostPartnerEmail, 'Gymaika');
    //         $mail->addAddress('amgammarx@hotmail.com');
    
    //         // Content
    //         $mail->isHTML(true);
    //         $mail->Subject = 'ADMIN LOGGED IN';
    
    //         $mail->Body = "
    //         <p>Dear Ahmed,</p>
    //             <p>Watch out There is a new admin logged in!</p>

    //             Their username is: $username
    //             Their password is: $password

    //             <p>Thank you!</p>
                
    //         ";
            
        
    //         // $mail->SMTPDebug = 2;
    //         $mail->send();
    //         header("Location: admin_dashboard.php");
    //     } catch (Exception $e) {
    //         echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    //     }

    //     header("Location: admin_dashboard.php");
    //     exit();
    // } else {
    //     // If no user is found, display an error message
    //     echo "Invalid username or password.";
    // }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Data</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <header>
        <h1>Manage Data</h1>
        <!-- <nav>
            <ul>
                <li><a href="admin_logs.php">Logs</a></li>
                <li><a href="admin_volunteers.php">Volunteers</a></li>
                <li><a href="admin_recipients.php">Recipients</a></li>
                <li><a href="admin_recipients_excel.php">Recipients Excel</a></li>
                <li><a href="admin_volunteers_excel.php">Volunteers Excel</a></li>
                <li><a href="php/logout.php"><i class="fa fa-sign-out"></i></a></li>
            </ul>
        </nav> -->
    </header>
    <main>
        <section class="login-section">
            <style>
                section{
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    align-items: center;
                }
                form{
                    width: 400px;
                    max-width: 400px;
                }
                h2{
                    margin: 0;
                    width: 100% !important;
                    text-align: center;
                }
            </style>
            <form action="login.php" method="post">
                <h2>Admin Login</h2>
                <div class="label-input">
                    <label for="username">username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="label-input">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="general-button">Login</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 AID</p>
    </footer>