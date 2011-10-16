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
 * @version $Id: Mysql.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * @see phpRack_Adapters_Db_Mysql
 */
require_once PHPRACK_PATH . '/Adapters/Db/Mysql.php';

/**
 * Db abstract
 *
 * @package Tests
 */
class phpRack_Package_Db_Mysql extends phpRack_Package
{
    /**
     * MySQL adapter
     *
     * @var phpRack_Adapters_Db_Mysql
     * @see __construct()
     */
    private $_adapter;

    /**
     * Construct the class
     *
     * @param phpRack_Result
     * @return void
     * @see phpRack_Package::__construct()
     */
    public function __construct(phpRack_Result $result)
    {
        parent::__construct($result);
        $this->_adapter = new phpRack_Adapters_Db_Mysql();
    }

    /**
     * Check that we can connect to mysql server
     *
     * This method converts connection parameters to JDBC URL, and uses
     * DB adapter in order to establish a real connection with MySQL. We
     * url-encode all parameters, since JDBC URL is just an URL after all.
     *
     * @param string Host
     * @param integer Port
     * @param string User name
     * @param string User password
     * @return $this
     * @see phpRack_Adapters_Db_Mysql
     */
    public function connect($host, $port, $username, $password)
    {
        $jdbcUrl = "jdbc:mysql://{$host}:{$port}"
            . '?username=' . urlencode($username)
            . '&password=' . urlencode($password);

        try {
            $this->_adapter->connect($jdbcUrl);
            $this->_success("Connected successfully to MySQL server '{$host}':'{$port}'");
        } catch(Exception $e) {
            assert($e instanceof Exception); // for ZCA only
            $this->_failure("Can't connect to MySQL server '{$host}':'{$port}', login: '{$username}'");
        }

        return $this;
    }

    /**
     * Check that database exists, and selects this database as current
     *
     * @param string Database name
     * @return $this
     * @see connect()
     * @throws Exception If this method is called before connect()
     */
    public function dbExists($dbName)
    {
        if (!$this->_adapter->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        try {
            $this->_adapter->query(sprintf("USE `%s`", addcslashes($dbName, '`')));
            $this->_success("Database '{$dbName}' exists");
        } catch (Exception $e) {
            $this->_failure($e->getMessage());
        }

        return $this;
    }

    /**
     * Check that table exists
     *
     * @param string Table name
     * @return $this
     * @see connect()
     * @throws Exception If this method is called before connect()
     * @throws Exception If this method is called before dbExists()
     */
    public function tableExists($tableName)
    {
        if (!$this->_adapter->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        if (!$this->_adapter->isDatabaseSelected()) {
            throw new Exception('You must call dbExists() method before');
        }

        $response = $this->_adapter->query(sprintf("SHOW TABLES LIKE '%s'", addslashes($tableName)));
        if ($response == '') {
            $this->_failure("Table '{$tableName}' doesn't exist");
        } else {
            $this->_success("Table '{$tableName}' exists");
        }

        return $this;
    }

    /**
     * Execute query and return results
     *
     * @param string Query to execute
     * @return $this
     * @see connect()
     * @throws Exception If this method is called before connect()
     */
    public function query($query)
    {
        if (!$this->_adapter->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        try {
            $result = $this->_adapter->query($query);
            $this->_log($result);
        } catch (Exception $e) {
            $this->_failure($e->getMessage());
        }

        return $this;
    }

    /**
     * Show database schema
     *
     * @return $this
     * @throws Exception If this method is called before connect()
     * @throws Exception If this method is called before dbExists()
     * @throws Exception If something wrong happen during getting database schema
     */
    public function showSchema()
    {
        if (!$this->_adapter->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        if (!$this->_adapter->isDatabaseSelected()) {
            throw new Exception('You must call dbExists() method before');
        }

        $result = $this->_adapter->showSchema();
        $this->_log($result);

        return $this;
    }

    /**
     * Show connections and their status
     *
     * @return $this
     * @throws Exception If this method is called before connect()
     */
    public function showConnections()
    {
        if (!$this->_adapter->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        $result = $this->_adapter->showConnections();
        if ($result === false) {
            $this->_failure('MySQL user does not have GRANT PROCESS|ALL permissions');
        }

        $this->_log($result);
        return $this;
    }

    /**
     * Show server info
     *
     * @return $this
     * @throws Exception If this method is called before connect()
     */
    public function showServerInfo()
    {
        if (!$this->_adapter->isConnected()) {
            throw new Exception('You must call connect() method before');
        }

        $this->_log($this->_adapter->showServerInfo());
        return $this;
    }

    /**
     * Close connection to db
     *
     * @return void
     */
    public function closeConnection()
    {
        $this->_adapter->closeConnection();
    }
}
