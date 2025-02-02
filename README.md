# kanban_web
Este é um projeto de um **Kanban** (Quadro Kanban) desenvolvido para gerenciar tarefas em colunas, permitindo a criação, movimentação e exclusão de cards e colunas. O projeto foi desenvolvido utilizando **PHP**, **MySQL**, **JavaScript** e **Bootstrap**.


## **Tecnologias Utilizadas**

- **Frontend**:
  - HTML, CSS, JavaScript.
  - Bootstrap para estilização.
  - Drag-and-drop nativo do JavaScript.

- **Backend**:
  - PHP para lógica do servidor.
  - MySQL para armazenamento de dados.

- **Ferramentas**:
  - XAMPP (para servidor local com Apache e MySQL).
  - Visual Studio Code (editor de código).


1. **Configurar o Banco de Dados:**:

Abra o phpMyAdmin (ou outra interface para MySQL).

Crie um banco de dados chamado todo_kanban_db.

Execute o seguinte SQL para criar as tabelas necessárias:
CREATE TABLE tbl_columns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    column_name VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL UNIQUE,
    position INT NOT NULL
);

CREATE TABLE tbl_list (
    tbl_list_id INT AUTO_INCREMENT PRIMARY KEY,
    list VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL,
    position INT NOT NULL,
    FOREIGN KEY (status) REFERENCES tbl_columns(status)
);

Licença
Este projeto está licenciado sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.
