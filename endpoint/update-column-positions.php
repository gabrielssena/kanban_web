<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order = $data['order'];

    if (empty($order)) {
        echo json_encode(["success" => false, "error" => "Dados inválidos"]);
        exit();
    }

    try {
        $conn->beginTransaction();

        foreach ($order as $index => $status) {
            $stmt = $conn->prepare("UPDATE tbl_columns SET position = :position WHERE status = :status");
            $stmt->bindParam(":position", $index, PDO::PARAM_INT);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();
        }

        $conn->commit();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>