<?php

namespace Sipp\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sipp\Entity\Company;
use Sipp\Form\CompanyForm;

class SippController extends AbstractActionController {

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
            $companyEntity = $this->getEntityManager()->getRepository('Sipp\Entity\Company');
            $companies = $companyEntity->findAll();
            $viewModel->setVariable('companies', $companies);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $viewModel;
    }

    public function addAction() {
        $form = new CompanyForm();
        $form->get('submit')->setValue('Add');
        $form->setAttribute('action', '/sipp/company/add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $company = new Company();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $company->setName($formData['name']);
                $company->setCode($formData['code']);
                $this->getEntityManager()->persist($company);
                $this->getEntityManager()->flush();
                
                return [
                    'form' => $form,
                    'message' => 'Se ha agregado correctamente'
                ];
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('sipp/crud_entities', [
                        'controller' => 'Company',
                        'action' => 'add'
            ]);
        }
        $company = $this->getEntityManager()->find('Sipp\Entity\Company', $id);
        if (!$company) {
            return $this->redirect()->toRoute('sipp/crud_entities', [
                        'controller' => 'Company',
                        'action' => 'index'
            ]);
        }
        $form = new CompanyForm();
        $form->bind($company);
        $form->get('submit')->setAttribute('value', 'Editar');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getEntityManager()->flush();
                return [
                    'form' => $form,
                    'message' => 'Se ha editado correctamente'
                ];
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
            return $this->redirect()->toRoute('sipp');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $company = $this->getEntityManager()->find('Sipp\Entity\Company', $id);
                if ($company) {
                    $this->getEntityManager()->remove($company);
                    $this->getEntityManager()->flush();
                    
                    return [
                        'message' => 'Se ha borrado correctamente'
                    ];
                }
            }
            return [
                'message' => 'Ha ocurrido un error al borrar'
            ];
        }
        return array(
            'id' => $id,
            'album' => $this->getEntityManager()->find('Sipp\Entity\Company', $id)
        );
    }
}
