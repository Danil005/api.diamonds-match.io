<?php

namespace App\Utils;

use JetBrains\PhpStorm\Pure;

trait Response
{
    /**
     * @return Response\Response
     */
    #[Pure] public function response(): Response\Response
    {
        return new \App\Utils\Response\Response();
    }
}
