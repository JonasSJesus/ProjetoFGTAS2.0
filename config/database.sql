CREATE DATABASE atendimentos;

USE atendimentos;

-- Tabela de Usuários
CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cargo ENUM('atendente', 'admin') NOT NULL DEFAULT 'atendente',
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    ativo ENUM('S', 'N') DEFAULT 'S'
);

-- Tabela de Público
CREATE TABLE publico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_cliente VARCHAR(100) NOT NULL -- (empregador, trabalhador, outras ag, ads, fgtas, interesados mercado trabalho, outra (personalizado))
);

-- Tabela para campos especificos do Publico. Usada no caso de Publico ser empregador, trabalhador ou outra (personalizado)
CREATE TABLE informacoes_pessoais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    publico_id INT UNIQUE,
    nome VARCHAR(255) NOT NULL,
    contato varchar(20) NOT NULL,
    documento VARCHAR(30) NOT NULL, -- CPF ou CNPJ
    FOREIGN KEY (publico_id) REFERENCES publico(id)
);

-- Tabela de Tipos de Atendimento
CREATE TABLE tipo_atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(255) NOT NULL, -- (VCH, "C,SD,V", orientações sobre cursos ou empreendedorismo)
    descricao TEXT -- Dependendo do tipo, pode ter ou não algo aqui
);

-- Tabela de Atendimentos
CREATE TABLE atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_de_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo_atendimento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    publico_id INT NOT NULL,
    forma_atendimento TEXT, -- Campo para armazenar a forma de atendimento (presencial, whats, ligacao, email, redes soc, team ou outra)
    detalhes_atendimento TEXT, -- Campo para armazenar os detalhes específicos do atendimento (JSON ou texto)
    FOREIGN KEY (tipo_atendimento_id) REFERENCES tipo_atendimento(id),
    FOREIGN KEY (usuario_id) REFERENCES usuario(id),
    FOREIGN KEY (publico_id) REFERENCES publico(id)
);