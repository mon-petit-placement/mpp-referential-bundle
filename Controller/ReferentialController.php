<?php

namespace Mpp\ReferentialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReferentialController extends AbstractController
{
    public function getValues($referential, $_format)
    {
        dd($referential, $format);
    }
}