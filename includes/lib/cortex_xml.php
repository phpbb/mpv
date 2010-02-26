<?php
/**
 * XML element
 *
 * @package		Cortex
 * @copyright	2006-2008 Coronis - http://www.coronis.nl
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @author		Vic D'Elfant
 *
 * @version		$Id$
 */

/**
 * cortex_xml class
 *
 * @package		Cortex
 */
class cortex_xml extends cortex_base
{
	/**
	 * This file's revision number
	 *
	 * @access	public
	 * @var		string
	 */
	const revision = '$Rev$';

	/**
	 * This element's corresponding DOMElement
	 *
	 * @access	private
	 * @var		object
	 */
	private $element;

	/**
	 * Error stack
	 *
	 * @access	private
	 * @var		array
	 */
	private $error_stack;

	/**
	 * Load an XML file and return a cortex_xml element
	 *
	 * @access	public
	 * @param	string		Filename
	 * @return	object
	 */
	public static function load_file($filename)
	{
		$document = new DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;
		$document->preserveWhiteSpace = false;

		// Attempt to load the document
		if (!$document->load($filename))
		{
			return false;
		}

		return self::load_element($document->documentElement);
	}

	/**
	 * Create a cortex_xml object based on a given DOMElement
	 *
	 * @access	public
	 * @param	object		DOMElement
	 * @return	object
	 */
	private static function load_element(DOMElement $element)
	{
		$object = new cortex_xml();
		$object->element = $element;

		return $object;
	}

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string		Name
	 * @param	string		Namespace URI
	 * @param	mixed		Initial value
	 * @return	void
	 */
	public function __construct($name = null, $namespace_uri = null, $value = null)
	{
		if (is_null($name))
		{
			return;
		}

		$document = new DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;
		$document->preserveWhiteSpace = false;

		$this->element = (!is_null($namespace_uri)) ? $document->createElementNS($namespace_uri, $name) : $document->createElement($name);

		if (!is_null($namespace_uri))
		{
			$this->element->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		}

		if (!is_null($value))
		{
			$this->element->value = $value;
		}

		$document->appendChild($this->element);
	}

	/**
	 * Add an attribute to this node
	 *
	 * @access	public
	 * @param	string		Name
	 * @param	mixed		Value
	 * @return	bool		True on success, otherwise false
	 */
	public function add_attribute($name, $value)
	{
		return $this->set_attribute($name, $value);
	}

	/**
	 * Set the value of an existing attribute of this node
	 *
	 * @access	public
	 * @param	string		Name
	 * @param	mixed		Value
	 * @return	bool		True on success, otherwise false
	 */
	public function set_attribute($name, $value)
	{
		return $this->element->setAttribute($name, $value);
	}

	/**
	 * Remove an attribute from this node
	 *
	 * @access	public
	 * @param	string		Name
	 * @return	bool		True on success, otherwise false
	 */
	public function remove_attribute($name)
	{
		return $this->element->removeAttribute($name);
	}

	/**
	 * Add a child node to the current node
	 *
	 * @access	public
	 * @param	string		Name
	 * @param	mixed		Initial value
	 * @return	object		cortex_xml object
	 */
	public function add_node($name, $value = null)
	{
		$node = new cortex_xml();
		$node->element = $this->document->createElement($name);

		if (!is_null($value))
		{
			$node->value = $value;
		}

		$this->element->appendChild($node->element);
		return $node;
	}

	/**
	 * Append a child to the current document/node
	 *
	 * @access	public
	 * @param	object		cortex_xml object
	 * @return	void
	 */
	public function append_child(cortex_xml $node)
	{
		$child = $this->document->importNode($node->element, true);
		$this->element->appendChild($child);
	}

	/**
	 * Remove this node from the document
	 *
	 * @access	public
	 * @return	void
	 */
	public function remove()
	{
		$this->element->parentNode->removeChild($this->element);
	}

	/**
	 * Execute an XPath query and return the resulting elements
	 *
	 * @access	public
	 * @param	string		XPath
	 * @param	bool		Return only the first result
	 * @return	mixed		Array if !$return_first, cortex_xml object or null
	 */
	public function get_xpath($path, $return_first = false)
	{
		$xpath = new DOMXpath($this->document);

		// Take care of the default namespace, DOMXpath doesn't support this
		if (!is_null($this->default_ns))
		{
			$path = preg_replace('#/([^:/]+?)#', '/doc-ns:$1', $path);
			$xpath->registerNameSpace('doc-ns', $this->default_ns);
		}

		$nodes = $this->parse_children($xpath->query($path));
		if ($return_first)
		{
			return (sizeof($nodes) > 0) ? $nodes[0] : null;
		}

		return $nodes;
	}

	/**
	 * Get all child nodes with a given name
	 *
	 * @access	public
	 * @param	string		Name
	 * @param	bool		Return only the first node
	 * @return	mixed		Array if !$return_first, cortex_xml object or null
	 */
	public function get_by_name($name, $return_first = true)
	{
		return $this->get_xpath('//' . $name, $return_first);
	}

	/**
	 * Get all child nodes of which a given attribute has a certain value
	 *
	 * @access	public
	 * @param	string		Name
	 * @param	mixed		Value
	 * @return	mixed		Array if !$return_first, cortex_xml object or null
	 */
	public function get_by_attribute($name, $value, $return_first = true)
	{
		return $this->get_xpath('//*[attribute::' . $name . '=\'' . $value . '\']', $return_first);
	}

	/**
	 * Validate the document against its schema. Will attempt to download the schema using its URI
	 * if $schema == null
	 *
	 * @access	public
	 * @param	string		Path to XSD, XSD data or null to automatically download the XSD
	 * @return	mixed		TRUE or an array describing the errors
	 */
	public function validate($schema = null)
	{
		if ((is_null($schema) && !is_null($this->schema_location)) || strpos($schema, 'http://') !== false)
		{
			$schema = @file_get_contents((strpos($schema, 'http://') !== false) ? $schema : $this->schema_location);
			if ($schema === false)
			{
				return true;
			}
		}
		else if (@file_exists($schema))
		{
			set_error_handler(array($this, 'error_handler'));

			$valid = $this->document->schemaValidate($schema);
			restore_error_handler();

			return ($valid === false) ? $this->error_stack : true;
		}

		set_error_handler(array($this, 'error_handler'));

		$valid = $this->document->schemaValidateSource($schema);
		restore_error_handler();

		return ($valid === false) ? $this->error_stack : true;
	}

	/**
	* Internal error handler for filling the error stack. Should not be called
	* from outside of this class.
	*
	* @access	public
	* @return	void
	*/
	public function error_handler($error_no, $msg_text, $error_file, $error_line)
	{
		$this->error_stack[] = substr($msg_text, strpos($msg_text, ']: Element') + 3);
	}

	/**
	 * Directly output the data to the client
	 *
	 * @access	public
	 * @param	bool		Set to TRUE to send the 'no-cache' header. Should be done in order
	 * 						to prevent headaches and other mental disorders when using AJAX
	 * @param	bool		exit() after having sent the data
	 * @return	void
	 */
	public function output_xml($no_cache = false, $exit = false)
	{
		$ob_contents = ob_get_contents();
		if ($ob_contents !== false)
		{
			ob_clean();
		}

		header('Content-type: application/xml');

		if ($no_cache)
		{
			header('Pragma: no-cache');
			header('Cache-Control: no-cache');
			header('Expires: ' . date('r', 0));
		}

		print $this;

		if ($exit)
		{
			exit();
		}
	}

	/**
	 * Write XML to a file
	 *
	 * @access	public
	 * @param	string		Filename to write to
	 * @return	void
	 */
	public function save_file($filename)
	{
		if (!@file_put_contents($filename, (string) $this))
		{
			throw new cortex_exception('cortex_xml: Could not store XML data', 'Could not write to "' . $filename . '"');
		}
	}

	#region // Getters

	/**
	 * Getter for this element's DOMDocument
	 *
	 * @access	protected
	 * @return	object
	 */
	protected function __get_document()
	{
		return $this->element->ownerDocument;
	}

	/**
	 * Getter for the default namespace URI
	 *
	 * @access	protected
	 * @return	mixed		String or null
	 */
	protected function __get_default_ns()
	{
		return $this->document->documentElement->namespaceURI;
	}

	/**
	 * Getter for the default namespace's prefix
	 *
	 * @access	protected
	 * @return	mixed		String or null
	 */
	protected function __get_default_ns_prefix()
	{
		return $this->document->documentElement->lookupPrefix($this->default_ns);
	}

	/**
	 * Getter for the URI from which we should be able to get this document's schema
	 *
	 * @access	protected
	 * @return	mixed		String or null
	 */
	protected function __get_schema_location()
	{
		if (is_null($this->default_ns) && isset($this->attributes['noNamespaceSchemaLocation']))
		{
			return $this->attributes['noNamespaceSchemaLocation'];
		}
		else if (is_null($this->default_ns) || !isset($this->attributes['schemaLocation']))
		{
			return null;
		}

		$url_start = strpos($this->attributes['schemaLocation'], $this->default_ns . ' ');
		if ($url_start === false)
		{
			return null;
		}

		$schema_url = substr($this->attributes['schemaLocation'], $url_start + strlen($this->default_ns . ' '));
		if (strpos($schema_url, ' ') !== false)
		{
			$schema_url = substr($schema_url, 0, strpos($schema_url, ' '));
		}

		return $schema_url;
	}

	/**
	 * Getter for the name of this node
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function __get_name()
	{
		return $this->element->tagName;
	}

	/**
	 * Getter for this node's children
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function __get_children()
	{
		return $this->parse_children($this->element->childNodes);
	}

	/**
	 * Getter for this node's attributes
	 *
	 * @access	protected
	 * @return	array
	 */
	protected function __get_attributes()
	{
		$attributes = array();
		$dom_attributes = $this->element->attributes;

		$i = 0;
		while ($dom_attribute = $dom_attributes->item($i))
		{
			$attributes[$dom_attribute->name] = $dom_attribute->value;
			$i++;
		}

		return $attributes;
	}

	/**
	 * Getter vor this node's value
	 *
	 * @access	protected
	 * @return	string
	 */
	protected function __get_value()
	{
		return $this->element->nodeValue;
	}

	/**
	 * Getter for the document's raw XML (without the XML declaration)
	 *
	 * @access	public
	 * @return	void
	 */
	protected function __get_raw_xml()
	{
		$raw_xml = (string) $this;
		$raw_xml = trim(substr($raw_xml, strpos($raw_xml, "\n")));

		return $raw_xml;
	}

	/**
	 * Getter for this node's *inner* XML
	 *
	 * @access	public
	 * @return	string
	 */
	protected function __get_inner_xml()
	{
		$raw_xml = $this->raw_xml;
		$raw_xml = substr($raw_xml, strpos($raw_xml, '<' . $this->name) + 1);
		$raw_xml = substr($raw_xml, strpos($raw_xml, '>') + 1);
		$raw_xml = substr($raw_xml, 0, strrpos($raw_xml, '</' . $this->name . '>'));

		return trim($raw_xml);
	}

	#endregion

	#region // Setters

	/**
	 * Setter for the document's schema location
	 *
	 * @access	protected
	 * @param	string		URI
	 * @return	void
	 */
	protected function __set_schema_location($value)
	{
		if (is_null($this->default_ns))
		{
			$this->set_attribute('xsi:noNamespaceSchemaLocation', $value);
		}
		else
		{
			$this->set_attribute('xsi:schemaLocation', $this->default_ns . ' ' . $value);
		}
	}

	/**
	 * Setter for the node's value. Automatically uses CDATA when needed
	 *
	 * @access	protected
	 * @param	mixed		Value
	 * @return	void
	 */
	protected function __set_value($value)
	{
		foreach ($this->element->childNodes as $child)
		{
			$this->element->removeChild($child);
		}

		// It could be that the passed value is an array
		if (is_array($value))
		{
			foreach ($value as $node_name => $node_value)
			{
				$this->add_node($node_name, $node_value);
			}

			return;
		}

		// Figure out whether to use CDATA or not
		if (strpos($value, '<') !== false || strpos($value, '>') !== false || strpos($value, '&') !== false)
		{
			$text_node = $this->document->createCDATASection($value);
		}
		else
		{
			$text_node = $this->document->createTextNode($value);
		}

		$this->element->appendChild($text_node);
	}

	/**
	 * Setter for the node's attributes
	 *
	 * @access	public
	 * @param	array		Value
	 * @return	void
	 */
	protected function __set_attributes($value)
	{
		while (list($attribute, ) = each($value))
		{
			$this->remove_attribute($attribute);
		}

		foreach ($value as $attr_name => $attr_value)
		{
			$this->add_attribute($attr_name, $attr_value);
		}
	}

	#endregion

	/**
	 * Return the document's raw XML
	 *
	 * @access	public
	 * @return	string
	 */
	public function __toString()
	{
		$this->document->normalizeDocument();

		$raw_xml = explode("\n", $this->document->saveXML());
		for ($i = 0, $_i = sizeof($raw_xml); $i < $_i; $i++)
		{
			$raw_xml[$i] = str_repeat("\t", strspn($raw_xml[$i], '  ') / 2) . trim($raw_xml[$i]);
		}

		return implode("\n", $raw_xml);
	}

	/**
	 * Get an array of cortex_xml objects based on an array of DOM* objects
	 *
	 * @access	private
	 * @param	array		DOM* objects
	 * @return	array
	 */
	private function parse_children($elements)
	{
		$children = array();
		foreach ($elements as $element)
		{
			if (get_class($element) == 'DOMElement')
			{
				$children[] = self::load_element($element);
			}
		}

		return $children;
	}
}
?>
