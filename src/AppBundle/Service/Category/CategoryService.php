<?php
declare(strict_types = 1);

namespace AppBundle\Service\Category;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private $categoryRepository;
    private $validator;

    public function __construct(
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->validator = $validator;
    }

    /**
     * @return Category[]
     *
     * @throws \Exception
     */
    public function listCategories(): array
    {
        return $this->categoryRepository->findAllCategories()->getArrayResult();
    }
}
