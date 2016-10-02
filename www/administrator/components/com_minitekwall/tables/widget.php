<?php
/**
* @title			Minitek Wall
* @copyright   		Copyright (C) 2011-2015 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.minitek.gr/
* @developers   	Minitek.gr
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class MinitekWallTableWidget extends JTable
{
	public function __construct(&$_db)
	{
		$this->checked_out_time = $_db->getNullDate();
		parent::__construct('#__minitek_wall_widgets', 'id', $_db);
	}
	
	public function check()
	{
		// Check for valid name.
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_MINITEKWALL_WIDGETS_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		// Clean up description -- eliminate quotes and <> brackets
		if (!empty($this->description))
		{
			// Only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->description = JString::str_ireplace($bad_characters, "", $this->description);
		}

		return true;
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		// Verify that the name is unique
		$table = JTable::getInstance('Widget', 'MinitekWallTable');

		if ($table->load(array('name' => $this->name)) && ($table->id != $this->id || $this->id == 0))
		{
			//$this->setError(JText::_('COM_MINITEKWALL_WIDGETS_ERROR_UNIQUE_NAME'));

			//return false;
			$this->name = $this->name.' - '.date('D, d M Y H:i:s');
		}

		return parent::store($updateNulls);
	}

	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE ' . $this->_db->quoteName($this->_tbl) .
			' SET ' . $this->_db->quoteName('state') . ' = ' . (int) $state .
			' WHERE (' . $where . ')' .
			$checkin
		);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}
	
}
