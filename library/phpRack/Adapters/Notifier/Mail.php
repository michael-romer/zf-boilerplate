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
 * @version $Id: Mail.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Mail adapter used for mailing phpRack reports
 *
 * @package Adapters
 */
class phpRack_Adapters_Notifier_Mail
{
    /**
     * Factory method to get one of Sendmail or Smtp class instances.
     * 
     * Depends on options specified. Available options depend on transport
     * you choose.
     *
     * @see phpRack_Adapters_Notifier_Mail_Smtp
     * @see phpRack_Adapters_Notifier_Mail_Sendmail
     * @param array List of parameters
     * @return phpRack_Adapters_Mail
     * @throws Exception
     */
    public static function factory($class = 'sendmail', array $params = array())
    {
        $transport = ucfirst(strtolower($class));
        /**
         * @see phpRack_Adapters_Notifier_Mail_Abstract
         */
        $classFile = PHPRACK_PATH . "/Adapters/Notifier/Mail/{$transport}.php";
        if (!file_exists($classFile)) {
            throw new Exception("Transport {$transport} is absent");
        }
        eval("require_once '{$classFile}';"); // for ZCA validation
        $transportClass = 'phpRack_Adapters_Notifier_Mail_' . $transport;
        return new $transportClass($params);
    }
}
