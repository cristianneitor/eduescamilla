<?php

namespace Sipp\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sipp\Entity\Employee;
use Sipp\Form\EmployeeForm;

class EmployeeController extends AbstractActionController {

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
            $employeeEntity = $this->getEntityManager()->getRepository('Sipp\Entity\Employee');
            $employees = $employeeEntity->findAll();
            $viewModel->setVariable('employees', $employees);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $viewModel;
    }

    public function addAction() {
        $form = new EmployeeForm();
        $form->get('submit')->setValue('Agregar');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $employee = new Employee();
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $employee->setUserId($formData['user_id']);
                $employee->setCompanyCode($formData['company_code']);
                $employee->setState($formData['state']);
                $this->getEntityManager()->persist($employee);
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
                        'controller' => 'employee',
                        'action' => 'add'
            ]);
        }
        $employee = $this->getEntityManager()->find('Sipp\Entity\Employee', $id);
        if (!$employee) {
            return $this->redirect()->toRoute('sipp/crud_entities', [
                        'controller' => 'Employee',
                        'action' => 'index'
            ]);
        }
        $form = new EmployeeForm();
        $form->bind($employee);
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
                $employee = $this->getEntityManager()->find('Sipp\Entity\Employee', $id);
                if ($employee) {
                    $this->getEntityManager()->remove($employee);
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
            'employee' => $this->getEntityManager()->find('Sipp\Entity\Employee', $id)
        );
    }
}
