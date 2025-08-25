// public/js/tv.js
// Lógica para la pantalla TV: actualiza turno y clima automáticamente

document.addEventListener('DOMContentLoaded', () => {
  let ultimoMostrado = null;

  // Actualizar turno llamado cada 10s
  async function actualizarTurno() {
    try {
      const res = await fetch('/turnero/src/turno/ultimo_turno_llamado.php');
      const data = await res.json(); // <-- faltaba esto
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
    document.getElementById('turno-puesto').textContent = `Puesto: ${turno.puesto}`;
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
      document.getElementById('weather-info').textContent =
        `Temperatura: ${weather.temperature}°C | Viento: ${weather.windspeed} km/h`;
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
