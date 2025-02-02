<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list = trim($_POST['list']);
    $status = trim($_POST['status']);

    if (empty($list) || empty($status)) {
        echo json_encode(["success" => false, "error" => "Por favor, preencha todos os campos"]);
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM tbl_list WHERE status = :status");
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $position = $result['count'];

        $stmt = $conn->prepare("INSERT INTO tbl_list (list, status, position) VALUES (:list, :status, :position)");
        $stmt->bindParam(":list", $list, PDO::PARAM_STR);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);
        $stmt->bindParam(":position", $position, PDO::PARAM_INT);
        $stmt->execute();

        $lastInsertId = $conn->lastInsertId();
        echo json_encode([
            "success" => true,
            "card" => [
                "tbl_list_id" => $lastInsertId,
                "list" => $list,
                "status" => $status,
            ],
        ]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>