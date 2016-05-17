<?php

namespace App\Model\Facades;

use App\Model\Repositories;


/**
 * Cinema facade.
 */
class CinemaFacade extends BaseFacade
{	
	/** @var Repositories\CinemaRepository */
	private $cinema;
	
	/** @var Repositories\MovieRepository */
	private $movie;
	
	/** @var Repositories\ProgramRepository */
	private $program;
	
	
	/**
	 * @param Repositories\CinemaRepository $cinema
	 * @param Repositories\MovieRepository $movie
	 * @param Repositories\ProgramRepository $program
	 */
	public function __construct(Repositories\CinemaRepository $cinema, Repositories\MovieRepository $movie,
		Repositories\ProgramRepository $program)
	{
		$this->cinema = $cinema;
		$this->movie = $movie;
		$this->program = $program;
	}
	
	
	/**
	 * @param int $cinemaID
	 * @return array
	 */
	public function getCinemaData($cinemaID)
	{
		if (!($cinema = $this->cinema->findById($cinemaID)))
		{
			return $cinema;
		}		
		
		return [
			'cinema' => $cinema->name,
			'street' => $cinema->address->street . ' ' . $cinema->address->street_number . ($cinema->address->house_number ? '/' . $cinema->address->house_number : ''),
			'city' => $cinema->address->city,
			'postal_code' => substr($cinema->address->postal_code, 0, 3) . ' ' . substr($cinema->address->postal_code, -2)
		];
	}
	
	
	/**
	 * @return array
	 */
	public function getCinemaList()
	{
		return $this->cinema->findAll()
			->select('id, name')
			->order('name')
			->fetchPairs('id', 'name');
	}
	
	
	/**
	 * @param int $cinemaID
	 * @return array
	 */
	public function getUnassignedMovieList($cinemaID)
	{
		return $this->movie->findAll()
			->select('id, name')
			->where('id NOT IN', $this->program->findAll()->select('movie_id')->where('cinema_id', $cinemaID))
			->fetchPairs('id', 'name');
	}
	
}
