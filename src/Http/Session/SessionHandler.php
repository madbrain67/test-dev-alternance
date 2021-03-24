<?php

namespace App\Http\Session;

use App\Http\Response;
use App\Model\AbstractManager;

class SessionHandler extends AbstractManager
{
    /**
     * Variable $this->_session.
     *
     * @var Session
     */
    private $_session;

    /**
     * @var string Database driver
     */
    private $_driver = APP_DB_TYPE;

    /**
     * @var string Table name
     */
    private $_table = 'sessions';

    /**
     * @var string Column for session id
     */
    private $_idCol = 'sess_id';

    /**
     * @var string Column for session id
     */
    private $_userCol = 'sess_user_id';

    /**
     * @var string Column for session data
     */
    private $_dataCol = 'sess_data';

    /**
     * @var string Column for lifetime
     */
    private $_lifetimeCol = 'sess_lifetime';

    /**
     * @var string Column for timestamp
     */
    private $_timeCol = 'sess_time';

    /**
     * Void __construct().
     */
    public function __construct()
    {
        $this->_session = new Session();
        parent::__construct();
    }

    /**
     * Creates the table to store sessions which can be called once for setup.
     *
     * Session ID is saved in a column of maximum length 128 because that is enough even
     * for a 512 bit configured session.hash_function like Whirlpool. Session data is
     * saved in a BLOB. One could also use a shorter inlined varbinary column
     * if one was sure the data fits into it.
     *
     * @throws \PDOException    When the table already exists
     * @throws \DomainException When an unsupported PDO driver is used
     */
    public function createTable()
    {
        switch ($this->_driver) {
            case 'mysql':
                // We use varbinary for the ID column because it prevents unwanted conversions:
                // - character set conversions between server and client
                // - trailing space removal
                // - case-insensitivity
                // - language processing like é == e
                $sql = "CREATE TABLE $this->table ($this->idCol VARBINARY(128) NOT NULL PRIMARY KEY, $this->userCol INTEGER UNSIGNED NOT NULL, $this->dataCol BLOB NOT NULL, $this->lifetimeCol MEDIUMINT NOT NULL, $this->timeCol INTEGER UNSIGNED NOT NULL) COLLATE utf8_bin, ENGINE = InnoDB";
                break;
            case 'sqlite':
                $sql = "CREATE TABLE $this->table ($this->idCol TEXT NOT NULL PRIMARY KEY, $this->userCol INTEGER UNSIGNED NOT NULL, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)";
                break;
            case 'pgsql':
                $sql = "CREATE TABLE $this->table ($this->idCol VARCHAR(128) NOT NULL PRIMARY KEY, $this->userCol INTEGER UNSIGNED NOT NULL, $this->dataCol BYTEA NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)";
                break;
            case 'oci':
                $sql = "CREATE TABLE $this->table ($this->idCol VARCHAR2(128) NOT NULL PRIMARY KEY, $this->userCol INTEGER UNSIGNED NOT NULL, $this->dataCol BLOB NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)";
                break;
            case 'sqlsrv':
                $sql = "CREATE TABLE $this->table ($this->idCol VARCHAR(128) NOT NULL PRIMARY KEY, $this->userCol INTEGER UNSIGNED NOT NULL, $this->dataCol VARBINARY(MAX) NOT NULL, $this->lifetimeCol INTEGER NOT NULL, $this->timeCol INTEGER NOT NULL)";
                break;
            default:
                throw new \DomainException(sprintf('Creating the session table is currently not implemented for PDO driver "%s".', '_'));
        }

        try {
            $this->pdo->exec($sql);
        } catch (\PDOException $e) {
            //$this->rollback();
            throw $e;
        }
    }

    /**
     * Undocumented function.
     *
     * @param string $session_id comment
     * @param int    $user       comment
     */
    public function selectTimestamp(string $session_id, int $user): array
    {
        $results = [];

        try {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM $this->_table WHERE $this->_idCol = :id AND $this->_userCol = :user"
            );
            $stmt->bindParam(':id', $session_id, \PDO::PARAM_STR);
            $stmt->bindParam(':user', $user, \PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($results) {
                $results = $results[0];
                if ($results['sess_time'] > time()) {
                    $this->updateTimestamp($session_id, $user, $this->_session->all());
                } else {
                    $this->destroyTimestamp($session_id);
                    $this->_session->destroy();
                    $response = new Response();
                    $response->redirection(BASE);
                }
            } else {
                if ('' !== $this->_session->get('user')) {
                    $this->_session->destroy();
                    $response = new Response();
                    $response->redirection(BASE);
                }
            }
        } catch (\PDOException $e) {
            //$this->rollback();
            throw $e;
        }

        //echo '<pre>' . print_r($results, true) . '</pre>';

        return $results;
    }

    /**
     * Insert the record associated with this id.
     *
     * @param string $session_id comment
     * @param int    $user       comment
     * @param array  $data       comment
     */
    public function insertTimestamp(string $session_id, int $user, array $data): bool
    {
        $maxlifetime = (int) ini_get('session.gc_maxlifetime');
        $data = json_encode(end($data));

        try {
            $insertStmt = $this->pdo->prepare(
                "INSERT INTO $this->_table ($this->_idCol, $this->_userCol, $this->_dataCol, $this->_lifetimeCol, $this->_timeCol) VALUES (:id, :user, :data, :lifetime, :time)"
            );
            $insertStmt->bindParam(':id', $session_id, \PDO::PARAM_STR);
            $insertStmt->bindParam(':user', $user, \PDO::PARAM_INT);
            $insertStmt->bindParam(':data', $data, \PDO::PARAM_STR);
            $insertStmt->bindParam(':lifetime', $maxlifetime, \PDO::PARAM_INT);
            $insertStmt->bindValue(':time', (time() + $maxlifetime), \PDO::PARAM_INT);
            $insertStmt->execute();
        } catch (\PDOException $e) {
            //$this->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Update the record associated with this id.
     *
     * @param string $session_id comment
     * @param int    $user       comment
     * @param array  $data       comment
     */
    public function updateTimestamp(string $session_id, int $user, array $data): bool
    {
        $maxlifetime = (int) ini_get('session.gc_maxlifetime');
        $data = json_encode(end($data));

        try {
            $updateStmt = $this->pdo->prepare(
                "UPDATE $this->_table SET $this->_userCol = :user, $this->_dataCol = :data, $this->_lifetimeCol = :lifetime, $this->_timeCol = :time WHERE $this->_idCol = :id"
            );
            $updateStmt->bindParam(':id', $session_id, \PDO::PARAM_STR);
            $updateStmt->bindParam(':user', $user, \PDO::PARAM_INT);
            $updateStmt->bindParam(':data', $data, \PDO::PARAM_STR);
            $updateStmt->bindParam(':lifetime', $maxlifetime, \PDO::PARAM_INT);
            $updateStmt->bindValue(':time', (time() + $maxlifetime), \PDO::PARAM_INT);
            $updateStmt->execute();
        } catch (\PDOException $e) {
            //$this->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Delete the record associated with this id.
     *
     * @return void
     */
    public function destroyTimestamp(string $session_id)
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM $this->_table WHERE $this->_idCol = :id"
            );
            $stmt->bindParam(':id', $session_id, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            //$this->rollback();
            throw $e;
        }

        return true;
    }

    /**
     * Helper method to rollback a transaction.
     */
    private function rollback()
    {
        // We only need to rollback if we are in a transaction. Otherwise the resulting
        // error would hide the real problem why rollback was called. We might not be
        // in a transaction when not using the transactional locking behavior or when
        // two callbacks (e.g. destroy and write) are invoked that both fail.
        if ($this->inTransaction) {
            if ('sqlite' === '_') {
                $this->pdo->exec('ROLLBACK');
            } else {
                $this->pdo->rollBack();
            }
            $this->inTransaction = false;
        }
    }
}
