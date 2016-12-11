<?php
namespace FTumiwan\DomainCore;

interface iBm
{
	public function store($value);
	public function find($value);  //search single base on parent entity
	public function queryAnd($value); //search with query property, base on parent entity
	public function update($value);
	public function delete($value);
}

