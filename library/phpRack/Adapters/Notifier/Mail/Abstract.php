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
 * @version $Id: Abstract.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Abstract class for the phpRack_Adapters_Notifier_Mail_*
 *
 * @package Adapters
 */
abstract class phpRack_Adapters_Notifier_Mail_Abstract
{
    /**
     * Our array with list of options
     *
     * @var array
     * @see __construct()
     */
    protected $_options = array();

    /**
     * Default text of the body
     *
     * @var string
     * @see setBody()
     */
    protected $_body;

    /**
     * List of destination e-mail addresses
     *
     * @var string
     * @see setTo()
     */
    protected $_to = array();

    /**
     * Sender e-mail address
     *
     * @var string
     */
    protected $_from = 'no-reply@phprack.com';

    /**
     * Default message subject
     *
     * @var string
     * @see setSubject()
     */
    protected $_subject = 'phpRack';

    /**
     * Constructor
     *
     * @param array List of parameters
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->_options = $options;
    }

    /**
     * Assigns body of a mail
     *
     * @param string Text to be assigned
     * @return phpRack_Adapters_Mail
     */
    public function setBody($plain)
    {
        $this->_body = trim($plain);
        return $this;
    }

    /**
     * Assigns subject of a mail
     *
     * @param string
     * @return phpRack_Adapters_Mail
     */
    public function setSubject($text)
    {
        $this->_subject = $text;
        return $this;
    }

    /**
     * Sets destination mail or mails.
     *
     * @param string|array $mails
     * @return phpRack_Adapters_Mail
     */
    public function setTo($mails)
    {
        $this->_to = (!is_array($mails)) ? array($mails) : $mails;
        return $this;
    }

    /**
     * Checks if we are ready to build mail
     *
     * @return bool
     * @see phpRack_Adapters_Notifier_Mail_Sendmail->send()
     * @see phpRack_Adapters_Notifier_Mail_Smtp->send()
     * @throws Exception if To not defined
     * @throws Exception if Body not defined
     */
    protected function _validateBeforeSend()
    {
        if (!count($this->_to)) {
            throw new Exception('Recipients are not specified');
        }
        if (empty($this->_body)) {
            throw new Exception('Body is not specified');
        }
        return true;
    }

    /**
     * Encodes subject to UTF-8
     *
     * @see setSubject()
     * @return string base64 encoded string with special chars
     */
    protected function _getEncodedSubject()
    {
        return '=?UTF-8?B?' . base64_encode($this->_subject) . '?=';
    }

    /**
     * Encodes body to UTF-8.
     * Output text has fixed width
     *
     * @see setBody()
     * @return string base64 encoded string
     */
    protected function _getEncodedBody()
    {
        return rtrim(chunk_split(base64_encode($this->_body), 72, "\n"));
    }
}
