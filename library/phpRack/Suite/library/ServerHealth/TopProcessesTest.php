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
 * @version $Id: TopProcessesTest.php 570 2010-05-07 06:00:24Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Suite_Test
 */
require_once PHPRACK_PATH . '/Suite/Test.php';

/**
 * List of processes running on the server
 *
 * @package Tests
 */
class phpRack_Suite_ServerHealth_ProcessesTest extends phpRack_Suite_Test
{
    /**
     * Pre-configuration of the test
     *
     * @return void
     */
    protected function _init()
    {
        $this->setAjaxOptions(
            array(
                'reload' => 5, // every 5 seconds, if possible
            )
        );
    }

    /**
     * Show full list of top-processes and some other supplementary information
     *
     * @return void
     * @todo #48 This test works only on Linux, so we should change it
     *      soon to something more portable
     */
    public function testShowProcesses()
    {
        $this->assert->shell->exec('date 2>&1');
        $this->assert->shell->exec('uptime 2>&1');
        $this->assert->shell->exec(
            'ps o "%cpu %mem nice user time stat command" ax | '
            . 'awk \'NR==1; NR > 1 {print $0 | "sort -k 1 -r"}\' | '
            . 'grep -v "^ 0.0" 2>&1'
        );
        $this->assert->shell->exec('df 2>&1');
    }
}
