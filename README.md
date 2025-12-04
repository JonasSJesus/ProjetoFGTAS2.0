# 📋 Cadastro de Atendimentos — FGTAS

Sistema web interno desenvolvido como A3 da disciplina *Usabilidade, Desenvolvimento Web e Jogos* (1º semestre de 2025). Este sistema foi criado sob demanda da **Fundação Gaúcha do Trabalho e Assistência Social (FGTAS)**.

---

## 📌 Descrição

Este sistema tem como objetivo facilitar o **registro e gerenciamento de atendimentos realizados pela instituição**, além de oferecer recursos para geração de relatórios e controle de usuários.

---

## ✅ Funcionalidades

- 🔐 Sistema de autenticação e controle de sessões
- 👤 Controle de acesso por perfil: Administradores e Atendentes
- 👥 CRUD completo de usuários
- 📝 Cadastro e gerenciamento de atendimentos
- 📄 Geração de relatórios nos formatos **CSV** e **PDF**

---

## 🛠 Tecnologias utilizadas

<div>
  <img height="70" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" alt="PHP"/>
  <img height="70" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg" alt="HTML5"/>
  <img height="70" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original.svg" alt="CSS3"/>
  <img height="70" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" alt="JavaScript"/>
</div>

- Backend: PHP (com Slim Framework)
- Frontend: HTML5, CSS3, JavaScript
- Relatórios: PDF e CSV
- Autenticação: Gerenciamento de sessões nativas do PHP
- Banco de Dados: MySQL (via PDO e Doctrine DBAL)

---

## ⚙️ Requisitos

- PHP 8.1 ou superior
- Composer
- Servidor web (Apache, Nginx ou embutido do PHP)

---

## 🚀 Instalação e Execução

### 1. Clonar o repositório

```bash
git clone https://github.com/JonasSJesus/ProjetoFGTAS2.0.git

# entre na pasta
cd ProjetoFGTAS2.0-main
```

### 2. Instalar as dependências

```bash
# Instala apenas as dependências de produção
composer install --no-dev
```

### 3. Configurar o ambiente

```bash
# Renomeie o arquivo de exemplo para ".env"
cp .env.example .env
```

Edite o arquivo `.env` com as configurações do seu banco de dados:

```env
DB_HOST="SERVIDOR_MYSQL"
DB_PORT="3306"
DB_NAME="atendimentos"
DB_USERNAME="SEU_USUARIO"
DB_PASSWORD="SUA_SENHA"
```

### 4. Configurar o banco de dados

O script de criação do banco está localizado em:

```
/config/database.sql
```

Importe esse arquivo no seu SGBD (por exemplo, via phpMyAdmin ou CLI do MySQL):

```bash
mysql -u seu_usuario -p atendimentos < config/database.sql
```

### 5. Iniciar o servidor

```bash
# Inicia o servidor PHP embutido na porta 8080
composer start
```

Acesse no navegador:

```
http://localhost:8080/home
```

---

## 🧪 Testes E2E (End-to-End)

Este projeto inclui uma suíte completa de **testes automatizados E2E** usando **Selenium WebDriver**.

### 📦 Testes Disponíveis

**Testes Principais:**
- ✅ Login com sucesso
- ❌ Login com credenciais inválidas
- 👤 Cadastro de novo usuário
- 📋 Formulário de atendimento completo
- ⚠️ Validação de campos obrigatórios

### 🚀 Executar Testes

#### 1. Instalar Selenium
```bash
pip install selenium
```

#### 2. Executar todos os testes
```bash
python testes_e2e.py
```