<?php
/*
 * Functions create a Google Visualization API Data Source from a PHP numerically indexed array with each
 * column of one type.
 *
 * A 4 line php file like this will create a simple example data source.
 * testGvisUtils.js.php:
<?php
include 'gvisUtils.php';
testFormatArrayAsGvisSource();
?>
 */

/*
 * function testFormatArrayAsGvisSource()
 * Test formatArrayAsGvisSource($callback, $header, $columns, $data)
 */
function testFormatArrayAsGvisSource() {
	$reqId = "0";
	$header = array( "version" => "0.6",
			"reqId" => $reqId,
			"status" => "ok" 
		);
	$columns = array(
			array( "label" => "Name", "type" => "string", "pattern" => "" ),
			array( "label" => "Value", "type" => "number", "pattern" => "#0.###############" )
		);
	$data = array(
			array( "Hello", 1 ),
			array( "World!", 10)
		);
	$phpArray = $header;
	$callback = 'google.visualization.Query.setResponse';
	$json = '{
    "version": "0.6",
    "reqId":"0",
    "status": "ok",
    "sig": "202289222",
    "table": {
        "cols": [{
            "id": "A",
            "label": "Name",
            "type": "string",
            "pattern": ""
        },
        {
            "id": "B",
            "label": "Value",
            "type": "number",
            "pattern": "#0.###############"
        }],
        "rows": [{
            "c": [{
                "v": "Hello"
            },
            {
                "v": 1.0
            }]
        },
        {
            "c": [{
                "v": "World!"
            },
            {
                "v": 10.0
            }]
        }]
    }
}';
	$phpObj = json_decode($json,true);
	$jsonObj = json_encode($phpObj);
	$jsonString = formatArrayAsGvisSource($callback, $header, $columns, $data);
	echo "/*\n";
	print_r($phpArray);
	print_r($phpObj);
	print_r($jsonObj);
	echo "\n*/\n";
	echo $jsonString;
}
/*
 * function formatArrayAsGvisTable($columns, $data)
 * Create a Google Visualization API DataTable
 * http://code.google.com/apis/visualization/documentation/reference.html#DataTable
 * $data is a numerically indexed PHP array with each column of a single type
 * The returned value is a PHP array.
 * json_encode of the returned array is a JavaScript/JSON object in DataTable format.
 */
function formatArrayAsGvisTable($columns, $data) {
	$newColumns = array();
	$id = ord("A");
	foreach ($columns as $column) {
		if ( !array_key_exists("id", $column) ) {
			$column["id"] = chr($id++);
		}
		$newColumns[] = $column;
	}
	$rows = array();
	foreach ($data as $row) {
		$newRow = array();
		$i = 0;
		foreach($newColumns as $column) {
			$element = $row[$i++];
			switch ($column["type"]) {
				case "string":
					if (!is_string($element)) {
						$element = strval($element);
					}
					break;
				case "number":
					$element = $element + 0;
					break;
			}
			$newRow[] = array("v" => $element);
		}
		$rows[] = array("c" => $newRow);
	}
	return array( "cols" => $newColumns, "rows" => $rows );
}

function formatArrayAsGvisObj($header, $columns, $data) {
	$header[table] = formatArrayAsGvisTable($columns, $data);
	return $header;
}
/*
 * function formatArrayAsGvisSource($callback, $header, $columns, $data) {
 * Return a string representing a Google Visualization API Data Source
 * http://code.google.com/apis/visualization/documentation/dev/implementing_data_source_overview.html
 * $data is a numerically indexed PHP array with each column of a single type.
 * The returned string is executable JavaScript.
 */
function formatArrayAsGvisSource($callback, $header, $columns, $data) {
	return $callback."(".json_encode(formatArrayAsGvisObj($header, $columns, $data)).");";
}

?>
