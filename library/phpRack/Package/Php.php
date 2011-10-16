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
 * @version $Id: Php.php 605 2010-07-08 05:00:52Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * PHP related assertions
 *
 * @package Tests
 */
class phpRack_Package_Php extends phpRack_Package
{
    /**
     * Format of a megabyte
     *
     * @see http://en.wikipedia.org/wiki/Megabyte
     * @var int
     */
    const SIZE_FORMAT = 1024;

    /**
     * Storage for the ini value
     *
     * @var string
     */
    protected $_iniValue;

    /**
     * Check php.ini param with expected value
     *
     * Good example:
     *
     * <code>
     * class MyTest extends phpRack_Test {
     *   public fuction testPhpIni() {
     *     $this->assert->php->ini('short_open_tag');
     *   }
     * }
     * </code>
     *
     * @param string Name of param to check
     * @param mixed Expected value
     * @return $this
     */
    public function ini($param, $expected = true)
    {
        $this->_iniValue = ini_get($param);
        if ($this->_iniValue != $expected) {
            $this->_failure(
                "php.ini '{$param}' is set to '{$this->_iniValue}', while '{$expected}' expected"
            );
        } else {
            $this->_log("php.ini '{$param}' is set to '{$this->_iniValue}', it's OK");
        }
        return $this;
    }

    /**
     * Check value according required size
     *
     * Example:
     *
     * <code>
     * class MyTest extends phpRack_Test {
     *   public fuction testPhpIniAtleast() {
     *     $this->assert->php->ini('memory_limit')->atLeast('128M');
     *   }
     * }
     * </code>
     *
     * @param string value to check
     * @return $this
     */
    public function atLeast($value)
    {
        // checking digit part
        if (!preg_match('~^\d+(K|M|G)?$~i', $value)) {
            $this->_failure("php.ini value has incorrect numeric format: '{$value}'");
            return $this;
        }

        if ($this->_sizeFormat($value) < $this->_sizeFormat($this->_iniValue)) {
            $this->_success("php.ini value is set to {$this->_iniValue}, {$value} required");
        } else {
            $this->_failure("php.ini value has only {$this->_iniValue}, but {$value} is required");
        }

        // check
        return $this;
    }

    /**
     * Show phpinfo() in proper format
     *
     * @return $this
     */
    public function phpinfo()
    {
        ob_start();
        phpinfo(INFO_ALL);
        $html = ob_get_clean();

        // maybe it's CLI version, not HTML?
        if (strpos($html, '<') !== 0) {
            $this->_log($html);
            return $this;
        }

        // clean HTML out of special symbols
        $html = preg_replace('/&(#\d+|\w+);/', ' ', $html);

        $lines = array();
        $xml = simplexml_load_string($html);
        foreach ($xml->xpath("//tr | //h1 | //h2") as $line) {
            switch (strtolower($line->getName())) {
                case 'tr':
                    $ln = '';
                    foreach ($line->children() as $td) {
                        $ln .= trim(strval($td)) . "\t";
                    }
                    $line = $ln;
                    break;
                case 'h1':
                    $line = "\n\n= {$line} =";
                    break;
                case 'h2':
                    $line = "\n== {$line} ==";
                    break;
            }
            $lines[] = strval($line);
        }
        $this->_log(implode("\n", $lines));

        return $this;
    }

    /**
     * Check files in directory have correct php syntax
     *
     * Possible options are:
     *
     * <code>
     * class MyTest extends phpRack_Test {
     *   public function testCodeValidity() {
     *     $this->assert->php->lint(
     *       '/home/myproject/php', // path to PHP files
     *       array(
     *         'extensions' => 'php,phtml', // comma-separated list of extensions to parse
     *         'exclude' => array('/\.svn/'), // list of RegExps to exclude
     *         'verbose' => true, // show detailed log, with one file per line
     *       )
     *     );
     *   }
     * }
     * </code>
     *
     * @param string Directory path to check
     * @param array List of options
     * @return $this
     */
    public function lint($dir, array $options = array())
    {
        require_once PHPRACK_PATH . '/Adapters/File.php';
        $dir = phpRack_Adapters_File::factory($dir)->getFileName();

        if (!file_exists($dir)) {
            $this->_failure("Directory '{$dir}' does not exist");
            return $this;
        }

        // Create our file iterator
        require_once PHPRACK_PATH . '/Adapters/Files/DirectoryFilterIterator.php';
        $iterator = phpRack_Adapters_Files_DirectoryFilterIterator::factory($dir);

        if (!empty($options['exclude'])) {
            $iterator->setExclude($options['exclude']);
        }

        if (!empty($options['extensions'])) {
            $iterator->setExtensions($options['extensions']);
        }

        $lintCommand = 'php -l';

        $valid = $invalid = 0;
        foreach ($iterator as $file) {
            $file = realpath($file->getPathname());
            $command = $lintCommand . ' ' . escapeshellarg($file) . ' 2>&1';

            /**
             * @see phpRack_Adapters_Shell_Command
             */
            require_once PHPRACK_PATH . '/Adapters/Shell/Command.php';
            $output = phpRack_Adapters_Shell_Command::factory($command)->run();

            if (preg_match('#^No syntax errors detected#', $output)) {
                if (!empty($options['verbose'])) {
                    $this->_success("File '{$file}' is valid");
                }
                $valid++;
            } else {
                $this->_failure("File '{$file}' is NOT valid:");
                $this->_log($output);
                $invalid++;
            }
        }

        // notify phpRack about success in the test
        if (!$invalid) {
            $this->_success("{$valid} files are LINT-valid");
        }
        $this->_log(
            sprintf(
                '%d files LINT-checked, among them: %d valid and %d invalid',
                $valid + $invalid,
                $valid,
                $invalid
            )
        );
        return $this;
    }

    /**
     * Convert string into size
     *
     * @param mixed value to convert
     * @return int
     */
    protected function _sizeFormat($value)
    {
        // set of sizes
        $sizeSet = array(
            'K' => self::SIZE_FORMAT,
            'M' => self::SIZE_FORMAT * self::SIZE_FORMAT,
            'G' => self::SIZE_FORMAT * self::SIZE_FORMAT * self::SIZE_FORMAT,
        );

        // calculation
        $size = $mask = '';
        sscanf($value, '%d%s', $size, $mask);
        if ($mask) {
            $size *= $sizeSet[$mask];
        }
        return $size;
    }
}
