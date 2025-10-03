<?php
// /models/Person.php
class Person {
    private $id;
    private $user_id;
    private $name;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
}
