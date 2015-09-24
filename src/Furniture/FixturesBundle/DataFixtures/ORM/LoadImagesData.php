<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadImagesData as BaseLoadImagesData;


class LoadImagesData extends BaseLoadImagesData
{
    protected $product_path = '/../../Resources/fixtures/productImages';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->productImages($manager);
    }

    protected function productImages(ObjectManager $manager){
        $finder = new Finder();
        $uploader = $this->get('sylius.image_uploader');

        foreach ($finder->files()->in(__DIR__.$this->path) as $img) {
            $image = $this->getProductVariantImageRepository()->createNew();
            $image->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));
            $uploader->upload($image);

            $manager->persist($image);
            
            $this->setReference('Sylius.Image.Product.'.$img->getBasename('.jpg'), $image);
        }

        $manager->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
