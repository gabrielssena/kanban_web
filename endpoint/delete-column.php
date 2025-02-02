<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $status = trim($data['status']);

    if (empty($status)) {
        echo json_encode(["success" => false, "error" => "Status inválido"]);
        exit();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM tbl_list WHERE status = :status");
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM tbl_columns WHERE status = :status");
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>