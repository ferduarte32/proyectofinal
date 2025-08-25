public\css\styletv.css/* public/css/styletv.css */

.container {
  display: flex;
  height: 100vh;
  background-color: #F8F9FA;
}
.left-panel {
  flex: 1;
  background: white;
  padding: 40px;
  border-right: 5px solid #005BAC;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.left-panel .logo {
  margin-bottom: 20px;
}
.left-panel h2 {
  color: #005BAC;
  margin-bottom: 20px;
}
.turno-numero {
  font-size: 4rem;
  font-weight: 700;
  color: #333333;
  margin-bottom: 10px;
}
.turno-nombre {
  font-size: 2rem;
  font-weight: 600;
  color: #005BAC;
  margin-bottom: 5px;
}
.turno-motivo {
  font-size: 1.2rem;
  color: #333333;
}
.right-panel {
  flex: 1;
  padding: 40px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.card {
  background: white;
  border: 1px solid #DDDDDD;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.card h3 {
  color: #005BAC;
  margin-bottom: 10px;
}
.alerta {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #E5007A;
  color: white;
  padding: 25px 40px;
  font-size: 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  z-index: 1000;
  opacity: 0;
  transition: opacity 0.5s ease;
}
.alerta.mostrar {
  opacity: 1;
}
/* public/css/style.css */

/* Fuente y reset básico */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  font-family: 'Poppins', sans-serif;
  background-color: #F8F9FA;
  color: #333333;
  line-height: 1.6;
}
.container {
  width: 90%;
  max-width: 1200px;
  margin: 0 auto;
}
.site-header {
  background-color: #005BAC;
  padding: 10px 0;
}
.site-header .logo img {
  height: 40px;
}
.main-nav ul {
  list-style: none;
  display: flex;
  gap: 20px;
}
.main-nav a {
  color: white;
  text-decoration: none;
  font-weight: 600;
}
.main-nav a.active,
.main-nav a:hover {
  color: #E5007A;
}
.hero {
  text-align: center;
  padding: 60px 0;
}
.hero h1 {
  font-size: 2.5rem;
  color: #005BAC;
  margin-bottom: 10px;
}
.hero p {
  font-size: 1.2rem;
  margin-bottom: 20px;
}
.hero .button {
  background-color: #005BAC;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin: 40px 0;
}
.feature-card {
  background: white;
  border: 1px solid #DDDDDD;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.feature-card h2 {
  color: #005BAC;
  margin-bottom: 10px;
}
.button {
  background-color: #005BAC;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px 15px;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.button:hover {
  background-color: #004A90;
}
.table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
}
.table th,
.table td {
  border: 1px solid #DDDDDD;
  padding: 12px;
}
.table th {
  background-color: #005BAC;
  color: white;
  font-weight: 600;
}
.form-group {
  margin-bottom: 15px;
}
.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 5px;
}
.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #DDDDDD;
  border-radius: 4px;
}
.site-footer {
  background-color: #333333;
  color: white;
  text-align: center;
  padding: 20px 0;
  margin-top: 40px;
}
.site-footer .social-links a {
  color: white;
  margin: 0 10px;
  font-size: 1.2rem;
}

public\403.html<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>403 Prohibido</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; text-align: center; padding: 50px; }
    h1 { font-size: 4rem; color: #E5007A; }
    p  { font-size: 1.2rem; }
    a.button {
      display: inline-block; margin-top: 20px;
      background-color: #005BAC; color: white;
      padding: 10px 20px; text-decoration: none;
      border-radius: 4px;
    }
    a.button:hover { background-color: #004A90; }
  </style>
</head>
<body>
  <h1>403 Prohibido</h1>
  <p>No tienes permiso para acceder a esta página.</p>
  <a href="/turnero/public/index.html" class="button">Ir al inicio</a>
</body>
</html>
public\404.html <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>404 No Encontrado</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; text-align: center; padding: 50px; }
    h1 { font-size: 4rem; color: #005BAC; }
    p  { font-size: 1.2rem; }
    a.button {
      display: inline-block; margin-top: 20px;
      background-color: #005BAC; color: white;
      padding: 10px 20px; text-decoration: none;
      border-radius: 4px;
    }
    a.button:hover { background-color: #004A90; }
  </style>
</head>
<body>
  <h1>404 Página No Encontrada</h1>
  <p>Lo sentimos, la página que buscas no existe.</p>
  <a href="/turnero/public/index.html" class="button">Volver al inicio</a>
</body>
</html>
public\login.php<?php
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
public\panel_operadores.html <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel de Operadores</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="site-header">
    <div class="container">
      <a href="index.html" class="logo"><img src="assets/logo/gigared_logo.png" alt="Gigared" height="40"/></a>
      <nav class="main-nav">
        <ul>
          <li><a href="index.html">Inicio</a></li>
          <li><a href="sacar_turno.html">Sacar Turno</a></li>
          <li><a href="panel_operadores.html" class="active">Panel Operadores</a></li>
          <li><a href="reporte_operador.html">Reportes</a></li>
          <li><a href="admin/gestion_puestos.php">Administración</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container">
    <h1>Panel de Operadores</h1>
    <table class="table" id="tabla-turnos">
      <thead>
        <tr>
          <th># Turno</th>
          <th>Nombre</th>
          <th>Motivo</th>
          <th>Espera (min)</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="turnos-body">
        <!-- Filas dinámicas -->
      </tbody>
    </table>

    <!-- Modal para derivar turno -->
    <div id="modal-derivar" class="modal">
      <div class="modal-content">
        <span class="close" id="close-derivar">&times;</span>
        <h2>Derivar Turno</h2>
        <label for="select-puesto-derivar">Seleccione puesto destino:</label>
        <select id="select-puesto-derivar"></select>
        <button id="btn-confirm-derivar" class="button">Confirmar</button>
      </div>
    </div>

    <!-- Modal para finalizar turno -->
    <div id="modal-finalizar" class="modal">
      <div class="modal-content">
        <span class="close" id="close-finalizar">&times;</span>
        <h2>Finalizar Turno</h2>
        <label for="select-resultado">Resultado:</label>
        <select id="select-resultado">
          <option value="cumplido">Cumplido</option>
          <option value="pendiente">Pendiente</option>
          <option value="cancelado">Cancelado</option>
          <option value="otro">Otro</option>
        </select>
        <textarea id="observaciones" placeholder="Observaciones (opcional)"></textarea>
        <button id="btn-confirm-finalizar" class="button">Finalizar</button>
      </div>
    </div>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>&copy; 2025 Gigared. Todos los derechos reservados.</p>
    </div>
  </footer>

  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="js/operador.js"></script>
</body>
</html>
public\pantalla_tv.html <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pantalla TV - Turnos</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/styletv.css" />
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <img src="assets/logo/gigared_logo.png" alt="Logo" class="logo" />
      <h2>Turno Actual</h2>
      <div id="turno-numero" class="turno-numero">--</div>
      <div id="turno-nombre" class="turno-nombre">Esperando...</div>
      <div id="turno-motivo" class="turno-motivo"></div>
    </div>
    <div class="right-panel">
      <div class="card clima">
        <h3>Clima en tu ciudad</h3>
        <div id="weather-info">Cargando...</div>
      </div>
      <div class="card video">
        <iframe id="video-institucional" width="100%" height="315" src="https://www.youtube.com/embed/tgbNymZ7vqY" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </div>

  <div id="alerta" class="alerta"></div>

  <script src="js/tv.js"></script>
</body>
</html>
public\sacar_turno.html <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sacar Turno</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="site-header">
    <div class="container">
      <a href="index.html" class="logo"><img src="assets/logo/gigared_logo.png" alt="Gigared" height="40"/></a>
      <nav class="main-nav">
        <ul>
          <li><a href="index.html">Inicio</a></li>
          <li><a href="sacar_turno.html" class="active">Sacar Turno</a></li>
          <li><a href="panel_operadores.html">Panel Operadores</a></li>
          <li><a href="reporte_operador.html">Reportes</a></li>
          <li><a href="admin/gestion_puestos.php">Administración</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container">
    <h1>Sacar Turno</h1>
    <form id="formTurno">
      <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required />
      </div>
      <div class="form-group">
        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required />
      </div>
      <div class="form-group">
        <label for="motivo">Motivo:</label>
        <select id="motivo" name="motivo" required>
          <option value="">Seleccione motivo</option>
        </select>
      </div>
      <button type="submit" class="button">Sacar turno</button>
    </form>
    <div id="resultado" class="result"></div>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>&copy; 2025 Gigared. Todos los derechos reservados.</p>
    </div>
  </footer>

  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="js/main.js"></script>
</body>
</html>
public\test_hash.php <?php
$hash = '$2y$10$NOnJK0ZQAUNejd4URF/ztuYVaaH4wNO4Unv5Yp4VFb5osn9JpZw0G';
var_dump(password_verify('fer32', $hash));
