<?php
// /models/Card.php
class Card {
    private $id;
    private $user_id;
    private $card_name;
    private $card_holder_name;
    private $card_number;
    private $expiry_date;
    private $cvv;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getCardName() { return $this->card_name; }
    public function setCardName($card_name) { $this->card_name = $card_name; }
    public function getCardHolderName() { return $this->card_holder_name; }
    public function setCardHolderName($card_holder_name) { $this->card_holder_name = $card_holder_name; }
    public function getCardNumber() { return $this->card_number; }
    public function setCardNumber($card_number) { $this->card_number = $card_number; }
    public function getExpiryDate() { return $this->expiry_date; }
    public function setExpiryDate($expiry_date) { $this->expiry_date = $expiry_date; }
    public function getCvv() { return $this->cvv; }
    public function setCvv($cvv) { $this->cvv = $cvv; }
}
