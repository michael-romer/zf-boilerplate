<?php
namespace Ctrlr\Entity;
/**
 * @Entity
 * @Table(name="person")
 */
class Person
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /** @Column(type="string") */
    private $vorname;
    /** @Column(type="string") */
    private $nachname;
    /** @Column(type="date", nullable=true) */
    private $geburtstag;
    /** @Column(type="decimal", nullable=true) */
    private $gewicht;

    public function getId() {
        return $this->id;
    }
    public function getVorname() {
        return $this->vorname;
    }
    public function setVorname($vorname) {
        $this->vorname = $vorname;
        return $this;
    }
    public function getNachname() {
        return $this->nachname; 
    }
    public function setNachname($nachname) {
        $this->nachname = $nachname;
        return $this;
    }
    public function getGeburtstag() {
        return $this->geburtstag;
    }
    /**
     * Setter für $geburtstag
     *
     * Da für das Attribut der Doctrine-Typ "date" festgelegt wurde, müssen
     * wir dafür sorgen, dass hier ein DateTime übergeben wird.
     *
     * @param \DateTime $geburtstag
     * @return Person
     */
    public function setGeburtstag(\DateTime $geburtstag) {
        $this->geburtstag = $geburtstag;
        return $this;
    }
    public function getGewicht() {
        return $this->gewicht;
    }
    public function setGewicht($gewicht) {
        $this->gewicht = $gewicht;
        return $this;
    }
    public function getAdressen() {
        return $this->adressen;
    }
    public function addAdresse(Adresse $adresse) {
        $this->adressen[] = $adresse;
    }
    public function __toString() {
        return $this->getVorname() . ' ' . $this->getNachname()
                . ' (' . $this->getId() . ')';
    }
}