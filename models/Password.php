<?php
class Password {
    private $id;
    private $user_id;
    private $group_id; // Adicionado
    private $name;
    private $description;
    private $site_url;
    private $email;
    private $password_text;
    private $recovery_codes;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getGroupId() { return $this->group_id; } // Adicionado
    public function setGroupId($group_id) { $this->group_id = $group_id; } // Adicionado
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }
    public function getSiteUrl() { return $this->site_url; }
    public function setSiteUrl($site_url) { $this->site_url = $site_url; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    public function getPasswordText() { return $this->password_text; }
    public function setPasswordText($password_text) { $this->password_text = $password_text; }
    public function getRecoveryCodes() { return $this->recovery_codes; }
    public function setRecoveryCodes($recovery_codes) { $this->recovery_codes = $recovery_codes; }
}
