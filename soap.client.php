<?php 
date_default_timezone_set('America/Chicago');
ini_set("soap.wsdl_cache_enabled", "0");
ini_set('display_errors', true);
error_reporting(E_ALL);

if(isset($_REQUEST['getfunction'])) $client = get_soap_client_connection($_REQUEST);

?>
<html>
<head>
<title>SOAP Tester (CLIENT)</title>
<link rel="stylesheet" href="http://gumbyframework.com/css/imports.css">
</head>
<body style="margin-top:10px;">
<div class="container">
  <div class="row">
    <div class="twelve columns">
      <header class="row" id="pg_head" style="margin-bottom:10px;">
        <hgroup class="six columns">
          <h1>SOAP Client </h1>
          <p>A PHP SOAP client testing tool for PHP.</p>
        </hgroup>
        <nav class="subnav six columns right">
          <ul>
            <li class="active"><a href="?">Processor</a></li>
            <li><a href="?anthem=yes">Anthemize</a></li>
            <li><a href="http://www.php.net/manual/en/soapclient.soapclient.php" target="_blank">PHP SOAP Manual</a></li>
          </ul>
        </nav>
      </header>
    </div>
  </div>
  
  <!-- START STEP 1  -->
  <form method="post" data-form="validate">
    <div class="row">
      <div class="twelve columns">
        <hr />
        <h2>Step 1: Setup Connection</h2>
      </div>
    </div>
    <div class="row">
      <div class="twelve columns">
        <dl class="field row">
          <dt class="text">
            <input id="wsdl" name="wsdl" type="text" placeholder="URL of SOAP Service" data-form="required" <?php if(isset($_GET['anthem'])) : ?>value="http://extranet.careercollege.edu/Test_FCC_WebService/FCC_Service.svc?wsdl"<?php endif; ?>>
          </dt>
          <dd class="msg"><span class="caret"></span>This field is required.</dd>
        </dl>
      </div>
    </div>
    <div class="row">
      <div class="twelve columns">
        <dl class="field row">
          <dt class="text">
            <input id="endpoint" name="endpoint" type="text" placeholder="Optionally enter an alternative location endpoint URL" <?php if(isset($_GET['anthem'])) : ?>value="http://extranet.careercollege.edu/Test_FCC_WebService/FCC_Service.svc/soap?wsdl"<?php endif; ?>>
          </dt>
          <dd class="msg"><span class="caret"></span>This field is required.</dd>
        </dl>
      </div>
    </div>
    <div class="row">
      <div class="four columns">
        <ul class="field row">
          <li>
            <label class="checkbox checked" for="trace">
              <input name="trace" type="checkbox" id="trace" style="display:none;" checked="checked">
              <span></span> Enable Trace? </label>
          </li>
          <li>
            <label class="checkbox" for="version">
              <input name="version" type="checkbox" id="version" style="display:none;">
              <span></span> Enable SOAP 1.2? </label>
          </li>
        </ul>
      </div>
      <div class="four columns">
        <ul class="field row">
          <li>
            <label class="checkbox" for="cache">
              <input name="cache" type="checkbox" id="cache" style="display:none;">
              <span></span> Cache WSDL? </label>
          </li>
          <li>
            <label class="checkbox" for="exceptions">
              <input name="exceptions" type="checkbox" id="exceptions" style="display:none;">
              <span></span> Enable Exceptions? </label>
          </li>
        </ul>
      </div>
      <div class="foun columns">
        <input type="hidden" id="getfunction" name="getfunction" value="true" />
        <input type="submit" class="btn tertiary" value="Get Functions" />
      </div>
    </div>
  </form>
  
  <!-- START STEP 2  -->
  <div class="row">
    <div class="twelve columns">
      <hr />
      <h2>Step 2: Choose Function</h2>
    </div>
  </div>
  <?php if(!empty($_REQUEST['getfunction'])): ?>
  <form method="post" data-form="validate">
    <div id="step1submission">
      <?php foreach ($_POST as $label => $value) {
            //do not run process on step2
            if(preg_match('~(wsdl|endpoint|trace|getfunction)~', $label))
            echo '<input type="hidden" id="'.$label.'" name="'.$label.'" value="'.$value.'" />';
        }
        ?>
    </div>
    <div class="row">
      <div class="five columns">
        <?php
		$dd = $ddul = $selectedDD = '';
		if(isset($_REQUEST['gotfunction'])) $selectedDD = $_REQUEST['gotfunction'];
		foreach($client->__getFunctions() as $cursor => $function) {
			unset($selected);$selected[] = '';$selected[] = '';
			$functionName = trim(( substr(
				$function, 
				stripos($function, ' '), 
				stripos($function, '(') - stripos($function, ' ') )
			));
			
			if($functionName == $selectedDD) {
				$selected[0] = 'selected="selected"';
				$selected[1] = 'class="selected"';
			}
				
			$dd .= "<option $selected[0]>".$functionName.'</option>'.PHP_EOL;
			$ddul .= '<li $selected[1]><a href="#">'.$functionName.'</a></li>'.PHP_EOL;
		}
		?>
        <ul class="field row">
          <li class="picker">
            <select id="gotfunction" name="gotfunction">
              <option value="">Select a function from the SOAP server</option>
              <?php echo $dd; ?>
            </select>
            <a href="#" class="toggle" style="min-width: 300px;">
            <?php 
            	if(empty($_REQUEST['gotfunction']))echo 'Select a function from the SOAP server';
				else echo $_REQUEST['gotfunction'];
			?>
            <span class="caret"></span></a>
            <ul>
              <?php echo $ddul; ?>
            </ul>
          </li>
        </ul>
      </div>
      <div class="one column">
        <input type="submit" class="btn tertiary" value="Build Request Form" />
      </div>
    </div>
  </form>


  
  <!-- STEP 3 -->
  <?php
	if(!empty($_REQUEST['gotfunction'])) :
	?>
  <div class="row">
    <div class="twelve columns">
      <hr />
      <h2>Step 3: Execute with form parameters</h2>
    </div>
  </div>
  <form method="post" data-form="validate">
  <div id="step2submission">
      <?php foreach ($_POST as $label => $value) {
            //do not run process on step2
            if(preg_match('~(wsdl|endpoint|trace|getfunction|gotfunction|getfunction)~', $label))
            echo '<input type="hidden" id="'.$label.'" name="'.$label.'" value="'.$value.'" />';
        }
        ?>
  </div>
  <div class="row">
    <div class="five columns">
    <?php
	//build the form
	echo '<dl class="field row">';
		$soap_response_format = build_soap_form_and_response_format(get_response_structs($client), $_REQUEST['gotfunction']);
	echo '</dl>';
  ?>
    </div>
    <div class="five columns">
      <input type="hidden" id="run" name="run" value="" />
      <input type="submit" class="btn primary" value="Submit SOAP Request" />
      <?php
		echo '<div class="drawertoggle">';
		echo '<a href="#" class="toggle" data-for=".drawer">Debug</a>';
		echo'<div class="drawer"><pre>';
	    echo PHP_EOL.'$soap_response_format'.PHP_EOL.var_export($soap_response_format,true).'</pre><div style="clear:both;"></div></div>'; 
	  ?> 
    </div>
  </div>
</form>
</div>
  <?php endif; ?>
<?php endif; ?>
<div class="row">
  <hr />
  <h2>Results</h2>
<?php  
if( isset($_REQUEST['run']) ) :
	echo '
	<section class="pretty tabs">
	  <ul>
		<li class="active"><a href="#tab1">Result</a></li>
		<li><a href="#tab2">SOAP Request</a></li>
		<li><a href="#tab3">SOAP Response</a></li>
		<li><a href="#tab4">SOAP GET</a></li>
		<li><a href="#tab5">PAGE $_POST</a></li>
	  </ul>
	';
	echo '<div class="active" data-tab="tab1"><pre>';
	
	//fill sendback
	$soap_response_array = get_soap_response_array($soap_response_format);
	//var_export($soap_response_array);echo PHP_EOL;
	
	if(isset($_REQUEST['gotfunction'])) {
		try{
			$response = $client->$_REQUEST['gotfunction']($soap_response_array);
			//$response = $client->$_REQUEST['gotfunction'](array('composite'=>'this is a test'));
		} catch (Exception $e) {
			print_r($e);
		}
	}
	print_r($response);	
	
	
	echo '</pre></div>';
	
	echo '<div data-tab="tab2"><pre>';
	echo "Request Headers:<br>", htmlentities($client->__getLastRequestHeaders()), "<br>";
	$requestSOAP = htmlentities($client->__getLastRequest());
	$requestSOAP = str_replace('&gt;&lt;', '&gt;<br>&lt;',$requestSOAP);
	echo "Request :<br>", $requestSOAP, "<br>";
	echo '</pre></div>';
	
	echo '<div data-tab="tab3"><pre>';
	$responseSOAP = htmlentities($client->__getLastResponse());
	$responseSOAP = str_replace('&gt;&lt;', '&gt;<br>&lt;',$responseSOAP);	
	echo "Response :<br>", $responseSOAP, "<br>";
	echo '</pre></div>';
	
	echo '<div data-tab="tab4"><pre>';	
	echo '_GET_FUNCTIONS_';
	var_dump($client->__getFunctions()); 
	echo '<br>------------------------------------------------------<br>';
	echo '_GET_TYPE_';
	var_export($client->__getTypes());
	echo '</pre></div>';
	
	echo '<div data-tab="tab5"><pre>';
	print_r(array_merge($_POST, $_GET));
	echo '</pre></div>';
	
	echo '</section>';

else:
echo '<pre>'.'Waiting for test submission...'.PHP_EOL.print_r(array_merge($_POST, $_GET), true).'</pre>';
endif;
?>
</div>
</div>

<div id="footer_scripts"> 
  <!-- JavaScript at the bottom for fast page loading --> 
  
  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline --> 
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 
  <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.2.min.js"><\/script>')</script> 
  <script src="http://gumbyframework.com/js/libs/gumby.min.js"></script> 
  <script src="http://gumbyframework.com/js/plugins.js"></script> 
  <script src="http://gumbyframework.com/js/main.js"></script> 
  <script src="http://gumbyframework.com/js/events.js"></script> 
  <!-- end scripts--> 
</div>
</body>
</html>
<?php


//establishes the SOAP client connection to the server
function get_soap_client_connection($params = array()) { 
	//do not attempt to build client connector without a URL
	if(empty($params['wsdl'])) return false;
	
	//if URL is set, create the client and connect with the server
	if(isset($params['endpoint'])) $args['location'] = $params['endpoint'];
	if(isset($params['trace'])) $args['trace'] = true;
	if(isset($params['exceptions'])) $args['exceptions'] = 1;
	if(isset($params['cache'])) $args['cache_wsdl'] = WSDL_CACHE_NONE;
	//$args['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
	try { return new SoapClient($params['wsdl'], $args); }
	catch (Exception $e) { return $e; }
}

//build soap form and also build soap return template (envolope)
function build_soap_form_and_response_format($structs, $type, $objTypeName = '', $noecho = FALSE, $html = ''){
	foreach ($structs[$type] as $cursor => $value) {
		//var_export($value);echo'<hr>';
		$field = preg_split('~ ~', trim($value));
		$field[0] = trim($field[0]);
		if(isset($field[1])) $field[1] = trim($field[1], ';');

		$soap_response_format[$field[1]] = $field[0];
		switch ($field[0]) {
			case 'string':
				$html .= '<dt class="text"><input id="'.$objTypeName.$field[1].'" name="'.$objTypeName.$field[1].'" placeholder="Enter a String: '.$field[1].'" /></dt>';
				break;
			case 'int':
				$html .= '<dt class="text"><input type="number" id="'.$objTypeName.$field[1].'" name="'.$objTypeName.$field[1].'" placeholder="Enter a String: '.$field[1].'" /></dt>';
				break;
			case 'boolean':
				$html .= '<ul class="field row">
						  <li>
							<label class="checkbox" for="'.$objTypeName.$field[1].'">
							  <input name="'.$objTypeName.$field[1].'" type="checkbox" id="'.$objTypeName.$field[1].'" style="display:none;">
							  <span></span> '.$objTypeName.$field[1].'? </label>
						  </li></ul>';
				$html .= '<dt class="text"><input type="number" id="'.$objTypeName.$field[1].'" name="'.$objTypeName.$field[1].'" placeholder="Enter a String: '.$field[1].'" /></dt>';
				break;
			case '':
				$html .= 'No parameters for this function.';
				break;
			default:
				$html .= '<dt>Building Server Object Type:'.$field[0].'</dt>';
				$soap_response_format[$field[0]] = build_soap_form_and_response_format($structs, $field[0], $field[0], FALSE, $html);
		}
	}
	if(isset($html) && !$noecho) echo $html;
	return $soap_response_format;
}

//setup response form template
function get_response_structs($client) {
	//go through all the "structs" and build them out
	foreach ($client->__getTypes() as $cursor => $value) {
		preg_match('~^struct (.*)\{(.*)\}.*~s', $value, $matches);
		unset($matches[0]);
		if(!empty($matches)) $soap_response_structs[trim($matches[1])] = preg_split('~'.PHP_EOL.'~', trim($matches[2]));
	}

	return $soap_response_structs;
}

function get_soap_response_array($soap_response_format, $soap_response_array = array(), $objName = '') {
	foreach($soap_response_format as $name => &$type) {
		//echo 'Building '.$objName.' response object: ('.$type.')'.$name.PHP_EOL;
		switch ($type) {
			case 'string':
				$soap_response_array[$name] = strval($_POST[$objName.$name]);
				break;
			case 'int':
				$soap_response_array[$name] = intval($_POST[$objName.$name]);
				break;
			case 'boolean':
				$soap_response_array[$name] = (bool)($_POST[$objName.$name]);
				break;
			default:
				//START HERE - problem is that we need to send the new format in the next recursion instead of the previous format
				//var_export($soap_response_format);echo PHP_EOL;
				//$soap_response_format_child = build_soap_form_and_response_format($all_structs, $type, '', TRUE);
				$soap_response_format_child = $soap_response_format[$type]; 
				unset($soap_response_format[$type]);
				//var_export($soap_response_format_child);
				$soap_response_array[$name] = get_soap_response_array($soap_response_format_child,$soap_response_array, $type);
				//$soap_response_array[$name] = (Lead)$soap_response_array[$name];
				break;
		}
	}
	
	//if(empty($objName)) var_export($soap_response_array);
	return $soap_response_array;
}

//close SOAP connection
//unset($client);