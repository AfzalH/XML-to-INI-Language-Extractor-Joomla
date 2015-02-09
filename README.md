# XML-to-INI-Language-Extractor-Joomla
This php class will extract label/description/value of xml elements from an xml file, Generate and replace those values with automatically generated key and also create a .ini file. Created for automating translation file generation for Joomla
# How to use

<ol>
	<li>Copy your xml file (with static text) into the folder where convert.php is located</li>
	<li>open convert.php file to edit the following line <br />
		<code>$conv = new JoomlaXMLtoINI('mod_jusertube.xml','MOD_JUSERTUBE_');</code>
	</li>
	<li>change the parameters to match your filename (first one) and prefix (second one)</li>
	<li>run the script (convert.php)</li>
</ol>