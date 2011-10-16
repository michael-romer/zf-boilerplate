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
 * @version $Id: Cpu.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */


/**
 * CPU adapter used to get details about available processor
 *
 * @package Adapters
 */
class phpRack_Adapters_Cpu
{
    /**
     * CPU adapter factory return adapter depending on operating system
     *
     * @return phpRack_Adapters_Cpu_Abstract
     * @throws Exception If OS is not supported
     * @see For MacOS I think we should use system_profiler shell command.
     *      After that we can parse it output in similar way like we do it for
     *      Windows or Linux
     * @todo #17 How about Mac OS? There is no /proc directory in Mac OS
     */
    public static function factory()
    {
        /**
         * @see phpRack_Adapters_Os
         */
        require_once PHPRACK_PATH . '/Adapters/Os.php';
        $os = phpRack_Adapters_Os::get();
        $classFile = PHPRACK_PATH . '/Adapters/Cpu/' . ucfirst($os) . '.php';
        
        if (!file_exists($classFile)) {
            throw new Exception("OS '{$os}' is not supported yet");
        }
        eval ('require_once $classFile;'); // for ZCA validation
        $className = 'phpRack_Adapters_Cpu_' . ucfirst($os);
        return new $className();
    }
}
