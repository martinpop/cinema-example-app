<?php

namespace App\Model\Facades;

use App\Model\Repositories;


/**
 * Showtime facade.
 */
class ShowtimeFacade extends BaseFacade
{	
	/** @var Repositories\Showtime */
	private $showtime;
	
	
	/**
	 * @param Repositories\ShowtimeRepository $showtime
	 */
	public function __construct(Repositories\ShowtimeRepository $showtime)
	{
		$this->showtime = $showtime;
	}
	
	
	/**
	 * @return int
	 */
	public function getShowtimeCount()
	{
		return $this->showtime->findAll()->count();
	}
	
	
	/**
	 * @return array
	 */
	public function getShowtimeList()
	{
		return $this->showtime->findAll()
			->select("id, hour || ':' || substr('00' || minute, -2, 2) AS time")
			->order('id')
			->fetchPairs('id', 'time');
	}
	
}
