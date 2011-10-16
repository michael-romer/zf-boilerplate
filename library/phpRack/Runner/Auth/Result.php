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
 * @version $Id: Result.php 623 2010-07-19 12:23:28Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Result of authentication before running tests
 *
 * @package Tests
 * @see phpRack_Runner_Auth::_validated()
 */
class phpRack_Runner_Auth_Result
{

    /**
     * Stores auth result
     *
     * @var boolean
     * @see isValid()
     */
    protected $_valid;
    
    /**
     * Optional error message
     *
     * @var string
     * @see isValid()
     */
    protected $_message;
    
    /**
     * Constructor
     *
     * @param boolean Whether the auth is valid or not
     * @param string Optional error message
     * @return void
     */
    public function __construct($valid, $message = null)
    {
        $this->_valid = $valid;
        $this->_message = $message;
    }
    
    /**
     * Result is VALID?
     *
     * @return boolean
     */
    public function isValid() 
    {
        return $this->_valid;
    }
    
    /**
     * Error message, if exists
     *
     * @return string
     */
    public function getMessage() 
    {
        return $this->_message;
    }
    
}
