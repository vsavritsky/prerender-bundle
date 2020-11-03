<?php

namespace Vsavritsky\PrerenderBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Table(name="app_prerender_cache", indexes={@ORM\Index(name="path", columns={"path"})})
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class CachePage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $path;
    
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $httpCode = null;
    
    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $body;
    
    /**
     * Дата создания
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $createdAt;
    
    /**
     * Дата изменения
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $updatedAt;
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
    
    /**
     * @param string $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }
    
    /**
     * @return string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }
    
    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
    
    /**
     * @return string
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
    
    /**
     * @param string $httpCode
     */
    public function setHttpCode($httpCode): void
    {
        $this->httpCode = $httpCode;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function setUpdatedAt(\DateTime $dateTime)
    {
        $this->updatedAt = $dateTime;
    }
    
    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->updatedAt = new \DateTime();
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
    }
    
    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        if (!$this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }
    
    public function formatCreatedAt()
    {
        return $this->getCreatedAt() ? $this->getCreatedAt()->format('d.m.Y H:i:s') : '';
    }
    
    public function formatUpdatedAt()
    {
        return $this->getUpdatedAt() ? $this->getUpdatedAt()->format('d.m.Y H:i:s') : '';
    }
}
