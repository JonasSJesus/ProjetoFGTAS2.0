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

```bash
# Clone o repositório
git clone https://github.com/JonasSJesus/ProjetoFGTAS2.0.git

# Acesse o diretório do projeto
cd ProjetoFGTAS2.0

# Instale as dependências (excluindo as de desenvolvimento)
composer install --no-dev

# Execute o servidor na pasta raiz
composer start

# Acesse no navegador
http://localhost:8080/home
