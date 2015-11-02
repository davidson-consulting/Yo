<?php

namespace Sab\ReunionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ReunionBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
