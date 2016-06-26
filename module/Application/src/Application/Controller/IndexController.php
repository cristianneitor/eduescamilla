<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Album;
use Application\Form\AlbumForm;
use Doctrine\ORM\EntityManager;
use DOMPDFModule\View\Model\PdfModel;

class IndexController extends AbstractActionController {

    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function indexAction() {
        $viewModel = new ViewModel();

        try {
            $albumEntity = $this->getEntityManager()->getRepository('Application\Entity\Album');
            $albums = $albumEntity->findAll();
            $viewModel->setVariable('albums', $albums);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $viewModel;
    }

    public function addAction() {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $album->setArtist($formData['artist']);
                $album->setTitle($formData['title']);
                $this->getEntityManager()->persist($album);
                $this->getEntityManager()->flush();
                // Redirect to list of albums
                return $this->redirect()->toRoute('home');
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                        'action' => 'add'
            ));
        }
        $album = $this->getEntityManager()->find('Album\Entity\Album', $id);
        if (!$album) {
            return $this->redirect()->toRoute('album', array(
                        'action' => 'index'
            ));
        }
        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->flush();
                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $album = $this->getEntityManager()->find('Application\Entity\Album', $id);
                if ($album) {
                    $this->getEntityManager()->remove($album);
                    $this->getEntityManager()->flush();
                }
            }
            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }
        return array(
            'id' => $id,
            'album' => $this->getEntityManager()->find('Application\Entity\Album', $id)
        );
    }

    public function pdfAction() {
        // Instantiate new PDF Model
        $pdf = new PdfModel();

        // set filename
        //$pdf->setOption('filename', 'hello.pdf');
        
        $pdf->setOption("basePath", __DIR__ . '/../../../../../public');
        // "/var/www/eduescamilla.com/eduescamilla/public"

        // Defaults to "8x11"
        $pdf->setOption('paperSize', 'a4');

        // paper orientation
        $pdf->setOption('paperOrientation', 'portrait');

        $pdf->setVariables(array(
            'var1' => 'Liverpool FC',
            'var2' => 'Atletico Madrid',
            'var3' => 'Borussia Dortmund'
        ));

        return $pdf;
    }
    
    public function adminAction() {
        $viewModel = new ViewModel();
        return $viewModel;
    }
}
