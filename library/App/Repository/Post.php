<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;
 
class Post extends EntityRepository
{
    public function findThemAll()
    {
        return $this->_em->createQuery('SELECT p FROM App\Entity\Post p')
                         ->getResult();
    }
}
