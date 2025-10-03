<?php
// /dao/GroupDAO.php
require_once __DIR__ . '/../config/ConexaoPDO.php';

class GroupDAO {
    /**
     * Encontra um grupo pelo nome ou o cria se não existir.
     * @param string $groupName O nome do grupo.
     * @param int $userId O ID do usuário.
     * @return int|null O ID do grupo encontrado ou recém-criado.
     */
    public function createOrFind($groupName, $userId) {
        $groupName = trim($groupName);
        if (empty($groupName)) {
            return null; // Retorna nulo se o nome do grupo estiver vazio
        }

        try {
            $pdo = ConexaoPDO::getInstance();
            // Verifica se o grupo já existe para este usuário
            $stmt = $pdo->prepare("SELECT id FROM groups WHERE user_id = ? AND name = ?");
            $stmt->execute([$userId, $groupName]);
            $group = $stmt->fetch();

            if ($group) {
                // Se o grupo existe, retorna seu ID
                return $group['id'];
            } else {
                // Se não existe, cria um novo
                $stmt = $pdo->prepare("INSERT INTO groups (user_id, name) VALUES (?, ?)");
                $stmt->execute([$userId, $groupName]);
                return $pdo->lastInsertId(); // Retorna o ID do novo grupo
            }
        } catch (PDOException $e) {
            error_log("GroupDAO Error (createOrFind): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca todos os grupos de um usuário.
     * @param int $userId O ID do usuário.
     * @return array A lista de grupos.
     */
    public function getAllByUserId($userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("SELECT * FROM groups WHERE user_id = ? ORDER BY name ASC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Atualiza o nome de um grupo.
     * @param int $id O ID do grupo.
     * @param string $name O novo nome.
     * @param int $userId O ID do usuário (para segurança).
     * @return bool
     */
    public function update($id, $name, $userId) {
        try {
            $stmt = ConexaoPDO::getInstance()->prepare("UPDATE groups SET name = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$name, $id, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Apaga um grupo.
     * @param int $id O ID do grupo.
     * @param int $userId O ID do usuário (para segurança).
     * @return bool
     */
    public function delete($id, $userId) {
        try {
            // Senhas que estavam neste grupo ficarão sem grupo (group_id = NULL)
            $stmt = ConexaoPDO::getInstance()->prepare("DELETE FROM groups WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
