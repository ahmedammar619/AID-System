<?php
ob_start();
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
}
// $_SESSION['admin_id'] = 1;
ob_end_flush();
?>    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Data</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="FONTAWESOME_API_KEY" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <header>
        <h1>Manage Data</h1>
        <nav>
            <ul>
                <li><a href="admin_logs.php">Admin</a></li>
                <li><a href="admin_volunteers.php">Volunteers</a></li>
                <li><a href="admin_recipients.php">Recipients</a></li>
                <li><a href="admin_volunteers_excel.php">Vol.xlsx</a></li>
                <li><a href="admin_recipients_excel.php">Rec.xlsx</a></li>
                <li><a href="admin_delivery.php">Delivery</a></li>
                <li><a href="php/logout.php"><i class="fa fa-sign-out"></i></a></li>
            </ul>
        </nav>
    </header>

    <script>
        // Save scroll position before the page is unloaded or hidden
        const saveScrollPosition = () => {
            sessionStorage.setItem('scrollX', window.scrollX); // Horizontal scroll position
            sessionStorage.setItem('scrollY', window.scrollY); // Vertical scroll position
        };

        window.addEventListener('pagehide', saveScrollPosition);
        window.addEventListener('beforeunload', saveScrollPosition);

        // Restore scroll positions after the page is shown or loaded
        const restoreScrollPosition = () => {
            const scrollX = sessionStorage.getItem('scrollX');
            const scrollY = sessionStorage.getItem('scrollY');

            if (scrollX !== null && scrollY !== null) {
                // Add a small delay to ensure the page is fully loaded
                setTimeout(() => {
                    window.scrollTo(parseInt(scrollX), parseInt(scrollY)); // Restore both positions
                    sessionStorage.removeItem('scrollX'); // Clear stored horizontal position
                    sessionStorage.removeItem('scrollY'); // Clear stored vertical position
                }, 100); // Adjust the delay as needed
            }
        };

        window.addEventListener('pageshow', restoreScrollPosition);
        window.addEventListener('load', restoreScrollPosition);
    </script>