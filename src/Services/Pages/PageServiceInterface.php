<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:41
 */

namespace Jinya\Services\Pages;

use Jinya\Entity\Page\Page;

interface PageServiceInterface
{
    /**
     * Gets the specified @see Page by slug
     *
     * @param string $slug
     * @return Page
     */
    public function get(string $slug): Page;

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Page[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given @see Page
     *
     * @param \Jinya\Entity\Page\Page $page
     * @return Page
     */
    public function saveOrUpdate(Page $page): Page;

    /**
     * Deletes the given @see Page
     *
     * @param Page $entity
     */
    public function delete(Page $entity): void;
}
