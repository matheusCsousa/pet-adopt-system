<?php
//
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Adote um Pet!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fcfcfc; color: #333; }
        .navbar { border-bottom: 1px solid #eee; background-color: #fff !important; }
        .navbar-brand { font-weight: bold; color: #444 !important; }
        .nav-link { color: #666 !important; font-size: 0.9rem; }
        .nav-link:hover { color: #000 !important; }
    </style>
</head>
<nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="?page=home">🐾 ADOTE UM PET</a>
            
            <div class="d-flex align-items-center gap-4">
                <a class="nav-link" href="?page=home">Home</a>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a class="nav-link" href="?page=cadastrar_pet">Cadastrar Pet</a>
                    <a href="?page=logout" class="btn btn-outline-danger btn-logout">
                        Sair (<?php echo $_SESSION['usuario_nome']; ?>)
                    </a>
                <?php else: ?>
                    <a href="?page=login" class="btn btn-dark btn-sm px-4" style="border-radius: 20px;">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container">
