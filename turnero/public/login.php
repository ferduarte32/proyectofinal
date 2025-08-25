<?php
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Turnero</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
  <div class="container" style="max-width: 400px; margin-top: 100px;">
    <h1 style="text-align:center; color:#005BAC;">Ingreso al Sistema</h1>
    <form action="../src/auth/login.php" method="post" style="background:white; padding:20px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
      <div class="form-group">
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" id="usuario" required />
      </div>
      <div class="form-group">
        <label for="clave">Contraseña:</label>
        <input type="password" name="clave" id="clave" required />
      </div>
      <button type="submit" class="button" style="width:100%;">Entrar</button>

      <?php if($error): ?>
        <p style="color:red; margin-top:10px; text-align:center;"><?= $error ?></p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
