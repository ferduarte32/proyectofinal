// public/js/main.js

document.addEventListener('DOMContentLoaded', function () {
    cargarMotivos();

    document.getElementById('form-turno').addEventListener('submit', function (e) {
        e.preventDefault();
        solicitarTurno();
    });
});

function cargarMotivos() {
    fetch('../src/admin/get_motivos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('motivo');
                select.innerHTML = '<option value="">Seleccione motivo</option>';
                data.motivos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nombre;
                    select.appendChild(opt);
                });
            } else {
                alert('Error al cargar motivos');
            }
        })
        .catch(error => {
            console.error('Error al obtener los motivos:', error);
        });
}

function solicitarTurno() {
    const nombre   = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const motivo   = document.getElementById('motivo').value;

    if (!nombre || !apellido || !motivo) {
        alert('Por favor complete todos los campos.');
        return;
    }

    fetch('../src/turno/crear_turno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre, apellido, motivo_id: motivo })
    })
    .then(response => response.json())
    .then(data => {
        const out = document.getElementById('resultado');
        if (data.success) {
            out.innerHTML = `<p>Turno generado: <strong>${data.numero_turno}</strong></p>`;
            document.getElementById('form-turno').reset();
        } else {
            out.textContent = 'Error al generar el turno: ' + data.message;
        }
    })
    .catch(error => {
        console.error('Error al solicitar el turno:', error);
    });
}
