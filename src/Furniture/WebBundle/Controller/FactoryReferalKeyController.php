<?php

namespace Furniture\WebBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryReferalKey;

use Furniture\FactoryBundle\Form\Type\FactoryReferalKeyType;

class FactoryReferalKeyController extends ResourceController
{
    
    public function createAction(Request $request) {
        $factory = $this->getFactoryOr404($request->get('fid'));
        
        $refKey = new FactoryReferalKey();
        $refKey->setFactory($factory);
        
        $form = $this->createForm( new FactoryReferalKeyType(), $refKey);
        
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            
            $fatoryUserRelation = $form->getData();
            
            $this->getDoctrine()->getManager()->persist($refKey);
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirect($this->container->get('router')->generate('furniture_backend_factory_referal_key_index', ['fid' => $factory->getId()]));
        }
        
        $view = $this
            ->view([
                'factory'                        => $factory,
                $this->config->getResourceName() => $refKey,
                'form'                           => $form->createView(),
            ])
            ->setTemplate($this->config->getTemplate('create.html'))
        ;
        
        return $this->handleView($view);
    }
    
    public function getFactoryRepository(){
        return $this->getDoctrine()->getManager()->getRepository(Factory::class);
    }
    
    protected function getFactoryOr404($id){
        if(!$factory = $this->getFactoryRepository()->find($id)){
            throw new NotFoundHttpException(
                sprintf(
                    'Requested %s does not exist',
                    $this->config->getResourceName()
                )
            );
        }
        return $factory;
    }
    
}
