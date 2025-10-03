<?php
// /models/Transaction.php
class Transaction {
    private $id;
    private $user_id;
    private $category_id;
    private $name;
    private $description;
    private $value;
    private $type;
    private $transaction_date;

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getUserId(): ?int { return $this->user_id; }
    public function setUserId(int $user_id): void { $this->user_id = $user_id; }
    public function getCategoryId(): ?int { return $this->category_id; }
    public function setCategoryId(?int $category_id): void { $this->category_id = $category_id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function getValue(): ?string { return $this->value; }
    public function setValue(string $value): void { $this->value = $value; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }
    public function getTransactionDate(): ?string { return $this->transaction_date; }
    public function setTransactionDate(string $transaction_date): void { $this->transaction_date = $transaction_date; }
}