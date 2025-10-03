<?php
require_once __DIR__ . '/../config/ConexaoPDO.php';

class TransactionCategoryDAO {
    public function create($name, $userId) {
        $sql = "INSERT INTO transaction_categories (user_id, name) VALUES (:user_id, :name)";
        $stmt = ConexaoPDO::getInstance()->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':name', $name);
        return $stmt->execute();
    }

    public function getAllByUserId($userId) {
        $stmt = ConexaoPDO::getInstance()->prepare("SELECT * FROM transaction_categories WHERE user_id = ? ORDER BY name ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete($id, $userId) {
        // CUIDADO: Antes de apagar, o ideal seria desassociar as transações ou avisar o usuário.
        // Por simplicidade, vamos apenas apagar.
        $stmt = ConexaoPDO::getInstance()->prepare("DELETE FROM transaction_categories WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':user_id', $userId);
        return $stmt->execute();
    }
}