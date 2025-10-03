<?php
// /dao/BillDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/../security/Logger.php';

class BillDAO {
    public function create(Bill $bill, $masterPassword) {
        try {
            $sql = "INSERT INTO bills (user_id, name, value, due_date) VALUES (?, ?, ?, ?)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([
                $bill->getUserId(),
                Crypto::encrypt($bill->getName(), $masterPassword),
                Crypto::encrypt($bill->getValue(), $masterPassword),
                $bill->getDueDate()
            ]);
            Logger::log('INFO', "Nova conta criada pelo usuário ID {$bill->getUserId()}.");
            return true;
        } catch (PDOException $e) {
            Logger::log('ERROR', "BillDAO::create - " . $e->getMessage());
            return false;
        }
    }

    public function getBillsByMonthYear($userId, $month, $year) {
        try {
            $sql = "SELECT * FROM bills WHERE user_id = ? AND MONTH(due_date) = ? AND YEAR(due_date) = ? ORDER BY due_date ASC";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId, $month, $year]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    // NOVO MÉTODO PARA ATUALIZAR TODOS OS STATUS DE UMA VEZ
    public function updatePaidStatusesForMonth($userId, $month, $year, array $paidBillIds) {
        $pdo = ConexaoPDO::getInstance();
        try {
            $pdo->beginTransaction();

            // 1. Primeiro, marca TODAS as contas do mês como NÃO PAGAS.
            $sqlReset = "UPDATE bills SET is_paid = 0, paid_at = NULL WHERE user_id = ? AND MONTH(due_date) = ? AND YEAR(due_date) = ?";
            $stmtReset = $pdo->prepare($sqlReset);
            $stmtReset->execute([$userId, $month, $year]);

            // 2. Depois, marca como PAGAS apenas as que vieram no array de IDs.
            if (!empty($paidBillIds)) {
                // Cria os placeholders (?) para a query IN () de forma segura
                $placeholders = implode(',', array_fill(0, count($paidBillIds), '?'));
                $sqlMarkPaid = "UPDATE bills SET is_paid = 1, paid_at = NOW() WHERE user_id = ? AND id IN ($placeholders)";
                $stmtMarkPaid = $pdo->prepare($sqlMarkPaid);
                
                // Associa os valores (bind)
                $params = array_merge([$userId], $paidBillIds);
                $stmtMarkPaid->execute($params);
            }
            
            $pdo->commit();
            Logger::log('INFO', "Status de pagamento das contas de {$month}/{$year} atualizado pelo usuário ID {$userId}.");
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            Logger::log('ERROR', "BillDAO::updatePaidStatusesForMonth - " . $e->getMessage());
            return false;
        }
    }

    public function delete($billId, $userId) {
        try {
            $sql = "DELETE FROM bills WHERE id = ? AND user_id = ?";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$billId, $userId]);
            Logger::log('INFO', "Conta ID {$billId} apagada.");
            return true;
        } catch (PDOException $e) { return false; }
    }
}
