<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

class Repair extends AbstractOperation
{
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(): void
    {
        $leftId = 1;
        foreach ($this->getResults() as $result) {
            $this->nestedSetRepository->update(
                [
                    'left_id' => $leftId,
                    'right_id' => $leftId + 1,
                ],
                $result['id']
            );

            $leftId += 2;
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getResults(): array
    {
        if ($this->isBlockAware()) {
            return $this->nestedSetRepository->fetchAllSortedByBlock();
        }

        return $this->nestedSetRepository->fetchAll();
    }
}
