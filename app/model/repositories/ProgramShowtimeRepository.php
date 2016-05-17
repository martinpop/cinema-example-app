<?php

namespace App\Model\Repositories;


/**
 * Program showtim repository.
 */
class ProgramShowtimeRepository extends BaseRepository
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
		return $this->database->table('program_showtime');
	}
	
}
