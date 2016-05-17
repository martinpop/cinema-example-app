<?php

namespace App\Model\Facades;

use Nette\Database;
use App\Model\Facades;
use App\Model\Repositories;


/**
 * Program facade.
 */
class ProgramFacade extends BaseFacade
{	
	const DEFAULT_CINEMA_ID = 3,
		ORDER_MOVIE_NAME = 'movie.name';	
	
	/** @var Repositories\ProgramRepository */
	private $program;
	
	/** @var Facades\ShowtimeFacade */
	private $showtime;
	
	/** @var Repositories\ProgramShowtimeRepository */
	private $programShowtime;
	
	/** @var Database\Connection */
	private $database;
	
	
	/**
	 * @param Repositories\ProgramRepository $program
	 * @param Facades\ShowtimeFacade $showtime
	 * @param Repositories\ProgramShowtimeRepository $programShowtime
	 * @param Database\Connection $database
	 */
	public function __construct(Repositories\ProgramRepository $program, Facades\ShowtimeFacade $showtime,
		Repositories\ProgramShowtimeRepository $programShowtime, Database\Connection $database)
	{
		$this->showtime = $showtime;
		$this->program = $program;
		$this->programShowtime = $programShowtime;	
		$this->database = $database;
	}
	
	
	/**
	 * @param int $id
	 * @return Database\Table\ActiveRow
	 */
	public function getProgramByID($id)
	{
		return $this->program->findById($id);
	}
	
	
	/**
	 * @param int $programID
	 * @return array
	 */
	public function getProgramShowtime($programID)
	{
		if (!($program = $this->getProgramByID($programID)))
		{
			return $program;
		}
		
		return [
			'program' => $program,
			'showtimeIDList' => $this->getProgramShowtimeIDList($programID)
		];
	}
	
	
	/**
	 * @return array
	 */
	public function getProgramShowtimeIDList($programID)
	{
		return $this->programShowtime->findAll()
			->select('showtime_id')
			->where('program.id', $programID)
			->fetchPairs(NULL, 'showtime_id');
	}
	
	
	/**
	 * @param int $cinemaID
	 * @param string $order
	 * @return array
	 */
	public function getCinemaProgram($cinemaID, $order = NULL)
	{
		$result = [];
		$showtimeList = $this->showtime->getShowtimeList();		
		$order = $order ? $order : self::ORDER_MOVIE_NAME . ' ASC';
		
		foreach ($this->program->findAll()->where('cinema.id', $cinemaID)->order($order) as $program)
		{			
			$programShowtimeIDList = $this->getProgramShowtimeIDList($program->id);			
			$programShowtime = [];
			
			foreach ($showtimeList as $id => $time)
			{
				$programShowtime[] = in_array($id, $programShowtimeIDList) ? $time : NULL;
			}
			
			$result[] = [
				'program' => $program,
				'showtime' => $programShowtime
			];
		}
		
		return $result;
	}
	
	
	/**
	 * @param int $cinemaID
	 * @param int $movieID
	 * @param array $showtimeValues
	 * @return int program.id
	 */
	public function createProgramShowtime($cinemaID, $movieID, array $showtimeValues)
	{
		$this->database->beginTransaction();
		
		// Create new program
		$program = $this->program->findAll()->insert(['cinema_id' => $cinemaID, 'movie_id' => $movieID]);
		
		// Create new program_showtime
		$programShowtimeData = [];
		foreach ($showtimeValues as $showtimeValue)
		{
			$programShowtimeData[] = ['program_id' => $program->id, 'showtime_id' => $showtimeValue];
		}		
		$this->programShowtime->findAll()->insert($programShowtimeData);
		
		$this->database->commit();
		return $program->id;
	}
	
	
	/**
	 * @param int $programID
	 * @param array $showtimeValues
	 * @return bool
	 */
	public function changeProgramShowtime($programID, array $showtimeValues)
	{
		$this->database->beginTransaction();
		
		// Verify program
		if (!($program = $this->getProgramByID($programID)))
		{
			$this->database->rollBack();
			return FALSE;
		}
		
		// Delete old program_showtime
		$this->programShowtime->findAll()->where('program_id', $program->id)->delete();
		
		// Create new program_showtime
		$programShowtimeData = [];
		foreach ($showtimeValues as $showtimeValue)
		{
			$programShowtimeData[] = ['program_id' => $program->id, 'showtime_id' => $showtimeValue];
		}		
		$this->programShowtime->findAll()->insert($programShowtimeData);
		
		$this->database->commit();
		return TRUE;
	}
	
	
	/**
	 * @param int $programID
	 * @return bool
	 */
	public function removeProgramShowtime($programID)
	{
		$this->database->beginTransaction();
		
		// Verify program
		if (!($program = $this->getProgramByID($programID)))
		{
			$this->database->rollBack();
			return FALSE;
		}
		
		// Delete program_showtime
		$this->programShowtime->findAll()->where('program_id', $program->id)->delete();
		
		// Delete program
		$program->delete();
		
		$this->database->commit();
		return TRUE;
	}
	
}
