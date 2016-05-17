<?php

namespace App\Presenters;

use Nette\Application\UI;
use Nette\Database;
use Nette\Forms\Controls;
use Nette\Utils;
use App\Components;
use App\Model\Facades;


/**
 * Admin.
 */
class AdminPresenter extends BasePresenter
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
	
	
	/**
	 * @param int $programID
	 * @return array
	 */
	private function getProgramShowtime($programID)
	{
		if (!($program = $this->program->getProgramShowtime($programID)))
		{
			$this->error('Record not found.');
		}
		return $program;
	}
	
	
	/**
	 * @param int $programID
	 * @return Database\Table\ActiveRow
	 */
	private function getProgram($programID)
	{
		if (!($program = $this->program->getProgramByID($programID)))
		{
			$this->error('Record not found');
		}
		return $program;
	}
	
	
	/********************* Components *********************/
	
	
	/**
	 * @return Components\CinemaForm\CinemaForm
	 */
	protected function createComponentCinemaForm()
	{
		return $this->cinemaFormFactory->create($this->cinemaID);
	}
	
	
	/**
	 * @return UI\Form
	 */
	protected function createComponentCinemaProgramForm()
	{
		$form = new UI\Form();
		$form->getElementPrototype()->id = 'cinema-program';
		
		$form->addSelect('movie', Utils\Html::el()->setHtml('<span class="input-required">Film</span>'))
			->setRequired('Film musí být vybrán.')
			->setAttribute('class', 'form-control');
		
		$form->addCheckboxList('showtime', Utils\Html::el()->setHtml('<span class="input-required">Promítací čas</span>'))
			->setAttribute('class', 'form-control')
			->setRequired('Alespoň jeden promítací čas musí být vybrán.')
			->getSeparatorPrototype()
				->setName('div')->addClass('col-sm-2');
		
		$saveButton = $form->addSubmit('save', 'Uložit')
			->setAttribute('class', 'btn btn-default');
		$saveButton->onClick[] = $this->cinemaProgramFormSucceeded;
		
		$cancelButton = $form->addSubmit('cancel', 'Zrušit')
			->setAttribute('class', 'btn btn-default')
			->setValidationScope(FALSE);
		$cancelButton->onClick[] = $this->formCanceled;
		
		return $form;
	}
	
	
	/**
	 * @param Controls\SubmitButton $button
	 */
	public function cinemaProgramFormSucceeded(Controls\SubmitButton $button)
	{
		$values = $button->getForm()->getValues();		
		$programID = (int) $this->getParameter('programID');
		
		if ($programID)
		{
			$this->program->changeProgramShowtime($programID, $values->showtime)
				? $this->flashMessage('Editace programu proběhla v pořádku.', 'success')
				: $this->flashMessage('Editace programu se nepodařila.', 'danger');			
		}
		else
		{
			if ($this->program->createProgramShowtime($this->cinemaID, $values->movie, $values->showtime))
			{
				$this->flashMessage('Přidání filmu proběhlo v pořádku.', 'success');
				$this->redirect('default');				
			}
			else
			{
				$this->flashMessage('Přidání filmu se nepodařilo.', 'danger');
			}			
		}
	}
	
	
	/**
	 * @return UI\Form
	 */
	protected function createComponentProgramDeleteForm()
	{
		$form = new UI\Form();
		$form->getElementPrototype()->id = 'cinema-program-delete';
		
		$saveButton = $form->addSubmit('delete', 'Odebrat')
			->setAttribute('class', 'btn btn-default');
		$saveButton->onClick[] = $this->programDeleteFormSucceeded;
		
		$cancelButton = $form->addSubmit('cancel', 'Zrušit')
			->setAttribute('class', 'btn btn-default')
			->setValidationScope(FALSE);
		$cancelButton->onClick[] = $this->formCanceled;
		
		return $form;
	}
	
	
	public function programDeleteFormSucceeded()
	{
		if ($this->program->removeProgramShowtime($this->getParameter('programID')))
		{
			$this->flashMessage('Odebrání programu proběhlo v pořádku.', 'success');
			$this->redirect('default');			
		}
		else
		{
			$this->flashMessage('Odebrání programu se nepodařilo.', 'danger');
		}
	}
	
	
	public function formCanceled()
	{
		$this->redirect('default');
	}
	
	
	/********************* View default *********************/
	
	
	public function renderDefault()
	{		
		$this->template->cinemaID = $this->cinemaID;
		
		$this->template->cinemaData = $this->getCinemaData($this->cinemaID);
		$this->template->showtimeCount = $this->showtime->getShowtimeCount();
		$this->template->cinemaProgram = $this->program->getCinemaProgram($this->cinemaID);
		$this->template->redirectLink = $this->getHttpRequest()->getUrl()->getBaseUrl() . 'admin/?cinemaID=';
	}
	
	
	/********************* View add *********************/
	
	
	public function actionAdd()
	{
		$form = $this['cinemaProgramForm'];		
		$form['movie']->setItems($this->cinema->getUnassignedMovieList($this->cinemaID));
		$form['showtime']->setItems($this->showtime->getShowtimeList());
	}
	
	
	public function renderAdd()
	{
		$this->template->cinemaData = $this->getCinemaData($this->cinemaID);
		$this->template->hasMovies = !empty($this['cinemaProgramForm']['movie']->items);
	}
	
	
	/********************* View edit *********************/
	
	
	/**
	 * @param int $programID
	 */
	public function actionEdit($programID = 0)
	{
		$programShowtime = $this->getProgramShowtime($programID);
		
		$form = $this['cinemaProgramForm'];		
		$form['movie']->setItems([$programShowtime['program']->movie->id => $programShowtime['program']->movie->name]);
		$form['showtime']->setItems($this->showtime->getShowtimeList());
		
		if (!$form->isSubmitted())
		{
			$form['showtime']->setDefaultValue($programShowtime['showtimeIDList']);
		}
	}
	
	
	/**
	 * @param int $programID
	 */
	public function renderEdit($programID = 0)
	{
		$this->template->cinemaData = $this->getCinemaData($this->getProgram($programID)->cinema_id);
	}
	
	
	/********************* View delete *********************/
	
		
	/**
	 * @param int $programID
	 */
	public function renderDelete($programID = 0)
	{
		$this->template->program = $this->getProgram($programID);
		$this->template->cinemaData = $this->getCinemaData($this->template->program->cinema_id);
	}

}
