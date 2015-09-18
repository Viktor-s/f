<?php

namespace Furniture\WebBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;

use Furniture\FactoryBundle\Form\FactoryUserRelationType;

class FactoryUserRelationController extends ResourceController {

    
    protected function getFactoryRelationForm($data){
        
        $roles = $this->container->getParameter('factory.content_access_control.roles');
        
        return $this->createForm(new FactoryUserRelationType(), 
                                    $data,
                                    [
                                        'content_access_user_roles' => $roles
                                        ]);
    }
    
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');
        $factory = $this->getFactoryOr404($request->get('id'));
        
        $fatoryUserRelation = new FactoryUserRelation();
        $fatoryUserRelation->setFactory($factory);
        
        $form = $this->getFactoryRelationForm($fatoryUserRelation);
        
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            
            $fatoryUserRelation = $form->getData();
            
            $this->getDoctrine()->getManager()->persist($fatoryUserRelation);
            $this->getDoctrine()->getManager()->flush();
            
            return $this->redirectHandler->redirectTo($fatoryUserRelation);
        }
        
        $view = $this
            ->view([
                'factory'                        => $factory,
                $this->config->getResourceName() => $fatoryUserRelation,
                'form'                           => $form->createView(),
            ])
            ->setTemplate($this->config->getTemplate('create.html'))
        ;

        return $this->handleView($view);
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getFactoryRepository(){
        return $this->getDoctrine()->getManager()
                ->getRepository('Furniture\FactoryBundle\Entity\Factory');
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getFactoryUserRelationRepository(){
        return $this->getDoctrine()->getManager()
                ->getRepository('Furniture\FactoryBundle\Entity\FactoryUserRelation');
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
    
    protected function handleView(View $view)
    {
        $factory = $this->getFactoryOr404($this->container->get('request')->get('id'));
        
        if(is_array($view->getData())){
            $data = $view->getData();
        }else{
            $data = [
                $this->config->getPluralResourceName() => $view->getData()
            ];
        }
        $data['factory'] = $factory;
        $view
            ->setData($data)
        ;
        
        $handler = $this->get('fos_rest.view_handler');
        $handler->setExclusionStrategyGroups($this->config->getSerializationGroups());

        if ($version = $this->config->getSerializationVersion()) {
            $handler->setExclusionStrategyVersion($version);
        }

        return $handler->handle($view);
    }
    
}

