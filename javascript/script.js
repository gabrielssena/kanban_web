document.addEventListener("DOMContentLoaded", () => {
    setupDragAndDrop(); 
    document.getElementById("add-column-btn").addEventListener("click", addColumn);
});

let draggableTodo = null;
let originColumnId = null;


function setupDragAndDrop() {
    document.querySelectorAll(".todo").forEach(setupDragAndDropForElement);
    document.querySelectorAll(".status").forEach(setupDropEventsForColumn);
}

function setupDragAndDropForElement(element) {
    element.addEventListener("dragstart", cardDragStart);
    element.addEventListener("dragend", cardDragEnd);
}

function setupDropEventsForColumn(column) {
    column.addEventListener("dragover", cardDragOver);
    column.addEventListener("dragenter", cardDragEnter);
    column.addEventListener("dragleave", cardDragLeave);
    column.addEventListener("drop", cardDragDrop);
}

function cardDragStart(e) {
    draggableTodo = this;
    originColumnId = this.parentElement ? this.parentElement.getAttribute("id") : null;
    this.classList.add("dragging");
    setTimeout(() => {
        this.style.display = "none";
    }, 0);
}

function cardDragEnd(e) {
    draggableTodo = null;
    this.classList.remove("dragging");
    this.style.display = "block";
}

function cardDragOver(e) {
    e.preventDefault();
}

function cardDragEnter(e) {
    e.preventDefault();
    this.classList.add("drag-over");
}

function cardDragLeave(e) {
    this.classList.remove("drag-over");
}

async function cardDragDrop(e) {
    e.preventDefault();
    e.stopPropagation(); 
    this.classList.remove("drag-over");

    if (!draggableTodo) return;


    if (draggableTodo instanceof Node) {
        this.appendChild(draggableTodo);
    }

    const newStatus = this.getAttribute("id");
    const cardId = draggableTodo.getAttribute("data-id");

    try {

        const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/update-status.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: cardId, status: newStatus }),
        });

        if (!data.success) {
            console.error("Falha ao atualizar status:", data.error);
        }
    } catch (error) {
        console.error("Erro ao atualizar status:", error);
    }

    await updatePositions(newStatus);

    if (originColumnId && originColumnId !== newStatus) {
        await updatePositions(originColumnId);
    }
}


function moveColumnLeft(status) {
    const container = document.querySelector(".todo-container");
    const columns = Array.from(container.querySelectorAll(".status"));
    const currentColumn = document.getElementById(status);
    const currentIndex = columns.indexOf(currentColumn);

    if (currentIndex === 0) {
        alert("Esta coluna já está na primeira posição.");
        return;
    }

    const previousColumn = columns[currentIndex - 1];
    container.insertBefore(currentColumn, previousColumn);

    const newOrder = Array.from(container.querySelectorAll(".status")).map(col => col.id);
    updateColumnPositions(newOrder);
}

function moveColumnRight(status) {
    const container = document.querySelector(".todo-container");
    const columns = Array.from(container.querySelectorAll(".status"));
    const currentColumn = document.getElementById(status);
    const currentIndex = columns.indexOf(currentColumn);

    if (currentIndex === columns.length - 1) {
        alert("Esta coluna já está na última posição.");
        return;
    }

    const nextColumn = columns[currentIndex + 1];
    container.insertBefore(nextColumn, currentColumn);

    const newOrder = Array.from(container.querySelectorAll(".status")).map(col => col.id);
    updateColumnPositions(newOrder);
}


async function updatePositions(status) {
    const column = document.getElementById(status);
    const cards = Array.from(column.querySelectorAll(".todo"));
    const order = cards.map((card, index) => ({
        id: parseInt(card.getAttribute("data-id")),
        position: index,
    }));

    try {
        const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/update-positions.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ status, order }),
        });

        if (!data.success) {
            console.error("Falha ao atualizar posições:", data.error);
        }
    } catch (error) {
        console.error("Erro ao atualizar posições:", error);
    }
}

async function updateColumnPositions(order) {
    try {
        const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/update-column-positions.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order }),
        });

        if (!data.success) {
            console.error("Falha ao atualizar posições das colunas:", data.error);
        }
    } catch (error) {
        console.error("Erro ao atualizar posições das colunas:", error);
    }
}


async function addColumn(event) {
    event.preventDefault();
    const columnName = prompt("Digite o nome da nova coluna:");
    if (!columnName || columnName.trim() === "") {
        alert("Por favor, insira um nome válido para a coluna.");
        return;
    }
     window.reload
    try {
        const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/add-column.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `column_name=${encodeURIComponent(columnName)}`,
        });

        if (data.success) {
            location.reload()
            const columnElement = createColumnElement(data.column);
            document.querySelector(".todo-container").appendChild(columnElement);
            setupDropEventsForColumn(columnElement); 
        } else {
            console.error("Erro ao criar coluna:", data.error);
            alert("Erro ao criar coluna: " + data.error);
        }
    } catch (error) {
        console.error("Erro ao criar coluna:", error);
        alert("Erro ao criar coluna. Verifique o console para mais detalhes.");
    }
}

async function addCard(status) {
    const cardTitle = prompt("Digite o título do card:");
    if (!cardTitle || cardTitle.trim() === "") {
        alert("Por favor, insira um título válido para o card.");
        return;
    }

    try {
        const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/add-todo.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `list=${encodeURIComponent(cardTitle)}&status=${encodeURIComponent(status)}`,
        });

        if (data.success) {
            const columnElement = document.getElementById(status);
            const cardElement = createCardElement(data.card);
            columnElement.appendChild(cardElement);
            setupDragAndDropForElement(cardElement); 
        } else {
            console.error("Erro ao criar card:", data.error);
            alert("Erro ao criar card: " + data.error);
        }
    } catch (error) {
        console.error("Erro ao criar card:", error);
        alert("Erro ao criar card. Verifique o console para mais detalhes.");
    }
}


async function deleteColumn(status) {
    if (confirm("Deseja excluir esta coluna? Todos os cards dentro dela também serão excluídos.")) {
        try {
            const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/delete-column.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ status }),
            });

            if (data.success) {
                const columnElement = document.getElementById(status);
                if (columnElement) {
                    columnElement.remove();
                }
            } else {
                console.error("Falha ao excluir coluna:", data.error);
            }
        } catch (error) {
            console.error("Erro ao excluir coluna:", error);
        }
    }
}

async function deleteList(id) {
    if (confirm("Deseja excluir o card?")) {
        try {
            const data = await fetchJSON("http://localhost:8080/todo-list-in-kanban-board/endpoint/delete-todo.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id }),
            });

            if (data.success) {
                const cardElement = document.querySelector(`.todo[data-id="${id}"]`);
                if (cardElement) {
                    cardElement.remove();
                }
            } else {
                console.error("Falha ao excluir card:", data.error);
            }
        } catch (error) {
            console.error("Erro ao excluir card:", error);
        }
    }
}


function createColumnElement(column) {
    const div = document.createElement("div");
    div.className = "status";
    div.id = column.status;
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <button class="btn btn-sm btn-secondary" onclick="moveColumnLeft('${column.status}')">⬅️</button>
            <h2>${column.column_name}</h2>
            <button class="btn btn-sm btn-secondary" onclick="moveColumnRight('${column.status}')">➡️</button>
        </div>
        <button class="btn btn-danger btn-sm" onclick="deleteColumn('${column.status}')">Excluir</button>
        <button class="btn btn-dark form-control" onclick="addCard('${column.status}')">Adicionar Card</button>
    `;
    return div;
}

function createCardElement(card) {
    const div = document.createElement("div");
    div.className = "todo";
    div.draggable = true;
    div.setAttribute("data-id", card.tbl_list_id);
    div.innerHTML = `
        <input type="hidden" id="listId-${card.tbl_list_id}" value="${card.tbl_list_id}">
        <span id="list-${card.tbl_list_id}">${card.list}</span>
        <span class="close" onclick="deleteList(${card.tbl_list_id})">&times;</span>
    `;
    return div;
}


async function fetchJSON(url, options) {
    const response = await fetch(url, options);
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    return response.json();
}


window.addCard = addCard;
window.deleteColumn = deleteColumn;
window.deleteList = deleteList;
window.addColumn = addColumn;
window.moveColumnLeft = moveColumnLeft;
window.moveColumnRight = moveColumnRight;