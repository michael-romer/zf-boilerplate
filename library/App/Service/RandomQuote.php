<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 14.03.11
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */
namespace App\Service;

class RandomQuote
{
    protected $_randomizer;
    private $_quotes = array(
        array("There are only two ways to live your life. One is as
              though nothing is a miracle. The other is as though
              everything is a miracle.", "Albert Einstein"),
        array("One of the most tragic things I know about human nature
              is that all of us tend to put off living. We are all
              dreaming of some magical rose garden over the
              horizon-instead of enjoying the roses blooming outside
              our windows today.", "Dale Carnegie"),
        array("Never let your memories be greater than your dreams.",
              "Doug Ivester"),
        array("The most important thing about goals is having one.",
              "Geoffry F. Abert"),
        array("All great changes are preceded by chaos.", "Deepak Chopra"),
        array("Don’t go around saying the world owes you a living.
              The world owes you nothing. It was here first.", "Mark Twain"),
        array("Take care of your body. It’s the only place you have to live.",
              "Jim Rohn"),
        array("One person with a belief is equal to a force of 99 who have
              only interests.", "John Stuart Mill"),
        array("Do what you love and the money will follow.", "Marsha Sinetar"),
        array("The world makes way for the man who knows where he is going.",
              "Ralph Waldo Emerson"),
        array("You will not do incredible things without an incredible dream.",
              "John Eliot"),
        array("You are never too old to set another goal or to dream a new
              dream.", "Les Brown")
        );

    public function __construct($randomizer)
    {
        $this->_randomizer = $randomizer;
    }

    public function getQuote()
    {
        $n = $this->_randomizer->getNumber(0, count($this->_quotes)-1);
        return $this->_quotes[$n];
    }
}
