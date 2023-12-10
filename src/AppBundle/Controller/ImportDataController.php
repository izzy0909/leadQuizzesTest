<?php
declare(strict_types = 1);

namespace AppBundle\Controller;

use AppBundle\Service\ImportDataService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportDataController.
 */
class ImportDataController extends AbstractFOSRestController
{
    /**
     * @var ImportDataService
     */
    private $importDataService;

    /**
     * ImportDataController constructor.
     *
     * @param ImportDataService $importDataService
     */
    public function __construct(
        ImportDataService $importDataService
    ) {
        $this->importDataService = $importDataService;
    }

    /**
     * @Route("/api/import", methods={"POST"})
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Import data"
     *  )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function importData(Request $request): JsonResponse
    {
        try {
            $this->importDataService->execute($request->getContent());

            return new JsonResponse(
                [
                    'imported' => true,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
