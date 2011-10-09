<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5
 *
 * @category EasyBib
 * @package  ViewHelper
 * @author   Michael Scholl <michael@sch0ll.de>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  git: $id$
 * @link     https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Messages helper
 *
 * @category EasyBib
 * @package  ViewHelper
 * @author   Michael Scholl <michael@sch0ll.de>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  Release: @package_version@
 * @link     https://github.com/easybib/EasyBib_Form_Decorator
 */

class EasyBib_View_Helper_MessagesFormatter extends Zend_View_Helper_Abstract
{
    /**
     * Formats given messages in a paragraph with given class
     * input format can be string, array, multi dimensional array
     *
     * With array use following notation: array('error', 'message')
     * -> first child is class for paragraph
     *      - use success|notice|error for blueprint
     *      - use (alert-message) warning|error|success|info for bootstrap
     * -> second child is printed message
     *
     * @param  array  $messages
     * @param  string $tag (default=p)
     * @param  string $format (default=blueprint)
     * @return string
     */
    public function messagesFormatter($messages, $tag = 'p', $format = 'bootstrap')
    {
        $return = '';

        if (is_array($messages) && count($messages) > 0) {
            if (is_array($messages[0])) {
                foreach ($messages AS $msg) {
                    if (is_array($msg)) {
                        if ($format == 'bootstrap') {
                            $class = 'class="alert-message '.$msg[0].'"';
                        } else {
                            $class = 'class="notice"';
                        }
                        $return .= '<'.$tag.' '.$class.'>';
                        if ($format == 'bootstrap') {
                            $return .= '<p>';
                        }
                        $return .= $msg[1];
                        if ($format == 'bootstrap') {
                            $return .= '</p>';
                        }
                        $return .= '</'.$tag.'>';
                    }
                }
            } else {
                if ($format == 'bootstrap') {
                    $class = 'class="alert-message '.$messages[0].'"';
                } else {
                    $class = 'class="notice"';
                }
                $return .= '<'.$tag.' '.$class.'>';
                if ($format == 'bootstrap') {
                    $return .= '<p>';
                }
                $return .= $messages[1];
                if ($format == 'bootstrap') {
                    $return .= '</p>';
                }
                $return .= '</'.$tag.'>';
            }
        } else if (is_string($messages)) {
            if ($format == 'bootstrap') {
                $class = 'class="alert-message warning"';
            } else {
                $class = 'class="notice"';
            }
            $return .= '<'.$tag.' '.$class.'>';
            if ($format == 'bootstrap') {
              $return .= '<p>';
            }
            $return .= $messages;
            if ($format == 'bootstrap') {
              $return .= '</p>';
            }
            $return .= '</'.$tag.'>';
        }

        return $return;
    }
}
