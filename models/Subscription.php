<?php
// /models/Subscription.php
class Subscription {
    private $id;
    private $user_id;
    private $password_id;
    private $card_id; // Estava faltando
    private $name;
    private $value;
    private $renewal_day; // Estava faltando
    private $is_shared;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }

    public function getPasswordId() { return $this->password_id; }
    public function setPasswordId($password_id) { $this->password_id = $password_id; }

    // Método que estava faltando
    public function getCardId() { return $this->card_id; }
    public function setCardId($card_id) { $this->card_id = $card_id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getValue() { return $this->value; }
    public function setValue($value) { $this->value = $value; }

    // Método que estava faltando
    public function getRenewalDay() { return $this->renewal_day; }
    public function setRenewalDay($renewal_day) { $this->renewal_day = $renewal_day; }

    public function getIsShared() { return $this->is_shared; }
    public function setIsShared($is_shared) { $this->is_shared = $is_shared; }
}
