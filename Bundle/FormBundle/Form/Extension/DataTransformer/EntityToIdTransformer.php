<?php


namespace Snappminds\Utils\Bundle\FormBundle\Form\Extension\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Util\PropertyPath;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;


class EntityToIdTransformer implements DataTransformerInterface
{

    private $em;
    private $class;
    
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }
    
    protected function getIdentifiers()
    {
        return $this->em->getClassMetadata($this->class)->getIdentifierFieldNames();
    }

    /**
     * Obtiene las claves de identificación de las entidades.
     * Se contemplan los casos de NULL y cadenas vacías y se devuelve un arreglo
     * vacío.
     * 
     * @param mixed $entity Entidad | Arreglo de entidades a transformar.
     * @return array Arreglo de entidades.
     */
    public function transform($entity)
    {
        if (null === $entity || '' === $entity) {
            return null;
        }
               
        if (is_array($entity)) {
            $entities = $entity;
        } else {
            $entities = array($entity);
        }
        
        $identifiers = $this->getIdentifiers();
        
        if (count($identifiers) > 1) {            
            $result = array();
            foreach ($entities as $entity) {
                $key = array(); //Clave compuesta
                
                //Obtener el valor de cada item de la clave compuesta.
                foreach ($identifiers as $identifier) {
                    $property = new PropertyPath($identifier);
                    $key[$identifier] = $property->getValue($entity);                   
                }
                
                //Agregar la clave al resultado.
                $result[] = $key; 
            }
            
        } else {
            $result = array();            
            foreach ($entities as $entity) {
                $property = new PropertyPath($identifiers[0]);
                $result[] = $property->getValue($entity);
            }
        }
        
        
        if (\count($result) == 0)
            return null;
        
        if (\count($result) == 1)            
            return $result[0];
        else
            return $result;
    }

    /**
     * Devuelve la/s entidad/es identificadas por las claves provistas.
     * 
     * Si la identidad de las entidades es una clave compuesta debe utilizarse
     * un arreglo asociativo para cada una. (El parámetro será entonces un
     * arreglo de arreglos).
     * 
     * 
     * @param array $keys Arreglo de claves.
     * @return array Arreglo de entidades.
     */
    public function reverseTransform($keys)
    {
        if ('' === $keys || null === $keys) {
            return null;
        }
        
        $entities = array();
        
        $identifiers = $this->getIdentifiers();
        
        if (count($identifiers) > 1) {
            //TODO: Chequear que exista el índice 0
            if (!is_array($keys[0])) {
                $keys = array($keys[0]);
            }
        } else {
            if (!is_array($keys)) {
                $keys = array($keys);
            }
        }
        
        /**
         * Recuperar cada una de las entidades.
         * @TODO: Podría optimizarse este código para hacer una sola consulta.
         */
        foreach ($keys as $key) {
            $entity = $this->getEntity($key);
            if (!$entity)
                throw new TransformationFailedException(\sprintf('La entidad con clave "%s" no ha sido encontrada', $key));
            $entities[] = $entity;            
        }
                
        if (count($entities) == 1)
            return $entities[0];
        else
            return $entities;
        
    }
    
    protected function getEntity($key)
    {
        $identifiers = $this->getIdentifiers();
        
        if (count($identifiers) > 1) {
            //La clave es compuesta.
            $qb = $this->em->getRepository($this->class)->createQueryBuilder('e');
            foreach ($key as $keyComponent) {
                $qb->andWhere($keyComponent . ' = ' . ':' . $keyComponent);
                $qb->setParameter(':' . $keyComponent, $key[$keyComponent]);
            }
            return $qb->getQuery()->getResult();            
        } else {
            return $this->em->getRepository($this->class)->findOneById($key);
        }
        
    }
}
