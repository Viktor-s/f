<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sylius\Component\Rbac\Model\Permission;
use Sylius\Component\Rbac\Model\Role;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160330112956 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $permissionsArray;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->permissionsArray = [
            'furniture.manage.factories_retialers_relations' => 'manage factories retailers relations',
            'furniture.factories_retialers_relations.index'  => 'relations list',
            'furniture.factories_retialers_relations.update' => 'update current relation',
            'furniture.factories_retialers_relations.delete' => 'delete relation',
        ];
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );
    }

    public function postUp(Schema $schema)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $permissionRepo = $em->getRepository(Permission::class);
        $rootPermission = $permissionRepo->findOneBy(['code' => 'root']);

        $managePermission = new Permission();
        $managePermission->setCode('furniture.manage.factories_retialers_relations');
        $managePermission->setDescription('manage factories retailers relations');
        $managePermission->setParent($rootPermission);
        $em->persist($managePermission);

        $childPermission1 = new Permission();
        $childPermission1->setCode('furniture.factories_retialers_relations.index');
        $childPermission1->setDescription('relations list');
        $childPermission1->setParent($managePermission);
        $em->persist($childPermission1);

        $childPermission2 = new Permission();
        $childPermission2->setCode('furniture.factories_retialers_relations.update');
        $childPermission2->setDescription('update current relation');
        $childPermission2->setParent($managePermission);
        $em->persist($childPermission2);

        $childPermission3 = new Permission();
        $childPermission3->setCode('furniture.factories_retialers_relations.delete');
        $childPermission3->setDescription('delete relation');
        $childPermission3->setParent($managePermission);
        $em->persist($childPermission3);

        $roleRepository = $em->getRepository(Role::class);
        $administrationRole = $roleRepository->findOneBy(['code' => 'administrator']);
        if($administrationRole){
            $administrationRole->addPermission($managePermission);
            $em->persist($administrationRole);
        }
        $em->flush();

    }

    public function postDown(Schema $schema)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $permissionRepo = $em->getRepository(Permission::class);
        $roleRepository = $em->getRepository(Role::class);
        $administrationRole = $roleRepository->findOneBy(['code' => 'administrator']);
        $managePermission = $permissionRepo->findOneBy(['code' => 'furniture.manage.factories_retialers_relations']);
        $administrationRole->removePermission($managePermission);
        $em->persist($administrationRole);

        $permissions = $permissionRepo->findBy(['code' => array_keys($this->permissionsArray)]);

        foreach ($permissions as $permission) {
            $em->remove($permission);
        }

        $em->flush();
    }
}
