document.addEventListener('DOMContentLoaded', () => {
    const tarjeta = document.querySelector('.tarjeta-animada');
    
    if (tarjeta) {
        // Un pequeño retraso para que la animación sea perceptible
        setTimeout(() => {
            tarjeta.classList.add('visible');
            console.log("Script de Nosotros ejecutado correctamente.");
        }, 300);
    }
});