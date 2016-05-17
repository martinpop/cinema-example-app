<?php

namespace App\Model\Repositories;


/**
 * Program repository.
 */
class ProgramRepository extends BaseRepository
{	
	
	/**
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function findById($id)
	{
		return $this->findAll()->get($id);
	}
	
	
	/** 
	 * @return Nette\Database\Table\Selection
	 */
	public function findAll()
	{
		return $this->database->table('program');
	}
	
}
