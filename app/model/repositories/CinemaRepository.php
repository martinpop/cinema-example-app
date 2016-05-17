<?php

namespace App\Model\Repositories;


/**
 * Cinema repository.
 */
class CinemaRepository extends BaseRepository
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
		return $this->database->table('cinema');
	}
	
}
