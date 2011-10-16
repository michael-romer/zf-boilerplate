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
 * @version $Id: Abstract.php 545 2010-05-04 09:40:46Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Abstract adapter for DB connectivity
 *
 * @package Adapters
 * @subpackage Db
 */
abstract class phpRack_Adapters_Db_Abstract
{
    
    /**
     * Connect to the server
     *
     * @param string JDBC URL to connect to the server
     * @return void
     * @see http://java.sun.com/docs/books/tutorial/jdbc/basics/connecting.html
     * @throws Exception If something wrong happens there
     */
    abstract public function connect($url);
    
    /**
     * Execute SQL query on the server
     *
     * @param string SQL query
     * @return string Raw result from the server, in text
     * @throws Exception If something wrong happens there
     */
    abstract public function query($sql);

    /**
     * Parse JDBC URL and return its components
     *
     * This method matches URLSs like:
     *
     * <code>
     * jdbc:mysql://localhost:3306/test?username=login&password=password
     * jdbc:mysql://localhost:3306/test
     * jdbc:mysql://localhost:3306
     * jdbc:mysql://localhost
     * </code>
     *
     * Mandatory parts of the URL are: "adapter", "host". All other params are
     * optional and could be omitted. 
     *
     * @param string JDBC URL to parse
     * @throws Exception If JDBC URL have wrong format
     * @return array We set "adapter", "host", "port", "database", "params"
     */
    protected function _parseJdbcUrl($url)
    {
        $pattern = '#^jdbc:(?P<adapter>[^:]+)'
            . '://(?P<host>[^:/]+)'
            . '(?::(?P<port>\d+))?'
            . '(?:/(?P<database>[^?]+))?'
            . '(?:\?(?P<params>.*))?$#';

        $matches = array();
        if (!preg_match($pattern, $url, $matches)) {
            throw new Exception('JDBC URL parse error');
        }

        // Convert params string to array
        if (isset($matches['params'])) {
            $paramsString = $matches['params'];
            parse_str($paramsString, $matches['params']);
        }

        return $matches;
    }
}
