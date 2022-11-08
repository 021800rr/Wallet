<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\Request;

class Offset extends AbstractSession implements OffsetInterface
{
    public function set(Request $request)
    {
        if (null !== $request->query->get('offset')) {
            $intOffset = $request->query->getInt('offset', 0);

            $this->requestStack->getSession()->set('offset', $intOffset);
        }
    }

    public function get(Request $request): int
    {
        if (null !== $request->query->get('offset')) {
            return $request->query->getInt('offset', 0);
        }

        return $this->requestStack->getSession()->get('offset', 0);
    }

    public function reset()
    {
        $this->requestStack->getSession()->set('offset', 0);
    }
}
