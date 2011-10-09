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
 * @package  EasyBib_Form
 * @author   Michael Scholl <michael@sch0ll.de>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  git: $id$
 * @link     https://github.com/easybib/EasyBib_Form_Decorator
 */

/**
 * Default Decorators Set
 *
 * General usage:
 * EasyBib_Form_Decorator::setFormDecorator($form, 'div', 'submit', 'cancel');
 * EasyBib_Form_Decorator::setFormDecorator(
 *   Instance of form,
 *   Decorator Mode - 3 different options:
 *      - EasyBib_Form_Decorator::TABLE     (html table style)
 *      - EasyBib_Form_Decorator::DIV       (div style)
 *      - EasyBib_Form_Decorator::BOOTSTRAP (twitter bootstrap style)
 *   Name of submit button,
 *   Name of cancel button
 * );
 *
 * @category EasyBib
 * @package  EasyBib_Form
 * @author   Michael Scholl <michael@sch0ll.de>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  Release: @package_version@
 * @link     https://github.com/easybib/EasyBib_Form_Decorator
 */
class EasyBib_Form_Decorator
{
    /**
     * Constants Definition for Decorator
     */
    const TABLE = 'table';

    const DIV = 'div';

    const BOOTSTRAP = 'bootstrap';

    /**
     * Element Decorator
     *
     * @staticvar array
     */
    protected static $_ElementDecorator = array(
        'table' => array(
            'ViewHelper',
            array(
                'Description',
                array(
                    'tag' => '',
                    'escape' => false,
                    'image' => '/images/icons/information.png'
                )
            ),
            'Errors',
            array(
                array(
                    'data' => 'HtmlTag'
                ),
                array(
                    'tag' => 'td'
                )
            ),
            array(
                'Label',
                array(
                    'tag' => 'td'
                )
            ),
            array(
                array(
                    'row' => 'HtmlTag'
                ),
                array(
                    'tag' => 'tr'
                )
            )
        ),
        'div' => array(
            array(
                'ViewHelper'
            ),
            array(
                'Errors'
            ),
            array(
                'Description',
                array(
                    'tag'   => 'span',
                    'class' => 'hint'
                )
            ),
            array(
                'Label'
            ),
            array(
                'HtmlTag',
                array(
                    'tag' => 'div'
                )
            )
        ),
        'bootstrap' => array(
            array(
                'ViewHelper'
            ),
            array(
                'BootstrapErrors'
            ),
            array(
                'Description',
                array(
                    'tag'   => 'span',
                    'class' => 'help-inline'
                )
            ),
            array(
                'BootstrapTag',
                array(
                    'class' => 'input'
                )
            ),
            array(
                'Label'
            ),
            array(
                'HtmlTag',
                array(
                    'tag'   => 'div',
                    'class' => 'clearfix'
                )
            )
        )
    );

    /**
     * Submit Element Decorator
     *
     * @staticvar array
     */
    protected static $_SubmitDecorator = array(
        'table' => array(
            'ViewHelper'
        ),
        'div' => array(
            'ViewHelper'
        ),
        'bootstrap' => array(
            'ViewHelper',
            array(
                'HtmlTag',
                array(
                    'tag'   => 'div',
                    'class' => 'actions',
                    'openOnly' => false
                )
            )
        )
    );

    /**
     * Reset Element Decorator
     *
     * @staticvar array
     */
    protected static $_ResetDecorator = array(
        'table' => array(
            'ViewHelper'
        ),
        'div' => array(
            'ViewHelper'
        ),
        'bootstrap' => array(
            'ViewHelper',
            array(
                'HtmlTag',
                array(
                    'closeOnly' => false
                )
            )
        )
    );

    /**
     * Hiden Element Decorator
     *
     * @staticvar array
     */
    protected static $_HiddenDecorator = array(
        'table' => array(
            'ViewHelper'
        ),
        'div' => array(
            'ViewHelper'
        ),
        'bootstrap' => array(
            'ViewHelper'
        )
    );

    /**
     * Form Element Decorator
     *
     * @staticvar array
     */
    protected static $_FormDecorator = array(
        'table' => array(
            'FormElements',
            'Form'
        ),
        'div' => array(
            'FormElements',
            'Form'
        ),
        'bootstrap' => array(
            'FormElements',
            'Form'
        )
    );

    /**
     * DisplayGroup Decorator
     *
     * @staticvar array
     */
    protected static $_DisplayGroupDecorator = array(
        'table' => array(
            'FormElements',
            array(
                'HtmlTag',
                array(
                    'tag' => 'table',
                    'summary' => ''
                )
            ),
            'Fieldset'
        ),
        'div' => array(
            'FormElements',
            'Fieldset'
        ),
        'bootstrap' => array(
            'FormElements',
            'Fieldset'
        )

    );

    /**
     * Set the form decorators by the given string format or by the default div style
     *
     * @param object $objForm        Zend_Form pointer-reference
     * @param string $constFormat    Project_Plugin_FormDecoratorDefinition constants
     * @return NULL
     */
    public static function setFormDecorator(Zend_Form $form, $format = self::BOOTSTRAP, $submit_str = 'submit', $cancel_str = 'cancel') {
        /**
         * - disable default decorators
         * - set form & displaygroup decorators
         */
        $form->setDisableLoadDefaultDecorators(true);
        $form->setDisplayGroupDecorators(self::$_DisplayGroupDecorator[$format]);
        $form->setDecorators(self::$_FormDecorator[$format]);

        // set needed prefix path for bootstrap decorators
        if ($format == self::BOOTSTRAP) {
            $form->addElementPrefixPath(
                'EasyBib_Form_Decorator',
                'EasyBib/Form/Decorator',
                Zend_Form::DECORATOR
            );
        }

        // set form element decorators
        $form->setElementDecorators(self::$_ElementDecorator[$format]);

        // set submit button decorators
        if ($form->getElement($submit_str)) {
            $form->getElement($submit_str)->setDecorators(self::$_SubmitDecorator[$format]);
            if ($format == self::BOOTSTRAP) {
                $attribs = $form->getElement($submit_str)->getAttrib('class');
                if (empty($attribs)) {
                    $attribs = array('btn', 'primary');
                } else {
                    if (is_string($attribs)) {
                        $attribs = array($attribs);
                    }
                    $attribs = array_unique(array_merge(array('btn'), $attribs));
                }
                $form->getElement($submit_str)
                    ->setAttrib('class', $attribs)
                    ->setAttrib('type', 'submit');
                if ($form->getElement($cancel_str)) {
                    $form->getElement($submit_str)->getDecorator('HtmlTag')
                        ->setOption('openOnly', true);
                }
            }
        }

        // set cancel button decorators
        if ($form->getElement($cancel_str)) {
            $form->getElement($cancel_str)->setDecorators(self::$_ResetDecorator[$format]);
            if ($format == self::BOOTSTRAP) {
                $attribs = $form->getElement($cancel_str)->getAttrib('class');
                if (empty($attribs)) {
                    $attribs = array('btn');
                } else {
                    if (is_string($attribs)) {
                        $attribs = array($attribs);
                    }
                    $attribs = array_unique(array_merge(array('btn'), $attribs));
                }
                $form->getElement($cancel_str)
                    ->setAttrib('class', $attribs)
                    ->setAttrib('type', 'reset');
                if ($form->getElement($submit_str)) {
                    $form->getElement($cancel_str)->getDecorator('HtmlTag')
                        ->setOption('closeOnly', true);
                }
            }
        }

        // set hidden input decorators
        foreach ($form->getElements() as $e) {
            if ($e->getType() == 'Zend_Form_Element_Hidden') {
                $e->setDecorators(self::$_HiddenDecorator[$format]);
            }
        }
    }
}