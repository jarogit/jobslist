<?php

namespace App\Dto;

class PaginateLoaderResultDto
{
    public bool $isOk = true;
    public int $total = 0;
    public array $items = [];
}
