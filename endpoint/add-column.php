<?php
include("../conn/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['column_name']) && !empty(trim($_POST['column_name']))) {
        $columnName = trim($_POST['column_name']);
        $status = strtolower(preg_replace('/\s+/', '-', $columnName));

        $stmt = $conn->prepare("SELECT MAX(position) as max_position FROM tbl_columns");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $position = $result['max_position'] !== null ? $result['max_position'] + 1 : 0;

        try {
            $stmt = $conn->prepare("INSERT INTO tbl_columns (column_name, status, position) VALUES (:column_name, :status, :position)");
            $stmt->bindParam(":column_name", $columnName, PDO::PARAM_STR);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->bindParam(":position", $position, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                "success" => true,
                "column" => [
                    "id" => $conn->lastInsertId(),
                    "column_name" => $columnName,
                    "status" => $status,
                    "position" => $position
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Por favor, insira um nome para a coluna"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>