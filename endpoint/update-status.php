<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'], $data['status']) || !is_numeric($data['id']) || !is_string($data['status'])) {
        echo json_encode(["success" => false, "error" => "Dados inválidos"]);
        exit();
    }

    $id = $data['id'];
    $status = $data['status'];

    try {
        $stmt = $conn->prepare("UPDATE tbl_list SET status = :status WHERE tbl_list_id = :id");
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>