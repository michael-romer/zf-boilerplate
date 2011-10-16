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
 * @version $Id: Sendmail.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Notifier_Mail_Abstract
 */
require_once PHPRACK_PATH . '/Adapters/Notifier/Mail/Abstract.php';

/**
 * Sendmail implementation of phpRack mail
 *
 * @see phpRack_Notifier_Mail_Abstract
 */
class phpRack_Adapters_Notifier_Mail_Sendmail extends phpRack_Adapters_Notifier_Mail_Abstract
{
    /**
     * Preparing and sending mail.
     * 
     * Function returns result of the operation
     *
     * @return bool
     * @link http://php.net/manual/en/function.mail.php
     */
    public function send()
    {
        $this->_validateBeforeSend();
        return mail(
            $this->_to[0],
            $this->_getEncodedSubject(),
            $this->_getEncodedBody(),
            $this->_getHeaders()
        );
    }

    /**
     * Function builds headers for mail
     *
     * @return string Plain list with headers
     * @see send()
     */
    private function _getHeaders()
    {
        $headers = array();
        if (count($this->_to) > 1) {
            $headers['Cc'] = implode(', ', array_slice($this->_to, 1));
        }
        $headers['From'] = $this->_from;
        $headers['MIME-Version'] = '1.0';
        $headers['Content-Type'] = 'text/plain; charset=UTF-8';
        $headers['Content-transfer-encoding'] = 'base64';
        
        return implode(
            "\r\n", 
            array_map(
                create_function('$v, $k', 'return $k . ": " . $v;'), 
                $headers,
                array_keys($headers)
            )
        );
    }
}
