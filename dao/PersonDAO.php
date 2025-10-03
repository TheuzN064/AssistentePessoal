<?php
// /dao/PersonDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';

class PersonDAO {
    public function create($name, $userId, $masterPassword) {
        try {
            $sql = "INSERT INTO people (user_id, name) VALUES (?, ?)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $encryptedName = Crypto::encrypt($name, $masterPassword);
            $stmt->execute([$userId, $encryptedName]);
            return true;
        } catch (PDOException $e) { return false; }
    }

    public function getAllByUserId($userId) {
        try {
            $sql = "SELECT id, name FROM people WHERE user_id = ? ORDER BY name ASC";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
    
    public function delete($id, $userId) {
        try {
            $sql = "DELETE FROM people WHERE id = ? AND user_id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$id, $userId]);
            return true;
        } catch (PDOException $e) { return false; }
    }
}
