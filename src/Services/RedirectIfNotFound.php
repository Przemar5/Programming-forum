<?php

namespace App\Services;

trait RedirectIfNotFound
{
    private function redirectIfNotFound($entity, string $paramName, string $param)
    {
        $category = $this->getDoctrine()
                        ->getRepository($entity)
                        ->findBy([
                            $paramName => $param,
                        ]);

        if (!$category) {
            throw $this->createNotFoundException();
        }
    }
}