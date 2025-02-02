<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $status = trim($data['status']);
    $order = $data['order'];

    if (empty($status)) {
        echo json_encode(["success" => false, "error" => "Status inválido"]);
        exit();
    }

    try {
        if (empty($order)) {
            echo json_encode(["success" => true, "message" => "Nenhum card para atualizar"]);
            exit();
        }

        foreach ($order as $item) {
            $stmt = $conn->prepare("UPDATE tbl_list SET position = :position WHERE tbl_list_id = :id AND status = :status");
            $stmt->bindParam(":position", $item['position'], PDO::PARAM_INT);
            $stmt->bindParam(":id", $item['id'], PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();
        }

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>