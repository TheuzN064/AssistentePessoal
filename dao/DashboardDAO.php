<?php
// /dao/DashboardDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';
require_once __DIR__ . '/../security/Logger.php';

class DashboardDAO {
    public function getSummaryData($userId) {
        $summary = [
            'Senhas' => 0, 
            'Assinaturas' => 0, 
            'Cartões' => 0, 
            'Autenticadores' => 0, 
            'Contas a Pagar' => 0
        ];

        try {
            $pdo = ConexaoPDO::getInstance();
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM passwords WHERE user_id = ?");
            $stmt->execute([$userId]);
            $summary['Senhas'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM subscriptions WHERE user_id = ?");
            $stmt->execute([$userId]);
            $summary['Assinaturas'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE user_id = ?");
            $stmt->execute([$userId]);
            $summary['Cartões'] = (int) $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM two_factor_auth WHERE user_id = ?");
            $stmt->execute([$userId]);
            $summary['Autenticadores'] = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bills WHERE user_id = ? AND is_paid = 0");
            $stmt->execute([$userId]);
            $summary['Contas a Pagar'] = (int) $stmt->fetchColumn();

            return $summary;
        } catch (PDOException $e) {
            Logger::log('ERROR', "DashboardDAO::getSummaryData - " . $e->getMessage());
            return $summary;
        }
    }
}