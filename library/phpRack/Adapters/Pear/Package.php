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
 * @version $Id: Package.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * PEAR adapter used for checking PEAR packages availability
 *
 * @package Adapters
 */
class phpRack_Adapters_Pear_Package
{
    /**
     * Package name
     *
     * @var string
     * @see __construct()
     * @see getName()
     */
    private $_name;

    /**
     * Raw package info returned from "pear info $packageName" command
     *
     * @var string
     * @see __construct()
     * @see getName()
     * @see getVersion()
     */
    private $_rawInfo;

    /**
     * Construct the class
     *
     * @param string Name of the package
     * @throws Exception if PEAR is not installed properly
     * @return void
     */
    public function __construct($name)
    {
        $this->_name = $name;

        $command = 'pear info ' . escapeshellarg($this->_name);
        /**
         * @see phpRack_Adapters_Shell_Command
         */
        require_once PHPRACK_PATH . '/Adapters/Shell/Command.php';
        $result = phpRack_Adapters_Shell_Command::factory($command)->run();

        if (!$result) {
            throw new Exception('PEAR is not installed properly');
        }

        $this->_rawInfo = $result;
    }

    /**
     * Get name of the package
     *
     * @return string
     */
    public function getName() 
    {
        return $this->_name;
    }

    /**
     * Check whether Package exists
     *
     * @return boolean
     * @throws Exception If package has invalid version number
     * @see phpRack_Package_Pear::package()
     */
    public function getVersion()
    {
        $matches = array();
        if (!preg_match('/^Release Version\s+(\S+)/m', $this->_rawInfo, $matches)) {
            throw new Exception('Invalid version for the package');
        }
        return $matches[1];
    }

    /**
     * Get package raw info
     *
     * @return string
     * @see phpRack_Package_Pear::package()
     */
    public function getRawInfo()
    {
        return $this->_rawInfo;
    }
}
