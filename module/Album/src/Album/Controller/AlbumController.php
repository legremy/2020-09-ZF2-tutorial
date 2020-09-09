<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AlbumController extends AbstractActionController
{

    protected $albumTable;

    public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
    
    public function indexAction()
    {
        return [
            'albums' => $this->getAlbumTable()->fetchAll(),
        ];  
    }
    
    public function addAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }
}