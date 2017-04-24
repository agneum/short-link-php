<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DisableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Link
 *
 * @ORM\Table(name="link")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinkRepository")
 */
class Link
{
    use TimestampableEntity, DisableEntity;

    const MIN_CODE_LENGTH = 4;
    const MAX_CODE_LENGTH = 6;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=6, unique=true)
     * @Assert\Length(
     *   min = Link::MIN_CODE_LENGTH,
     *   minMessage = "Code cannot be shorter than {{ limit }} characters",
     *   max = Link::MAX_CODE_LENGTH,
     *   maxMessage = "Code cannot be longer than {{ limit }} characters"
     * )
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="originalLink", type="text")
     * @Assert\NotBlank(message="'originalLink' must not be blank")
     * @Assert\Url(
     *  message = "This value for the 'originalLink' is not a valid URL"
     * )
     */
    private $originalLink;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Link
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set originalLink
     *
     * @param string $originalLink
     * @return Link
     */
    public function setOriginalLink($originalLink)
    {
        $this->originalLink = $originalLink;

        return $this;
    }

    /**
     * Get originalLink
     *
     * @return string 
     */
    public function getOriginalLink()
    {
        return $this->originalLink;
    }
}
