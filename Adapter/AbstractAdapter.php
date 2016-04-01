<?php

namespace BlueSteel42\SettingsBundle\Adapter;

use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorBuilder;
use Symfony\Component\PropertyAccess\PropertyPath;


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
        return $this->getAccessor()->getValue($this->getValues(), $this->normalizePath($name));
    }

    /**
     * @inheritDoc
     */
    public function set($name, $value)
    {
        $name = $this->normalizePath($name);

        try {
            $this->delete($name);
        } catch (NoSuchIndexException $e) {

        }
        $this->getAccessor()->setValue($this->values, $name, $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function delete($name)
    {
        $path = new PropertyPath($name);

        $elements = $path->getElements();

        if (1 == count($elements)) {
            if (!array_key_exists($elements[0], $this->getValues())) {
                throw new NoSuchIndexException(sprintf('The key %s does not exist', $name));
            }
            unset ($this->values[$elements[0]]);
        } else {
            $last = array_pop($elements);
            $pathParent = sprintf('[%s]', implode('][', $elements));
            $parent = $this->getAccessor()->getValue($this->getValues(), $pathParent);
            if (!array_key_exists($last, $parent)) {
                throw new NoSuchIndexException(sprintf('The key %s does not exist', $name));
            }
            unset($parent[$last]);

            $this->getAccessor()->setValue($this->values, $pathParent, $parent);
        }

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
            $this->accessor = (new PropertyAccessorBuilder())->enableExceptionOnInvalidIndex()->getPropertyAccessor();
        }

        return $this->accessor;
    }

    /**
     * @return array
     */
    protected function getValues()
    {
        if ($this->values === null) {
            $this->values = (array)$this->doGetValues();
        }

        return $this->values;
    }

    /**
     * @param string $dirtyPath
     * @return string
     */
    protected function normalizePath($dirtyPath)
    {
        $path = new PropertyPath($dirtyPath);

        return sprintf('[%s]', implode('][', $path->getElements()));
    }

    /**
     * @return array
     */
    protected abstract function doGetValues();
}