<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $listId = $data['id'];

    if (!is_numeric($listId)) {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
        exit();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM tbl_list WHERE tbl_list_id = :list");
        $stmt->bindParam(":list", $listId, PDO::PARAM_INT);
        $query_execute = $stmt->execute();

        if ($query_execute) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha ao excluir o card']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
}
?>