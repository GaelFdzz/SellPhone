document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const usuarioInput = document.querySelector('#usuario');
    const comentarioInput = document.querySelector('#comentario');
    const calificacionSelect = document.querySelector('#calificacion');
    const calificacionPreview = document.querySelector('#calificacionPreview');
    const errorMessage = document.createElement('p');
    errorMessage.style.color = 'red';

    // Mostrar mensaje de error
    function showError(message) {
        errorMessage.textContent = message;
        if (!form.contains(errorMessage)) {
            form.insertBefore(errorMessage, form.firstChild);
        }
    }

    // Limpiar mensaje de error
    function clearError() {
        if (form.contains(errorMessage)) {
            form.removeChild(errorMessage);
        }
    }

    // Validar formulario antes de enviar
    form.addEventListener('submit', (event) => {
        clearError();
        
        const usuario = usuarioInput.value.trim();
        const comentario = comentarioInput.value.trim();
        const calificacion = calificacionSelect.value;

        if (!usuario) {
            showError('El nombre es obligatorio.');
            event.preventDefault();
            return;
        }

        if (!comentario) {
            showError('El comentario es obligatorio.');
            event.preventDefault();
            return;
        }

        if (!calificacion || isNaN(calificacion) || calificacion < 1 || calificacion > 5) {
            showError('La calificación debe estar entre 1 y 5.');
            event.preventDefault();
            return;
        }

        // Mostrar vista previa de calificación
        calificacionPreview.textContent = `Calificación seleccionada: ${calificacion}`;
    });

    // Mostrar vista previa de calificación al seleccionar
    calificacionSelect.addEventListener('change', () => {
        const selectedValue = calificacionSelect.value;
        if (selectedValue) {
            calificacionPreview.textContent = `Calificación seleccionada: ${selectedValue}`;
        } else {
            calificacionPreview.textContent = '';
        }
    });
});
