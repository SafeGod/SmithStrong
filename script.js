// Validación del formulario
document.getElementById('registroForm').addEventListener('submit', function (e) {
    const nombre = this.nombre.value.trim();
    if (nombre.length < 2) {
        e.preventDefault();
        alert('El nombre debe tener al menos 2 caracteres');
        return;
    }
});

document.getElementById('imcForm').addEventListener('submit', function (e) {
    const peso = parseFloat(this.peso.value);
    const altura = parseFloat(this.altura.value);

    if (peso < 1 || peso > 300) {
        e.preventDefault();
        alert('El peso debe estar entre 1 y 300 kg');
        return;
    }

    if (altura < 50 || altura > 250) {
        e.preventDefault();
        alert('La altura debe estar entre 50 y 250 cm');
        return;
    }
});

// Efectos visuales
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate__animated', 'animate__fadeInUp');
    });
});

// Calculadora en tiempo real
function calcularIMCTiempoReal() {
    const pesoInput = document.querySelector('input[name="peso"]');
    const alturaInput = document.querySelector('input[name="altura"]');

    if (pesoInput && alturaInput) {
        const peso = parseFloat(pesoInput.value);
        const altura = parseFloat(alturaInput.value) / 100;

        if (peso > 0 && altura > 0) {
            const imc = peso / (altura * altura);
            console.log('IMC calculado:', imc.toFixed(2));
        }
    }
}

// Event listeners para cálculo en tiempo real
document.addEventListener('input', function (e) {
    if (e.target.name === 'peso' || e.target.name === 'altura') {
        calcularIMCTiempoReal();
    }
});