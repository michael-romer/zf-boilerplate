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
 * @version $Id: Db.php 169 2010-03-23 07:04:08Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * Assertions related to SHELL
 *
 * @package Tests
 */
class phpRack_Package_Shell extends phpRack_Package
{

    /**
     * Execute a command and tries to find a regex inside it's result
     *
     * Use it like this, to make sure that PHP scripts are started
     * by "apache" user:
     *
     * <code>
     * class MyTest extends phpRack_Test {
     *   public function testAuthorship() {
     *     $this->assert->shell->exec('whoami', '/apache/');
     *   }
     * }
     * </code>
     *
     * @param string Command to run
     * @param string Regular exception
     * @return $this
     */
    public function exec($cmd, $regex = null) 
    {
        /**
         * @see phpRack_Adapters_Shell_Command
         */
        require_once PHPRACK_PATH . '/Adapters/Shell/Command.php';
        $result = phpRack_Adapters_Shell_Command::factory($cmd)->run();
        
        $this->_log('$ ' . $cmd);
        $this->_log($result);
        if (!is_null($regex)) {
            if (!preg_match($regex, $result)) {
                $this->_failure("Result of '{$cmd}' doesn't match regex '{$regex}': '{$result}'");
            } else {
                $this->_success("Result of '{$cmd}' matches regex '{$regex}'");
            }
        }
        return $this;
    }

}
