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
 * @version $Id: Windows.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Cpu_Abstract
 */
require_once PHPRACK_PATH . '/Adapters/Cpu/Abstract.php';

/**
 * Windows CPU Adapter
 *
 * @package Adapters
 * @subpackage Cpu
 */
class phpRack_Adapters_Cpu_Windows extends phpRack_Adapters_Cpu_Abstract
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
        /**
         * On Windows return approximated result which can be calculated using
         * this formula: CPU clock * 2
         */
        return $this->getCpuFrequency() * 2;
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
        $wmi = new COM('Winmgmts://');
        $query = 'SELECT maxClockSpeed FROM CIM_Processor';

        // get CPUS-s data
        $cpus = $wmi->execquery($query);
        $maxClockSpeed = 0;

        /**
         * We must iterate through all CPU-s because $cpus is object
         * and we can't get single entry by $cpus[0]->maxClockSpeed
         */
        foreach ($cpus as $cpu) {
            $maxClockSpeed = max($maxClockSpeed, $cpu->maxClockSpeed);
        }

        /**
         * If returned $cpus set was empty(some error occured)
         *
         * We can't check it earlier with empty($cpus) or count($cpus)
         * because $cpus is object and doesn't implement countable
         * interface.
         */
        if (!$maxClockSpeed) {
            throw new Exception(
                "Unable to get maxClockSpeed using COM 'Winmgmts://' and '{$query}' query"
            );
        }
        return floatval($maxClockSpeed);
    }
}
