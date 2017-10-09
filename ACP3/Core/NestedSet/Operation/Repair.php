<?php
namespace ACP3\Core\NestedSet\Operation;


class Repair extends AbstractOperation
{
    public function execute()
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

    private function getResults(): array
    {
        if ($this->isBlockAware()) {
            return $this->nestedSetRepository->fetchAllSortedByBlock();
        }

        return $this->nestedSetRepository->fetchAll();
    }
}
