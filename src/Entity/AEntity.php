<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

use Trejjam\MailChimp\Exception\ReadOnlyEntityException;

abstract class AEntity
{
    /**
     * @var bool[]
     */
    protected $readOnly = [];

    protected $associations = [];

    protected $initData = [];

    /**
     * @var AEntity[]|Entries[]|string[]|int[]
     */
    protected $data = [];

    public function __construct(array $data)
    {
        foreach ($this->associations as $key => $class) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            if (is_array($class)) {
                $class = $class[0];

                $data[$key] = new Entries($data[$key], $class);
            }
            else {
                $data[$key] = new $class($data[$key]);
            }
        }

        $this->initData = $data;
        $this->data = $data;

        $this->init();
    }

    public function init() : void
    {

    }

    public function __set(string $key, $value)
    {
        if (array_key_exists($key, $this->readOnly)) {
            throw new ReadOnlyEntityException($key);
        }

        $this->data[$key] = $value;
    }

    public function __get(string $key)
    {
        if (!array_key_exists($key, $this->data)) {
            return null;
        }

        return $this->data[$key];
    }

    public function toArray() : array
    {
        $out = $this->data;

        foreach ($out as $key => $entityData) {
            if ($entityData instanceof AEntity) {
                $out[$key] = $entityData->toArray();
            }
            else if ($entityData instanceof Entries) {
                $out[$key] = $entityData->toArray();
            }
        }

        return $out;
    }

    public function getUpdated() : array
    {
        $out = [];

        foreach ($this->data as $key => $entityData) {
            if (
                //or handle nested?
                $entityData instanceof AEntity
                || $entityData instanceof Entries
            ) {
                continue;
            }

            if ($entityData !== $this->initData[$key]) {
                $out[$key] = $entityData;
            }
        }

        return $out;
    }
}
