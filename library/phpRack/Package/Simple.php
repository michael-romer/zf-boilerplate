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
 * @version $Id: Simple.php 587 2010-05-17 05:52:02Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * @see phpRack_Package
 */
require_once PHPRACK_PATH . '/Package.php';

/**
 * Simple package, for simple assertions
 *
 * @package Tests
 */
class phpRack_Package_Simple extends phpRack_Package
{
    
    /**
     * Just fail the test
     *
     * @param string Message to show
     * @return $this
     */
    public function fail($msg) 
    {
        $this->_failure($msg);
        return $this;
    }

    /**
     * Mark the test as successful
     *
     * @param string Message to show
     * @return $this
     */
    public function success($msg) 
    {
        $this->_success($msg);
        return $this;
    }

    /**
     * Is it true?
     *
     * @param mixed Variable to check
     * @return $this
     */
    public function isTrue($var) 
    {
        if ($var) {
            $this->_success('Variable is TRUE, success');
        } else {
            $this->_failure('Failed to assert that variable is TRUE');
        }
        return $this;
    }
        
    /**
     * Is it false?
     *
     * @param mixed Variable to check
     * @return $this
     */
    public function isFalse($var) 
    {
        if (!$var) {
            $this->_success('Variable is FALSE, success');
        } else {
            $this->_failure('Failed to assert that variable is FALSE');
        }
        return $this;
    }
        
}
