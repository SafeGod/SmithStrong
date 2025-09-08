<?php
session_start();

// Inicializar usuarios y historial si no existen
if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [];
}
if (!isset($_SESSION['historial'])) {
    $_SESSION['historial'] = [];
}

// Procesar formularios
if ($_POST) {
    if (isset($_POST['registrar'])) {
        $usuario = [
            'nombre' => $_POST['nombre'],
            'edad' => $_POST['edad'],
            'genero' => $_POST['genero'],
            'condiciones' => $_POST['condiciones']
        ];
        $_SESSION['usuarios'][$_POST['nombre']] = $usuario;
        $_SESSION['usuario_actual'] = $_POST['nombre'];
    }
    
    if (isset($_POST['calcular'])) {
        $nombre = $_POST['usuario'];
        $peso = floatval($_POST['peso']);
        $altura = floatval($_POST['altura']) / 100; // convertir cm a metros
        $imc = round($peso / ($altura * $altura), 2);
        
        $calculo = [
            'fecha' => date('Y-m-d H:i:s'),
            'peso' => $peso,
            'altura' => $_POST['altura'],
            'imc' => $imc
        ];
        
        if (!isset($_SESSION['historial'][$nombre])) {
            $_SESSION['historial'][$nombre] = [];
        }
        $_SESSION['historial'][$nombre][] = $calculo;
        $_SESSION['ultimo_imc'] = $imc;
        $_SESSION['usuario_actual'] = $nombre;
    }
    
    if (isset($_POST['seleccionar_usuario'])) {
        $_SESSION['usuario_actual'] = $_POST['usuario_seleccionado'];
    }
}

// Funciones para determinar categoría IMC y sugerencias
function categoriaIMC($imc) {
    if ($imc < 18.5) return ['categoria' => 'Bajo peso', 'clase' => 'warning'];
    if ($imc < 25) return ['categoria' => 'Peso normal', 'clase' => 'success'];
    if ($imc < 30) return ['categoria' => 'Sobrepeso', 'clase' => 'warning'];
    return ['categoria' => 'Obesidad', 'clase' => 'danger'];
}

function obtenerSugerencias($imc, $edad, $genero, $condiciones) {
    $sugerencias = ['dieta' => [], 'rutina' => []];
    
    // Sugerencias de dieta basadas en IMC
    if ($imc < 18.5) {
        $sugerencias['dieta'] = [
            'Aumentar ingesta calórica con alimentos nutritivos',
            'Incluir proteínas en cada comida (carnes, huevos, legumbres)',
            'Consumir grasas saludables (aguacate, frutos secos, aceite de oliva)',
            'Comer frecuentemente (5-6 comidas pequeñas al día)'
        ];
    } elseif ($imc < 25) {
        $sugerencias['dieta'] = [
            'Mantener una dieta equilibrada y variada',
            'Incluir frutas y verduras en cada comida',
            'Consumir proteínas magras (pollo, pescado, tofu)',
            'Mantener hidratación adecuada (8 vasos de agua al día)'
        ];
    } elseif ($imc < 30) {
        $sugerencias['dieta'] = [
            'Reducir porciones y controlar calorías',
            'Aumentar consumo de verduras y fibra',
            'Limitar azúcares refinados y grasas saturadas',
            'Optar por granos integrales en lugar de refinados'
        ];
    } else {
        $sugerencias['dieta'] = [
            'Consultar con nutricionista para plan personalizado',
            'Crear déficit calórico controlado',
            'Priorizar alimentos de bajo índice glucémico',
            'Evitar alimentos procesados y bebidas azucaradas'
        ];
    }
    
    // Sugerencias de rutina basadas en edad, género e IMC
    if ($edad < 30) {
        if ($genero === 'masculino') {
            $sugerencias['rutina'] = [
                'Entrenamiento de fuerza 4-5 veces por semana',
                'Ejercicios compuestos (sentadillas, press de banca, peso muerto)',
                'Cardio HIIT 2-3 veces por semana',
                'Descanso activo con deportes o actividades recreativas'
            ];
        } else {
            $sugerencias['rutina'] = [
                'Combinación de fuerza y cardio 4-5 veces por semana',
                'Pilates o yoga para flexibilidad y core',
                'Entrenamiento funcional con peso corporal',
                'Actividades cardiovasculares variadas (baile, natación)'
            ];
        }
    } elseif ($edad < 50) {
        $sugerencias['rutina'] = [
            'Entrenamiento de fuerza 3-4 veces por semana',
            'Ejercicios de movilidad y flexibilidad diarios',
            'Cardio moderado 30-45 minutos, 3-4 veces por semana',
            'Incluir ejercicios de equilibrio y coordinación'
        ];
    } else {
        $sugerencias['rutina'] = [
            'Entrenamiento de fuerza ligero 2-3 veces por semana',
            'Caminatas diarias de 30-45 minutos',
            'Ejercicios en agua (aqua aeróbicos)',
            'Yoga suave o tai chi para flexibilidad y balance'
        ];
    }
    
    // Ajustar por condiciones físicas
    if (strpos(strtolower($condiciones), 'lesion') !== false || 
        strpos(strtolower($condiciones), 'dolor') !== false) {
        $sugerencias['rutina'][] = 'IMPORTANTE: Consultar con fisioterapeuta antes de iniciar rutina';
        $sugerencias['rutina'][] = 'Evitar ejercicios que causen dolor';
    }
    
    return $sugerencias;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmithStrong - Calculadora IMC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-dumbbell"></i> SmithStrong Gym</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Registro de Usuario -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-user-plus"></i> Registro de Cliente</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="registroForm">
                            <div class="mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Edad</label>
                                <input type="number" class="form-control" name="edad" min="1" max="120" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Género</label>
                                <select class="form-select" name="genero" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Condiciones físicas especiales</label>
                                <textarea class="form-control" name="condiciones" rows="3" placeholder="Ej: lesiones, limitaciones, medicamentos..."></textarea>
                            </div>
                            <button type="submit" name="registrar" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Registrar Cliente
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Calculadora IMC -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-calculator"></i> Calculadora IMC</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="imcForm">
                            <div class="mb-3">
                                <label class="form-label">Cliente</label>
                                <select class="form-select" name="usuario" required>
                                    <option value="">Seleccionar cliente...</option>
                                    <?php foreach ($_SESSION['usuarios'] as $nombre => $datos): ?>
                                        <option value="<?php echo $nombre; ?>" 
                                                <?php echo (isset($_SESSION['usuario_actual']) && $_SESSION['usuario_actual'] == $nombre) ? 'selected' : ''; ?>>
                                            <?php echo $nombre; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Peso (kg)</label>
                                <input type="number" class="form-control" name="peso" step="0.1" min="1" max="300" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Altura (cm)</label>
                                <input type="number" class="form-control" name="altura" min="50" max="250" required>
                            </div>
                            <button type="submit" name="calcular" class="btn btn-success w-100">
                                <i class="fas fa-calculator"></i> Calcular IMC
                            </button>
                        </form>

                        <?php if (isset($_SESSION['ultimo_imc']) && isset($_SESSION['usuario_actual'])): ?>
                            <?php 
                            $imc = $_SESSION['ultimo_imc'];
                            $categoria = categoriaIMC($imc);
                            ?>
                            <div class="imc-result alert alert-<?php echo $categoria['clase']; ?>">
                                IMC: <?php echo $imc; ?><br>
                                <small><?php echo $categoria['categoria']; ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sugerencias -->
        <?php if (isset($_SESSION['ultimo_imc']) && isset($_SESSION['usuario_actual'])): ?>
            <?php 
            $usuario_actual = $_SESSION['usuarios'][$_SESSION['usuario_actual']];
            $sugerencias = obtenerSugerencias(
                $_SESSION['ultimo_imc'], 
                $usuario_actual['edad'], 
                $usuario_actual['genero'], 
                $usuario_actual['condiciones']
            );
            ?>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="suggestion-card">
                        <h5><i class="fas fa-utensils text-success"></i> Sugerencias de Dieta</h5>
                        <ul class="list-unstyled">
                            <?php foreach ($sugerencias['dieta'] as $sugerencia): ?>
                                <li><i class="fas fa-check-circle text-success"></i> <?php echo $sugerencia; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="suggestion-card">
                        <h5><i class="fas fa-dumbbell text-primary"></i> Rutina Recomendada</h5>
                        <ul class="list-unstyled">
                            <?php foreach ($sugerencias['rutina'] as $rutina): ?>
                                <li><i class="fas fa-check-circle text-primary"></i> <?php echo $rutina; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Historial -->
        <?php if (isset($_SESSION['usuario_actual']) && isset($_SESSION['historial'][$_SESSION['usuario_actual']])): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-history"></i> Historial de IMC - <?php echo $_SESSION['usuario_actual']; ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php 
                                $historial = array_reverse($_SESSION['historial'][$_SESSION['usuario_actual']]);
                                foreach ($historial as $registro): 
                                    $categoria = categoriaIMC($registro['imc']);
                                ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="history-item text-white">
                                            <div class="d-flex justify-content-between">
                                                <small><?php echo date('d/m/Y H:i', strtotime($registro['fecha'])); ?></small>
                                                <span class="badge bg-<?php echo $categoria['clase']; ?>">
                                                    <?php echo $categoria['categoria']; ?>
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                <strong>IMC: <?php echo $registro['imc']; ?></strong><br>
                                                <small>Peso: <?php echo $registro['peso']; ?> kg | Altura: <?php echo $registro['altura']; ?> cm</small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de Usuarios -->
        <?php if (!empty($_SESSION['usuarios'])): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-users"></i> Clientes Registrados</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="mb-3">
                                <div class="input-group">
                                    <select class="form-select" name="usuario_seleccionado">
                                        <option value="">Seleccionar cliente para ver historial...</option>
                                        <?php foreach ($_SESSION['usuarios'] as $nombre => $datos): ?>
                                            <option value="<?php echo $nombre; ?>"><?php echo $nombre; ?> (<?php echo $datos['edad']; ?> años)</option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="seleccionar_usuario" class="btn btn-outline-primary">Ver Historial</button>
                                </div>
                            </form>
                            
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Edad</th>
                                            <th>Género</th>
                                            <th>Registros IMC</th>
                                            <th>Último IMC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($_SESSION['usuarios'] as $nombre => $datos): ?>
                                            <tr>
                                                <td><?php echo $nombre; ?></td>
                                                <td><?php echo $datos['edad']; ?> años</td>
                                                <td><?php echo ucfirst($datos['genero']); ?></td>
                                                <td>
                                                    <?php 
                                                    $count = isset($_SESSION['historial'][$nombre]) ? count($_SESSION['historial'][$nombre]) : 0;
                                                    echo $count . ' registro(s)';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (isset($_SESSION['historial'][$nombre]) && !empty($_SESSION['historial'][$nombre])) {
                                                        $ultimo = end($_SESSION['historial'][$nombre]);
                                                        $categoria = categoriaIMC($ultimo['imc']);
                                                        echo '<span class="badge bg-' . $categoria['clase'] . '">' . $ultimo['imc'] . '</span>';
                                                    } else {
                                                        echo '<span class="text-muted">Sin registros</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

</body>
</html>