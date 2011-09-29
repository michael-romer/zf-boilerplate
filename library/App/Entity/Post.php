<?php
namespace App\Entity;

/**
 * @Entity(repositoryClass="App\Repository\Post")
 * @Table(name="post")
 */
class Post
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $_id;
    /** @Column(type="string") */
    private $_title;
    /** @Column(type="date", nullable=true) */
    private $_publishDate;

    public function getId()
    {
        return $this->_id;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getPublishDate()
    {
        return $this->_publishDate;
    }

    public function setPublishDate(\DateTime $publishDate)
    {
        $this->_publishDate = $publishDate;
        return $this;
    }

    public function __toString()
    {
        return $this->getTitle() . ' (' . $this->getId() . ')';
    }
}