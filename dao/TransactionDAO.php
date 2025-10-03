<?php
// /dao/TransactionDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/../security/Logger.php';
require_once __DIR__ . '/../models/Transaction.php';

class TransactionDAO {
    
    public function create(Transaction $transaction, $masterPassword) {
        try {
            $sql = "INSERT INTO transactions (user_id, category_id, name, description, value, type, transaction_date) 
                    VALUES (:user_id, :category_id, :name, :description, :value, :type, :transaction_date)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->bindValue(':user_id', $transaction->getUserId());
            $stmt->bindValue(':category_id', $transaction->getCategoryId());
            $stmt->bindValue(':name', Crypto::encrypt($transaction->getName(), $masterPassword));
            $stmt->bindValue(':description', Crypto::encrypt($transaction->getDescription(), $masterPassword));
            $stmt->bindValue(':value', Crypto::encrypt($transaction->getValue(), $masterPassword));
            $stmt->bindValue(':type', $transaction->getType());
            $stmt->bindValue(':transaction_date', $transaction->getTransactionDate());
            $success = $stmt->execute();
            if ($success) {
                Logger::log('INFO', "Nova transação criada pelo usuário ID " . $transaction->getUserId());
            }
            return $success;
        } catch (PDOException $e) {
            Logger::log('ERROR', "TransactionDAO::create - " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function getByMonthYear($userId, $month, $year, $masterPassword) {
        $sql = "SELECT t.*, tc.name as category_name 
                FROM transactions t
                LEFT JOIN transaction_categories tc ON t.category_id = tc.id
                WHERE t.user_id = :user_id AND MONTH(t.transaction_date) = :month AND YEAR(t.transaction_date) = :year
                ORDER BY t.transaction_date DESC, t.id DESC";
        $stmt = ConexaoPDO::getInstance()->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':month', $month);
        $stmt->bindValue(':year', $year);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $key => $row) {
            try {
                $results[$key]['name'] = Crypto::decrypt($row['name'], $masterPassword);
                $results[$key]['description'] = Crypto::decrypt($row['description'], $masterPassword);
                $results[$key]['value'] = (float) Crypto::decrypt($row['value'], $masterPassword);
            } catch (Exception $e) {
                $results[$key]['name'] = '[Erro na descriptografia]';
                $results[$key]['value'] = 0.0;
                Logger::log('ERROR', "Falha ao descriptografar dados da transação ID: " . $row['id']);
            }
        }
        return $results;
    }

    public function getMonthlySummary($userId, $month, $year, $masterPassword) {
        $summary = ['income' => 0.0, 'expense' => 0.0, 'balance' => 0.0];
        
        $transactionsThisMonth = $this->getByMonthYear($userId, $month, $year, $masterPassword);
        foreach ($transactionsThisMonth as $transaction) {
            if ($transaction['type'] === 'income') {
                $summary['income'] += $transaction['value'];
            } else {
                $summary['expense'] += $transaction['value'];
            }
        }

        $firstDayOfNextMonth = date('Y-m-d', strtotime("{$year}-{$month}-01 +1 month"));
        $sql = "SELECT value, type FROM transactions WHERE user_id = :user_id AND transaction_date < :end_date";
        $stmt = ConexaoPDO::getInstance()->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':end_date', $firstDayOfNextMonth);
        $stmt->execute();
        $allTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalBalance = 0.0;
        foreach ($allTransactions as $t) {
            try {
                $value = (float) Crypto::decrypt($t['value'], $masterPassword);
                if ($t['type'] === 'income') {
                    $totalBalance += $value;
                } else {
                    $totalBalance -= $value;
                }
            } catch (Exception $e) { continue; }
        }
        $summary['balance'] = $totalBalance;
        
        return $summary;
    }
    
    public function delete($id, $userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("DELETE FROM transactions WHERE id = :id AND user_id = :user_id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':user_id', $userId);
            $success = $stmt->execute();
            if ($success && $stmt->rowCount() > 0) {
                Logger::log('INFO', "Transação ID {$id} apagada pelo usuário ID {$userId}.");
                return true;
            }
            return false;
        } catch (PDOException $e) {
            Logger::log('ERROR', "TransactionDAO::delete - " . $e->getMessage());
            return false;
        }
    }
}