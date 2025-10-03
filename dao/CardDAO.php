<?php
// /dao/CardDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';

class CardDAO {
    public function create(Card $card, $masterPassword) {
        try {
            $sql = "INSERT INTO cards (user_id, card_name, card_holder_name, card_number, expiry_date, cvv) 
                    VALUES (:user_id, :card_name, :card_holder_name, :card_number, :expiry_date, :cvv)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->bindValue(':user_id', $card->getUserId());
            $stmt->bindValue(':card_name', Crypto::encrypt($card->getCardName(), $masterPassword));
            $stmt->bindValue(':card_holder_name', Crypto::encrypt($card->getCardHolderName(), $masterPassword));
            $stmt->bindValue(':card_number', Crypto::encrypt($card->getCardNumber(), $masterPassword));
            $stmt->bindValue(':expiry_date', Crypto::encrypt($card->getExpiryDate(), $masterPassword));
            $stmt->bindValue(':cvv', Crypto::encrypt($card->getCvv(), $masterPassword));
            return $stmt->execute();
        } catch (PDOException $e) { return false; }
    }

    public function update(Card $card, $masterPassword) {
        try {
            $sql = "UPDATE cards SET 
                        card_name = :card_name, 
                        card_holder_name = :card_holder_name, 
                        card_number = :card_number, 
                        expiry_date = :expiry_date, 
                        cvv = :cvv 
                    WHERE id = :id AND user_id = :user_id";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->bindValue(':id', $card->getId());
            $stmt->bindValue(':user_id', $card->getUserId());
            $stmt->bindValue(':card_name', Crypto::encrypt($card->getCardName(), $masterPassword));
            $stmt->bindValue(':card_holder_name', Crypto::encrypt($card->getCardHolderName(), $masterPassword));
            $stmt->bindValue(':card_number', Crypto::encrypt($card->getCardNumber(), $masterPassword));
            $stmt->bindValue(':expiry_date', Crypto::encrypt($card->getExpiryDate(), $masterPassword));
            $stmt->bindValue(':cvv', Crypto::encrypt($card->getCvv(), $masterPassword));
            return $stmt->execute();
        } catch (PDOException $e) { return false; }
    }

    public function getAllByUserId($userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("SELECT * FROM cards WHERE user_id = ? ORDER BY card_name ASC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return []; }
    }

    public function getById($id, $userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("SELECT * FROM cards WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return false; }
    }
    
    public function delete($id, $userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("DELETE FROM cards WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) { return false; }
    }
}