# Sistema Grupo Fassil - Ambiente Docker

Este diretório contém toda a estrutura dockerizada e pronta para execução do sistema e site do **Grupo Fassil**.

## 📁 Estrutura de Pastas
* `public_html/` - Código-fonte do site principal e sistema administrativo (`/gerenciar`).
* `init-db/` - Arquivo SQL original do banco de dados (`01-grupofas_sistema.sql` ~16,6 MB). O Docker importa este arquivo **automaticamente** na primeira vez em que o banco sobe.
* `Dockerfile` - Imagem customizada com Apache, PHP 7.4, mod_rewrite e extensões (`gd`, `mysqli`, `pdo_mysql`, `mbstring`, `zip`).
* `docker-compose.yml` - Orquestração dos containers de aplicação (`app`) e banco de dados (`db`).

---

## 🚀 Como Executar o Projeto Localmente

1. Abra o terminal (PowerShell, CMD ou Git Bash) nesta pasta:
   ```bash
   cd "D:\BKP OUTROS ANTIGOS\sistema"
   ```

2. Execute o comando para construir e subir os containers em segundo plano:
   ```bash
   docker-compose up -d --build
   ```

3. Aguarde alguns segundos para que o MySQL inicialize e importe o arquivo SQL pela primeira vez.

4. Acesse no seu navegador:
   * **Site Principal**: [http://localhost:8080/](http://localhost:8080/)
   * **Sistema / Painel**: [http://localhost:8080/gerenciar](http://localhost:8080/gerenciar)

---

## 🔧 Informações do Banco de Dados
* **Host**: `db` (ou `localhost:33060` caso acesse de um cliente externo como DBeaver / MySQL Workbench)
* **Usuário**: `grupofas_admin`
* **Senha**: `fassil3017#`
* **Banco de Dados**: `grupofas_sistema`
* **Senha Root do MySQL**: `rootpassword`

---

## 🛑 Como Parar ou Reiniciar
Para parar os containers sem perder os dados:
```bash
docker-compose down
```

Para reiniciar o ambiente do zero (apagando e reimportando o banco de dados):
```bash
docker-compose down -v
docker-compose up -d --build
```
