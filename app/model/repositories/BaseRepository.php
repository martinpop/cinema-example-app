<?php

namespace App\Model\Repositories;

use Nette;


/**
 * Base repository.
 */
abstract class BaseRepository extends Nette\Object
{
	/** @var Nette\Database\Context */
	protected $database;
	
	
	/**
	 * @param Nette\Database\Context $database
	 */
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}
	
}
