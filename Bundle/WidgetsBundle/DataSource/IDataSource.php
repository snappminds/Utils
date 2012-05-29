<?php
	namespace Snappminds\Utils\Bundle\WidgetsBundle\DataSource;

	interface IDataSource
	{
		function getData($page = 1, $rowsPerPage = null);
		function getCount();
                function setCriteria(array $criteria);
	}
