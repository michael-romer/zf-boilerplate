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
 * @version $Id: Pear.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Pear_Package
 */
require_once PHPRACK_PATH . '/Adapters/Pear/Package.php';

/**
 * PEAR adapter used for checking PEAR packages availability
 *
 * @package Adapters
 */
class phpRack_Adapters_Pear
{
    /**
     * Find and create new package
     *
     * @param string Package name
     * @return phpRack_Adapters_Pear_Package|null
     * @throws Exception If PEAR is not installed properly
     * @see phpRack_Package_Pear::package()
     */
    public function getPackage($name)
    {
        $package = new phpRack_Adapters_Pear_Package($name);
        try {
            $package->getVersion();
        } catch (Exception $e) {
            assert($e instanceof Exception); // for ZCA only
            return null;
        }
        return $package;
    }

    /**
     * Get full list of installed packages
     *
     * @return array of phpRack_Adapters_Pear_Package
     * @throws Exception If some problem appear during package informations loading
     * @see phpRack_Package_Pear::showList()
     */
    public function getAllPackages()
    {
        $packages = array();
        $command = 'pear list -a';
        /**
         * @see phpRack_Adapters_Shell_Command
         */
        require_once PHPRACK_PATH . '/Adapters/Shell/Command.php';
        $result = phpRack_Adapters_Shell_Command::factory($command)->run();

        // divide command output by channels
        foreach (explode("\n\n", $result) as $channel) {
            $lines = explode("\n", $channel);
            $matches = array();

            // get channel name
            if (!preg_match('/CHANNEL ([^:]+):/', $lines[0], $matches)) {
                continue;
            }

            $channelName = $matches[1];

            // skip 3 first lines (channel, separator, packages header line)
            $packageLines = array_slice($lines, 3);

            foreach ($packageLines as $packageLine) {
                // get package name
                if (preg_match('/^(\w+)/', $packageLine, $matches)) {
                    // set full package name with channel
                    $packageName = "{$channelName}/{$matches[1]}";
                    $packages[] = new phpRack_Adapters_Pear_Package($packageName);
                }
            }
        }

        return $packages;
    }
}
