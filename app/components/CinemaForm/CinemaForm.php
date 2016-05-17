<?php

namespace App\Components\CinemaForm;

use Nette\Application\UI;
use App\Model\Facades;


/**
 * Cinema form.
 */
class CinemaForm extends UI\Control
{
	/** @var int */
	private $defaultCinemaID;
	
	/** @var Facades\CinemaFacade */
	private $cinema;


	/**
	 * @param int $defaultCinemaID
	 * @param Facades\CinemaFacade $cinema
	 */
	public function __construct($defaultCinemaID, Facades\CinemaFacade $cinema)
	{
		parent::__construct();
		$this->defaultCinemaID = $defaultCinemaID;
		$this->cinema = $cinema;
	}
	
	
	/**
	 * @return UI\Form
	 */
	protected function createComponentForm()
	{
		$form = new UI\Form;
		
		$form->addSelect('cinema', '', $this->cinema->getCinemaList())
			->setDefaultValue($this->defaultCinemaID)
			->setAttribute('id', 'cinema')
			->setAttribute('class', 'form-control');
		
		return $form;
	}
	
	
	/********************* Rendering *********************/
	
	
	public function render()
	{
        $this->template->setFile(__DIR__ . '/CinemaForm.latte');
        $this->template->render();
    }

}
