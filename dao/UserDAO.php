<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Logger.php';

class UserDAO {
    public function validateLogin(User $user) {
        try {
            $sql = "SELECT id, username, password_hash FROM users WHERE username = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$user->getUsername()]);
            $userDataFromDB = $stmt->fetch();

            if ($userDataFromDB && password_verify($user->getPassword(), $userDataFromDB['password_hash'])) {
                Logger::log('INFO', "Usuário '{$user->getUsername()}' validado com sucesso.");
                return ['id' => $userDataFromDB['id'], 'username' => $userDataFromDB['username']];
            }
            return false;
        } catch (PDOException $e) {
            Logger::log('ERROR', "UserDAO::validateLogin - " . $e->getMessage());
            return false;
        }
    }

    public function validateVaultPassword($userId, $vaultPassword) {
        try {
            $sql = "SELECT vault_password_hash FROM users WHERE id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch();

            if ($result && password_verify($vaultPassword, $result['vault_password_hash'])) {
                Logger::log('INFO', "Senha do cofre validada para o usuário ID {$userId}.");
                return true;
            }
            Logger::log('WARNING', "Tentativa inválida de validação da senha do cofre para o usuário ID {$userId}.");
            return false;
        } catch (PDOException $e) {
            Logger::log('ERROR', "UserDAO::validateVaultPassword - " . $e->getMessage());
            return false;
        }
    }
}
