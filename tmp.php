<?php


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/app/bootstrap.php.cache';
require_once __DIR__.'/app/AppKernel.php';

$kernel = new AppKernel('dev', true);

$kernel->boot();
$container = $kernel->getContainer();
$em = $container->get('doctrine.orm.default_entity_manager');

/** @var \Doctrine\Common\DataFixtures\AbstractFixture[] $fixtures */
$fixtures = [
    new \Furniture\FixturesBundle\DataFixtures\ORM\LoadProductReadinessData(),
    new \Furniture\FixturesBundle\DataFixtures\ORM\LoadProductSpaceData(),
    new \Furniture\FixturesBundle\DataFixtures\ORM\LoadProductCategoryData(),
    new \Furniture\FixturesBundle\DataFixtures\ORM\LoadProductTypeData(),
    new \Furniture\FixturesBundle\DataFixtures\ORM\LoadProductStyleData()
];

foreach ($fixtures as $fixture) {
    print "Load: " . get_class($fixture);

    $fixture->setReferenceRepository(new \Doctrine\Common\DataFixtures\ReferenceRepository($em));

    if ($fixture instanceof \Symfony\Component\DependencyInjection\ContainerAwareInterface) {
        $fixture->setContainer($container);
    }

    $fixture->load($em);

    print " - Success\n";
}