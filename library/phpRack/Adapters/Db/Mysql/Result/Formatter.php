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
 * @version $Id: Formatter.php 611 2010-07-12 14:23:40Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * MySQL adapter result formatter
 *
 * @package Adapters
 * @subpackage Db
 */
class phpRack_Adapters_Db_Mysql_Result_Formatter
{
    /**
     * Format SQL query result with spaces for better readability
     *
     * @param resource returned from mysql_query()
     * @return string formatted query result as plain text
     * @see phpRack_Adapters_Db_Mysql::query()
     */
    public static function formatResult($result)
    {
        $response = '';
        // margin between columns in spaces
        $columnsMargin = 2;

        // create array for storing columns meta data
        $columns = array_fill(0, mysql_num_fields($result), array());

        // determine columns lenght and create columns headers
        foreach ($columns as $columnIndex => &$column) {
            // get column data for this index
            $column['meta'] = mysql_fetch_field($result, $columnIndex);

            // set what length should has this columns (get max length from data and column name)
            $column['length'] = max(strlen($column['meta']->name), $column['meta']->max_length);

            // add centered column header
            $response .= str_pad($column['meta']->name, $column['length'], ' ', STR_PAD_BOTH);

            // add margin between columns for better readability
            $response .= str_repeat(' ', $columnsMargin);
        }

        $response .= "\n";

        // foreach row in result
        while (false !== ($row = mysql_fetch_row($result))) {
            // foreach column in result row
            foreach ($row as $columnIndex => $value) {
                $column = &$columns[$columnIndex];

                // choose which padding type we should use
                if ($column['meta']->numeric) {
                    $padType = STR_PAD_LEFT;
                } else {
                    $padType = STR_PAD_RIGHT;
                }
                // pad value with spaces for have equal width in all rows
                $response .= str_pad($value, $column['length'], ' ', $padType);

                // add margin between columns for better readability
                $response .= str_repeat(' ', $columnsMargin);
            }
            $response .= "\n";
        }

        return $response;
    }

    /**
     * Remove header line from query result, which is added by _formatResult()
     * method. Sometimes we just need raw result without this extra line.
     *
     * @param string query result with header line
     * @return string
     * @see formatResult()
     * @see phpRack_Adapters_Db_Mysql::isDatabaseSelected()
     */
    public static function removeColumnHeadersLine($result)
    {
        $pos = strpos($result, "\n");
        // If we have only headers line
        if ($pos === false || strlen($result) == $pos + 1) {
            return '';
        }
        return substr($result, $pos + 1);
    }

}
