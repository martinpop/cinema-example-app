<?php

namespace App\Components\CinemaForm;


interface ICinemaFormFactory
{
	
	/**
	 * @param int $defaultCinemaID
	 * @return CinemaForm
	 */
	function create($defaultCinemaID);
	
}