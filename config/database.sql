-- Tabela de Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cargo ENUM('atendente', 'admin') NOT NULL DEFAULT 'usuario',
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT True
);

-- Tabela de Formas de Atendimento
CREATE TABLE forma_atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    forma VARCHAR(100) NOT NULL -- (presencial, whats, ligacao, email, redes soc, team ou outra)
);

-- Tabela de Público
CREATE TABLE publicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_cliente VARCHAR(100) NOT NULL, -- (empregador, trabalhador, outras ag, ads, fgtas, interesados mercado trabalho, outra (personalizado))
    campos_especificos JSON -- no caso de empregador, trabalhador ou outra: nome, cpf/cnpj, telefone
);

-- Tabela de Tipos de Atendimento
CREATE TABLE tipos_atendimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(255) NOT NULL, -- (VCH, "C,SD,V", orientações sobre cursos ou empreendedorismo)
    descricao TEXT, -- Dependendo do tipo, pode ter ou não algo aqui
    publico_id INT NOT NULL,
    FOREIGN KEY (publico_id) REFERENCES publicos(id)
);

-- Tabela de Atendimentos
CREATE TABLE atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_de_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    forma_atendimento_id INT NOT NULL,
    tipo_atendimento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    publico_id INT NOT NULL,
    --detalhes_atendimento TEXT, -- Campo para armazenar os detalhes específicos do atendimento (JSON ou texto)
    FOREIGN KEY (forma_atendimento_id) REFERENCES formas_atendimento(id),
    FOREIGN KEY (tipo_atendimento_id) REFERENCES tipos_atendimento(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (publico_id) REFERENCES publicos(id)
);