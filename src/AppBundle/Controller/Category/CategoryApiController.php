<?php
declare(strict_types = 1);

namespace AppBundle\Controller\Category;

use AppBundle\Service\Category\CategoryService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryApiController extends FOSRestController
{
    private $categoryService;

    /**
     * CategoryApiController constructor.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(
        CategoryService $categoryService
    ) {
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/api/category/list")
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of categories."
     *  )
     */
    public function getAllCategories(): JsonResponse
    {
        try {
            $categories = $this->categoryService->listCategories();

            return new JsonResponse(
                [
                    'categories' => $categories
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}