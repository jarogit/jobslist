<?php

namespace App\Service;

use App\Dto\PaginateLoaderResultDto;

interface IPageLoader
{
    const ERROR_PARAM = 'dataLoadingError';

    public function load(int $page, int $itemsPerPage): PaginateLoaderResultDto;
}
