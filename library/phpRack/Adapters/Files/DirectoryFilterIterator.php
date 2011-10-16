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
 * @version $Id: DirectoryFilterIterator.php 611 2010-07-12 14:23:40Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Iterator filter class which help to get only files which match filter rules
 *
 * @package Adapters
 * @subpackage Files
 * @see phpRack_Package_Disc::showDirectory()
 */
class phpRack_Adapters_Files_DirectoryFilterIterator extends FilterIterator
{
    
    /**
     * Directory we're iterating
     *
     * @var string
     * @see __construct()
     */
    protected $_dir;
    
    /**
     * Maximum depth to be visible
     *
     * @var integer
     * @see setMaxDepth()
     */
    private $_maxDepth = null;

    /**
     * Regular expression patterns used to determine what files should be ignored
     *
     * @var string[]
     */
    private $_excludePatterns;

    /**
     * Regular expression pattern used to determine what files should returned
     *
     * @var string
     */
    private $_extensionsPattern;

    /**
     * Constructor, private, don't call it directly, instead use factory()
     *
     * @param string Path
     * @return void
     * @see factory()
     */
    public function __construct($dir)
    {
        parent::__construct(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir),
                RecursiveIteratorIterator::SELF_FIRST
            )
        );
        $this->_dir = $dir;
    }

    /**
     * Create new iterator from directory path
     *
     * @param string Path
     * @return phpRack_Adapters_Files_DirectoryFilterIterator
     */
    public static function factory($dir) 
    {
        return new self($dir);
    }

    /**
     * Set which extensions will be used as whitelist
     *
     * @param string|array Comma separated list of extensions, or list of them
     * @return $this
     */
    public function setExtensions($extensions)
    {
        if (!is_array($extensions)) {
            $extensions = explode(',', $extensions);
        }

        // Escape extension special chars to have always valid regular expression
        foreach ($extensions as &$extension) {
            preg_quote(trim($extension));
        }

        $this->_extensionsPattern = '#(\.' . implode('|', $extensions). '$)#';
        return $this;
    }

    /**
     * Set pattern which will be used as blacklist
     *
     * @param string|array Regular expression pattern, or list of them
     * @return $this
     */
    public function setExclude($excludePatterns)
    {
        if (!is_array($excludePatterns)) {
            $excludePatterns = array($excludePatterns);
        }
        $this->_excludePatterns = $excludePatterns;
        return $this;
    }
    
    /**
     * Set maximum directory depth
     *
     * @param integer Maximum depth
     * @return $this
     */
    public function setMaxDepth($maxDepth) 
    {
        $this->_maxDepth = $maxDepth;
        return $this;
    }

    /**
     * Callback function which will be called to determine current file should be in collection or no
     *
     * @return boolean
     */
    public function accept()
    {
        $file = $this->current();

        // Ignore "dots files" which appear in some systems
        if (trim($file, '.') == '') {
            return false;
        }
        
        if (!is_null($this->_maxDepth)
            && substr_count(substr($file, strlen($this->_dir) + 1), '/') > $this->_maxDepth
        ) {
            return false;
        }

        // Ignore files which don't match extensionsPattern
        if ($this->_extensionsPattern && !preg_match($this->_extensionsPattern, $file)) {
            return false;
        }

        // Ignore files which match excludePattern
        if ($this->_excludePatterns) {
            foreach ($this->_excludePatterns as $pattern) {
                if (preg_match($pattern, $file)) {
                    return false;
                }
            }
        }

        // Everything rest is allowable
        return true;
    }
    
}
