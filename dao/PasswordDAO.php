<?php
require_once __DIR__ . '/../models/Password.php';
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/../security/Logger.php';

class PasswordDAO {
    
    public function create(Password $password, $masterPassword) {
        try {
            $sql = "INSERT INTO passwords (user_id, group_id, name, description, site_url, email, password, recovery_codes) 
                    VALUES (:user_id, :group_id, :name, :description, :site_url, :email, :password, :recovery_codes)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->bindValue(':user_id', $password->getUserId());
            $stmt->bindValue(':group_id', $password->getGroupId());
            $stmt->bindValue(':name', Crypto::encrypt($password->getName(), $masterPassword));
            $stmt->bindValue(':description', Crypto::encrypt($password->getDescription(), $masterPassword));
            $stmt->bindValue(':site_url', Crypto::encrypt($password->getSiteUrl(), $masterPassword));
            $stmt->bindValue(':email', Crypto::encrypt($password->getEmail(), $masterPassword));
            $stmt->bindValue(':password', Crypto::encrypt($password->getPasswordText(), $masterPassword));
            $stmt->bindValue(':recovery_codes', Crypto::encrypt($password->getRecoveryCodes(), $masterPassword));
            $stmt->execute();
            $newId = ConexaoPDO::getInstance()->lastInsertId();
            Logger::log('INFO', "Nova senha criada (ID: {$newId}) para o usuário ID {$password->getUserId()}.");
            return $newId;
        } catch (PDOException $e) { 
            Logger::log('ERROR', "PasswordDAO::create - " . $e->getMessage());
            return false; 
        }
    }

    public function update(Password $password, $masterPassword) {
        try {
            $sql = "UPDATE passwords SET group_id = :group_id, name = :name, description = :description, site_url = :site_url, email = :email, password = :password, recovery_codes = :recovery_codes 
                    WHERE id = :id AND user_id = :user_id";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->bindValue(':id', $password->getId());
            $stmt->bindValue(':user_id', $password->getUserId());
            $stmt->bindValue(':group_id', $password->getGroupId());
            $stmt->bindValue(':name', Crypto::encrypt($password->getName(), $masterPassword));
            $stmt->bindValue(':description', Crypto::encrypt($password->getDescription(), $masterPassword));
            $stmt->bindValue(':site_url', Crypto::encrypt($password->getSiteUrl(), $masterPassword));
            $stmt->bindValue(':email', Crypto::encrypt($password->getEmail(), $masterPassword));
            $stmt->bindValue(':password', Crypto::encrypt($password->getPasswordText(), $masterPassword));
            $stmt->bindValue(':recovery_codes', Crypto::encrypt($password->getRecoveryCodes(), $masterPassword));
            $stmt->execute();
            Logger::log('INFO', "Senha ID {$password->getId()} atualizada pelo usuário ID {$password->getUserId()}.");
            return $password->getId();
        } catch (PDOException $e) { 
            Logger::log('ERROR', "PasswordDAO::update - " . $e->getMessage());
            return false; 
        }
    }
    
    public function getAllDetailsByUserId($userId) {
        try {
            $sql = "SELECT p.*, g.name as group_name
                    FROM passwords p 
                    LEFT JOIN groups g ON p.group_id = g.id 
                    WHERE p.user_id = ? 
                    ORDER BY p.name ASC";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            Logger::log('ERROR', "PasswordDAO::getAllDetailsByUserId - " . $e->getMessage());
            return []; 
        }
    }

    public function getPasswordById($id, $userId) {
        try {
            $sql = "SELECT p.*, p.group_id, g.name as group_name, tfa.secret_key 
                    FROM passwords p 
                    LEFT JOIN groups g ON p.group_id = g.id
                    LEFT JOIN two_factor_auth tfa ON p.id = tfa.password_id
                    WHERE p.id = ? AND p.user_id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$id, $userId]);
            return $stmt->fetch();
        } catch (PDOException $e) { 
            Logger::log('ERROR', "PasswordDAO::getPasswordById - " . $e->getMessage());
            return null; 
        }
    }
    
    public function delete($id, $userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            Logger::log('INFO', "Senha ID {$id} apagada pelo usuário ID {$userId}.");
            return true;
        } catch (PDOException $e) { 
            Logger::log('ERROR', "PasswordDAO::delete - " . $e->getMessage());
            return false; 
        }
    }

    public function getPasswordsWithout2FA($userId) {
        try {
            $sql = "SELECT p.id, p.name 
                    FROM passwords p
                    LEFT JOIN two_factor_auth tfa ON p.id = tfa.password_id
                    WHERE p.user_id = ? AND tfa.id IS NULL
                    ORDER BY p.name ASC";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { 
            Logger::log('ERROR', "PasswordDAO::getPasswordsWithout2FA - " . $e->getMessage());
            return []; 
        }
    }
    
    public function getByUserId($userId) {
        try {
            $sql = "SELECT id, name FROM passwords WHERE user_id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::log('ERROR', "PasswordDAO::getByUserId - " . $e->getMessage());
            return [];
        }
    }
}