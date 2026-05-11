<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}
$page = $_GET['page'] ?? 'dashboard';

// Récupérer le thème depuis le cookie
$theme = $_COOKIE['theme'] ?? 'light';
?>

<!DOCTYPE html>
<html lang="fr" data-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Backoffice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== VARIABLES MODE CLAIR/SOMBRE ===== */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --gray-700: #4A5A6E;
            --gray-800: #1E293B;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            --bg-body: #f0fdf4;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --sidebar-bg: linear-gradient(180deg, #0d3320 0%, #1a1a1a 100%);
            --sidebar-text: rgba(255,255,255,0.7);
            --sidebar-hover: rgba(46, 125, 50, 0.3);
            --sidebar-border: rgba(255,255,255,0.1);
            --topbar-bg: rgba(255,255,255,0.95);
            --stat-card-bg: #ffffff;
        }

        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --white: #1a1a2e;
            --gray-100: #1a1a2e;
            --gray-200: #16213e;
            --gray-600: #a0a0a0;
            --gray-700: #cbd5e1;
            --gray-800: #eeeeee;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
            --bg-body: #0f0f1a;
            --card-bg: #16213e;
            --border-color: #2c3e50;
            --text-muted: #a0a0a0;
            --sidebar-bg: linear-gradient(180deg, #0a0a0f 0%, #1a1a2e 100%);
            --sidebar-text: rgba(255,255,255,0.7);
            --sidebar-hover: rgba(46, 125, 50, 0.3);
            --sidebar-border: rgba(255,255,255,0.1);
            --topbar-bg: rgba(26, 26, 46, 0.95);
            --stat-card-bg: #16213e;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            transition: background 0.2s ease, color 0.2s ease;
        }

        /* ===== SWITCH MODE ===== */
        .theme-switch-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.2s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.2s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: var(--primary);
        }
        input:checked + .slider:before {
            transform: translateX(24px);
        }
        .theme-icon {
            font-size: 14px;
        }
        .light-icon, .dark-icon {
            display: none;
        }
        [data-theme="light"] .light-icon {
            display: inline;
        }
        [data-theme="light"] .dark-icon {
            display: none;
        }
        [data-theme="dark"] .light-icon {
            display: none;
        }
        [data-theme="dark"] .dark-icon {
            display: inline;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-logo {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .sidebar-logo span {
            color: white;
            background: none;
            -webkit-background-clip: unset;
        }

        .sidebar-subtitle {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: 5px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.3s;
            margin: 4px 12px;
            border-radius: 12px;
        }

        .sidebar-nav-item:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        .sidebar-nav-item.active {
            background: linear-gradient(135deg, #2E7D32, #1b5e20);
            color: white;
            box-shadow: 0 4px 15px rgba(46,125,50,0.3);
        }

        .sidebar-nav-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 280px;
            padding: 20px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        /* ===== TOP BAR ===== */
        .top-bar {
            background: var(--topbar-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-badge {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            color: white;
        }

        /* ===== STATS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--stat-card-bg);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2E7D32, #4CAF50);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            background: rgba(46,125,50,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #2E7D32;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .stat-change.positive { color: #22c55e; }
        .stat-change.negative { color: #ef4444; }

        /* ===== CARDS ===== */
        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 30px;
            padding: 28px 24px;
            border-radius: 24px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .page-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1b5e20;
            background: rgba(46, 125, 50, 0.12);
        }

        .dashboard-header h2 {
            margin-top: 12px;
            font-size: 1.75rem;
            line-height: 1.2;
            color: var(--gray-800);
        }

        .page-description {
            margin-top: 10px;
            max-width: 680px;
            color: var(--text-muted);
            line-height: 1.75;
            font-size: 0.95rem;
        }

        .period-switch {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-end;
        }

        .dashboard-hero {
            display: grid;
            grid-template-columns: 1.9fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
        }

        .dashboard-hero-card {
            position: relative;
            border-radius: 28px;
            padding: 36px;
            background: linear-gradient(140deg, rgba(46, 125, 50, 0.95), rgba(76, 175, 80, 0.95));
            color: white;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
        }

        .dashboard-hero-card::before {
            content: '';
            position: absolute;
            width: 170px;
            height: 170px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            top: -40px;
            right: -40px;
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .hero-icon {
            font-size: 1.6rem;
            opacity: 0.85;
        }

        .hero-value {
            font-size: 3.4rem;
            margin: 0 0 12px;
            letter-spacing: -0.03em;
            line-height: 1;
        }

        .hero-text {
            max-width: 520px;
            margin-bottom: 24px;
            color: rgba(255,255,255,0.9);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .hero-stats div {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 16px 18px;
            text-align: center;
        }

        .hero-number {
            display: block;
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .hero-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.75);
        }

        .hero-number.positive { color: #d4f8e8; }

        .hero-footer {
            margin-top: 22px;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.82);
        }

        .dashboard-summary-cards {
            display: grid;
            gap: 20px;
        }

        .circle-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .circle-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 26px;
            padding: 26px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            position: relative;
        }

        .circle-card::before {
            content: '';
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(46, 125, 50, 0.08);
            right: -40px;
            top: -40px;
            z-index: 0;
        }

        .circle-card.circle-card--secondary::before {
            background: rgba(33, 150, 243, 0.08);
        }

        .circle-card.circle-card--accent::before {
            background: rgba(123, 31, 162, 0.08);
        }

        .circle-ring {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(46,125,50,0.15), rgba(76,175,80,0.05));
            border: 7px solid rgba(46,125,50,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: 18px;
            position: relative;
            z-index: 1;
        }

        .circle-card.circle-card--secondary .circle-ring {
            background: linear-gradient(180deg, rgba(33,150,243,0.15), rgba(33,150,243,0.05));
            border-color: rgba(33,150,243,0.18);
        }

        .circle-card.circle-card--accent .circle-ring {
            background: linear-gradient(180deg, rgba(123,31,162,0.15), rgba(123,31,162,0.05));
            border-color: rgba(123,31,162,0.18);
        }

        .circle-info {
            position: relative;
            z-index: 1;
        }

        .circle-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .circle-info p {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .line-metric {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 22px;
        }

        .line-metric span {
            display: block;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(46,125,50,0.8), rgba(46,125,50,0.15));
            opacity: 0.7;
        }

        .line-metric span:nth-child(1) { width: 100%; }
        .line-metric span:nth-child(2) { width: 85%; }
        .line-metric span:nth-child(3) { width: 70%; }
        .line-metric span:nth-child(4) { width: 55%; }

        .circle-card.circle-card--secondary .line-metric span {
            background: linear-gradient(90deg, rgba(33,150,243,0.8), rgba(33,150,243,0.15));
        }

        .circle-card.circle-card--accent .line-metric span {
            background: linear-gradient(90deg, rgba(123,31,162,0.8), rgba(123,31,162,0.15));
        }

        .summary-card {
            border-radius: 24px;
            padding: 24px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .summary-card--green { border-color: rgba(46,125,50,0.2); }
        .summary-card--blue { border-color: rgba(33,150,243,0.2); }
        .summary-card--purple { border-color: rgba(123,31,162,0.2); }

        .summary-title {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: inline-block;
            letter-spacing: 0.08em;
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-800);
            margin-bottom: 10px;
        }

        .summary-meta {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .summary-card--green .summary-value { color: #2E7D32; }
        .summary-card--blue .summary-value { color: #1E88E5; }
        .summary-card--purple .summary-value { color: #6A1B9A; }

        .period-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 999px;
            font-size: 0.9rem;
            color: var(--gray-700);
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            background: var(--gray-100);
        }

        .period-chip:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        .period-chip.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary-dark);
            box-shadow: 0 8px 20px rgba(0, 109, 91, 0.14);
        }

        /* ===== QUICK STATS ===== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .quick-stat-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }

        .quick-stat-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .progress-bar {
            height: 8px;
            background: var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2E7D32, #4CAF50);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2E7D32;
        }

        /* ===== TABLES ===== */
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: var(--gray-100);
            font-weight: 600;
            color: var(--gray-700);
        }

        td {
            color: var(--gray-600);
        }

        /* ===== BADGES ===== */
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-admin { background: #E3F2FD; color: #1976D2; }
        .badge-agent { background: #FFF3E0; color: #E65100; }
        .badge-user { background: #E8F5E9; color: #2E7D32; }
        .badge-citoyen { background: #E8F5E9; color: #2E7D32; }
        .badge-professionnel { background: #E3F2FD; color: #1976D2; }
        .badge-agent-public { background: #FFF3E0; color: #E65100; }

        [data-theme="dark"] .badge-admin { background: #1a3a5a; color: #87cefa; }
        [data-theme="dark"] .badge-agent { background: #4a3a1a; color: #f5d742; }
        [data-theme="dark"] .badge-user { background: #1a4a2a; color: #a3e4b7; }
        [data-theme="dark"] .badge-citoyen { background: #1a4a2a; color: #a3e4b7; }
        [data-theme="dark"] .badge-professionnel { background: #1a3a5a; color: #87cefa; }
        [data-theme="dark"] .badge-agent-public { background: #4a3a1a; color: #f5d742; }

        /* ===== BUTTONS ===== */
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-edit {
            background: #1D74D0;
            color: white;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            min-width: 44px;
            justify-content: center;
            transition: transform 0.2s ease, background 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit:hover {
            background: #145ea8;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #d32f2f;
            color: white;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            min-width: 44px;
            justify-content: center;
            transition: transform 0.2s ease, background 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-delete:hover {
            background: #b71c1c;
            transform: translateY(-1px);
        }

        .btn-add {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(0, 109, 91, 0.14);
        }

        .btn-add:hover {
            background: var(--primary-dark);
        }

        .btn-filter {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 10px 18px;
            border: 1px solid transparent;
            min-width: 150px;
        }

        .btn-filter:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-clear {
            background: transparent;
            color: var(--gray-800);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 10px 18px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-clear:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .sort-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--gray-700);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .sort-link:hover {
            color: var(--primary);
        }

        .sort-icon {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .filters-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 22px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .filters-form {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            width: 100%;
        }

        .search-input {
            position: relative;
            flex: 1 1 320px;
            min-width: 250px;
        }

        .search-input i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .search-input input {
            width: 100%;
            padding: 13px 16px 13px 42px;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            background: var(--bg-body);
            color: var(--gray-800);
        }

        .filters-form select {
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            color: var(--gray-800);
            min-width: 180px;
        }

        .results-info {
            margin-top: 18px;
            padding: 18px 22px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            box-shadow: var(--shadow-sm);
        }

        .no-results {
            padding: 45px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .clear-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .table-container table tbody tr:hover {
            background: rgba(46, 125, 50, 0.05);
        }

        .table-container table th,
        .table-container table td {
            padding: 16px 18px;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--card-bg);
            color: var(--text-muted);
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        /* ===== USER CELL ===== */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
            flex-shrink: 0;
        }

        .user-avatar.admin { background: linear-gradient(135deg, #1976D2, #42A5F5); }
        .user-avatar.agent { background: linear-gradient(135deg, #E65100, #FF9800); }
        .user-avatar.user { background: linear-gradient(135deg, #2E7D32, #4CAF50); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .dashboard-hero { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .quick-stats { grid-template-columns: repeat(2, 1fr); }
            .dashboard-summary-cards { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .dashboard-hero { grid-template-columns: 1fr; }
            .dashboard-summary-cards { grid-template-columns: 1fr; }
            .dashboard-hero-card { padding: 28px; }
            .hero-stats { grid-template-columns: 1fr; }
            .hero-top { flex-direction: column; align-items: flex-start; gap: 12px; }
            .stats-grid { grid-template-columns: 1fr; }
            .quick-stats { grid-template-columns: 1fr; }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
        }
            .quick-stats { grid-template-columns: 1fr; }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <!-- TOP BAR AVEC SWITCH MODE -->
    <div class="top-bar">
        <div class="page-title">
            <h1>Plateforme administrée</h1>
        </div>
        <div class="admin-info">
            <!-- SWITCH MODE SOMBRE/CLAIR -->
            <div class="theme-switch-wrapper">
                <span class="theme-icon light-icon">☀️</span>
                <label class="theme-switch">
                    <input type="checkbox" id="theme-toggle" <?= $theme === 'dark' ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
                <span class="theme-icon dark-icon">🌙</span>
            </div>
            <span class="admin-badge"><?= strtoupper($_SESSION['user_role'] ?? 'admin') ?></span>
            <div class="admin-avatar">
                <?= strtoupper(substr($_SESSION['user_nom'] ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </div>

    <?php
    if ($page == 'dashboard') {
        include 'dashboard.php';
    } elseif ($page == 'liste_utilisateurs') {
        include 'liste_utilisateurs.php';
    } elseif ($page == 'ajouter_utilisateur') {
        include 'ajouter_utilisateur.php';
    } elseif ($page == 'modifier_utilisateur') {
        include 'modifier_utilisateur.php';
    } elseif ($page == 'concours') {
        include 'concours.php';
    } elseif ($page == 'emploi') {
        include 'emploi.php';
    } elseif ($page == 'demandes') {
        include 'demandes.php';
    } elseif ($page == 'reclamations') {
        include 'reclamations.php';
    } elseif ($page == 'rendez_vous') {
        include 'rendez_vous.php';
    } else {
        include 'dashboard.php';
    }
    ?>
</div>

<script>
    // MODE SOMBRE/CLAIR
    (function() {
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        const THEME_KEY = 'innogov_theme';
        
        function setTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            document.cookie = `theme=${theme}; path=/; max-age=31536000; SameSite=Lax`;
            localStorage.setItem(THEME_KEY, theme);
            if (themeToggle) themeToggle.checked = (theme === 'dark');
        }
        
        function initTheme() {
            const savedTheme = localStorage.getItem(THEME_KEY);
            const cookieTheme = document.cookie.replace(/(?:(?:^|.*;\s*)theme\s*=\s*([^;]*).*$)|^.*$/, "$1");
            const theme = savedTheme || cookieTheme || 'light';
            setTheme(theme);
        }
        
        if (themeToggle) {
            themeToggle.addEventListener('change', function() {
                const newTheme = this.checked ? 'dark' : 'light';
                setTheme(newTheme);
            });
        }
        
        initTheme();
    })();
</script>

</body>
</html>