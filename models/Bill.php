<?php
// /models/Bill.php
class Bill {
    private $id;
    private $user_id;
    private $name;
    private $value;
    private $due_date;
    private $is_paid;

    // Getters e Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    public function getValue() { return $this->value; }
    public function setValue($value) { $this->value = $value; }
    public function getDueDate() { return $this->due_date; }
    public function setDueDate($due_date) { $this->due_date = $due_date; }
    public function getIsPaid() { return $this->is_paid; }
    public function setIsPaid($is_paid) { $this->is_paid = $is_paid; }
}

