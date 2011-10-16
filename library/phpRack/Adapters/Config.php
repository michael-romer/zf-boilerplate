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
 * @version $Id: Config.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */


/**
 * Config adapter used for store tests configuration and provide object
 * oriented access methods
 *
 * You can use it like this:
 *
 * <code>
 * $config = new phpRack_Adapters_Config(
 *   array(
 *     'alpha' => array(
 *       'beta' => 123,
 *     )
 *   )
 * );
 * assert($config->alpha->beta == 123);
 * </code>
 *
 * @package Adapters
 */
class phpRack_Adapters_Config
{
    /**
     * Contains array of configuration data
     *
     * @var array
     * @see __construct()
     */
    protected $_data;

    /**
     * Create object oriented config container
     *
     * @param array Config data as array
     * @return void
     * @see ConfigTest::testConfigIni() and other integration tests
     */
    public function __construct(array $data)
    {
        $this->_data = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new self($value);
            } else {
                $this->_data[$key] = $value;
            }
        }
    }

    /**
     * Magic method which provide access to configuration options
     *
     * @param string Name of config option
     * @return mixed
     * @throws Exception if config option not exists
     * @see ConfigTest::testConfigIni() and other integration tests
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->_data)) {
            throw new Exception("Config option '{$name}' doesn't exist");
        }
        return $this->_data[$name];
    }

    /**
     * Magic method which provide possibility to check whether some
     * configuration option exists
     *
     * @param string Name of config option
     * @return boolean
     * @see ConfigTest::testConfigIni() and other integration tests
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }
}
