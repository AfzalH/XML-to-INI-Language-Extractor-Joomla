<?php
/**
 * Creator: Md. Afzal Hossain
 * Author Email: afzal.csedu@gmail.com
 * Website: http://www.srizon.com
 * License: GNU GPL V2
 *
 * Usage Instructions:
 * First param is for target file name/path, second one is for prefix.
 * Copy your target xml file into the directory of this file and change the 2 parameters below and run the script
 */

$conv = new JoomlaXMLtoINI('mod_jusertube.xml','MOD_JUSERTUBE_'); // change the params here

// You probably don't want to change anything below this line

$t1 = microtime(true);
$conv->convert();
$t2 = microtime(true);

echo '<div style="text-align: center">Execution Time: '. ($t2 - $t1). ' microsecond or '. ($t2 - $t1)/1000 . ' millisecond.</div>';

class JoomlaXMLtoINI {
	protected $filename;
	protected $prefix;
	protected $curid;
	protected $outputfileini;
	protected $outputfilexml;
	protected $trans_array = array();

	function __construct($filename, $prefix) {
		$this->filename = $filename;
		$this->prefix = $prefix;
		$rawname = str_replace('.xml','',$this->filename);
		$this->outputfileini = $rawname.'.ini';
		$this->outputfilexml = $rawname.'_newXMLfile.xml';
		$this->curid = 0;
	}

	protected function save_translation_file(){
		$output = '';
		foreach ($this->trans_array as $key => $value) {
			$output = $output.$key.'='.'"'.$value.'"'."\n";
		}
		file_put_contents($this->outputfileini,$output);
	}

	protected function save_xml_file_with_translation_key(&$xml){
		file_put_contents($this->outputfilexml,$xml->asXML());
	}

	protected function show_finished_msg(){
		echo <<<EOL
		<div style="text-align: center; width: 500px; margin: 0 auto;">
			<h2>Script Executed</h2>
			<p>If successful, you should see the following 2 files in the source directory</p>
			<ul style="list-style:none;">
				<li><i>{$this->outputfileini}</i></li>
				<li><i>{$this->outputfilexml}</i></li>
			</ul>
			<p>Copy the contents of these files to your desired location</p>
		</div>
EOL;

	}

	public function convert() {
		$xml = new SimpleXMLElement($this->filename, 0, true);
		$this->process_xml_recursive($xml);
		$this->save_translation_file();
		$this->save_xml_file_with_translation_key($xml);
		$this->show_finished_msg();
	}

	protected function process_xml_recursive(&$elem) {
		foreach ($elem->children() as $child) {
			if (isset($child['label'])) $this->add_trans($child, 'label');
			if (isset($child['description'])) $this->add_trans($child, 'desc');;
			if (count($child->children()) == 0 && ($child->getName() == 'option') or $child->getName() == 'description' ) {
				$this->add_trans($child, $child->getName());
			} else {
				$this->process_xml_recursive($child);
			}
		}
	}

	protected function clean($string) {
		$string = str_replace(' ', '', $string); // Replaces all spaces.
		return preg_replace('/[^A-Za-z]/', '', $string); // Removes special chars.
	}

	protected function add_trans(&$elem, $where) {
		$key = $this->prefix;
		$value = '';
		if ($where == 'option') {
			if(strtoupper($elem->__toString()) == $elem->__toString() && strlen($this->clean($elem->__toString()))>0) return;
			$key .= 'OPT_';
			if (isset($elem['value'])) $key .= strtoupper($elem['value']);
			else $key .= $this->curid++;
			$value = $elem->__toString();
			$elem[0] = $key;
		} else if ($where == 'label') {
			if(strtoupper($elem['label']) == $elem['label']) return;
			if (isset($elem['name'])) $key .= strtoupper($elem['name']);
			else $key .= $this->curid++;
			$key .= '_LABEL';
			$value = $elem['label']->__toString();
			$elem['label'] = $key;
		} else if ($where == 'desc') {
			if(strtoupper($elem['description']) == $elem['description']) return;
			if (isset($elem['name'])) $key .= strtoupper($elem['name']);
			else $key .= $this->curid++;
			$key .= '_DESCRIPTION';
			$value = $elem['description']->__toString();
			$elem['description'] = $key;
		} else if( $where == 'description'){

			if(strtoupper($elem->__toString()) == $elem->__toString() && strlen($this->clean($elem->__toString()))>0) return;
			$key .= 'XML_DESCRIPTION';
			$value = $elem->__toString();
			$elem[0] = $key;
		}
		$this->trans_array[$key] = htmlentities($value);
	}
}
