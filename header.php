<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="general.js" defer></script>
    <link rel="stylesheet" href="general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="FONTAWESOME_API_KEY" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
        <div id="header-container">
            <div class="logo-icon-text-div">
                <a href="" class="logo-icon-text-div">
                    <img src="logo.png" alt="logo" id="logo-icon"></img>
                    <h1 id="logo-text" >AID</h1>
                </a>
            </div>
            <div id="nav-list">
                <ul id="nav-list-div">
                    <i class="fa fa-times" id="hide-menu-button"></i>
                    
                    <?php $pageName = basename($_SERVER['PHP_SELF'], ".php"); ?>
                    <li <?php if ($pageName=="volunteerform") echo 'class="nav-text current"'; ?>><a href="volunteerform.php" arabic="المتطوعين الجدد" Farsi="المتطوعين الجدد" Spanish="Nuevo Voluntario" Urdu="المتطوعين الجدد" Myanmar="အသစ်ထည့်ရန်" Pashto="نوی مولودوری">New Volunteer</a></li>
                    <li <?php if ($pageName=="recipientform") echo 'class="nav-text current"'; ?>><a href="recipientform.php" arabic="متلقي جديد" Farsi="متلقي جديد" Spanish="Nuevo Destinatario" Urdu="متلقي جديد" Myanmar="အသစ်ထည့်ရန်" Pashto="نوی مولودوری">New Recipient</a></li>
                    <li <?php if ($pageName=="recipientpage") echo 'class="nav-text current"'; ?>><a href="recipientpage.php" arabic="متلقي" Farsi="متلقي" Spanish="Destinatario" Urdu="متلقي" Myanmar="အသစ်ထည့်ရန်" Pashto="مولودوری">Recipient</a></li>
                    <li <?php if ($pageName=="deliver") echo 'class="nav-text current"'; ?>><a href="deliver.php" arabic="توصيل" Farsi="توصيل" Spanish="Entrega" Urdu="توصيل" Myanmar="အသစ်ထည့်ရန်" Pashto="مولودوری">Deliver</a></li>
                    <li><a href="#" ><i class="fa fa-globe"></i></a></li>
                    <script>

                    </script>

                    <?php if (isset($_SESSION['volunteerID'])){
                        echo '  
                        <li><a href="php/logout.php"><i class="fa fa-sign-out"></i></a></li>
                        ';
                    }
                    ?>
                    <?php if (isset($_SESSION['recipientID'])){
                        echo '  
                        <li><a href="php/logout.php"><i class="fa fa-sign-out"></i></a></li>
                        ';
                    }
                    ?>
                </ul>
                <i class="fa fa-bars" id="show-menu-button" ></i>
            </div>
            
        </div>



    <div class="popup" id="languagePopup">
        <button onclick="setLanguage('English')">English</button>
        <button onclick="setLanguage('Arabic')">Arabic | العربية</button>
        <button onclick="setLanguage('Farsi')">Farsi | فارسی</button>
        <button onclick="setLanguage('Spanish')">Spanish | Español</button>
        <button onclick="setLanguage('Urdu')">Urdu | اردو</button>
        <button onclick="setLanguage('Myanmar')">Burmese | မြန်မာဘာသာ</button>
        <button onclick="setLanguage('Pashto')">Pashto | پښتو</button>
    </div>
        
        
