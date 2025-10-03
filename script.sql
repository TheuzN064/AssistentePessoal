-- SQL Script for Personal Assistant Database
-- Version 1.1
-- Includes the 'description' field in the 'passwords' table.

-- Tabela de usuário (a base de tudo)
-- Armazena o hash da sua senha mestra.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de senhas
-- Armazena os dados de login. Todos os campos sensíveis devem ser criptografados.
CREATE TABLE passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT, -- Novo campo para descrição
    site_url TEXT,
    email TEXT,
    password TEXT NOT NULL,
    recovery_codes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de autenticadores 2FA (TOTP)
-- Ligada diretamente a uma entrada de senha.
CREATE TABLE two_factor_auth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    password_id INT NOT NULL UNIQUE,
    secret_key TEXT NOT NULL, -- O segredo TOTP, deve ser CRIPTOGRAFADO
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (password_id) REFERENCES passwords(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de cartões de crédito
-- Armazena dados de cartões. Todos os campos sensíveis devem ser criptografados.
CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_name VARCHAR(255) NOT NULL,
    card_holder_name TEXT NOT NULL,
    card_number TEXT NOT NULL,
    expiry_date TEXT NOT NULL, -- Formato MM/AA
    cvv TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de "pessoas" para assinaturas compartilhadas
CREATE TABLE people (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de assinaturas
-- Centraliza as informações sobre serviços recorrentes.
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    password_id INT, -- Qual login/senha essa assinatura usa? (Pode ser nulo)
    card_id INT,     -- Qual cartão é usado para pagar? (Pode ser nulo)
    name VARCHAR(255) NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    is_shared BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (password_id) REFERENCES passwords(id) ON DELETE SET NULL,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de ligação para assinaturas compartilhadas (relação N:N)
-- Conecta uma assinatura a várias pessoas.
CREATE TABLE subscription_people (
    subscription_id INT NOT NULL,
    person_id INT NOT NULL,
    PRIMARY KEY (subscription_id, person_id),
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de contas a pagar
CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    due_date DATE NOT NULL,
    is_paid BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de logs de acesso e erros
-- Registra eventos importantes para segurança e depuração.
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_level VARCHAR(50) NOT NULL, -- Ex: "INFO", "ERROR", "SECURITY_ALERT"
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

