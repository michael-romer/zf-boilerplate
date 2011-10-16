<?php
/**
 * phpRack: Integration Testing Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL: http://www.phprack.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phprack.com so we can send you a copy immediately.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright (c) phpRack.com
 * @version $Id: Mysql.php 611 2010-07-12 14:23:40Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Db_Abstract
 */
require_once PHPRACK_PATH . '/Adapters/Db/Abstract.php';

/**
 * MySQL adapter
 *
 * The class is using native PHP mysql_ methods, without any specific
 * extensions like PDO or Mysqli.
 *
 * @package Adapters
 * @subpackage Db
 */
class phpRack_Adapters_Db_Mysql extends phpRack_Adapters_Db_Abstract
{

    /**
     * Current mysql connection link identifier
     *
     * @var int Result of mysql_connect()
     * @see connect()
     */
    private $_connection;

    /**
     * Destructor automatically close opened connection
     *
     * @return void
     */
    public function __destruct()
    {
        $this->closeConnection();
    }

    /**
     * Connect to the server
     *
     * @param string JDBC URL to connect to the server
     * @return void
     * @see http://java.sun.com/docs/books/tutorial/jdbc/basics/connecting.html
     * @throws Exception If MySQL extension is not loaded
     * @throws Exception If any of the required params are missed in the URL
     */
    public function connect($url)
    {
        // Parse JDBC URl, and throw exception if it is invalid
        $jdbcUrlParts = $this->_parseJdbcUrl($url);

        if (!extension_loaded('mysql')) {
            throw new Exception('MySQL extension is not loaded');
        }

        $server = $jdbcUrlParts['host'];

        // Check whether server port was set in JDBC URL
        if (isset($jdbcUrlParts['port'])) {
            $server .= ':' . $jdbcUrlParts['port'];
        }

        // Check whether username was set in JDBC URL
        if (isset($jdbcUrlParts['params']['username'])) {
            $username = $jdbcUrlParts['params']['username'];
        } else {
            $username = ini_get('mysql.default_user');
        }

        // Check whether password was set in JDBC URL
        if (isset($jdbcUrlParts['params']['password'])) {
            $password = $jdbcUrlParts['params']['password'];
        } else {
            $password = ini_get('mysql.default_password');
        }

        // Try to connect with MySQL server
        $this->_connection = @mysql_connect($server, $username, $password);

        if (!$this->_connection) {
            throw new Exception("Can't connect to MySQL server: '{$server}'");
        }

        // Check whether database was set in JDBC URL
        if (!empty($jdbcUrlParts['database'])) {
            // Try to set this database as current
            if (!@mysql_select_db($jdbcUrlParts['database'], $this->_connection)) {
                throw new Exception("Can't select database '{$jdbcUrlParts['database']}'");
            }
        }
    }

    /**
     * Execute SQL query on the server
     *
     * @param string SQL query
     * @return string Raw result from the server, in text
     * @throws Exception If something wrong happens there
     * @see mysql_query()
     */
    public function query($sql)
    {
        if (!$this->_connection) {
            throw new Exception('connect() method should be called before');
        }

        $result = mysql_query($sql, $this->_connection);

        // INSERT, UPDATE, DELETE, DROP, USE etc type queries
        // on success return just true
        if ($result === true) {
            return '';
        }

        // Something goes wrong
        if ($result === false) {
            throw new Exception('MySQL query error: ' . mysql_error());
        }

        // SELECT, SHOW type queries
        // if MySQL server returned some rows, format them for return
        if (mysql_num_rows($result)) {
            /**
             * @see phpRack_Adapters_Db_Mysql_Result_Formatter
             */
            require_once PHPRACK_PATH . '/Adapters/Db/Mysql/Result/Formatter.php';
            $response = phpRack_Adapters_Db_Mysql_Result_Formatter::formatResult($result);
        } else {
            $response = '';
        }

        return $response;
    }

    /**
     * Show database schema
     *
     * @return string Raw result from the server, in text
     * @throws Exception If connect() method wasn't executed earlier
     * @throws Exception If no database was selected as current
     * @throws Exception Passed from query()
     * @see phpRack_Package_Db_Mysql::showSchema()
     */
    public function showSchema()
    {
        if (!$this->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        if (!$this->isDatabaseSelected()) {
            throw new Exception('No database selected yet');
        }

        $response = '';
        $queries = array('SHOW TABLES', 'SHOW TRIGGERS', 'SHOW PROCEDURE STATUS');
        foreach ($queries as $query) {
            $response .= sprintf(
                "'%s' returns:\n%s\n",
                $query,
                $result = $this->query($query) // Exception is possible here
            );

            if ($query == 'SHOW TABLES') {
                // foreach table show CREATE TABLE
                foreach (array_slice(explode("\n", $result), 1, -1) as $tableName) {
                    $query = sprintf("SHOW CREATE TABLE `%s`", addcslashes(trim($tableName), '`'));
                    $response .= sprintf(
                        "'%s' returns:\n%s\n",
                        $query,
                        $this->query($query) // Exception is possible
                    );
                }
            }
        }
        return $response;
    }

    /**
     * Show connections and their status
     *
     * @return string Raw result from the server, in text
     * @throws Exception If connect() method wasn't executed earlier
     * @see phpRack_Package_Db_Mysql::showConnections()
     */
    public function showConnections()
    {
        if (!$this->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        $answer = $this->query('SHOW GRANTS FOR CURRENT_USER');
        if (!preg_match('~GRANT (PROCESS|ALL)~', $answer)) {
            return false;
        }

        return $this->query('SHOW FULL PROCESSLIST');
    }

    /**
     * Show server info
     *
     * @return string Raw result from the server, in text
     * @throws Exception If connect() method wasn't executed earlier
     * @see phpRack_Package_Db_Mysql::showServerInfo()
     */
    public function showServerInfo()
    {
        $out = '';

        // Users privileges. We must check grants for it
        $privileges = $this->query('SHOW GRANTS FOR CURRENT_USER');
        if (preg_match('~GRANT (ALL|SELECT ON (`mysql`|\*)\.\*)~', $privileges)) {
            $dbUsers = $this->query(
                'SELECT CONCAT(User, "\'@\'", Host) FROM `mysql`.`user`'
            );
            $dbUsers = explode("\n", rtrim($dbUsers, "\n"));
            $dbUsers = array_map('trim', $dbUsers);
            for ($i=1; $i<count($dbUsers); $i++) {
                 $priv = $this->query("SHOW GRANTS FOR '{$dbUsers[$i]}'");
                 $out .= preg_replace('~GRANT (.+?) TO.*~', '\1', trim($priv));
                 $out .= "\n\n";
            }
        }

        // Table stats. We do not need to check privileges here
        $dbList = $this->query('SHOW DATABASES');
        $dbList = explode("\n", rtrim($dbList, "\n"));
        if (count($dbList) > 2) {
            $dbList = array_map('trim', $dbList);
            for ($i=2; $i<count($dbList); $i++) {
                $out .= "Database: {$dbList[$i]}\n";
                $out .= $this->query(
                    "SELECT COUNT(TABLE_NAME) AS 'Count Of Tables',
                        SUM(TABLE_ROWS) AS 'Count Of Rows',
                        SUM(DATA_LENGTH) AS 'Size Of Data',
                        SUM(INDEX_LENGTH) AS 'Index Size'
                        FROM `information_schema`.`TABLES`
                        WHERE TABLE_SCHEMA = '{$dbList[$i]}'"
                );
               $out .= "\n";
            }
        }

        // Mysql version. We do not need to check privileges here
        $out .= $this->query("SHOW VARIABLES LIKE 'version'") . "\n";

        // Mysql variables. We do not need to check privileges here
        $out .= $this->query('SHOW GLOBAL VARIABLES') . "\n";
        return $out;
    }

    /**
     * Return true if adapter is connected with database
     *
     * @return boolean
     * @see $this->_connection
     */
    public function isConnected()
    {
        if ($this->_connection) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return true if some database was selected for use
     *
     * @return boolean
     */
    public function isDatabaseSelected()
    {
        $result = $this->query('SELECT DATABASE()');
        /**
         * @see phpRack_Adapters_Db_Mysql_Result_Formatter
         */
        require_once PHPRACK_PATH . '/Adapters/Db/Mysql/Result/Formatter.php';

        if (trim(phpRack_Adapters_Db_Mysql_Result_Formatter::removeColumnHeadersLine($result)) == '') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Close connection to database, if was earlier opened
     *
     * @return void
     */
    public function closeConnection()
    {
        if (is_resource($this->_connection)) {
            mysql_close($this->_connection);
            $this->_connection = null;
        }
    }
}
