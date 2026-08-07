<?php

namespace Formulair\Core;

class Paginator
{
    public const DEFAULT_PER_PAGE = 50;

    public int $currentPage;
    public int $perPage;
    public int $totalItems;

    public function __construct(int $currentPage, int $perPage, int $totalItems)
    {
        $this->perPage = max(1, $perPage);
        $this->totalItems = max(0, $totalItems);
        $this->currentPage = max(1, min($currentPage, $this->totalPages()));
    }

    public static function fromQuery($pageParam, int $totalItems, int $perPage = self::DEFAULT_PER_PAGE): self
    {
        return new self((int) ($pageParam ?? 1), $perPage, $totalItems);
    }

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->totalItems / $this->perPage));
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public static function renderNav(self $paginator, string $baseUrl): string
    {
        if ($paginator->totalPages() <= 1) {
            return '';
        }

        $prevDisabled = $paginator->currentPage <= 1;
        $nextDisabled = $paginator->currentPage >= $paginator->totalPages();

        $prevLink = $prevDisabled
            ? '<span class="px-4 py-2 text-gray-300 cursor-not-allowed text-sm">← Précédent</span>'
            : '<a href="' . htmlspecialchars($baseUrl) . '?page=' . ($paginator->currentPage - 1) . '" class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-sm">← Précédent</a>';

        $nextLink = $nextDisabled
            ? '<span class="px-4 py-2 text-gray-300 cursor-not-allowed text-sm">Suivant →</span>'
            : '<a href="' . htmlspecialchars($baseUrl) . '?page=' . ($paginator->currentPage + 1) . '" class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition text-sm">Suivant →</a>';

        return '
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200">
                ' . $prevLink . '
                <span class="text-sm text-gray-600">Page ' . $paginator->currentPage . ' sur ' . $paginator->totalPages() . '</span>
                ' . $nextLink . '
            </div>
        ';
    }
}
