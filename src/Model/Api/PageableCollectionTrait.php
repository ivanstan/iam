<?php

namespace App\Model\Api;

trait PageableCollectionTrait
{
    protected int $page = 1;
    protected int $pageSize = 10;
    protected int $total = 0;

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getOffset(): int
    {
        $offset = 0;
        if ($this->page > 1) {
            $offset = ($this->page - 1) * $this->pageSize;
        }

        return $offset;
    }

    public function getPageCount(): int
    {
        return max(1, ceil($this->total / $this->pageSize));
    }

    public function getNextPage(): int {
        $nextPage = $this->getPage();
        if ($this->page < $this->getPageCount()) {
            $nextPage = $this->page + 1;
        }

        return $nextPage;
    }

    public function getPreviousPage(): int {
        $previousPage = $this->getPage();
        if ($this->page > 1) {
            $previousPage = $this->page - 1;
        }

        return $previousPage;
    }
}
