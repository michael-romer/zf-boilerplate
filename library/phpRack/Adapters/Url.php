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
 * @version $Id: Url.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Socket based adapter which help checking remote url accessibility
 * and retrieve content from them
 *
 * @package Adapters
 */
class phpRack_Adapters_Url
{
    /**
     * Socket returned by fsockopen()
     *
     * @see connect()
     * @var resource
     */
    private $_socket;

    /**
     * Host name, set in __construct
     *
     * @see __construct()
     */
    private $_host;

    /**
     * Port number used to connect with host.
     * Can be changed by passing URL with custom one to __construct()
     *
     * @see __construct()
     * @var integer
     */
    private $_port = 80;

    /**
     * Connection options, can be overwritten by passing new values as array
     * to constructor as second parameter
     *
     * @var array
     * @see __construct()
     * @see connect()
     * @see getContent()
     */
    private $_options = array(
        'connectTimeout' => 2,    // timeouts in seconds
        'readTimeout'    => 60
    );

    /**
     * Constructor
     *
     * @param string URL
     * @param array Options
     * @return void
     * @throws Exception if URL is not valid
     * @throws Exception if some of passed options is not recognized
     */
    public function __construct($url, array $options = array())
    {
        // If url has not sheme defined - add it
        if (!preg_match('#^\w+://#', $url)) {
            $url = 'http://' . $url;
        }

        $urlParts = @parse_url($url);

        // If there was url parsing error
        if ($urlParts === false) {
            throw new Exception('This is NOT valid url');
        }

        // Set host
        $this->_host = $urlParts['host'];

        // Set url path
        if (isset($urlParts['path'])) {
            $this->_path = $urlParts['path'];
        } else {
            $this->_path = '/';
        }

        // Check if have query params after "?", if yes attach them to our _path
        if (isset($urlParts['query'])) {
            $this->_path .= '?' . $urlParts['query'];
        }

        // Check if port number was passed in URL
        if (isset($urlParts['port'])) {
            $this->_port = $urlParts['port'];
        }

        // Overwrite default options
        foreach ($options as $option => $value) {
            if (!array_key_exists($option, $this->_options)) {
                throw new Exception("Option '{$option}' is not recognized");
            }
            $this->_options[$option] = $value;
        }
    }

    /**
     * Factory, to simplify calls
     *
     * @param string URL
     * @param array Options
     * @return phpRack_Adapters_Url
     */
    public static function factory($url, array $options = array())
    {
        return new self($url, $options);
    }

    /**
     * Create connection to server
     *
     * @return void
     * @throws Exception if can't connect to server
     */
    protected function _connect()
    {
        // If we are not connected
        if (!$this->_socket) {
            $errorNumber = null;
            $errorString = null;

            // Try to open connection to server
            $this->_socket = @fsockopen(
                $this->_host,
                $this->_port,
                $errorNumber,
                $errorString,
                $this->_options['connectTimeout']
            );
        }

        // If can't connect
        if (!$this->_socket) {
            throw new Exception (
                "Can't connect to '{$this->_host}':'{$this->_port}'"
                . " Error #'{$errorNumber}': '{$errorString}'"
            );
        }
    }

    /**
     * Close current connection
     *
     * @return void
     */
    protected function _disconnect()
    {
        // If we are connected
        if (is_resource($this->_socket)) {
            fclose($this->_socket);
            $this->_socket = null;
        }
    }

    /**
     * Check whether URL is accessible
     *
     * @return boolean
     */
    public function isAccessible()
    {
        try {
            $this->_connect();
        } catch (Exception $e) {
            assert($e instanceof Exception); // for ZCA only
            return false;
        }

        return true;
    }

    /**
     * Get content of URL passed to constructor
     *
     * @see __construct()
     * @return string Content of URL
     * @throws Exception If can't get content for some reason
     */
    public function getContent()
    {
        // Try to connect with server, if can't will throw exception
        $this->_connect();

        // Create HTTP request
        $request = "GET {$this->_path} HTTP/1.1\r\n"
            . "Host: {$this->_host}\r\n"
            . "Connection: Close\r\n\r\n\r\n";

        // Send request
        fwrite($this->_socket, $request);

        $response = '';

        stream_set_timeout($this->_socket, $this->_options['readTimeout']);

        // Key must be underscored because of array format
        // returned by stream_get_meta_data() function
        $info = array('timed_out' => false);
        // Receive response
        while (!feof($this->_socket) && !$info['timed_out']) {
            $line = fgets($this->_socket, 1024);
            $response .= $line;
            $info = stream_get_meta_data($this->_socket);
        }

        // Close connection
        $this->_disconnect();

        // If connection timeouted
        if ($info['timed_out']) {
            throw new Exception('Connection timed out!');
        }

        return $response;
    }
}
