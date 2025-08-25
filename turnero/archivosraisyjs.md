index.html<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Turnero - Inicio</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="site-header">
    <div class="container">
      <a href="index.html" class="logo">
        <img src="assets/logo/gigared_logo.png" alt="Gigared" height="40" />
      </a>
      <nav class="main-nav">
        <ul>
          <li><a href="index.html">Inicio</a></li>
          <li><a href="sacar_turno.html">Sacar Turno</a></li>
          <li><a href="panel_operadores.html">Panel Operadores</a></li>
          <li><a href="reporte_operador.html">Reportes</a></li>
          <li><a href="login.html">Administración</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="hero">
      <h1>Bienvenido al Sistema de Turnos</h1>
      <p>Gestione y consulte sus turnos de forma ágil y sencilla.</p>
      <a href="sacar_turno.html" class="button">Sacar Turno</a>
    </section>
    <section class="features">
      <div class="feature-card">
        <h2>Control de Operadores</h2>
        <p>Administre puestos y usuarios autorizados.</p>
      </div>
      <div class="feature-card">
        <h2>Panel de Atención</h2>
        <p>Llame y derive turnos en tiempo real.</p>
      </div>
      <div class="feature-card">
        <h2>Reportes</h2>
        <p>Visualice estadísticas y exporte datos.</p>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>&copy; 2025 Gigared. Todos los derechos reservados.</p>
      <div class="social-links">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>
  </footer>

  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="js/main.js"></script>
</body>
</html>
.htaccess # turnero/.htaccess

RewriteEngine On

# Bloquear acceso directo a carpetas sensibles
RewriteRule ^(src|config|logs)(/|$) - [F,L]

# Redirigir todas las peticiones que no estén en /public/ hacia /public/
RewriteCond %{REQUEST_URI} !^/turnero/public/
RewriteRule ^(.*)$ /turnero/public/$1 [L,NC]

# Evitar listado de directorios
Options -Indexes

# Páginas de error personalizadas
ErrorDocument 403 /turnero/public/403.html
ErrorDocument 404 /turnero/public/404.html
js\main.js
document.addEventListener('DOMContentLoaded', function () {
    cargarMotivos();

    document.getElementById('form-turno').addEventListener('submit', function (e) {
        e.preventDefault();
        solicitarTurno();
    });
});

function cargarMotivos() {
    fetch('../../src/admin/get_motivos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selectMotivos = document.getElementById('motivo');
                selectMotivos.innerHTML = '';

                data.motivos.forEach(motivo => {
                    const option = document.createElement('option');
                    option.value = motivo.id;
                    option.textContent = motivo.nombre;
                    selectMotivos.appendChild(option);
                });
            } else {
                alert('Error al cargar los motivos');
            }
        })
        .catch(error => {
            console.error('Error al obtener los motivos:', error);
        });
}

function solicitarTurno() {
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const motivo = document.getElementById('motivo').value;

    if (!nombre || !apellido || !motivo) {
        alert('Por favor complete todos los campos.');
        return;
    }

    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('apellido', apellido);
    formData.append('motivo', motivo);

    fetch('../../src/turno/crear_turno.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const resultado = document.getElementById('resultado-turno');
        if (data.success) {
            resultado.innerHTML = `<p>Turno generado: <strong>${data.turno}</strong></p>`;
            document.getElementById('form-turno').reset();
        } else {
            resultado.textContent = 'Error al generar el turno: ' + data.message;
        }
    })
    .catch(error => {
        console.error('Error al solicitar el turno:', error);
    });
}
js\operador.js // public/js/operador.js
// Lógica para el panel de operadores: cargar, llamar, derivar y finalizar turnos

document.addEventListener('DOMContentLoaded', () => {
  cargarTurnos();
  configurarModales();
  setInterval(cargarTurnos, 10000); // refrescar cada 10s
});

let turnoSeleccionado = null;

async function cargarTurnos() {
  try {
    const res = await fetch('../../src/turno/obtener_pendientes.php');
    const data = await res.json();
    const body = document.getElementById('turnos-body');
    body.innerHTML = '';

    if (data.success) {
      data.turnos.forEach(t => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${t.prefijo}${String(t.numero_correlativo).padStart(3, '0')}</td>
          <td>${t.nombre} ${t.apellido}</td>
          <td>${t.motivo}</td>
          <td>${t.minutos_espera}</td>
          <td>
            <button class="button" onclick="llamarTurno(${t.id})">Llamar</button>
            <button class="button" onclick="abrirDerivar(${t.id})">Derivar</button>
            <button class="button" onclick="abrirFinalizar(${t.id})">Finalizar</button>
          </td>
        `;
        body.appendChild(tr);
      });
    } else {
      console.error('Error al cargar turnos:', data.message);
    }
  } catch (e) {
    console.error('Error en fetch de turnos:', e);
  }
}

async function llamarTurno(id) {
  try {
    const res = await fetch('../../src/turno/llamar_turno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ turno_id: id })
    });
    const data = await res.json();
    if (data.success) {
      cargarTurnos();
    } else {
      alert('Error al llamar turno: ' + data.message);
    }
  } catch (e) {
    console.error('Error al llamar turno:', e);
  }
}

// --- Derivar Turno ---
function abrirDerivar(id) {
  turnoSeleccionado = id;
  // Cargar puestos de destino
  fetch('../../src/admin/get_puestos.php')
    .then(r => r.json())
    .then(js => {
      const select = document.getElementById('select-puesto-derivar');
      select.innerHTML = '';
      js.puestos.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.nombre;
        select.appendChild(opt);
      });
      document.getElementById('modal-derivar').style.display = 'block';
    });
}

document.getElementById('btn-confirm-derivar').addEventListener('click', async () => {
  const destino = document.getElementById('select-puesto-derivar').value;
  try {
    const res = await fetch('../../src/turno/derivar_turno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ turno_id: turnoSeleccionado, puesto_destino_id: destino })
    });
    const data = await res.json();
    alert(data.message);
    cerrarModal('modal-derivar');
    cargarTurnos();
  } catch (e) {
    console.error('Error al derivar turno:', e);
  }
});

document.getElementById('close-derivar').addEventListener('click', () => cerrarModal('modal-derivar'));

// --- Finalizar Turno ---
function abrirFinalizar(id) {
  turnoSeleccionado = id;
  document.getElementById('modal-finalizar').style.display = 'block';
}

document.getElementById('btn-confirm-finalizar').addEventListener('click', async () => {
  const resultado = document.getElementById('select-resultado').value;
  const observaciones = document.getElementById('observaciones').value.trim();
  try {
    const res = await fetch('../../src/turno/finalizar_turno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ turno_id: turnoSeleccionado, resultado, observaciones })
    });
    const data = await res.json();
    alert(data.message);
    cerrarModal('modal-finalizar');
    cargarTurnos();
  } catch (e) {
    console.error('Error al finalizar turno:', e);
  }
});

document.getElementById('close-finalizar').addEventListener('click', () => cerrarModal('modal-finalizar'));

// Funciones de utilidades y modales
function cerrarModal(id) {
  document.getElementById(id).style.display = 'none';
}

function configurarModales() {
  window.onclick = function(event) {
    ['modal-derivar','modal-finalizar'].forEach(id => {
      const modal = document.getElementById(id);
      if (event.target === modal) modal.style.display = 'none';
    });
  };
}
js\tv.js// public/js/tv.js
// Lógica para la pantalla TV: actualiza turno y clima automáticamente

document.addEventListener('DOMContentLoaded', () => {
  let ultimoMostrado = null;

  // Actualizar turno llamado cada 10s
  async function actualizarTurno() {
    try {
      const res = await fetch('../../src/turno/ultimo_turno_llamado.php');
      const data = await res.json();
      if (data.success && data.turno) {
        const turno = data.turno;
        if (turno.numero_turno !== ultimoMostrado) {
          ultimoMostrado = turno.numero_turno;
          mostrarTurno(turno);
          mostrarAlerta(`Llamado ${turno.numero_turno}: ${turno.nombre} ${turno.apellido}`);
        }
      }
    } catch (e) {
      console.error('Error al obtener último turno:', e);
    }
  }

  // Mostrar datos del turno en la pantalla
  function mostrarTurno(turno) {
    document.getElementById('turno-numero').textContent = turno.numero_turno;
    document.getElementById('turno-nombre').textContent = `${turno.nombre} ${turno.apellido}`;
    document.getElementById('turno-motivo').textContent = turno.motivo;
  }

  // Mostrar alerta emergente
  function mostrarAlerta(mensaje) {
    const alerta = document.getElementById('alerta');
    alerta.textContent = mensaje;
    alerta.classList.add('mostrar');
    setTimeout(() => {
      alerta.classList.remove('mostrar');
    }, 5000);
  }

  // Obtener clima
  async function actualizarClima() {
    try {
      const res = await fetch('https://api.open-meteo.com/v1/forecast?latitude=-31.74&longitude=-60.51&current_weather=true');
      const json = await res.json();
      const weather = json.current_weather;
      document.getElementById('weather-info').textContent = `Temperatura: ${weather.temperature}°C | Viento: ${weather.windspeed} km/h`;
    } catch (e) {
      console.error('Error al obtener clima:', e);
      document.getElementById('weather-info').textContent = 'No disponible';
    }
  }

  // Iniciar ciclos
  actualizarTurno();
  setInterval(actualizarTurno, 10000);
  actualizarClima();
  setInterval(actualizarClima, 600000); // cada 10 min
});
