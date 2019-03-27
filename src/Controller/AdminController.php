<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    public function tracking()
    {
        return $this->render(
            'partial/analytics.html.twig'
        );
    }
}
