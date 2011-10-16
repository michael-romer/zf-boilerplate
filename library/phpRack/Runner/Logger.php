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
 * @version $Id: Logger.php 623 2010-07-19 12:23:28Z yegor256@yahoo.com $
 * @category phpRack
 */

/**
 * Runner logger
 *
 * @package Tests
 * @see phpRack_Runner::run()
 */
class phpRack_Runner_Logger
{
    /**
     * Cuts log according to the limit provided
     *
     * @param string Log to cut
     * @param integer Limit in Kb
     * @see run()
     * @return string
     */
    public static function cutLog($log, $limit)
    {
        $len = 0;
        if (function_exists('mb_strlen')) {
            $len += mb_strlen($log, 'UTF-8');
        } elseif (function_exists('iconv_strlen')) {
            $len += iconv_strlen($log, 'UTF-8');
        } else {
            // bad variant
            $len += strlen($log) / 2;
        }

        $max = $limit * 1024; // in kb
        if ($len > $max) {
            $cutSize = $max / 2;
            $func = '';
            if (function_exists('iconv_substr')) {
                $func = 'iconv_substr';
            } elseif (function_exists('mb_substr')) {
                $func = 'mb_substr';
            }
            if ($func) {
                $head = call_user_func($func, $log, 0, $cutSize, 'UTF-8');
                $tail = call_user_func(
                    $func, $log, -1 * $cutSize, $cutSize, 'UTF-8'
                );
            } else {
                // bad variant
                $head = substr($log, 0, $cutSize / 2);
                $tail = substr($log, -1 * $cutSize / 2);
            }
            return $head . "\n\n... content skipped (" . ($len - $max) . " bytes) ...\n\n" . $tail;
        }
        return $log;
    }

    /**
     * Checks for string encoding, and if encoding is not utf-8, encodes to utf-8
     *
     * @param string String to convert into UTF-8
     * @return string Proper UTF-8 formatted string
     * @see run()
     * @see #60 I think that this method shall be extensively tested. Now I have problems
     *      with content that is not in English.
     */
    public static function utf8Encode($str)
    {
        return utf8_encode($str);
        // $isUtf = false;
        // if (function_exists('mb_check_encoding')) {
        //     $isUtf = mb_check_encoding($str, 'UTF-8');
        // }
        // if (function_exists('iconv')) {
        //     $isUtf = (@iconv('UTF-8', 'UTF-16', $str) !== false);
        // }
        // return (!$isUtf) ? utf8_encode($str) : $str;
    }
}