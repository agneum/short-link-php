<?php
namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DisableEntity
 * @package AppBundle\Entity\Traits
 */
trait DisableEntity
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="disabled", type="boolean", nullable=false, options={"default"=false})
     */
    protected $disabled = false;

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }
}
