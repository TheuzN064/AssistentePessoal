<?php
// /models/Authenticator.php
class Authenticator {
    private $id;
    private $user_id;
    private $password_id;
    private $secret_key;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getPasswordId() { return $this->password_id; }
    public function setPasswordId($password_id) { $this->password_id = $password_id; }
    public function getSecretKey() { return $this->secret_key; }
    public function setSecretKey($secret_key) { $this->secret_key = $secret_key; }
}
