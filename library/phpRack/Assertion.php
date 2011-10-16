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
 * @version $Id: Assertion.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * @see phpRack_Result
 */
require_once PHPRACK_PATH . '/Result.php';

/**
 * @see phpRack_Test
 */
require_once PHPRACK_PATH . '/Test.php';

/**
 * One single test assertion
 *
 * @package Tests
 * @see phpRack_Test::__get()
 */
class phpRack_Assertion
{
    
    /**
     * Result collector
     *
     * @var phpRack_Result
     * @see __construct()
     */
    protected $_result;
    
    /**
     * Construct the class
     *
     * @param phpRack_Test Test, which pushes results here
     * @return void
     * @see phpRack_Test::__get()
     */
    private function __construct(phpRack_Test $test)
    {
        $this->_result = new phpRack_Result($test);
    }

    /**
     * Create new assertion
     *
     * There is a combination of static factory() method and a private
     * constructor. However we don't have any static factory here, just an
     * incapsulation of constructor. Some time ago we had a static factory,
     * but then removed it. Maybe in the future we might get back to this
     * design approach.
     *
     * @param phpRack_Test Test that is using this assertion
     * @return phpRack_Assertion
     * @see phpRack_Test::__get()
     */
    public static function factory(phpRack_Test $test) 
    {
        return new self($test);
    }
    
    /**
     * Dispatcher of calls to packages
     *
     * @param string Name of the package to get
     * @return phpRack_Package
     * @see phpRack_Test::_log() and many other methods inside Integration Tests
     */
    public function __get($name) 
    {
        return phpRack_Package::factory($name, $this->_result);
    }
        
    /**
     * Call method, any one
     *
     * This magic method will be called when you're using any assertion and 
     * some method inside it, for example:
     * 
     * <code>
     * // inside your instance of phpRack_Test:
     * $this->assert->php->extensions->isLoaded('simplexml');
     * </code>
     *
     * The call in the example will lead you to this method, and will call
     * __call('simplexml', array()).
     *
     * @param string Name of the method to call
     * @param array Arguments to pass
     * @return mixed
     * @see PhpConfigurationTest::testPhpExtensionsExist isLoaded() reaches this point
     */
    public function __call($name, array $args) 
    {
        return call_user_func_array(
            array(
                phpRack_Package::factory('simple', $this->_result),
                $name
            ),
            $args
        );
    }    
    
    /**
     * Get instance of result collector
     *
     * @return phpRack_Result
     * @see phpRack_Test::_log() and many other methods inside Integration Tests
     */
    public function getResult() 
    {
        return $this->_result;
    }
    
}
