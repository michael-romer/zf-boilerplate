<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 07.10.11
 * Time: 04:43
 * To change this template use File | Settings | File Templates.
 */

namespace App\Form;

class AddQuote extends \EasyBib_Form
{
    public function init()
    {
        // $this->setDefaultTranslator(\Zend_Registry::get('Zend_Translate')); ???
        $this->setMethod('POST');
        $this->setAction('/index/add-custom');
        $this->setAttrib('id', 'addQuote');

        $quote = new \Zend_Form_Element_Textarea('quote');
        $name = new \Zend_Form_Element_Text('name');
        $submit = new \Zend_Form_Element_Button('submit');

        $quote->setLabel('Your wise words:')
            ->setRequired(true)
            ->setAttrib('rows', '4')
            ->setTranslator(\Zend_Registry::get('Zend_Translate'));

        $name->setLabel('Your name:')
            ->setRequired(true);

        $submit->setLabel('List my quote');
        $this->addElements(array($quote, $name, $submit));

        \EasyBib_Form_Decorator::setFormDecorator(
            $this, \EasyBib_Form_Decorator::BOOTSTRAP, 'submit'
        );
    }

    public function isValid($data)
    {
        if (!is_array($data)) {
            require_once 'Zend/Form/Exception.php';
            throw new \Zend_Form_Exception(__METHOD__ . ' expects an array');
        }
        return parent::isValid($data);
    }
}