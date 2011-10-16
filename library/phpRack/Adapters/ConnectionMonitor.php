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
 * @version $Id: ConnectionMonitor.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Connection monitor used to check whether connection between client
 * and our script is still opened
 *
 * @package Adapters
 * @author netcoderpl@gmail.com
 */
class phpRack_Adapters_ConnectionMonitor
{
    /**
     * Connection status last checked time
     *
     * @var int
     * @see ping()
     */
    private $_lastCheckTime = null;

    /**
     * Connection status checking interval
     *
     * @var int
     * @see ping()
     */
    private $_checkInterval = 1; // 1 second

    /**
     * phpRack_Adapters_ConnectionMonitor instance
     *
     * @var phpRack_Adapters_ConnectionMonitor
     * @see getInstance()
     */
    private static $_instance;

    /**
     * Get phpRack_Adapters_ConnectionMonitor instance
     *
     * @return phpRack_Adapters_ConnectionMonitor
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Check client connection is still opened. We can check it by sending
     * space char " " every $this->_checkInterval second.
     *
     * After that PHP can detect connection status. If it was closed objects
     * destructors will be automatically executed and script stop work.
     *
     * @return void
     */
    public function ping()
    {
        if ($this->_lastCheckTime === null
            || $this->_lastCheckTime + $this->_checkInterval < time()
        ) {
            echo ' ';

            /**
             * Bypass output buffering, without it PHP may not recognize that
             * connection is closed
             */
            if (ob_get_level()) {
                ob_flush();
            }

            flush();
            $this->_lastCheckTime = time();
        }
    }
}
