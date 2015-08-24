<?LassoScript

	//
	// JSON Encoding and Decoding
	//
	// Copyright 2007 LassoSoft, LLC
	//
	// This file was originally published as a Tip of the Week and then
	// updated in a subsequent tip of the week.
	//
	// <http://www.lassosoft.com/Documentation/TotW/index.lasso?9268>
	// <http://www.lassosoft.com/Documentation/TotW/index.lasso?9319>
	//
	// Changes - 2007-06-30 Whitespace between tokens and UTF-8 BOM
	// support by Göran Törnquist. 2007-08-07 New map method of
	// embedding native data types and -NoNative and -UseNative keywords
	// by Fletcher Sandbeck. 2007-09-28 by Fletcher Sandbeck. Added
	// [JSON_RPCCall] tag. Added new RPC.LassoApp files which
	// automatically recognizes incoming JSON-RPC calls over HTTP. Added
	// support for __jsonclass__ embedding method. Added [Object] and
	// [Literal] which make JavaScript objects and functions easier to
	// define.
	//
	// Installation - Either include this file in the Lasso page you
	// want to use these tags or place this file in the "LassoStartup"
	// folder within the Lasso Professional 8 application folder or the
	// sub-folder for a specific site.
	//
	// Description - These tags implement simple JSON (JavaScript Object
	// Notation) encoding and decoding in Lasso. JSON is a text format
	// used for language independent data exchange. It is based on the
	// syntax of JavaScript and JSON encoded objects can be passed into
	// the JavaScript eval() function directly.
	//
	// JSON supports the following data types natively: Array, Map,
	// String, Integer, Decimal, Boolean, Null, and Date. Every JSON
	// object must be wrapped in either an array or a map. If any other
	// data type is passed to [Encode_JSON] then it will be encoded as a
	// single element array. Literal is a subclass of string which will
	// be inserted without quotes or encoding. Object is a subclass of
	// map whose keys are embedded as literals.
	//
	// By default, data type which are not supported natively are
	// converted to the closest possible JSON data type. Sets, Lists,
	// Queues, etc. are converted to Arrays. 
	//
	// -UseNative can be used to turn off this automatic conversion. If
	// -UseNative is specified then each data type is embedded using the
	// serialization method described next.
	//
	// Other data types are embedded using the JSON-RPC standard.
	// {"__jsonclass__":["constructor", [param1,...]], "prop1": ...} In
	// Lasso, the constructor is always "deserialize" and there is one
	// parameter which contains the native Lasso serialization of the
	// type.
	//
	// NOTE - This replaces the Lasso-specific serialization scheme in
	// an earlier version of this document. However, the earlier
	// serialization scheme will still be decoded for backward
	// compatibility with earlier implementations.
	//
	// -NoNative can be used to turn off embedding data types as a
	// __jsonclass__. If -NoNative is specified then data types which
	// are not supported natively (and cannot be converted) will be
	// skipped.
	//
	// [Encode_JSON] - Encodes a Lasso data type as a string. If a map
	// or array is passed then it will be encoded directly. Any other
	// data type will be wrapped in a single element array and encoded.
	// The output will always use UTF-8 encoding without extra escape
	// sequences.
	//
	// [Decode_JSON] - Decodes a JSON object into native Lasso objects.
	// The result will be either an array or a map. The tag expects a
	// native Lasso string. If you are importing a JSON object encoded
	// in UTF-16 or UTF-32 then it should be imported into a Lasso
	// string before being decoded.
	//
	// [Literal] - This is a subclass of [String]. A literal works
	// exactly like a string, but will be inserted directly rather than
	// being encoded into JSON. This allows JavaScript elements like
	// functions to be inserted into JSON objects. This is most useful
	// when the JSON object will be used within a JavaScript on the
	// local page. [Map: 'fn'=(Literal: 'function(){ ...})] => {'fn':
	// function(){ ...}}
	//
	// [Object] - This is a subclass of [Map]. An object works exactly
	// like a map, but when it is encoded into JSON all of the keys will
	// be inserted literally. This makes it easy to create a JavaScript
	// object without extraneous quote marks. [Object: 'name'='value']
	// => {name: "value"}
	//
	// Note - [Object] and [Literal] should only be used when encoding
	// Javascript for use in local scripts. They will not generate valid
	// JSON encodings.
	//
	// Feedback - Please let us know if you have any problems with this
	// implementation. In particular, please forward us any JSON objects
	// which do not decode properly. Send mail to: <bugs@lassosoft.com>.
	//
	// More Information - More information about the JSON standard and
	// JSON-RPC can be found at the following URLs.
	//
	// <http://json.org/>
	// <http://json-rpc.org/>
	// <http://www.ietf.org/rfc/rfc4627.txt?number=4627>
	//
	
	Define_Tag: 'JSON', -Namespace='Encode_', -Required='value', -Optional='options', -priority='replace';
	
		Local: 'output' = '';
		Local: 'newoptions' = (Array: -Internal);
		If: !(Local_Defined: 'options') || (#options->(IsA: 'array') == False);
			Local: 'options' = (Array);
		/If;
		If: (#options >> -UseNative) || (Params >> -UseNative);
			#newoptions->(Insert: -UseNative);
		/If;
		If: (#options >> -NoNative) || (Params >> -NoNative);
			#newoptions->(Insert: -NoNative);
		/If;
		If: (#options !>> -Internal) && (#options !>> -UseNative) && (#value->(IsA: 'set')) || (#value->(IsA: 'list')) || (#value->(IsA: 'queue')) || (#value->(IsA: 'priorityqueue')) || (#value->(IsA: 'stack'));
			#output += (Encode_JSON: Array->(insertfrom: #value->iterator) &, -Options=#newoptions);
		Else: (#options !>> -Internal) && (#value->(Isa: 'array') == False) && (#value->(IsA: 'map') == False);
			#output += '[' + (Encode_JSON: #value, -Options=#newoptions) + ']';
		Else: (#value->(IsA: 'literal'));
			#output += #value;
		Else: (#value->(IsA: 'string'));
			#output += '"' + ((String: #value)->(Replace: '\"', '\\"') & (Replace: '\r', '\\r') & (Replace: '\n', '\\n') & (Replace: '\t', '\\t') & (Replace: '\f', '\\f') & (Replace: '\b', '\\b') &) + '"';
		Else: (#value->(IsA: 'integer')) || (#value->(IsA: 'decimal')) || (#value->(IsA: 'boolean'));
			#output += (String: #value);
		Else: (#value->(IsA: 'null'));
			#output += 'null';
		Else: (#value->(IsA: 'date'));
			if: #value->gmt;
				#output += '"' + #value->(format: '%QT%TZ') + '"';
			else;
				#output += '"' + #value->(format: '%QT%T') + '"';
			/if;
		Else: (#value->(IsA: 'array'));
			#output += '[';
			Iterate: #value, (Local: 'temp');
				#output += (Encode_JSON: #temp, -Options=#newoptions);
				If: #value->Size != Loop_Count;
					#output += ', ';
				/If;
			/Iterate;
			#output += ']';
		Else: (#value->(IsA: 'object'));
			#output += '{';
			Iterate: #value, (Local: 'temp');
				#output += #temp->First + ': ' + (Encode_JSON: #temp->Second, -Options=#newoptions);
				If: (#value->Size != Loop_Count);
					#output += ', ';
				/If;
			/Iterate;
			#output += '}';
		Else: (#value->(IsA: 'map'));
			#output += '{';
			Iterate: #value, (Local: 'temp');
				#output += (Encode_JSON: #temp->First, -Options=#newoptions) + ': ' + (Encode_JSON: #temp->Second, -Options=#newoptions);
				If: (#value->Size != Loop_Count);
					#output += ', ';
				/If;
			/Iterate;
			#output += '}';
		Else: (#value->(IsA: 'client_ip')) || (#value->(IsA: 'client_address'));
			#output += (Encode_JSON: (String: #value), -Options=#newoptions);
		Else: (#options !>> -UseNative) && (#value->(IsA: 'set')) || (#value->(IsA: 'list')) || (#value->(IsA: 'queue')) || (#value->(IsA: 'priorityqueue')) || (#value->(IsA: 'stack'));
			#output += (Encode_JSON: Array->(insertfrom: #value->iterator) &, -Options=#newoptions);
		Else: (#options !>> -NoNative);
			#output += (Encode_JSON: (Map: '__jsonclass__'=(Array:'deserialize',(Array:'<LassoNativeType>' + #value->Serialize + '</LassoNativeType>'))));
		/If;
		Return: @#output;		
	/Define_Tag;


	Define_Tag: 'JSON', -Namespace='Decode_', -Required='value', -priority='replace';

		(#value == '') ? Return: Null;
		
		Define_Tag: 'consume_string', -Required='ibytes';
			Local: 'obytes' = bytes;
			local: 'temp' = 0;
			While: ((#temp := #ibytes->(export8bits: #temp)) != 34);
				#obytes->(import8bits: #temp);
				(#temp == 92) ? #obytes->(import8bits: #ibytes->export8bits); // Escape \
			/While;
			Local: 'output' = ((String: #obytes)->(Replace: '\\"', '\"') & (Replace: '\\r', '\r') & (Replace: '\\n', '\n') & (Replace: '\\t', '\t') & (Replace: '\\f', '\f') & (Replace: '\\b', '\b') &);
			If: #output->(BeginsWith: '<LassoNativeType>') && #output->(EndsWith: '</LassoNativeType>');
				Local: 'temp' = #output - '<LassoNativeType>' - '</LassoNativeType>';
				Local: 'output' = null;
				Protect;
					#output->(Deserialize: #temp);
				/Protect;
			Else: (Valid_Date: #output, -Format='%QT%TZ');
				Local: 'output' = (Date: #output, -Format='%QT%TZ');
			Else: (Valid_Date: #output, -Format='%QT%T');
				Local: 'output' = (Date: #output, -Format='%QT%T');
			/If;			
			Return: @#output;
		/Define_Tag;

		Define_Tag: 'consume_token', -Required='ibytes', -required='temp';
			Local: 'obytes' = bytes->(import8bits: #temp) &;
			local: 'delimit' = (array: 9, 10, 13, 32, 44, 58, 93, 125); // \t\r\n ,:]}
			While: (#delimit !>> (#temp := #ibytes->export8bits));
				#obytes->(import8bits: #temp);
			/While;
			Local: 'output' = (String: #obytes);
			If: (#output == 'true') || (#output == 'false');
				Return: (Boolean: #output);
			Else: (#output == 'null');
				Return: Null;
			Else: (String_IsNumeric: #output);
				Return: (#output >> '.') ? (Decimal: #output) | (Integer: #output);
			/If;
			Return: @#output;
		/Define_Tag;

		Define_Tag: 'consume_array', -Required='ibytes';
			Local: 'output' = array;
			local: 'delimit' = (array:  9, 10, 13, 32, 44); // \t\r\n ,
			local: 'temp' = 0;
			While: ((#temp := #ibytes->export8bits) != 93); // ]
				If: (#delimit >> #temp);
					// Discard whitespace 
				Else: (#temp == 34); // "
					#output->(insert: (consume_string: @#ibytes));
				Else: (#temp == 91); // [
					#output->(insert: (consume_array: @#ibytes));
				Else: (#temp == 123); // {
					#output->(insert: (consume_object: @#ibytes));
				Else;
					#output->(insert: (consume_token: @#ibytes, @#temp));
					(#temp == 93) ? Loop_Abort;
				/If;
			/While;
			Return: @#output;
		/Define_Tag;
		
		Define_Tag: 'consume_object', -Required='ibytes';
			Local: 'output' = map;
			local: 'delimit' = (array:  9, 10, 13, 32, 44); // \t\r\n ,
			local: 'temp' = 0;
			local: 'key' = null;
			local: 'val' = null;
			While: ((#temp := #ibytes->export8bits) != 125); // }
				If: (#delimit >> #temp);
					// Discard whitespace 
				Else: (#key !== null) && (#temp == 34); // "
					#output->(insert: #key = (consume_string: @#ibytes));
					#key = null;
				Else: (#key !== null) && (#temp == 91); // [
					#output->(insert: #key = (consume_array: @#ibytes));
					#key = null;
				Else: (#key !== null) && (#temp == 123); // {
					#output->(insert: #key = (consume_object: @#ibytes));
					#key = null;
				Else: (#key !== null);
					#output->(insert: #key = (consume_token: @#ibytes, @#temp));
					(#temp == 125) ? Loop_abort;
					#key = null;
				Else;
					#key = (consume_string: @#ibytes);
 				    while(#delimit >> (#temp := #ibytes->export8bits));
					/while;
  					#temp != 58 ? Loop_Abort;
				/If;
			/While;
			
			If: (#output >> '__jsonclass__') && (#output->(Find: '__jsonclass__')->(isa: 'array')) && (#output->(Find: '__jsonclass__')->size >= 2) && (#output->(Find: '__jsonclass__')->First == 'deserialize');
				Return: #output->(find: '__jsonclass__')->Second->First;
			Else: (#output >> 'native') && (#output >> 'comment') && (#output->(find: 'comment') == 'http://www.lassosoft.com/json');
				Return: #output->(find: 'native');
			/If;
			Return: @#output;
		/Define_Tag;
		
		Local: 'ibytes' = (bytes: #value);
		Local: 'start' = 1;
 	  	#ibytes->removeLeading(BOM_UTF8);
		Local: 'temp' = #ibytes->export8bits;
		If: (#temp == 91); // [
			Local: 'output' = (consume_array: @#ibytes);
			Return: @#output;
		Else: (#temp == 123); // {
			Local: 'output' = (consume_object: @#ibytes);
			Return: @#output;
		/If;		
	/Define_Tag;


	Define_Type: 'Literal', 'String';
	/Define_Type;


	Define_Type: 'Object', 'Map';
	/Define_Type;


	Define_Tag: 'RPCCall', -Namespace='JSON_',
			-Required='method',
			-Optional='params',
			-Optional='id',
			-Optional='host', -priority='replace';
		!(Local_Defined: 'host') ? Local: 'host' = 'http://localhost/lassoapps.8/rpc/rpc.lasso';

		!(Local_Defined: 'id') ? Local: 'id' = Lasso_UniqueID;
		Local: 'request' = (Map: 'method' = #method, 'params' = #params, 'id' = #id);
		Local: 'request' = (Encode_JSON: #request);
		
		Local: 'result' = (Include_URL: #host, -PostParams=#request);
		Local: 'result' = (Decode_JSON: #result);
		Return: @#result;
	/Define_Tag;	
?>
