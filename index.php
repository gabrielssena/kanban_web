<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <title>Kanban Board</title>
</head>
<body>
    <div class="main">
        <h1>Kanban</h1>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button id="add-column-btn" class="btn btn-success">Adicionar Coluna</button>
        </div>
        <div class="todo-container">
            <?php
            include('./conn/conn.php');

            $stmt = $conn->prepare("SELECT * FROM tbl_columns ORDER BY position ASC");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($columns as $column):
                $status = htmlspecialchars($column['status']);
                $columnName = htmlspecialchars($column['column_name']);
            ?>
                <div class="status" id="<?= $status ?>">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <button class="btn btn-sm btn-secondary" onclick="moveColumnLeft('<?= $status ?>')">⬅️</button>
                            <h2><?= $columnName ?></h2>
                            <button class="btn btn-sm btn-secondary" onclick="moveColumnRight('<?= $status ?>')">➡️</button>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="deleteColumn('<?= $status ?>')">Excluir</button>
                    </div>
                    <button class="btn btn-dark form-control" onclick="addCard('<?= $status ?>')">Adicionar Card</button>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM tbl_list WHERE status = :status ORDER BY position ASC");
                    $stmt->bindParam(":status", $column['status'], PDO::PARAM_STR);
                    $stmt->execute();
                    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($cards as $card):
                        $cardId = htmlspecialchars($card['tbl_list_id']);
                        $cardTitle = htmlspecialchars($card['list']);
                    ?>
                        <div class="todo" draggable="true" data-id="<?= $cardId ?>">
                            <input type="hidden" id="listId-<?= $cardId ?>" value="<?= $cardId ?>">
                            <span id="list-<?= $cardId ?>"><?= $cardTitle ?></span>
                            <span class="close" onclick="deleteList(<?= $cardId ?>)">&times;</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="./javascript/script.js"></script>
</body>
</html>