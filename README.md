# Assistente Pessoal (Demo sem Criptografia)

Este repositório contém o sistema de gerenciamento de senhas/cartões preparado para demonstração em sala, sem nenhuma camada de criptografia ativa. Use as instruções abaixo para montar rapidamente um ambiente de testes local.

## Pré-requisitos
- PHP 8+ com extensões `pdo` e `pdo_mysql` habilitadas
- Composer
- Servidor MySQL/MariaDB com acesso local

## Passo a passo
1. **Instale dependências PHP**
   ```bash
   composer install
   ```

2. **Prepare o banco de dados**
   - Crie um banco (ex.: `assis_pessoal`).
   - Importe o schema inicial:
     ```bash
     mysql -u <usuario> -p < banco_nome < script.sql
     ```

3. **Configure a conexão**
   - Ajuste `config/ConexaoPDO.php` com host, nome do banco, usuário e senha do seu ambiente local.

4. **Suba o servidor local**
   ```bash
   php -S localhost:8000 -t public
   ```

5. **Crie um usuário**
   - Acesse `http://localhost:8000/create_user.php` para registrar um usuário mestre.

6. **Navegue pelo cofre**
   - Após criar o usuário, acesse `http://localhost:8000/` e use o login para testar senhas, cartões e autenticação 2FA.

## Observações úteis
- Se o arquivo `panic.flag` existir na raiz do projeto, o sistema entra em modo pânico e bloqueia a conexão ao banco; remova-o para voltar ao normal.
- Os dados são armazenados em **texto simples** para fins de apresentação, conforme solicitado.
