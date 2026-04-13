<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion Emplois et Shifts</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
        }
        .container{
            max-width:1000px;
            width:100%;
            background:white;
            padding:40px;
            border-radius:10px;
            box-shadow:0 10px 40px rgba(0,0,0,0.3);
        }
        h1{
            color:#005C9E;
            text-align:center;
            margin-bottom:10px;
            font-size:32px;
        }
        .subtitle{
            text-align:center;
            color:#666;
            margin-bottom:40px;
            font-size:14px;
        }
        .menu{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:30px;
            margin-bottom:40px;
        }
        .menu-item{
            background:linear-gradient(135deg, #005C9E 0%, #003f75 100%);
            color:white;
            padding:40px;
            border-radius:10px;
            text-align:center;
            text-decoration:none;
            transition:all 0.3s ease;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }
        .menu-item:hover{
            transform:translateY(-5px);
            box-shadow:0 15px 30px rgba(0,0,0,0.2);
        }
        .menu-item h2{
            margin:0 0 15px 0;
            font-size:28px;
        }
        .menu-item p{
            margin:0;
            font-size:14px;
            opacity:0.9;
            line-height:1.5;
        }
        .info{
            background:#e3f2fd;
            padding:20px;
            border-left:5px solid #005C9E;
            border-radius:3px;
            margin-bottom:20px;
        }
        .info strong{
            color:#005C9E;
        }
        .footer{
            text-align:center;
            color:#999;
            font-size:12px;
            margin-top:30px;
            padding-top:20px;
            border-top:1px solid #eee;
        }
        @media (max-width:768px){
            .menu{
                grid-template-columns:1fr;
            }
            h1{
                font-size:24px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🏢 Gestion des Emplois</h1>
    <div class="subtitle">Plateforme de gestion des horaires et des affectations</div>
    
    <div class="info">
        <strong>ℹ️ Bienvenue !</strong> Utilisez les menus ci-dessous pour gérer les shifts (horaires) et les emplois (affectations des agents).
    </div>
    
    <div class="menu">
        <a href="../backoffice/admin-shifts-lister.php" class="menu-item">
            <h2>⏰</h2>
            <h2>Shifts</h2>
            <p>Gérer les horaires et les tranches de travail (Matin, Après-midi, etc.)</p>
        </a>
        <a href="../backoffice/admin-emplois-lister.php" class="menu-item">
            <h2>📋</h2>
            <h2>Emplois</h2>
            <p>Affecter les agents aux services et horaires</p>
        </a>
    </div>
    
    <div class="info" style="background:#f0f7ff;border-left-color:#FFA500;">
        <strong style="color:#FFA500;">📌 Instructions :</strong>
        <ul style="margin-left:20px;margin-top:10px;color:#666;">
            <li>Commencez par créer les <strong>Shifts</strong> (ex: Matin 08:00-13:00)</li>
            <li>Puis créer les <strong>Emplois</strong> en affectant les agents</li>
            <li>Vous pouvez modifier ou supprimer à tout moment</li>
        </ul>
    </div>
    
    <div class="footer">
        Système de Gestion Municipal | Version 1.0
    </div>
</div>
</body>
</html>
