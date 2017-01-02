<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
                
        // Setup SMTP transport using PLAIN authentication over TLS
        $config = array(
            'ssl' => 'tls', 
            'port' => '587', 
            'auth' => 'login', 
            'username' => 'convencion@renacercolombia.com', 
            'password' => ''
        );
        
        $transport = new Zend\Mail\Transport\Smtp('smtp.gmail.com', $config);

        $mail = new Zend\Mail\Message();
        $mail->setBodyText('This is the text of the mail.');
        $mail->setFrom('convencion@renacercolombia.com', 'Convención Renacer');
        $mail->addTo('eescamillap@hotmail.com', 'Eduardo');
        $mail->setSubject('Prueba de emails');
        $mail->send($transport);
        
        
        return new ViewModel();
    }
}
