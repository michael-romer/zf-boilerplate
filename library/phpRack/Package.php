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
 * @version $Id: Package.php 590 2010-06-05 07:49:09Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Result
 */
require_once PHPRACK_PATH . '/Result.php';

/**
 * @see phpRack_Test
 */
require_once PHPRACK_PATH . '/Test.php';

/**
 * One test assertion package
 *
 * The package is used from your custom integration test, through the instance
 * of {@link phpRack_Assertion} class, which is available like this:
 *
 * <code>
 * class MyTest extends phpRack_Test {
 *   public function testPhpVersion() {
 *     $this->assert->php->version->atLeast('5.2');
 *   }
 * }
 * </code>
 *
 * In this example package {@link phpRack_Package_Php_Version} will be instantiated
 * and returned. Later, method atLeast() will be called in this package.
 *
 * @package Tests
 * @see phpRack_Assertion::__call()
 */
class phpRack_Package
{
    
    /**
     * Result collector
     *
     * @var phpRack_Result
     * @see __construct()
     * @see __get()
     */
    protected $_result;
    
    /**
     * Result of the latest call
     *
     * @var boolean TRUE means that the latest call was successful
     * @see _failure()
     * @see _success()
     */
    protected $_latestCallSuccess = false;
    
    /**
     * Construct the class
     *
     * The method is PROTECTED in order to avoid direct instantiation of
     * packages outside our factory method {@link factory()}. Some packages
     * may override the method, that's why it is protected, not private. For
     * example {@link phpRack_Package_Db_Mysql}.
     *
     * phpRack_Result passed here as parameter is used as a holder of test
     * results. This package will ADD new results to this holder, instead of
     * creating new one. This holder is passed from {@link phpRack_Test}, normally.
     *
     * @param phpRack_Result Result to use
     * @return void
     * @see factory()
     */
    protected function __construct(phpRack_Result $result)
    {
        $this->_result = $result;
    }

    /**
     * Call to unknown function
     *
     * @param string Name of the method
     * @param array List of arguments
     * @return void
     * @throws Exception
     */
    public final function __call($name, array $args) 
    {
        throw new Exception(
            sprintf(
                "Method '%s' is absent in '%s' package, %d args passed",
                $name,
                $this->getName(),
                count($args)
            )
        );
    }

    /**
     * Create new package
     *
     * The method is called factory, but this is not really a static factory. This method
     * just simplifies the instantiation of phpRack_Package and encapsulates private
     * constructor. Some time ago we had a static factory here, but then removed it. The 
     * organization of methods stay like before (factory + private constructor). Maybe in
     * the future we might decide to introduce static factory again.
     *
     * @param string Name of the package, like "php/version"
     * @param phpRack_Result Collector of log lines
     * @return phpRack_Package
     * @throws Exception
     * @see phpRack_Assertion::__call()
     */
    public static function factory($name, phpRack_Result $result) 
    {
        $sectors = array_map('ucfirst', explode('/', $name));
        $className = 'phpRack_Package_' . implode('_', $sectors);
        
        $packageFile = PHPRACK_PATH . '/Package/' . implode('/', $sectors) . '.php';
        if (!file_exists($packageFile)) {
            throw new Exception("Package '$name' is absent in phpRack: '{$packageFile}'");
        }
        
        // workaround against ZCA static code analysis
        eval('require_once $packageFile;');

        return new $className($result);
    }
    
    /**
     * Dispatcher of calls to packages
     *
     * Here we create a sub-package, for example:
     * 
     * <code>
     * // inside your instance of phpRack_Test:
     * $this->assert->php->extensions->isLoaded('simplexml');
     * </code>
     *
     * The call in the example will lead you to this method, and will call
     * __get('extensions'). In return we will create an instance of
     * phpRack_Package_Php_Extensions and return it.
     *
     * @param string Name of the property to get
     * @return phpRack_Package
     * @see PhpConfigurationTest::testPhpExtensionsExist ->extensions reaches this point
     */
    public function __get($name)
    {
        return self::factory($this->_getName() . '/' . $name, $this->_result);
    }
    
    /**
     * What to do on success?
     *
     * @param mixed What to do? STRING will log this string
     * @return $this
     */
    public final function onSuccess($action) 
    {
        if ($this->_latestCallSuccess) {
            $this->_log($action);
        }
        return $this;
    }
        
    /**
     * What to do on failure?
     *
     * @param mixed What to do? STRING will log this string
     * @return $this
     */
    public final function onFailure($action) 
    {
        if (!$this->_latestCallSuccess) {
            $this->_log($action);
        }
        return $this;
    }
    
    /**
     * Get my name, like: "php/version"
     *
     * @return string
     * @see __get()
     */
    protected function _getName() 
    {
        $sectors = explode('_', get_class($this)); // e.g. "phpRack_Package_Php_Version"
        return implode(
            '/', 
            array_slice(
                array_map(
                    create_function('$a', 'return strtolower($a[0]) . substr($a, 1);'),
                    $sectors
                ), 
                2
            )
        );
    }
    
    /**
     * Call failed
     *
     * @param string String to log
     * @return void
     * @see phpRack_Package_Php::lint() and many other methods
     */
    protected function _failure($log) 
    {
        $this->_latestCallSuccess = false;
        $this->_result->fail();
        $this->_log('[' . phpRack_Test::FAILURE . '] ' . $log);
    }
        
    /**
     * Call was successful
     *
     * @param string String to log
     * @return void
     * @see phpRack_Package_Php::lint() and many other methods
     */
    protected function _success($log) 
    {
        $this->_latestCallSuccess = true;
        $this->_log('[' . phpRack_Test::OK . '] ' . $log);
    }
        
    /**
     * Just log a line
     *
     * @param string String to log
     * @return void
     * @see phpRack_Package_Php::lint() and many other methods
     */
    protected function _log($log) 
    {
        $this->_result->addLog($log);
    }
    
}
