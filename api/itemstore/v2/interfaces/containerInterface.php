<?php

/**
 *  PSR-11
 * 
 * Describes the interface of a container that exposes methods to read its entries.
 * 
 */

interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     *
     * @return mixed Entry.
     */
	public static function get($id, $dependent_object=null);


    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
	public static function has($id): bool;
}