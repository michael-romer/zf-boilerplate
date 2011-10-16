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
 * @version $Id: Url.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * @see phpRack_Adapters_Url
 */
require_once PHPRACK_PATH . '/Adapters/Url.php';

/**
 * Network check using Url
 *
 * @package Tests
 */
class phpRack_Package_Network_Url extends phpRack_Package
{
    /**
     * Adapter used for cummunication with server
     *
     * @var phpRack_Adapters_Url
     * @see url()
     * @see regexp()
     */
    private $_adapter;

    /**
     * Contain url passed to url function, used in other methods of this class
     *
     * @var string
     * @see url()
     * @see regex()
     */
    private $_url;

    /**
     * Set URL and validate it
     *
     * @param string URL
     * @param array Options which will affect connection
     * @return $this
     * @throws Exception if URL is invalid
     */
    public function url($url, $options = array())
    {
        $this->_adapter = phpRack_Adapters_Url::factory($url, $options);
        $this->_url = $url;
        return $this;
    }

    /**
     * Make HTTP call and check that pattern exists in result
     *
     * @param string Pattern to check
     * @return $this
     * @see url()
     * @throws Exception If this method is called before url()
     * @throws Exception If can't connect
     */
    public function regex($pattern)
    {
        if (empty($this->_url)) {
            throw new Exception('url() function must be called before');
        }

        $content = $this->_adapter->getContent();

        $found = @preg_match($pattern, $content);

        // If regexp pattern is invalid, try to use it as string pattern
        if ($found === false) {
            $this->_log('Regex is not valid, try to use it as string');
            $found = (strpos($content, $pattern) !== false);
        }

        // If pattern was found in content
        if ($found) {
            $this->_success("Pattern '{$pattern}' was found on '{$this->_url}'");
        } else {
            $this->_failure("Pattern '{$pattern}' was NOT found on '{$this->_url}'");
        }

        return $this;
    }
}
