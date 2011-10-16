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
 * @version $Id: Ini.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Adapters_Config
 */
require_once PHPRACK_PATH . '/Adapters/Config.php';

/**
 * Config adapter used for store test configuration loaded from INI file
 *
 * You can use it like this:
 *
 * <code>
 * // app.ini:
 * // [production]
 * // params.db.username = 'test'
 * $ini = new phpRack_Adapters_Config_Ini('app.ini', 'production');
 * assert($ini->params->db->username == 'test');
 * </code>
 *
 * @package Adapters
 */
class phpRack_Adapters_Config_Ini extends phpRack_Adapters_Config
{
    /**
     * Create config object and load selected section from INI file,
     * or all sections if $sectionName == null
     *
     * @param string Filename of INI file
     * @param string|null Section name to load, or null to load all
     * @return void
     * @throws Exception if INI file not exists
     * @throws Exception if section not exists in INI file
     * @see ConfigTest::testConfigIni() and other integration tests
     */
    public function __construct($filename, $sectionName = null)
    {
        $sections = $this->_loadSectionsFromIniFile($filename);
        // one section to return
        if ($sectionName) {
            if (!array_key_exists($sectionName, $sections)) {
                throw new Exception("Section '{$sectionName}' doesn't exist in INI file '{$filename}'");
            }
            $dataArray = $this->_sectionToArray($sections[$sectionName]);
        } else {
            // all sections to return
            foreach ($sections as $key => $section) {
                $dataArray[$key] = $this->_sectionToArray($section);
            }
        }
        parent::__construct($dataArray);
    }

    /**
     * Convert section with "key.subkey1.subkey2" values as keys
     * to multidimensional associative array
     *
     * @param array section from ini file
     * @return array
     * @see __construct()
     */
    protected function _sectionToArray($section)
    {
        $dataArray = array();
        foreach ($section as $key => $value) {
            $currentElement =& $dataArray;
            foreach (explode('.', $key) as $keyFragment) {
                $currentElement =& $currentElement[$keyFragment];
            }
            $currentElement = $value;
        }
        return $dataArray;
    }

    /**
     * Load config sections from INI file, taking into account section inheritance
     *
     * @param string INI file to load and parse
     * @return array
     * @throws Exception if config INI file not exists
     * @see __construct()
     */
    protected function _loadSectionsFromIniFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception("INI file '{$filename}' doesn't exist");
        }
        $sections = array();
        $iniFileSections = parse_ini_file($filename, true);

        foreach ($iniFileSections as $sectionName => $data) {
            // divide section name to check it have some parent ([section : parent])
            $nameParts = explode(':', $sectionName);
            $thisSectionName = trim($nameParts[0]);

            // if section have parent
            if (isset($nameParts[1])) {
                $parentSectionName = trim($nameParts[1]);
                // merge current section values, with parent values
                $data = array_merge(
                    $sections[$parentSectionName],
                    $data
                );
            }
            $sections[$thisSectionName] = $data;
        }

        return $sections;
    }
}
