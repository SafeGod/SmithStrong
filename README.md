# SmithStrong

# Calculadora IMC - SmithStrong Gym

Ejercicio del **Taller 1.3** - Simulación en Grupos

## Qué hace

Una calculadora de IMC para gimnasios que:
- Registra clientes con sus datos personales
- Calcula el IMC (Peso/Altura²) 
- Da sugerencias de dieta y rutina según edad, género e IMC
- Guarda historial de cálculos por cliente

## Tecnologías usadas

- **PHP** (sin base de datos, usa sesiones)
- **Bootstrap 5** (diseño responsive)
- **JavaScript** (validaciones y efectos)

## Archivos

```
📁 proyecto/
├── index.php          # Archivo principal
├── styles.css         # Estilos personalizados  
├── script.js          # JavaScript
└── README.md          # Este archivo
```

## Cómo usar

1. Clona el repositorio
2. Pon los archivos en tu servidor local (XAMPP/WAMP/MAMP)
3. Abre `http://localhost/proyecto/index.php`
4. Registra clientes y calcula sus IMCs

## Funcionalidades

### Registro de Clientes
- Nombre, edad, género
- Condiciones físicas especiales

### Calculadora IMC
- Selecciona cliente
- Ingresa peso (kg) y altura (cm)
- Calcula automáticamente el IMC
- Muestra categoría (Bajo peso, Normal, Sobrepeso, Obesidad)

### Sugerencias Personalizadas
- **Dietas** según el IMC calculado
- **Rutinas** según edad, género e IMC
- Consideraciones especiales por condiciones físicas

### Historial
- Guarda todos los cálculos por cliente
- Muestra fecha, peso, altura e IMC
- Se puede consultar por cliente

## Nota

Los datos se guardan en sesiones PHP, así que se pierden al cerrar el navegador o reiniciar el servidor.