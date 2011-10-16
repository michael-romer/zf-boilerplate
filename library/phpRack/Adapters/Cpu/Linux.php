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
 * @version $Id: Linux.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Cpu_Abstract
 */
require_once PHPRACK_PATH . '/Adapters/Cpu/Abstract.php';

/**
 * Linux CPU Adapter
 *
 * @package Adapters
 * @subpackage Cpu
 */
class phpRack_Adapters_Cpu_Linux extends phpRack_Adapters_Cpu_Abstract
{
    /**
     * Get CPU BogoMips
     *
     * @return float
     * @throws Exception If unable to get BogoMips
     * @see phpRack_Package_Cpu_Performance::atLeast()
     * @see phpRack_Adapters_Cpu_Abstract::getBogoMips()
     */
    public function getBogoMips()
    {
        $matches = array();
        // on Linux parse ouput of "cat /proc/cpuinfo" command
        if (
            !preg_match(
                '/^bogomips\s*:\s*(.*)/m',
                $this->_getCpuInfoData(), // Exception is possible here
                $matches
            )
        ) {
            throw new Exception("Unable to find bogomips value in cpuinfo");
        }
        return floatval($matches[1]);
    }

    /**
     * Get CPU frequency in MHz
     *
     * @return float
     * @throws Exception If can't get cpu frequency
     * @see getBogoMips()
     * @see phpRack_Adapters_Cpu_Abstract::getCpuFrequency()
     */
    public function getCpuFrequency()
    {
        // on Linux parse ouput of "cat /proc/cpuinfo" command
        $matches = array();
        if (
            !preg_match(
                '/^cpu MHz\s*:\s*(.*)/m',
                $this->_getCpuInfoData(), // Exception is possible here
                $matches
            )
        ) {
            throw new Exception('Unable to find CPU MHz value in cpuinfo');
        }
        return floatval($matches[1]);
    }

    /**
     * Get result of "cat /proc/cpuinfo" shell command execution
     *
     * @return string
     * @throws Exception If unable to execute shell command
     * @see getBogoMips()
     * @see getCpuFrequency()
     */
    private function _getCpuInfoData()
    {
        $command = 'cat /proc/cpuinfo';
        /**
         * @see phpRack_Adapters_Shell_Command
         */
        require_once PHPRACK_PATH . '/Adapters/Shell/Command.php';
        // Exception is possible here
        $result = phpRack_Adapters_Shell_Command::factory($command)->run();
        return $result;
    }
}
