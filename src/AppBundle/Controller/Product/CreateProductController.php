<?php
declare(strict_types = 1);

namespace AppBundle\Controller\Product;

use AppBundle\Service\Product\CreateProductService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateProductController.
 */
class CreateProductController extends AbstractFOSRestController
{
    /**
     * @var CreateProductService
     */
    private $createProductService;

    /**
     * CreateProductController constructor.
     *
     * @param CreateProductService $createProductService
     */
    public function __construct(
        CreateProductService $createProductService
    ) {
        $this->createProductService = $createProductService;
    }

    /**
     * @Route("/api/product/create", methods={"POST"})
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Create a single product"
     *  )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createProduct(Request $request): JsonResponse
    {
        try {
            $this->createProductService->execute(is_string($request->getContent()) ? $request->getContent() : '');

            return new JsonResponse(
                [
                    'created' => true,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
