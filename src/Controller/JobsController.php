<?php

namespace App\Controller;

use App\Dto\PaginateLoaderResultDto;
use App\Service\IPageLoader;
use App\Service\RecruitisApi;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JobsController extends AbstractController
{
    #[Route('/jobs/{page<\d+>?1}', methods: ['GET'])]
    public function list(int $page, RecruitisApi $api, PaginatorInterface $paginator): Response
    {
        $loader = new class($api) implements IPageLoader {
            public function __construct(private RecruitisApi $api) {}
            public function load(int $page, int $itemsPerPage): PaginateLoaderResultDto
            {
                return $this->api->getJobs($page, $itemsPerPage);
            }
        };

        $pagination = $paginator->paginate($loader, $page);

        return $this->render('job/list.html.twig', ['pagination' => $pagination]);
    }
}
