<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 * EXCEPT KIND OF PORTED TO AirPHP LOLOL.
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
class uri extends library {
	public $keyval = array();
	public $uri_string;
	public $segments = array();
	public $rsegments = array();

	// --------------------------------------------------------------------

	/**
	 * Get the URI String
	 *
	 * @access	public
	 * @return	string
	 */
	public function _fetch_uri_string()
		{
		$this->uri_string = URL;
		return URL;
		}

	// --------------------------------------------------------------------

	/**
	 * Parse the REQUEST_URI
	 *
	 * Due to the way REQUEST_URI works it usually contains path info
	 * that makes it unusable as URI data.  We'll trim off the unnecessary
	 * data, hopefully arriving at a valid URI that we can use.
	 *
	 * @access	private
	 * @return	string
	 */
	private function _parse_request_uri()
		{
		if ( ! isset($_SERVER['REQUEST_URI']) OR $_SERVER['REQUEST_URI'] == '')
			{
			return '';
			}

		$request_uri = preg_replace("|/(.*)|", "\\1", str_replace("\\", "/", $_SERVER['REQUEST_URI']));

		if ($request_uri == '' OR $request_uri == SELF)
			{
			return '';
			}

		$fc_path = FCPATH.SELF;
		if (strpos($request_uri, '?') !== FALSE)
			{
			$fc_path .= '?';
			}

		$parsed_uri = explode("/", $request_uri);

		$i = 0;
		foreach(explode("/", $fc_path) as $segment)
			{
			if (isset($parsed_uri[$i]) && $segment == $parsed_uri[$i])
				{
				$i++;
				}
			}

		$parsed_uri = implode("/", array_slice($parsed_uri, $i));

		if ($parsed_uri != '')
			{
			$parsed_uri = '/'.$parsed_uri;
			}

		return $parsed_uri;
		}

	// --------------------------------------------------------------------

	/**
	 * Filter segments for malicious characters
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	private function _filter_uri($str)
		{
		if ($str != '' && s('config')->permitted_uri_chars != '')
			{
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
			// compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
			if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote(s('config')->permitted_uri_chars, '-'))."]+$|i", $str))
				{
				show_error('The URI you submitted has disallowed characters.', 400);
				}
			}

		// Convert programatic characters to entities
		$bad	= array('$', 		'(', 		')',	 	'%28', 		'%29');
		$good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');

		return str_replace($bad, $good, $str);
		}

	// --------------------------------------------------------------------

	/**
	 * Remove the suffix from the URL if needed
	 *
	 * @access	private
	 * @return	void
	 */
	function _remove_url_suffix()
		{
		if  (s('config')->url_suffix != "")
			{
			$this->uri_string = preg_replace("|".preg_quote(s('config')->url_suffix)."$|", "", $this->uri_string);
			}
		}

	// --------------------------------------------------------------------

	/**
	 * Explode the URI Segments. The individual segments will
	 * be stored in the $this->segments array.
	 *
	 * @access	private
	 * @return	void
	 */
	function _explode_segments()
		{
		foreach(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val)
			{
			// Filter segments for security
			$val = trim($this->_filter_uri($val));

			if ($val != '')
				{
				$this->segments[] = $val;
				}
			}
		}

	// --------------------------------------------------------------------
	/**
	 * Re-index Segments
	 *
	 * This function re-indexes the $this->segment array so that it
	 * starts at 1 rather than 0.  Doing so makes it simpler to
	 * use functions like $this->uri->segment(n) since there is
	 * a 1:1 relationship between the segment array and the actual segments.
	 *
	 * @access	public
	 * @return	void
	 */
	public function _reindex_segments()
		{
		array_unshift($this->segments, NULL);
		array_unshift($this->rsegments, NULL);
		unset($this->segments[0]);
		unset($this->rsegments[0]);
		}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment
	 *
	 * This function returns the URI segment based on the number provided.
	 *
	 * @access	public
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	public function segment($n, $no_result = FALSE)
		{
		return ( ! isset($this->segments[$n])) ? $no_result : $this->segments[$n];
		}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI "routed" Segment
	 *
	 * This function returns the re-routed URI segment (assuming routing rules are used)
	 * based on the number provided.  If there is no routing this function returns the
	 * same result as $this->segment()
	 *
	 * @access	public
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	public function rsegment($n, $no_result = FALSE)
		{
		return ( ! isset($this->rsegments[$n])) ? $no_result : $this->rsegments[$n];
		}

	// --------------------------------------------------------------------

	/**
	 * Generate a key value pair from the URI string
	 *
	 * This function generates and associative array of URI data starting
	 * at the supplied segment. For example, if this is your URI:
	 *
	 *	example.com/user/search/name/joe/location/UK/gender/male
	 *
	 * You can use this function to generate an array with this prototype:
	 *
	 * array (
	 *			name => joe
	 *			location => UK
	 *			gender => male
	 *		 )
	 *
	 * @access	public
	 * @param	integer	the starting segment number
	 * @param	array	an array of default values
	 * @return	array
	 */
	public function uri_to_assoc($n = 3, $default = array())
		{
	 	return $this->_uri_to_assoc($n, $default, 'segment');
		}
	/**
	 * Identical to above only it uses the re-routed segment array
	 *
	 */
	public function ruri_to_assoc($n = 3, $default = array())
		{
	 	return $this->_uri_to_assoc($n, $default, 'rsegment');
		}

	// --------------------------------------------------------------------

	/**
	 * Generate a key value pair from the URI string or Re-routed URI string
	 *
	 * @access	private
	 * @param	integer	the starting segment number
	 * @param	array	an array of default values
	 * @param	string	which array we should use
	 * @return	array
	 */
	private function _uri_to_assoc($n = 3, $default = array(), $which = 'segment')
		{
		if ($which == 'segment')
			{
			$total_segments = 'total_segments';
			$segment_array = 'segment_array';
			}
		else
			{
			$total_segments = 'total_rsegments';
			$segment_array = 'rsegment_array';
			}

		if ( ! is_numeric($n))
			{
			return $default;
			}

		if (isset($this->keyval[$n]))
			{
			return $this->keyval[$n];
			}

		if ($this->$total_segments() < $n)
			{
			if (count($default) == 0)
				{
				return array();
				}

			$retval = array();
			foreach ($default as $val)
				{
				$retval[$val] = FALSE;
				}
			return $retval;
			}

		$segments = array_slice($this->$segment_array(), ($n - 1));

		$i = 0;
		$lastval = '';
		$retval  = array();
		foreach ($segments as $seg)
			{
			if ($i % 2)
				{
				$retval[$lastval] = $seg;
				}
			else
				{
				$retval[$seg] = FALSE;
				$lastval = $seg;
				}

			$i++;
			}

		if (count($default) > 0)
			{
			foreach ($default as $val)
				{
				if ( ! array_key_exists($val, $retval))
					{
					$retval[$val] = FALSE;
					}
				}
			}

		// Cache the array for reuse
		$this->keyval[$n] = $retval;
		return $retval;
		}

	// --------------------------------------------------------------------

	/**
	 * Generate a URI string from an associative array
	 *
	 *
	 * @access	public
	 * @param	array	an associative array of key/values
	 * @return	array
	 */
	public function assoc_to_uri($array)
		{
		$temp = array();
		foreach ((array)$array as $key => $val)
			{
			$temp[] = $key;
			$temp[] = $val;
			}

		return implode('/', $temp);
		}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @access	public
	 * @param	integer
	 * @param	string
	 * @return	string
	 */
	public function slash_segment($n, $where = 'trailing')
		{
		return $this->_slash_segment($n, $where, 'segment');
		}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @access	public
	 * @param	integer
	 * @param	string
	 * @return	string
	 */
	public function slash_rsegment($n, $where = 'trailing')
		{
		return $this->_slash_segment($n, $where, 'rsegment');
		}

	// --------------------------------------------------------------------

	/**
	 * Fetch a URI Segment and add a trailing slash - helper function
	 *
	 * @access	private
	 * @param	integer
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	private function _slash_segment($n, $where = 'trailing', $which = 'segment')
		{
		if ($where == 'trailing')
			{
			$trailing	= '/';
			$leading	= '';
			}
		elseif ($where == 'leading')
			{
			$leading	= '/';
			$trailing	= '';
			}
		else
			{
			$leading	= '/';
			$trailing	= '/';
			}
		return $leading.$this->$which($n).$trailing;
		}

	// --------------------------------------------------------------------

	/**
	 * Segment Array
	 *
	 * @access	public
	 * @return	array
	 */
	public function segment_array()
		{
		return $this->segments;
		}

	// --------------------------------------------------------------------

	/**
	 * Routed Segment Array
	 *
	 * @access	public
	 * @return	array
	 */
	public function rsegment_array()
		{
		return $this->rsegments;
		}

	// --------------------------------------------------------------------

	/**
	 * Total number of segments
	 *
	 * @access	public
	 * @return	integer
	 */
	public function total_segments()
		{
		return count($this->segments);
		}

	// --------------------------------------------------------------------

	/**
	 * Total number of routed segments
	 *
	 * @access	public
	 * @return	integer
	 */
	public function total_rsegments()
		{
		return count($this->rsegments);
		}

	// --------------------------------------------------------------------

	/**
	 * Fetch the entire URI string
	 *
	 * @access	public
	 * @return	string
	 */
	public function uri_string()
		{
		return $this->uri_string;
		}


	// --------------------------------------------------------------------

	/**
	 * Fetch the entire Re-routed URI string
	 *
	 * @access	public
	 * @return	string
	 */
	public function ruri_string()
		{
		return '/'.implode('/', $this->rsegment_array()).'/';
		}

}
