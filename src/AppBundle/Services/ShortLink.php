<?php

namespace AppBundle\Services;

use AppBundle\Entity\Link;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;


/**
 * Class ShortLink
 * @package AppBundle\Services
 */
class ShortLink
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ShortLink constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function generateCode()
    {
        return substr(base64_encode(md5(uniqid())), 0, rand(Link::MIN_CODE_LENGTH, Link::MAX_CODE_LENGTH));
    }

    /**
     * @param Link $link
     * @return Link|bool
     */
    public function completeLink(Link $link)
    {
        for ($i = 0; $i < Link::MAX_ATTEMPTS; $i++) {
            try {
                if (!($generatedCode = $this->generateCode()) ||
                    $this->em->getRepository('AppBundle:Link')->findOneBy(['code' => $generatedCode])
                ) {
                    continue;
                }

                $link->setCode($generatedCode);
                $this->em->persist($link);
                $this->em->flush();

                return $link;

            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
        }

        return false;
    }
}

