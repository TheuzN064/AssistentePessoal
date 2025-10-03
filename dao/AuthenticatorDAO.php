<?php
// /dao/AuthenticatorDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/../security/Logger.php';

class AuthenticatorDAO {
    public function create(Authenticator $auth, $masterPassword) {
        try {
            $sql = "INSERT INTO two_factor_auth (user_id, password_id, secret_key) VALUES (?, ?, ?)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $encryptedSecret = Crypto::encrypt($auth->getSecretKey(), $masterPassword);
            $stmt->execute([$auth->getUserId(), $auth->getPasswordId(), $encryptedSecret]);
            Logger::log('INFO', "Novo autenticador 2FA criado para o password_id {$auth->getPasswordId()}.");
            return true;
        } catch (PDOException $e) {
            Logger::log('ERROR', "AuthenticatorDAO::create - " . $e->getMessage());
            return false;
        }
    }

    public function getAllByUserId($userId) {
        try {
            $sql = "SELECT tfa.*, p.name as password_name 
                    FROM two_factor_auth tfa
                    JOIN passwords p ON tfa.password_id = p.id
                    WHERE tfa.user_id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::log('ERROR', "AuthenticatorDAO::getAllByUserId - " . $e->getMessage());
            return [];
        }
    }

    public function delete($id, $userId) {
        try {
            $sql = "DELETE FROM two_factor_auth WHERE id = ? AND user_id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$id, $userId]);
            Logger::log('INFO', "Autenticador 2FA ID {$id} apagado pelo usuário ID {$userId}.");
            return true;
        } catch (PDOException $e) {
            Logger::log('ERROR', "AuthenticatorDAO::delete - " . $e->getMessage());
            return false;
        }
    }
}
