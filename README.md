# challenge

---

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) instalado  
- [Lando](https://lando.dev/) instalado
- [Git](https://git-scm.com) instalado
- Acesso ao terminal (PowerShell)

---

# Clonando o projeto

1. Abra o Git Bash e execute:

    ```bash
   git clone https://github.com/Alylson/challenge.git
   ```

# Iniciando o Ambiente com Lando

## Passos para subir os containers

1. Abra o PowerShell, vá até o diretório raiz do projeto (onde está o arquivo `.lando.yml`), execute:

   ```bash
   lando start
   ```

2. Aguarde até que todos os serviços tenham iniciado.

# Importação do banco

1. Vá até seu SGBD preferido e importe o arquivo backup-drupal.sql na raiz do projeto. As configurações para conexão com o banco estão no arquivo do Lando, na opção database.

# Collection

1. Vá até sua ferramenta para teste de API preferida (Postman/Insomnia) e importe o arquivo Collection.json na raiz do projeto.