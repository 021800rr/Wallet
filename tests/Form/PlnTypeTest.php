<?php

namespace App\Tests\Form;

use App\Entity\Pln;
use App\Form\PlnType;
use App\Tests\SetUp;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class PlnTypeTest extends KernelTestCase
{
    use SetUp;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        /** @var FormFactoryInterface $formFactory */
        $formFactory = static::getContainer()->get('form.factory');
        $this->formFactory = $formFactory;
    }

    /**
     * @return DoctrineOrmExtension[]
     */
    protected function getExtensions(): array
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManager);

        return [
            new DoctrineOrmExtension($registry),
        ];
    }

    public function testSubmitValidData(): void
    {
        $contractor = $this->contractorRepository->findOneBy(['description' => 'Netflix']);

        $formData = [
            'amount' => 100.00,
            'contractor' => $contractor?->getId(),
            'date' => '2024-08-20',
            'description' => 'Test Description',
        ];

        $model = new Pln();
        $form = $this->formFactory->create(PlnType::class, $model);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $expected = new Pln();
        $expected->setAmount(100.00);
        $expected->setContractor($contractor);
        $expected->setDate(new \DateTime('2024-08-20'));
        $expected->setDescription('Test Description');

        $this->assertEquals($expected, $model);
    }

    public function testSubmitInvalidData(): void
    {
        $formData = [
            'amount' => null,
            'contractor' => null,
            'date' => '2024-08-20',
        ];

        $form = $this->formFactory->create(PlnType::class);

        $form->submit($formData);

        $this->assertFalse($form->isValid());
        $this->assertCount(5, $form->getErrors(true));
    }
}
