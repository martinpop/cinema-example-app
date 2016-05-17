<?php

namespace App\Presenters;

use Nette;
use App\Components;
use App\Model\Facades;


/**
 * Cinema timetable.
 */
class ProgramPresenter extends BasePresenter
{	
	/** @var Components\CinemaForm\ICinemaFormFactory @inject */
	public $cinemaFormFactory;
	
	/** @var Facades\CinemaFacade @inject */
	public $cinema;
	
	/** @var Facades\ProgramFacade @inject */
	public $program;
	
	/** @var Facades\ShowtimeFacade @inject */
	public $showtime;
	
	/** @var int @persistent */
	public $cinemaID = Facades\ProgramFacade::DEFAULT_CINEMA_ID;
	
	/** @var string @persistent */
	public $sortOrder = 'film';
	
	
	/********************* Helpers *********************/
	
	
	/**
	 * @param int $cinemaID
	 * @return array
	 */
	private function getCinemaData($cinemaID)
	{
		if (!($cinemaData = $this->cinema->getCinemaData($cinemaID)))
		{
			$this->error('Record not found.');
		}
		return $cinemaData;
	}
	
	
	/********************* Handlers *********************/
	
	
	/**
	 * @param int $cinemaID
	 */
	public function handleChangeProgram($cinemaID)
	{
		if ($this->isAjax())
		{		
			$this->cinemaID = $cinemaID;
			
			$this->template->cinemaID = $this->cinemaID;
			$this->template->cinemaData = $this->getCinemaData($this->cinemaID);
			$this->template->showtimeCount = $this->showtime->getShowtimeCount();
			$this->template->cinemaProgram = $this->program->getCinemaProgram($this->cinemaID);
			
			$this->redrawControl('address');
			$this->redrawControl('program');
		}
	}
	
	
	/**
	 * @param string $order
	 * @return string
	 */
	private function translateSortOrder($order)
	{
		switch ($order)
		{
			case 'film':
				return Facades\ProgramFacade::ORDER_MOVIE_NAME . ' ASC';
				
			case 'film-desc':
				return Facades\ProgramFacade::ORDER_MOVIE_NAME . ' DESC';
				
			default:
				return Facades\ProgramFacade::ORDER_MOVIE_NAME . ' ASC';
		}
	}
	
	
	/**
	 * @param string $order
	 */
	public function handleSort($order)
	{
		if ($this->isAjax())
		{		
			// Asceding or descending
			if ($order === $this->sortOrder)
			{
				$orderArr = explode('-', $order);
				$order = $orderArr[0] . (isset($orderArr[1]) ? '' : '-desc');
			}
			$this->sortOrder = $order;
			
			$this->template->cinemaData = $this->getCinemaData($this->cinemaID);
			$this->template->showtimeCount = $this->showtime->getShowtimeCount();
			$this->template->cinemaProgram = $this->program->getCinemaProgram($this->cinemaID, $this->translateSortOrder($order));
			
			$this->redrawControl('program');
		}
	}
	
	
	/********************* Components *********************/
	
	
	/**
	 * @return Components\CinemaForm\CinemaForm
	 */
	protected function createComponentCinemaForm()
	{
		return $this->cinemaFormFactory->create($this->cinemaID);
	}
	
	
	/********************* Rendering *********************/
	
	
	public function renderDefault()
	{	
		if (!isset($this->template->cinemaProgram))
		{
			$this->template->cinemaID = $this->cinemaID;
			
			$this->template->cinemaData = $this->getCinemaData($this->cinemaID);
			$this->template->showtimeCount = $this->showtime->getShowtimeCount();
			$this->template->cinemaProgram = $this->program->getCinemaProgram($this->cinemaID);
		}
	}

}
