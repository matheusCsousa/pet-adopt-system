-- ONG Adoção de Animais

CREATE DATABASE IF NOT EXISTS ong_adocao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ong_adocao;

-- ABRIGO
CREATE TABLE Abrigo (
    Id_abrigo   INT AUTO_INCREMENT PRIMARY KEY,
    Nome        VARCHAR(100) NOT NULL,
    Capacidade  INT NOT NULL,
    Endereco    VARCHAR(200) NOT NULL,
    Telefone    VARCHAR(20)
);

-- FUNCIONARIO
CREATE TABLE Funcionario (
    Id_funcionario  INT AUTO_INCREMENT PRIMARY KEY,
    Nome            VARCHAR(100) NOT NULL,
    Cargo           VARCHAR(50) NOT NULL,
    Email           VARCHAR(100) NOT NULL UNIQUE,
    Senha           VARCHAR(255) NOT NULL
);

-- ADOTANTE
CREATE TABLE Adotante (
    Id_adotante INT AUTO_INCREMENT PRIMARY KEY,
    Nome        VARCHAR(100) NOT NULL,
    CPF         VARCHAR(14) NOT NULL UNIQUE,
    Telefone    VARCHAR(20),
    Email       VARCHAR(100) NOT NULL UNIQUE,
    Senha       VARCHAR(255) NOT NULL,
    Endereco    VARCHAR(200)
);

-- ANIMAL
CREATE TABLE Animal (
    Id_animal       INT AUTO_INCREMENT PRIMARY KEY,
    Nome            VARCHAR(100) NOT NULL,
    Raca            VARCHAR(100),
    Especie         VARCHAR(50) NOT NULL,
    Sexo            ENUM('M', 'F') NOT NULL,
    Porte           ENUM('Pequeno', 'Medio', 'Grande') NOT NULL,
    Status          ENUM('Cadastrado', 'Disponivel', 'Em_adocao', 'Adotado', 'Em_tratamento') NOT NULL DEFAULT 'Cadastrado',
    Data_entrada    DATE NOT NULL,
    Data_saida      DATE,
    fk_Abrigo_Id_abrigo INT NOT NULL,
    FOREIGN KEY (fk_Abrigo_Id_abrigo) REFERENCES Abrigo(Id_abrigo)
);

-- FOTO
CREATE TABLE Foto (
    Id_foto             INT AUTO_INCREMENT PRIMARY KEY,
    URL_foto            VARCHAR(300),
    Tipo_mime           VARCHAR(100),
    Dados               LONGBLOB,
    Is_principal        BOOLEAN NOT NULL DEFAULT FALSE,
    fk_Animal_Id_animal INT NOT NULL,
    FOREIGN KEY (fk_Animal_Id_animal) REFERENCES Animal(Id_animal)
);

-- VACINA
CREATE TABLE Vacina (
    Id_vacina   INT AUTO_INCREMENT PRIMARY KEY,
    Nome        VARCHAR(100) NOT NULL
);

-- APLICA (associativa Animal x Vacina)
CREATE TABLE Aplica (
    fk_Animal_Id_animal INT NOT NULL,
    fk_Vacina_Id_vacina INT NOT NULL,
    Data_aplicacao      DATE NOT NULL,
    Proxima_dose        DATE,
    PRIMARY KEY (fk_Animal_Id_animal, fk_Vacina_Id_vacina, Data_aplicacao),
    FOREIGN KEY (fk_Animal_Id_animal) REFERENCES Animal(Id_animal),
    FOREIGN KEY (fk_Vacina_Id_vacina) REFERENCES Vacina(Id_vacina)
);

-- ADOCAO
CREATE TABLE Adocao (
    Id_adocao               INT AUTO_INCREMENT PRIMARY KEY,
    Status                  ENUM('Pendente', 'Aprovada', 'Cancelada') NOT NULL DEFAULT 'Pendente',
    Data_abertura           DATE NOT NULL,
    Descricao               TEXT,
    Data_conclusao          DATE,
    fk_Adotante_Id_adotante INT NOT NULL,
    fk_Funcionario_Id_funcionario INT NOT NULL,
    fk_Animal_Id_animal     INT NOT NULL,
    FOREIGN KEY (fk_Adotante_Id_adotante) REFERENCES Adotante(Id_adotante),
    FOREIGN KEY (fk_Funcionario_Id_funcionario) REFERENCES Funcionario(Id_funcionario),
    FOREIGN KEY (fk_Animal_Id_animal) REFERENCES Animal(Id_animal)
);

-- INSERTS

INSERT INTO Abrigo (Nome, Capacidade, Endereco, Telefone) VALUES
('Abrigo Patinhas Felizes',  50, 'Rua das Flores, 123, Jundiai - SP',     '(11) 91111-1111'),
('Abrigo Vida Animal',       30, 'Av. Brasil, 456, Campinas - SP',         '(19) 92222-2222'),
('Abrigo Novo Lar',          40, 'Rua do Campo, 789, Sao Paulo - SP',      '(11) 93333-3333');

INSERT INTO Funcionario (Nome, Cargo, Email, Senha) VALUES
('Ana Paula',    'Administrador', 'ana@ong.com',     PASSWORD('ana123')),
('Carlos Lima',  'Atendente',     'carlos@ong.com',  PASSWORD('carlos123')),
('Mariana Souza','Atendente',     'mariana@ong.com', PASSWORD('mariana123'));

INSERT INTO Adotante (Nome, CPF, Telefone, Email, Senha, Endereco) VALUES
('Joao Silva',      '111.222.333-44', '(11) 94444-4444', 'joao@email.com',     PASSWORD('07092002'), 'Rua A, 10, Jundiai - SP'),
('Fernanda Costa',  '222.333.444-55', '(11) 95555-5555', 'fernanda@email.com', PASSWORD('fernanda123'), 'Rua B, 20, Jundiai - SP'),
('Ricardo Nunes',   '333.444.555-66', '(19) 96666-6666', 'ricardo@email.com',  PASSWORD('ricardo123'), 'Av. C, 30, Campinas - SP'),
('Lucia Mendes',    '444.555.666-77', '(11) 97777-7777', 'lucia@email.com',    PASSWORD('lucia123'), 'Rua D, 40, Sao Paulo - SP'),
('Pedro Alves',     '555.666.777-88', '(11) 98888-8888', 'pedro@email.com',    PASSWORD('pedro123'), 'Rua E, 50, Jundiai - SP');

INSERT INTO Animal (Nome, Raca, Especie, Sexo, Porte, Status, Data_entrada, Data_saida, fk_Abrigo_Id_abrigo) VALUES
('Rex',      'Labrador',         'Cachorro', 'M', 'Grande',   'Adotado',      '2024-01-10', '2024-03-15', 1),
('Mia',      'Vira-lata',        'Gato',     'F', 'Pequeno',  'Disponivel',   '2024-02-05', NULL,         1),
('Bob',      'Golden Retriever', 'Cachorro', 'M', 'Grande',   'Disponivel',   '2024-03-01', NULL,         2),
('Luna',     'Siames',           'Gato',     'F', 'Pequeno',  'Em_adocao',    '2024-03-10', NULL,         2),
('Thor',     'Pitbull',          'Cachorro', 'M', 'Grande',   'Disponivel',   '2024-04-01', NULL,         3),
('Mel',      'Poodle',           'Cachorro', 'F', 'Pequeno',  'Disponivel',   '2024-04-15', NULL,         1),
('Simba',    'Vira-lata',        'Gato',     'M', 'Medio',    'Disponivel',   '2024-05-01', NULL,         3),
('Nina',     'Beagle',           'Cachorro', 'F', 'Medio',    'Em_tratamento','2024-05-10', NULL,         2);

INSERT INTO Foto (URL_foto, Is_principal, fk_Animal_Id_animal) VALUES
('imgcarrosel/dog.jpg',         TRUE,  1),
('imgcarrosel/dogrosa.jpg',     FALSE, 1),
('imgcarrosel/gato.jpg',        TRUE,  2),
('imgcarrosel/dogrosa.jpg',     TRUE,  3),
('imgcarrosel/amarelo.jpg',     FALSE, 3),
('imgcarrosel/gatoamarelo.jpg', TRUE,  4),
('imgcarrosel/peruca.jpg',      TRUE,  5),
('imgcarrosel/laranja.jpg',     TRUE,  6),
('imgcarrosel/gatorosa.jpg',    TRUE,  7),
('imgcarrosel/gato.jpg',        TRUE,  8);

INSERT INTO Vacina (Nome) VALUES
('V10'),
('Antirrábica'),
('Giárdia'),
('Leucemia Felina'),
('Tríplice Felina');

INSERT INTO Aplica (fk_Animal_Id_animal, fk_Vacina_Id_vacina, Data_aplicacao, Proxima_dose) VALUES
(1, 1, '2024-01-15', '2025-01-15'),
(1, 2, '2024-01-15', '2025-01-15'),
(2, 4, '2024-02-10', '2025-02-10'),
(2, 5, '2024-02-10', '2025-02-10'),
(3, 1, '2024-03-05', '2025-03-05'),
(3, 2, '2024-03-05', '2025-03-05'),
(4, 4, '2024-03-15', '2025-03-15'),
(5, 1, '2024-04-05', '2025-04-05'),
(6, 1, '2024-04-20', '2025-04-20'),
(6, 2, '2024-04-20', '2025-04-20'),
(7, 5, '2024-05-05', '2025-05-05'),
(8, 1, '2024-05-15', '2025-05-15');

INSERT INTO Adocao (Status, Data_abertura, Descricao, Data_conclusao, fk_Adotante_Id_adotante, fk_Funcionario_Id_funcionario, fk_Animal_Id_animal) VALUES
('Aprovada',  '2024-03-01', 'Adotante tem experiencia com cachorros grandes.',  '2024-03-15', 1, 1, 1),
('Pendente',  '2024-05-10', 'Adotante mora em apartamento, tem espaco.',         NULL,         2, 2, 4),
('Cancelada', '2024-04-01', 'Adotante desistiu por problemas pessoais.',         '2024-04-10', 3, 2, 3),
('Pendente',  '2024-05-20', 'Primeira adocao do adotante.',                      NULL,         4, 3, 6),
('Pendente',  '2024-05-22', 'Adotante ja tem outro gato em casa.',               NULL,         5, 3, 7);
