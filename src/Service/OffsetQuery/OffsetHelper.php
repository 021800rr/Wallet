<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\Request;

readonly class OffsetHelper extends AbstractSession implements OffsetHelperInterface
{
    public function setOffset(Request $request): void
    {
        if (null !== $request->query->get('offset')) {
            $intOffset = $request->query->getInt('offset', 0);

            $this->requestStack->getSession()->set('offset', $intOffset);
        }
    }

    public function getOffset(Request $request): int
    {
        if (null !== $request->query->get('offset')) {
            return $request->query->getInt('offset', 0);
        }

        return $this->requestStack->getSession()->get('offset', 0);
    }

    public function resetOffset(): void
    {
        $this->requestStack->getSession()->set('offset', 0);
    }
}
