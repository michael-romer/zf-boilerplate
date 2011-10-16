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
 * @version $Id: Auth.php 623 2010-07-19 12:23:28Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Runner
 */
require_once PHPRACK_PATH . '/Runner.php';

/**
 * Runner authentication adapter
 *
 * @package Tests
 */
class phpRack_Runner_Auth
{

    /**
     * COOKIE name
     *
     * @see isAuthenticated()
     */
    const COOKIE_NAME = 'phpRack_auth';

    /**
     * COOKIE lifetime in seconds
     *
     * We set to 30 days, which equals to 30 * 24 * 60 * 60 = 2592000
     *
     * @see isAuthenticated()
     */
    const COOKIE_LIFETIME = 2592000;

    /**
     * Form param names
     *
     * @see isAuthenticated()
     * @see login.phtml
     */
    const POST_LOGIN = 'login';
    const POST_PWD = 'password';

    /**
     * Param names for authenticating using GET
     *
     * @see isAuthenticated()
     */
    const GET_LOGIN = 'login';
    const GET_PWD = 'password';

    /**
     * Auth result, if authentication was already performed
     *
     * @var phpRack_Runner_Auth_Result
     * @see authenticate()
     */
    protected $_authResult = null;

    /**
     * Tests runner
     *
     * @var phpRack_Runner
     * @see __construct()
     * @see isAuthenticated()
     */
    protected $_runner = null;

    /**
     * Authentication options
     *
     * @var array
     */
    protected $_options = null;

    public function __construct(phpRack_Runner $runner, $options)
    {
        $this->_runner = $runner;
        $this->_options = $options;
    }

    /**
     * Authenticate the user before running any tests
     *
     * @param string Login of the user
     * @param string Secret password of the user
     * @param boolean Defines whether second argument is password or it's hash
     * @return phpRack_Runner_Auth_Result
     * @see bootstrap.php
     */
    public function authenticate($login, $password, $isHash = false)
    {
        // if it's already authenticated, just return it
        if (!is_null($this->_authResult)) {
            return $this->_authResult;
        }

        // make sure that we're working with HASH
        $hash = ($isHash) ? $password : md5($password);

        switch (true) {
            // plain authentication by login/password
            // this option is set by default to NULL, here we validate that
            // it was changed to ARRAY
            case is_array($this->_options['auth']):
                $method = 'plain';
                break;

            // list of login/password provided in file
            // this option is set by default to NULL, here we just validate
            // that it contains a name of file
            case is_string($this->_options['htpasswd']):
                $method = 'file';
                break;

            // list of login/password provided in associative array
            // key is username and value is password
            case is_array($this->_options['htpasswd']):
                $method = 'array';
                break;

            // authenticated TRUE, if no authentication required
            default:
                return $this->_validated(true);
        }

        $className = 'phpRack_Adapters_Auth_' . ucfirst($method);
        eval("require_once PHPRACK_PATH . '/Adapters/Auth/' . ucfirst(\$method) . '.php';");
        $adapter = new $className();
        $adapter->setOptions($this->_options)
            ->setRequest(
                array(
                    'login'    => $login,
                    'password' => $password,
                    'hash'     => $hash,
                )
            );
        return $adapter->authenticate();
    }

    /**
     * Try HTTP POST authentication method
     *
     * Login/password are provided in HTTP request, through POST params.
     * we should save them in COOKIE in order to avoid further login requests.
     *
     * @throws Exception if some required request parameter is missed
     * @return array with retrieved login and hash
     */
    private function _tryHttpPost()
    {
        if (!array_key_exists(self::POST_LOGIN, $_POST)
            || !array_key_exists(self::POST_PWD, $_POST)
        ) {
            return array();
        }
        $login = $_POST[self::POST_LOGIN];
        $hash = md5($_POST[self::POST_PWD]);
        setcookie(
            self::COOKIE_NAME, // name of HTTP cookie
            $login . ':' . $hash, // hashed form of login and pwd
            time() + self::COOKIE_LIFETIME // cookie expiration date
        );
        return array (
            'login' => $login,
            'hash'  => $hash
        );
    }

    /**
     * Try HTTP GET authentication method
     *
     * Login/password are provided as GET params as it's only one-time Phing
     * bridge, we don't store them anywhere
     *
     * @throws Exception if some required request parameter is missed
     * @return array with retrieved login and hash
     */
    private function _tryHttpGet()
    {
        if (!array_key_exists(self::GET_LOGIN, $_GET)
            || !array_key_exists(self::GET_PWD, $_GET)
        ) {
            return array();
        }
        return array (
            'login' => $_GET[self::GET_LOGIN],
            'hash'  => md5($_GET[self::GET_PWD])
        );
    }

    /**
     * Try HTTP Cookie authentication method
     *
     * Login/password are provided as GET params as it's only one-time Phing
     * bridge, we don't store them anywhere
     *
     * @return array with retrieved login and hash
     */
    private function _tryHttpCookie()
    {
        // global variables, in case they are not declared as global yet
        global $_COOKIE;

        if (!array_key_exists(self::COOKIE_NAME, $_COOKIE)) {
            return array();
        }

        // we already have authentication information in COOKIE, we just
        // need to parse it and validate
        $data = explode(':', $_COOKIE[self::COOKIE_NAME]);
        return array (
            'login' => $data[0],
            'hash'  => $data[1]
        );
    }

    /**
     * Try HTTP Plan authentication method
     *
     * We expect authentication information to be sent via headers for example
     * by Phing
     *
     * @return array with retrieved login and hash
     */
    private function _tryPlainAuth()
    {
        if (!array_key_exists('PHP_AUTH_USER', $_SERVER)
            || !array_key_exists('PHP_AUTH_PW', $_SERVER)
        ) {
            return array();
        }
        return array (
            'login' => $_SERVER['PHP_AUTH_USER'],
            'hash'  => md5($_SERVER['PHP_AUTH_PW'])
        );
    }

    /**
     * Checks whether user is authenticated before running any tests
     *
     * @return boolean
     * @see bootstrap.php
     */
    public function isAuthenticated()
    {
        if (!is_null($this->_authResult)) {
            return $this->_authResult->isValid();
        }

        // this is CLI environment, not web -- we don't require any
        // authentication
        if ($this->_runner->isCliEnvironment()) {
            return $this->_validated(true)->isValid();
        }

        // there are a number of possible authentication scenarios
        switch (true) {
            case ($result = $this->_tryHttpPost()):
                break;

            case ($result = $this->_tryHttpGet()):
                break;

            case ($result = $this->_tryHttpCookie()):
                break;

            case ($result = $this->_tryPlainAuth());
                break;

            default:
                // no authinfo, chances are that site is not protected
                $result = array('login' => false, 'hash'=> false);
        }
        $this->_authResult = $this->authenticate($result['login'], $result['hash'], true);
        return $this->_authResult->isValid();
    }

    /**
     * Get current auth result, if it exists
     *
     * @return phpRack_Runner_Auth_Result
     * @see boostrap.php
     * @throws Exception If the result is not set yet
     */
    public function getAuthResult()
    {
        if (!isset($this->_authResult)) {
            throw new Exception("AuthResult is not set yet, use authenticate() before");
        }
        return $this->_authResult;
    }

    /**
     * Save and return an AuthResult
     *
     * @param boolean Success/failure of the validation
     * @param string Optional error message
     * @return phpRack_Runner_Auth_Result
     * @see authenticate()
     */
    protected function _validated($result, $message = null)
    {
        /**
         * @see phpRack_Runner_Auth_Result
         */
        require_once PHPRACK_PATH . '/Runner/Auth/Result.php';
        return $this->_authResult = new phpRack_Runner_Auth_Result($result, $message);
    }
    
}
