// public/js/operador.js
// Lógica para el panel de operadores: cargar, llamar, derivar y finalizar turnos

document.addEventListener('DOMContentLoaded', () => {
  cargarTurnos();
  configurarModales();
  setInterval(cargarTurnos, 10000); // refrescar cada 10s
});

let turnoSeleccionado = null;

async function cargarTurnos() {
  try {
    const res = await fetch('/turnero/src/turno/obtener_pendientes.php', { credentials: 'include' });
    const data = await res.json();
    const body = document.getElementById('turnos-body');
    body.innerHTML = '';

    if (data.success) {
      data.turnos.forEach(t => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
  <td>${t.numero_turno}</td>
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
    const res = await fetch('/turnero/src/turno/llamar_turno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
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
  fetch('/turnero/src/admin/get_puestos.php', { credentials: 'include' })
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
    const res = await fetch('/turnero/src/turno/derivar_turno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
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
    const res = await fetch('/turnero/src/turno/finalizar_turno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
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
  window.onclick = function (event) {
    ['modal-derivar', 'modal-finalizar'].forEach(id => {
      const modal = document.getElementById(id);
      if (event.target === modal) modal.style.display = 'none';
    });
  };
}
