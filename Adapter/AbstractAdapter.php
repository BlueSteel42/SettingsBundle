<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;


abstract class AbstractAdapter implements AdapterInterface
{

    /**
     * @var array
     */
    protected $values;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @inheritDoc
     */
    public function get($name)
    {
        return $this->getAccessor()->getValue($this->getValues(), $name);
    }

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        $this->getValues();
        $this->getAccessor()->setValue($this->values, $name, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->getValues();
    }

    /**
     * @inheritDoc
     */
    public function setAll(array $values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return PropertyAccessor
     */
    protected final function getAccessor()
    {
        if ($this->accessor === null) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->accessor;
    }

    protected function getValues()
    {
        if ($this->values === null) {
            $this->values = $this->doGetValues();
        }

        return $this->values;
    }

    protected abstract function doGetValues();
}