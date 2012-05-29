<?php

namespace Snappminds\Utils\Bundle\BluesBundle\Model;

class Object
{
    /*
     * @return boolean
     */
    public function equals( $object )
    {
        /**
         * Se verifica que uno sea instancia de la clase del otro, o viceversa .
         * Esto lo hacemos así porque Doctrine puede instanciar una Entity o un ProxyEntity y deberiamos tratarla como iguales
         */
        $this_class = get_class($this);
        $object_class = get_class($object);
        return ($object instanceof $this_class) || ($this instanceof $object_class);
    }    
}

?>
