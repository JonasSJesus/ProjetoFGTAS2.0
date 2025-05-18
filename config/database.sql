-- Copie o codigo e cole no MySQL

CREATE DATABASE atendimentos;

USE atendimentos;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cargo ENUM('atendente', 'admin') NOT NULL DEFAULT 'atendente',
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    ativo ENUM('S', 'N') DEFAULT 'S'
);

-- Tabela de Público
CREATE TABLE IF NOT EXISTS publico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_cliente VARCHAR(100) NOT NULL -- (empregador, trabalhador, outras ag, ads, fgtas, interesados mercado trabalho, outra (personalizado))
);

-- Tabela de Forma de Atendimento
CREATE TABLE IF NOT EXISTS forma_atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forma VARCHAR(100) NOT NULL -- (Presencial, Whatsapp, Ligação telefônica, E-mail, Redes Sociais, Teams, outra (personalizado))
);

-- Tabela para campos especificos do Publico. Usada no caso de Publico ser empregador, trabalhador ou outra (personalizado)
CREATE TABLE IF NOT EXISTS informacoes_pessoais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publico_id INT UNIQUE,
    nome VARCHAR(255) NOT NULL,
    contato VARCHAR(200) NOT NULL,
    documento VARCHAR(40) NOT NULL, -- CPF ou CNPJ
    FOREIGN KEY (publico_id) REFERENCES publico(id)
);

-- Tabela de Tipos de Atendimento
CREATE TABLE IF NOT EXISTS tipo_atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(255) NOT NULL, -- (VCH, "C,SD,V", orientações sobre cursos ou empreendedorismo)
    descricao TEXT, -- Dependendo do tipo, pode ter ou não algo aqui
    informacoes JSON
);

-- Tabela de Atendimentos
CREATE TABLE IF NOT EXISTS atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_de_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_atendimento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    publico_id INT NOT NULL,
    forma_atendimento_id INT NOT NULL,
    detalhes_atendimento TEXT, -- Campo para armazenar outros detalhes do atendimento (JSON ou texto)
    FOREIGN KEY (tipo_atendimento_id) REFERENCES tipo_atendimento(id),
    FOREIGN KEY (forma_atendimento_id) REFERENCES forma_atendimento(id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (publico_id) REFERENCES publico(id)
);

-- Copie ate aqui
-- Dados importantes para cadastrar atendimentos ------------

INSERT publico (perfil_cliente) VALUES ('Empregador'),
                                       ('Trabalhador'),
                                       ('Outras Agências'),
                                       ('ADS'),
                                       ('Setores da FGTAS'),
                                       ('Interessado em informações sobre o Mercado de Trabalho');


INSERT forma_atendimento (forma) VALUES ('Presencial'),
                                        ('Whatsapp'),
                                        ('Ligação Telefônica'),
                                        ('E-mail'),
                                        ('Redes Sociais'),
                                        ('Teams');
