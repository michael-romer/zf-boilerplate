<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
 
class Quote extends EntityRepository
{
    public function findThemAll()
    {
        $stmt = 'SELECT q FROM App\Entity\Quote q ORDER BY q._id DESC';
        return $this->_em->createQuery($stmt)->getResult();
    }
}
