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
 * @version $Id: File.php 588 2010-05-25 15:11:56Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * @see phpRack_Adapters_File
 */
require_once PHPRACK_PATH . '/Adapters/File.php';

/**
 * File informations and content
 *
 * @package Tests
 */
class phpRack_Package_Disc_File extends phpRack_Package
{
    /**
     * Buffer used is tail function to read blocks from file end
     */
    const READ_BUFFER_SIZE = 1024;
    
    /**
     * Default number of lines to show
     */
    const LINES_TO_SHOW = 25;
    
    /**
     * Maximum number of bytes we can render, if more we will skip the rest
     * 
     * @var int
     */
    protected $_maxBytesToRender = 50000;

    /**
     * Set another limit for max bytes to render
     *
     * @param int Number of bytes that is allowed for rendering
     * @return $this
     */
    public function setMaxBytesToRender($maxBytesToRender) 
    {
        $this->_maxBytesToRender = $maxBytesToRender;
        return $this;
    }

    /**
     * Show the content of the file
     *
     * @param string File name to display
     * @return $this
     */
    public function cat($fileName)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        // Check that file exists
        if (!$this->_isFileExists($fileName)) {
            return $this;
        }
        
        // too long/big files should not be returned
        if (filesize($fileName) > $this->_maxBytesToRender) {
            $this->_log(
                sprintf(
                    "File '%s' is too big (%d bytes), we can't render its content in full",
                    $fileName,
                    filesize($fileName)
                )
            );
            return $this->tail($fileName);
        }

        $content = file_get_contents($fileName);
        if ($content === false) {
            $this->_failure("Failed file_get_contents('{$fileName}')");
            return $this;
        }
        $this->_log($content);
        return $this;
    }

    /**
     * Show last x lines from the file
     *
     * @param string File name
     * @param string How many lines to display?
     * @return $this
     */
    public function tail($fileName, $linesCount = self::LINES_TO_SHOW)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        // Check that file exists
        if (!$this->_isFileExists($fileName)) {
            return $this;
        }
        
        // Open file and move pointer to end of file
        $fp = fopen($fileName, 'rb');
        fseek($fp, 0, SEEK_END);

        // Read offset of end of file
        $offset = ftell($fp);

        // set ajax option with file end offset for usage in next Ajax request
        $this->_result->getTest()->setAjaxOptions(
            array(
                'data' => array('fileLastOffset' => $offset)
            )
        );
        
        $content = '';

        do {
            // Move file pointer for new read
            $offset = max(0, $offset - self::READ_BUFFER_SIZE);
            fseek($fp, $offset, SEEK_SET);

            $readBuffer = fread($fp, self::READ_BUFFER_SIZE);
            $linesCountInReadBuffer = substr_count($readBuffer, "\n");

            // If we have enought lines extract from last readed fragment only required lines
            if ($linesCountInReadBuffer >= $linesCount) {
                $readBuffer = implode("\n", array_slice(explode("\n", $readBuffer), -$linesCount));
            }

            // Update how many lines still need to be readed
            $linesCount -= $linesCountInReadBuffer;

            // Attach last readed lines at beggining of earlier readed fragments
            $content = $readBuffer . $content;
            
            if (strlen($content) > $this->_maxBytesToRender) {
                $this->_log(
                    sprintf(
                        "Content is too long already (%d bytes), we won't render any more",
                        strlen($content)
                    )
                );
                break;
            }
        } while ($offset > 0 && $linesCount > 0);

        $this->_log($content);
        return $this;
    }

    /**
     * Show last x lines from the file, and refresh it imediatelly
     *
     * @param string File name
     * @param string How many lines to display?
     * @param string How many seconds each line should be visible
     * @return $this
     * @see phpRack_Runner::run()
     */
    public function tailf($fileName, $linesCount = self::LINES_TO_SHOW, $secVisible = 5)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();
        $test = $this->_result->getTest();
        
        $test->setAjaxOptions(
            array(
                'reload' => 0.5, //500ms I think is okey for delay between requests, can be lower
                'secVisible' => $secVisible,
                'linesCount' => $linesCount,
                'attachOutput' => true
            )
        );
        $options = $test->getAjaxOptions();

        clearstatcache();
        // get current file size
        $fileSize = @filesize($fileName);
        if ($fileSize === false) {
            $this->_failure("Failed to filesize('{$fileName}')");
            return $this;
        }

        // if it is first request or file was truncated, send all x last lines
        if (!isset($options['fileLastOffset']) || $fileSize < $options['fileLastOffset']) {
            $this->tail($fileName, $linesCount);
            return $this;
        }

        $fp = @fopen($fileName, 'rb');
        if (!$fp) {
            $this->_failure("Failed to fopen('{$fileName}')");
            return $this;
        }
        // get only new content since last time
        $content = @stream_get_contents($fp, -1, $options['fileLastOffset']);
        if ($content === false) {
            $this->_failure("Failed to stream_get_contents({$fp}/'{$fileName}', -1, {$options['fileLastOffset']})");
            return $this;
        }

        // save current offset
        $offset = @ftell($fp);
        if ($offset === false) {
            $this->_failure("Failed to ftell({$fp}/'{$fileName}')");
            return $this;
        }
        if (@fclose($fp) === false) {
            $this->_failure("Failed to fclose({$fp}/'{$fileName}')");
            return $this;
        }

        $this->_log($content);

        // set ajax option with new file end offset for usage in next Ajax request
        $test->setAjaxOptions(
            array(
                'data' => array('fileLastOffset' => $offset),
            )
        );
        return $this;
    }

    /**
     * Show first x lines from the file
     *
     * @param string File name
     * @param string How many lines to display?
     * @return $this
     */
    public function head($fileName, $linesCount = self::LINES_TO_SHOW)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        // Check that file exists
        if (!$this->_isFileExists($fileName)) {
            return $this;
        }

        $content = '';
        $readedLinesCount = 0;
        $fp = @fopen($fileName, 'rb');
        if ($fp === false) {
            $this->_failure("Failed to fopen('{$fileName}')");
            return $this;
        }

        // Read line by line until we have required count or we reach EOF
        while ($readedLinesCount < $linesCount && !feof($fp)) {
            $content .= @fgets($fp);
            $readedLinesCount++;
            if (strlen($content) > $this->_maxBytesToRender) {
                $this->_log(
                    sprintf(
                        "Content is too long already (%d bytes), we won't render any more",
                        strlen($content)
                    )
                );
                break;
            }
        }

        if (@fclose($fp) === false) {
            $this->_failure("Failed to fclose('{$fileName}')");
            return $this;
        }
        
        $this->_log($content);
        return $this;
    }

    /**
     * Checks whether a file exists
     *
     * @param string File name to check
     * @return $this
     */
    public function exists($fileName)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        clearstatcache();
        if (file_exists($fileName)) {
            $this->_success("File '{$fileName}' exists");
        } else {
            $this->_failure("File '{$fileName}' does not exist");
        }
        return $this;
    }

    /**
     * Checks whether a file is readable
     *
     * @param string File name to check
     * @return $this
     */
    public function isReadable($fileName)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        clearstatcache();
        if (is_readable($fileName)) {
            $this->_success("File '{$fileName}' is readable");
        } else {
            $this->_failure("File '{$fileName}' is not readable");
        }
        return $this;
    }

    /**
     * Check whether a file is writable
     *
     * @param string File name to check
     * @return $this
     */
    public function isWritable($fileName)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        clearstatcache();
        if (is_writable($fileName)) {
            $this->_success("File '{$fileName}' is writable");
        } else {
            $this->_failure("File '{$fileName}' is not writable");
        }
        return $this;
    }

    /**
     * Check whether the filename is a directory
     *
     * @param string File name to check
     * @return $this
     */
    public function isDir($fileName)
    {
        $fileName = phpRack_Adapters_File::factory($fileName)->getFileName();

        clearstatcache();
        if (is_dir($fileName)) {
            $this->_success("File '{$fileName}' is a directory");
        } else {
            $this->_failure("File '{$fileName}' is not a directory");
        }
        return $this;
    }
    
    /**
     * Check that file exists
     *
     * @param string File name to check
     * @return boolean True if file exists
     */
    protected function _isFileExists($fileName)
    {
        if (!file_exists($fileName)) {
            $this->_failure("File '{$fileName}' is not found");
            return false;
        }

        $this->_log(
            sprintf(
                "File '%s' (%d bytes, modified on %s):",
                realpath($fileName),
                filesize($fileName),
                $this->_modifiedOn(filemtime($fileName))
            )
        );
        return true;
    }

    /**
     * Show when this file was modified
     *
     * @param integer Time/date when this file was modifed, result of filemtime()
     * @return string
     * @see _isFileExists()
     */
    protected function _modifiedOn($time)
    {
        $mins = round((time() - $time)/60, 1);
        if ($mins < 1) {
            $age = round($mins * 60) . 'sec';
        } elseif ($mins < 60) {
            $age = $mins . 'min';
        } elseif ($mins < 24 * 60) {
            $age = round($mins/60) . 'hrs';
        } else {
            $age = round($mins/(60*24)) . 'days';
        }
        
        return date('d-M-y H:i:s', $time) . ', ' . $age . ' ago';
    }
}
