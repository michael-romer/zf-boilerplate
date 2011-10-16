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
 * @version $Id: Smtp.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Notifier_Mail_Abstract
 */
require_once PHPRACK_PATH . '/Adapters/Notifier/Mail/Abstract.php';

/**
 * Smtp implementation of phpRack mail
 *
 * @see phpRack_Notifier_Mail_Abstract
 */
class phpRack_Adapters_Notifier_Mail_Smtp
    extends phpRack_Adapters_Notifier_Mail_Abstract
{
    /**
     * Connection entry point
     *
     * @var resource
     * @see _connect()
     * @see _query()
     */
    protected $_connection;

    /**
     * Stores debug information
     *
     * @var array
     * @see _log()
     */
    protected $_log = array();

    /**
     * Connection address for the stream
     *
     * @var string
     * @see _connect();
     */
    protected $_address;

    /**
     * Constructor for the smtp protocol.
     *
     * Creates address to connect to
     *
     * @param array List of parameters
     * @return void
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $host = '127.0.0.1';
        if (!empty($this->_options['host'])) {
            $host = $this->_options['host'];
        }

        $port = 25;
        if (!empty($this->_options['port'])) {
            $port = (int)$this->_options['port'];
        }

        $protocol = 'tcp';
        if (!empty($this->_options['tls'])) {
            $protocol = 'tls';
        }

        $this->_address = $protocol . '://' . $host . ':' . $port;
    }

    /**
     * Prepares and sends an mail
     *
     * @throws Exception if connection doesn't established
     * @return void
     */
    public function send()
    {
        // Validating data
        $this->_validateBeforeSend();

        // Connecting
        $this->_connect();

        // Hello server
        $this->_query('EHLO ' . php_uname('n'))->_mustBe(220)->_mustBe(250);

        // If we must use STARTTLS
        if (strpos($this->_getLog(), 'STARTTLS') !== false) {
            $this->_query('STARTTLS')->_mustBe(220, 180);
            if (!@stream_socket_enable_crypto($this->_connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->_log("Can't apply TLS encryption", true);
            }
            // Hello once again
            $this->_query('EHLO ' . php_uname('n'))->_mustBe(250);
        }

        // Auth info
        $this->_query('AUTH LOGIN')->_mustBe(334)
            ->_query(base64_encode($this->_options['username']))->_mustBe(334)
            ->_query(base64_encode($this->_options['password']))->_mustBe(235);

        // Basic set
        $this->_query('MAIL FROM:<' . $this->_from . '>')->_mustBe(250)
            ->_query('RCPT TO:<' . $this->_to[0] . '>')->_mustBe(250)
            ->_query('DATA')->_mustBe(354);

        // Mail headers
        $toList = $this->_to;

        $this->_query('From: <' . $this->_from . '>')
            ->_query('To: <' . array_shift($toList) . '>');

        if (count($toList)) {
            $this->_query('Cc: <' . implode('>,<', $toList) . '>');
        }

        $this->_query('Subject: ' . $this->_getEncodedSubject())
            ->_query('MIME-Version: 1.0')
            ->_query('Content-Type: text/plain; charset=UTF-8')
            ->_query('Content-transfer-encoding: base64')
            ->_query("\r\n")
            ->_query($this->_getEncodedBody());

        // Closing data part and sending
        $this->_query('.')->_mustBe(250, 600)
            ->_query('QUIT')->_mustBe(221, 600);
    }

    /**
     * Connects to the stream and returns connection status
     *
     * @return void
     * @throws Exception if can't connect to the mail server
     */
    protected function _connect()
    {
        $this->_connection = @stream_socket_client($this->_address);
        if ($this->_connection === false) {
            $this->_log("Can't connect to the mail server: {$this->_address}", true);
        }
    }

    /**
     * Writes data to the connetion (stream)
     *
     * @var string Message to send to SMTP server
     * @return $this
     * @throws Exception if can't write to the stream
     */
    protected function _query($msg)
    {
        if (@fwrite($this->_connection, $msg . "\r\n") === false) {
            $this->_log("Can't write to a socket", true);
        }
        $this->_log($msg, false);
        return $this;
    }

    /**
     * Reads stream. Moves caret and checks for a code or codes.
     *
     * Second parameter used as time limit for read stream
     *
     * @var int|array $code
     * @var int Timeout (Default: 300)
     * @throws Exception if can't change stream timeout
     * @throws Exception if can't read from the socket
     * @see _log()
     * @return $this
     */
    protected function _mustBe($code, $timeout = 300)
    {
        if (!is_array($code)) {
            $code = array($code);
        }

        if (!@stream_set_timeout($this->_connection, $timeout)) {
            $this->_log("Can't change stream timeout", true);
        }

        $hasError = true;
        $log = $msg = $cmd = '';
        do {
            $log .= $data = @fgets($this->_connection, 1024);
            if ($data === false) {
                $this->_log("Can't read from the socket", true);
            }
            sscanf($data, '%d%s', $cmd, $msg);
            if (in_array($cmd, $code)) {
                $hasError = false;
            }
        } while (strpos($msg, '-') === 0);

        $this->_log($log, $hasError);
        return $this;
    }

    /**
     * Logs information about queries
     * and response to debug
     *
     * @see _mustBe()
     * @see _query()
     * @param string $msg
     * @param bool $throwError
     * @throws Exception if $throwError eq. true
     * @return void
     */
    protected function _log($msg, $throwError = false)
    {
        $this->_log[] = $msg;
        if ($throwError) {
            throw new Exception($this->_getLog());
        }
    }

    /**
     * Returns information about queries and response in plain format
     *
     * @return string
     */
    protected function _getLog()
    {
        return implode("\n", $this->_log);
    }

    /**
     * Destructor.
     *
     * Closes connection if needed
     *
     * @throws Exception if can't close connection
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->_connection)) {
            if (@fclose($this->_connection) === false) {
                $this->_log("Can't close connection", true);
            }
        }
    }
}
