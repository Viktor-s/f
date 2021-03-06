<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class LoadFactoriesData extends DataFixture
{
    
    protected $factory_path = '/../../Resources/fixtures/factoryImages/';
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        $factory = $this->createFactory(
                'Selva',
            [
                $this->defaultLocale => [
                    'description' => 'Selva factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Antonello Italia',
            [
                $this->defaultLocale => [
                    'description' => 'Antonello Italia factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Kartell',
            [
                $this->defaultLocale => [
                    'description' => 'Kartell factory description'
                ]
            ]);
        
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Arceos',
            [
                $this->defaultLocale => [
                    'description' => 'Arceos factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Passini',
            [
                $this->defaultLocale => [
                    'description' => 'Passini factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'i4 Mariani',
            [
                $this->defaultLocale => [
                    'description' => 'i4 Mariani factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $manager->flush();
    }

    /**
     * Create a new factory
     *
     * @param string $name
     * @param array  $translations
     *
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    private function createFactory($name, array $translations)
    {
        /* @var $factory \Furniture\FactoryBundle\Entity\Factory */
        $factory = $this->get('Furniture.repository.factory')->createNew();
        $factory->setName($name);
        $factory->setEnabled(true);
        
        foreach ($translations as $locale => $presentation) {
            $factory->setCurrentLocale($locale);
            $factory->setDescription($presentation['description']);
        }

        $img = new \SplFileInfo(__DIR__.$this->factory_path.$name.'-logo.jpg');
        $uploadFile = new UploadedFile($img->getRealPath(), $img->getFilename());
        
        $image = new \Furniture\FactoryBundle\Entity\FactoryImage();
        $image->setFile($uploadFile);
        $image->setFactory($factory);
        $this->get('sylius.image_uploader')->upload($image);
        
        $factory->addImage($image);
        
        $this->setReference('Furniture.factory.'.$name, $factory);
        
        return $factory;
        
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 19;
    }
}
