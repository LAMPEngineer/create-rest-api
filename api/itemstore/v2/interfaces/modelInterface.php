<?php

interface ModelInterface
{

	public function getAll(): array;

	public function getDetailsById(): array;

	public function getResultSetRowCountById():int;

	public function getResultSetById():object;

	public function insert():bool;

	public function update($request_verb):bool;

	public function delete():bool;

	public function getTableFields(): array;

	
}