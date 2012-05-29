<?php
	namespace Snappminds\Utils\Bundle\WidgetsBundle\DataSource;


	class ArrayDataSource implements IDataSource
	{
		private $arrayData;
                private $criteria = array();

		public function __construct(array $data)
		{
			$this->arrayData = $data;
		}

		public function getData($page = 1, $rowsPerPage = null)
		{
			$count = $this->getCount();

			if (!$rowsPerPage) {
				$rowsPerPage = $count;
			} else {
				if ($rowsPerPage > $count)
					$rowsPerPage = $count;
			}



			$start = ($page - 1) * $rowsPerPage;
			$end = ($start + $rowsPerPage > $count)?$count:$start + $rowsPerPage;
			
			$result = array();		
			for ($i = $start; $i < $end; $i++)
			{
				$result [] = $this->arrayData[$i];
			}

			return $result;
		}

		public function getCount()
		{
			return count($this->arrayData);
		}
                
                public function setCriteria(array $criteria)
                {
                    $this->criteria = $criteria;
                }
	}
