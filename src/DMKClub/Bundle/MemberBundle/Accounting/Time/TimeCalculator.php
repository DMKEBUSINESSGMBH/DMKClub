<?php

namespace DMKClub\Bundle\MemberBundle\Accounting\Time;


/**
 */
class TimeCalculator {
	private $monthDays = array(31,28,31,30,31,30,31,31,30,31,30,31);
	/**
	 *
	 * @param \DateTime $startDate
	 * @param \DateTime $endDate
	 * @param array[\DateInterval]
	 */
	public function calculateTimePeriods(\DateTime $startDate, \DateTime $endDate, $span = 0) {
		$ret = [];
		if($span == 0) {
			// monthly
			$start = ((int)$startDate->format('Y') *12)  + ((int)$startDate->format('m'));
			$end = ((int)$endDate->format('Y')*12) + ((int)$endDate->format('m'));
			$months = abs($end - $start) + 1; // Es zählt jeder angefangene Monat

			// Intervall erstmal fest 1 Monat.
			$interval = new \DateInterval('P1M');
			for($i =0; $i< $months; $i++) {
				// Für jeden Monat ein volles Intervall anlegen
				$ret[] = $interval;
			}
		}
		return $ret;
	}

	public function getLastDayInMonth(\DateTime $date) {
		// Anzahl Tage des Monats holen
		return new \DateTime($date->format('Y-m-t'));
	}

}
