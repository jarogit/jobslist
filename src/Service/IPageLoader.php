<?php

namespace App\Service;

use App\Dto\PaginateLoaderResultDto;

interface IPageLoader
{
    public function load(int $page, int $itemsPerPage): PaginateLoaderResultDto;
}
