<?php
$filename = 'estadoMapa.json';

// Manejar la lectura
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($filename)) {
        header('Content-Type: application/json');
        echo file_get_contents($filename);
    } else {
        echo json_encode(['queue' => []]);
    }
}

// Manejar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Agregar un nuevo ID a la cola
    if (isset($data['estadoId'])) {
        // Leer el contenido actual
        $jsonData = json_decode(file_get_contents($filename), true);

        // Verificar si el ID ya está en la cola
        if (in_array($data['estadoId'], $jsonData['queue'])) {
            // Si el ID ya existe, no lo agregamos
            echo json_encode(['success' => false, 'message' => 'El ID ya está en la cola']);
            return; // Termina la ejecución aquí
        }

        // Agregar el nuevo estado a la cola
        $jsonData['queue'][] = $data['estadoId'];

        // Guardar la cola actualizada
        file_put_contents($filename, json_encode($jsonData));
        echo json_encode(['success' => true, 'message' => $data['estadoId']]);
    } 
    // Obtener y eliminar el primer ID en la cola
    else if (isset($data['get'])) {
        $jsonData = json_decode(file_get_contents($filename), true);
        if (count($jsonData['queue']) > 0) {
            $estadoId = array_shift($jsonData['queue']); // Obtener el primer ID
            // Guardar la cola actualizada
            file_put_contents($filename, json_encode($jsonData));
            echo json_encode(['success' => true, 'estadoId' => $estadoId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cola vacía']);
        }
    } 
    // Vaciar la cola completamente
    else if (isset($data['clear'])) {
        file_put_contents($filename, json_encode(['queue' => []])); // Reiniciar la cola
        echo json_encode(['success' => true, 'message' => 'Cola vaciada']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Solicitud no válida']);
    }
}
?>