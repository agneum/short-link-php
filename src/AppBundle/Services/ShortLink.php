<?php

namespace AppBundle\Services;

use AppBundle\Entity\Link;


/**
 * Class ShortLink
 * @package AppBundle\Services
 */
class ShortLink
{
    public function generateCode()
    {
        return substr(base64_encode(md5(uniqid())), 0, rand(Link::MIN_CODE_LENGTH, Link::MAX_CODE_LENGTH));
    }
}
