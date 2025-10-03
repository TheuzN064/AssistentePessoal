<?php
// /dao/SubscriptionDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Crypto.php';
require_once __DIR__ . '/../security/Logger.php';

class SubscriptionDAO {
    // ... os métodos create, linkPeopleToSubscription, getAllByUserId, markPayment continuam os mesmos ...
    public function create(Subscription $sub, $masterPassword) {
        try {
            $sql = "INSERT INTO subscriptions (user_id, password_id, card_id, name, value, renewal_day, is_shared) 
                    VALUES (:user_id, :password_id, :card_id, :name, :value, :renewal_day, :is_shared)";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->bindValue(':user_id', $sub->getUserId());
            $stmt->bindValue(':password_id', $sub->getPasswordId());
            $stmt->bindValue(':card_id', $sub->getCardId());
            $stmt->bindValue(':name', Crypto::encrypt($sub->getName(), $masterPassword));
            $stmt->bindValue(':value', Crypto::encrypt($sub->getValue(), $masterPassword));
            $stmt->bindValue(':renewal_day', $sub->getRenewalDay(), PDO::PARAM_INT);
            $stmt->bindValue(':is_shared', $sub->getIsShared(), PDO::PARAM_INT);
            $stmt->execute();
            $lastId = ConexaoPDO::getInstance()->lastInsertId();
            Logger::log('INFO', "Nova assinatura criada (ID: {$lastId}) pelo usuário ID {$sub->getUserId()}.");
            return $lastId;
        } catch (PDOException $e) { 
            Logger::log('ERROR', "SubscriptionDAO::create - " . $e->getMessage());
            return false; 
        }
    }

    public function linkPeopleToSubscription($subscriptionId, array $peopleIds) {
        try {
            $pdo = ConexaoPDO::getInstance();
            $stmt = $pdo->prepare("DELETE FROM subscription_people WHERE subscription_id = ?");
            $stmt->execute([$subscriptionId]);

            $sql = "INSERT INTO subscription_people (subscription_id, person_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            foreach ($peopleIds as $personId) {
                $stmt->execute([$subscriptionId, $personId]);
            }
            Logger::log('INFO', "Pessoas (" . implode(',', $peopleIds) . ") associadas à assinatura ID {$subscriptionId}.");
            return true;
        } catch (PDOException $e) { 
            Logger::log('ERROR', "SubscriptionDAO::linkPeopleToSubscription - " . $e->getMessage());
            return false; 
        }
    }

    public function getAllByUserId($userId) {
        try {
            $currentMonth = date('n');
            $currentYear = date('Y');

            $sql = "SELECT s.*, c.card_name, p.name as password_name
                    FROM subscriptions s
                    LEFT JOIN cards c ON s.card_id = c.id
                    LEFT JOIN passwords p ON s.password_id = p.id
                    WHERE s.user_id = ? ORDER BY s.id DESC";
            $stmt = ConexaoPDO::getInstance()->prepare($sql);
            $stmt->execute([$userId]);
            $subscriptions = $stmt->fetchAll();

            for ($i = 0; $i < count($subscriptions); $i++) {
                $stmt = ConexaoPDO::getInstance()->prepare(
                    "SELECT p.id, p.name 
                     FROM people p 
                     JOIN subscription_people sp ON p.id = sp.person_id 
                     WHERE sp.subscription_id = ?"
                );
                $stmt->execute([$subscriptions[$i]['id']]);
                $people = $stmt->fetchAll();

                for ($j = 0; $j < count($people); $j++) {
                    $stmt = ConexaoPDO::getInstance()->prepare(
                        "SELECT COUNT(*) FROM subscription_payments 
                         WHERE subscription_id = ? AND person_id = ? AND payment_month = ? AND payment_year = ?"
                    );
                    $stmt->execute([$subscriptions[$i]['id'], $people[$j]['id'], $currentMonth, $currentYear]);
                    $people[$j]['has_paid'] = $stmt->fetchColumn() > 0;
                }
                $subscriptions[$i]['shared_with_details'] = $people;
            }
            return $subscriptions;
        } catch (PDOException $e) { 
            Logger::log('ERROR', "SubscriptionDAO::getAllByUserId - " . $e->getMessage());
            return []; 
        }
    }
    
    public function updateMonthlyPayments($subscriptionId, array $paidPersonIds, $month, $year) {
        $pdo = ConexaoPDO::getInstance();
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("DELETE FROM subscription_payments WHERE subscription_id = ? AND payment_month = ? AND payment_year = ?");
            $stmt->execute([$subscriptionId, $month, $year]);

            if (!empty($paidPersonIds)) {
                $sql = "INSERT INTO subscription_payments (subscription_id, person_id, payment_month, payment_year) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                foreach ($paidPersonIds as $personId) {
                    $stmt->execute([$subscriptionId, $personId, $month, $year]);
                }
            }
            
            $pdo->commit();
            Logger::log('INFO', "Pagamentos atualizados para a assinatura ID {$subscriptionId} para o mês {$month}/{$year}.");
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            Logger::log('ERROR', "SubscriptionDAO::updateMonthlyPayments - " . $e->getMessage());
            return false;
        }
    }

    public function getSubscriptionById($id, $userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("SELECT * FROM subscriptions WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            Logger::log('ERROR', "SubscriptionDAO::getSubscriptionById - " . $e->getMessage());
            return null;
        }
    }

    // MÉTODO ATUALIZADO PARA O EXTRATO COMPLETO
    public function getPaymentHistory($subscriptionId, $userId) {
        try {
            $pdo = ConexaoPDO::getInstance();

            // 1. Pega todas as pessoas que já foram associadas a esta assinatura
            $stmt_people = $pdo->prepare(
                "SELECT p.id, p.name FROM people p
                 JOIN subscription_people sp ON p.id = sp.person_id
                 WHERE sp.subscription_id = ? AND p.user_id = ?"
            );
            $stmt_people->execute([$subscriptionId, $userId]);
            $all_people = $stmt_people->fetchAll(PDO::FETCH_ASSOC);

            if (empty($all_people)) return [];

            // 2. Pega todos os períodos (mês/ano) que tiveram pelo menos um pagamento
            $stmt_periods = $pdo->prepare(
                "SELECT DISTINCT payment_month, payment_year
                 FROM subscription_payments
                 WHERE subscription_id = ?
                 ORDER BY payment_year DESC, payment_month DESC"
            );
            $stmt_periods->execute([$subscriptionId]);
            $payment_periods = $stmt_periods->fetchAll(PDO::FETCH_ASSOC);

            $history = [];
            foreach ($payment_periods as $period) {
                $month = $period['payment_month'];
                $year = $period['payment_year'];
                $monthYearKey = DateTime::createFromFormat('!m', $month)->format('F') . ' ' . $year;
                
                $people_status_in_month = [];
                foreach ($all_people as $person) {
                    // Para cada pessoa, verifica se ela pagou neste período específico
                    $stmt_payment = $pdo->prepare(
                        "SELECT COUNT(*) FROM subscription_payments
                         WHERE subscription_id = ? AND person_id = ? AND payment_month = ? AND payment_year = ?"
                    );
                    $stmt_payment->execute([$subscriptionId, $person['id'], $month, $year]);
                    $has_paid = $stmt_payment->fetchColumn() > 0;

                    $people_status_in_month[] = [
                        'name' => $person['name'], // encrypted
                        'paid' => $has_paid
                    ];
                }
                $history[$monthYearKey] = $people_status_in_month;
            }
            return $history;
        } catch (PDOException $e) {
            Logger::log('ERROR', "SubscriptionDAO::getPaymentHistory - " . $e->getMessage());
            return [];
        }
    }
}
