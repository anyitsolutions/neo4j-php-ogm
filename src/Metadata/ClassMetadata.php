<?php

namespace GraphAware\Neo4j\OGM\Metadata;

class ClassMetadata
{
    protected $className;

    protected $type;

    protected $fields = [];

    protected $associations = [];

    protected $label;

    public function __construct($type, $label, array $fields, array $associations)
    {
        $this->type = $type;
        $this->label = $label;
        $this->fields = $fields;
        $this->associations = $associations;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getAssociations()
    {
        return $this->associations;
    }

    public function addField(array $field)
    {
        $this->fields[$field[0]] = $field;
    }

    public function addAssociation(array $association)
    {
        $this->associations[$association[0]] = $association;
    }

    public function getIdentityValue($entity)
    {
        $reflO = new \ReflectionObject($entity);
        $property = $reflO->getProperty('id');
        $property->setAccessible(true);

        return $property->getValue($reflO);
    }

    public function getAssociatedObjects($entity)
    {
        $relatedObjects = [];
        $reflClass = new \ReflectionClass(get_class($entity));
        foreach ($this->associations as $k => $assoc) {
            $property = $reflClass->getProperty($k);
            $property->setAccessible(true);
            $value = $property->getValue($entity);
            if (null !== $value) {
                $relatedObjects[] = [$assoc, $value];
            }
        }

        return $relatedObjects;
    }
}