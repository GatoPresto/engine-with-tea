<?php
namespace Core;

class Model extends \ActiveRecord\Model {
    /**
     * Determines if an attribute exists for this {@link Model}.
     *
     * @param string $attribute_name
     * @return boolean
     */
    public function __isset($attribute_name) {
        if(parent::__isset($attribute_name))
            return true;

        // check for getters
        if (method_exists($this, "get_${attribute_name}"))
            return true;

        // check for relationships
        if (static::table()->has_relationship($attribute_name))
            return true;

        return false;
    }
}
