<?php
//
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Adote um Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            background-color: #fdfbf9; 
            color: #333; 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-content {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }

        .navbar { 
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px); 
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.2rem 0;
        }

        .navbar-brand { 
            font-weight: 800; 
            font-size: 1.2rem;
            letter-spacing: -0.5px;
            color: #2d3436 !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link { 
            font-weight: 500; 
            color: #636e72 !important; 
            font-size: 0.95rem;
            padding: 0 15px !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover { 
            color: #ff7e67 !important; 
        }

        .btn-login {
            background-color: #2d3436;
            color: white !important;
            border-radius: 12px;
            padding: 8px 24px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            background-color: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-adopt {
        background-color: #ff7e67; 
        color: white !important;
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 126, 103, 0.2);
        }

        .btn-adopt:hover {
            background-color: #ef6b52;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 126, 103, 0.3);
            color: white !important;
        }

        .btn-outline-custom {
            background-color: transparent;
            color: #65b6cc;
            border: 2px solid #65b6cc;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background-color: #65b6cc;
            color: white !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="?page=home">
            <span style="font-size: 1.5rem;">🐾</span> ADOTE UM PET
        </a>
        
        <div class="d-flex align-items-center gap-2">
            <a class="nav-link" href="?page=home">Home</a>
            <a class="nav-link" href="?page=listar_pets">Ver Pets</a> 

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php if (($_SESSION["usuario_tipo"] ?? "") === "funcionario"): ?>
                    <a class="nav-link fw-bold text-dark" href="?page=cadastrar_pet">Cadastrar</a>
                    <a class="nav-link fw-bold text-dark" href="?page=anunciar_pet">Anunciar</a>
                    <a class="nav-link fw-bold text-dark" href="?page=gerenciar_pets">Gerenciar</a>
                    <a class="nav-link fw-bold text-dark" href="?page=validar_adocoes">Validar</a>
                <?php elseif (($_SESSION["usuario_tipo"] ?? "") === "adotante"): ?>
                    <a class="nav-link fw-bold text-dark" href="?page=minhas_adocoes">Minhas candidaturas</a>
                <?php endif; ?>
                <a class="nav-link" href="?page=perfil"><?= htmlspecialchars($_SESSION["usuario_nome"] ?? "") ?></a>
                <a href="?page=logout" class="btn btn-outline-danger ms-2" style="border-radius: 10px; font-size: 0.8rem;">Sair</a>
            <?php else: ?>
                <a href="?page=cadastro" class="nav-link">Cadastrar-se</a>
                <a href="?page=login" class="btn btn-login ms-2">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container page-content">
