<?php
require_once __DIR__ . '/auth.php';
require_login();
$u = current_user();
$page = $page ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>STOCKLY — <?= htmlspecialchars($page) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-body">
<div class="app-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="app-main">
    <header class="app-header">
      <button class="btn-icon" id="toggleSidebar" aria-label="Menú">☰</button>
      <div class="header-right">
        <span class="badge-role"><?= htmlspecialchars($u['rol']) ?></span>
        <span><?= htmlspecialchars($u['nombre']) ?></span>
        <a href="logout.php" class="btn btn-ghost">Salir</a>
      </div>
    </header>
    <main class="app-content">