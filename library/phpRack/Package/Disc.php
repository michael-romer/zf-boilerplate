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
 * @version $Id: Disc.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * Local HDD related assertions
 *
 * @package Tests
 */
class phpRack_Package_Disc extends phpRack_Package
{
    
    /**
     * Show directory structure
     *
     * @param string Relative path, in relation to the location of {@link PHPRACK_PATH} file
     * @param array List of options
     * @return $this
     */
    public function showDirectory($dir, array $options = array()) 
    {
        require_once PHPRACK_PATH . '/Adapters/File.php';
        $dir = phpRack_Adapters_File::factory($dir)->getFileName();
        
        if (!file_exists($dir)) {
            $this->_failure("Directory '{$dir}' is absent");
            return $this;
        }
        
        $this->_log("Directory tree '" . realpath($dir) . "':");
        
        // Create our file iterator
        require_once PHPRACK_PATH . '/Adapters/Files/DirectoryFilterIterator.php';
        $iterator = phpRack_Adapters_Files_DirectoryFilterIterator::factory($dir);
        if (array_key_exists('exclude', $options)) {
            $iterator->setExclude($options['exclude']);
        }
        
        if (array_key_exists('maxDepth', $options)) {
            $iterator->setMaxDepth($options['maxDepth']);
        }
        $this->_log(
            implode(
                "\n", 
                $this->_convertDirectoriesToLines($iterator, $dir)
            )
        );
        
        return $this;
    }
    
    /**
     * Convert list of files to lines to show
     *
     * @param Iterator List of files
     * @param string Parent directory name, absolute
     * @return void
     * @see showDirectory()
     */
    protected function _convertDirectoriesToLines(Iterator $iterator, $dir) 
    {
        $lines = array();
        foreach ($iterator as $file) {
            $name = substr($file, strlen($dir) + 1);
            
            $line = str_repeat('  ', substr_count($name, '/')) . $file->getBaseName();
            $attribs = array();
            
            if ($file->isFile()) {
                $attribs[] = $file->getSize() . ' bytes';
                $attribs[] = date('d-M-y H:i:s', $file->getMTime());
                $attribs[] = sprintf('0x%o', $file->getPerms());
            }
            
            if ($file->isLink()) {
                $attribs[] = "link to '{$file->getRealPath()}']";
            }
            
            $lines[] = $line . ($attribs ? ': ' . implode('; ', $attribs) : false);
        }
        return $lines;
    }
    
}
