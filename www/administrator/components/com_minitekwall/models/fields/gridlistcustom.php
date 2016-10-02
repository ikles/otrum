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

JFormHelper::loadFieldClass('radio');

$document = JFactory::getDocument();
$document->addCustomTag('<script src="'.JURI::root().'components/com_minitekwall/assets/js/mnwall-isotope.js" type="text/javascript"></script>');
$document->addCustomTag('<script src="'.JURI::root().'components/com_minitekwall/assets/js/mnwall-isotope-packery.js" type="text/javascript"></script>');

class JFormFieldGridList extends JFormFieldRadio
{
	public $type = 'GridList';
	
	protected function getInput()
	{
		$html = array();

		// Initialize some field attributes.
		$class     = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';

		// Get the field options.
		$options = $this->getOptions();

		// Build the radio field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			$disabled = !empty($option->disable) || ($readonly && !$checked);

			$disabled = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

			$html[] = '<div class="grid-radio">';
				
				$html[] = '<label for="' . $this->id . $i . '"' . $class . ' >';
				
					$html[] = '<p>';
						$html[] = JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname));
					$html[] = '</p>';
						
					$html[] = '<div class="grid-radio-demo-cont">';
						$html[] = '<div class="grid-radio-demo">';
							foreach ($option->items as $key=>$wallitem)
							{
								$itemwidth = $wallitem[0];
								$itemheight = $wallitem[1];
								$itemsize = $wallitem[2];
								$html[] = '<div style="width: '.$itemwidth.'%; height: '.$itemheight.'px;" class="grid-wall-item">';
									$html[] = '<div><div class="grid-item-size">'.$wallitem[2].'</div></div>';
								$html[] = '</div>';
							}
						$html[] = '</div>';
					$html[] = '</div>';
					
				$html[] = '</label>';
				
				$html[] = '<div>';
					$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $required . $onclick
					. $onchange . $disabled . ' />';
				$html[] = '</div>';
			
			$html[] = '</div>';
			
			$required = '';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}
	
	protected function getOptions()
	{		
		$elements = Array( 
			Array(
				'value' => '1',
				'items' => array("0"=>array("100","100","B")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_1'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '3a',
				'items' => array("0"=>array("50","100","B"), "1"=>array("50","50","L"), "2"=>array("50","50","L")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_3A'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '3b',
				'items' => array("0"=>array("50","50","L"), "1"=>array("50","100","B"), "2"=>array("50","50","L")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_3B'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '3c',
				'items' => array("0"=>array("50","100","B"), "1"=>array("25","100","P"), "2"=>array("25","100","P")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_3C'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '4',
				'items' => array("0"=>array("50","100","B"), "1"=>array("50","50","L"), "2"=>array("25","50","S"), "3"=>array("25","50","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_4'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '5',
				'items' => array("0"=>array("40","50","L"), "1"=>array("20","50","S"), "2"=>array("40","100","B"), "3"=>array("20","50","S"), "4"=>array("40","50","L")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_5'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '5b',
				'items' => array("0"=>array("25","50","S"), "1"=>array("25","50","S"), "2"=>array("50","100","B"), "3"=>array("25","50","S"), "4"=>array("25","50","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_5B'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '6',
				'items' => array("0"=>array("40","100","B"), "1"=>array("20","50","S"), "2"=>array("40","50","L"), "3"=>array("20","50","S"), "4"=>array("20","50","S"), "5"=>array("20","50","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_6'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '7',
				'items' => array("0"=>array("50","100","B"), "1"=>array("50","50","L"), "2"=>array("25","50","S"), "3"=>array("25","100","P"), "4"=>array("25","50","S"), "5"=>array("25","50","S"), "6"=>array("25","50","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_7'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '8',
				'items' => array("0"=>array("40","50","L"), "1"=>array("40","100","B"), "2"=>array("20","50","S"), "3"=>array("20","50","S"), "4"=>array("20","50","S"), "5"=>array("20","100","P"), "6"=>array("40","50","L"), "7"=>array("40","50","L")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_8'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '9',
				'items' => array("0"=>array("50","80","B"), "1"=>array("50","40","L"), "2"=>array("25","40","S"), "3"=>array("25","80","P"), "4"=>array("25","80","P"), "5"=>array("25","80","P"), "6"=>array("25","40","S"), "7"=>array("25","40","S"), "8"=>array("25","40","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_9'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '98o',
				'items' => array("0"=>array("25","60","C"), "1"=>array("25","60","C"), "2"=>array("25","60","C"), "3"=>array("25","60","C"), "4"=>array("25","60","C"), "5"=>array("25","60","C"), "6"=>array("25","60","C"), "7"=>array("25","60","C")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_MASONRY_E'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '9r',
				'items' => array("0"=>array("33.33","50","L"), "1"=>array("33.33","50","L"), "2"=>array("33.33","50","L"), "3"=>array("50","80","B"), "4"=>array("50","80","B"), "5"=>array("25","40","S"), "6"=>array("25","40","S"), "7"=>array("25","40","S"), "8"=>array("25","40","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_ROWS_9'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '12r',
				'items' => array("0"=>array("25","50","L"), "1"=>array("25","50","L"), "2"=>array("25","50","L"), "3"=>array("25","50","L"), "4"=>array("20","40","S"), "5"=>array("20","40","S"), "6"=>array("20","40","S"), "7"=>array("20","40","S"), "8"=>array("20","40","S"), "9"=>array("33.33","80","B"), "10"=>array("33.33","80","B"), "11"=>array("33.33","80","B")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_ROWS_12'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '16r',
				'items' => array("0"=>array("40","40","L"), "1"=>array("20","40","S"), "2"=>array("20","40","S"), "3"=>array("20","40","S"), "4"=>array("40","40","L"), "5"=>array("20","40","S"), "6"=>array("40","40","L"), "7"=>array("20","40","S"), "8"=>array("20","40","S"), "9"=>array("20","40","S"), "10"=>array("20","40","S"), "11"=>array("20","40","S"), "12"=>array("20","40","S"), "13"=>array("20","40","S"), "14"=>array("40","40","L"), "15"=>array("20","40","S")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_ROWS_16'),
				'class' => 'grid-radio-input'
			),
			Array(
				'value' => '99v',
				'items' => array("0"=>array("20","40","V"), "1"=>array("80","40",""), "2"=>array("20","40","V"), "3"=>array("80","40",""), "4"=>array("20","40","V"), "5"=>array("80","40",""), "6"=>array("20","40","V"), "7"=>array("80","40","")),
				'text' => JText::_('COM_MINITEKWALL_FIELD_VERTICAL_LIST'),
				'class' => 'grid-radio-input'
			)
		); 	
		
		foreach ($elements as $option)
		{
			$disabled = false;

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option['text']), 'value', 'text',
				$disabled
			);

			// Set some option attributes.
			$tmp->class = $option['class'];
			$tmp->items = $option['items'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
	
}
