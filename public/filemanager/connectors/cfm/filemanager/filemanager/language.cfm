<!---

	Filemanager Coldfusion connector
	
	language.cfm
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cffunction name="lang" access="public" output="false" returntype="string">
	<cfargument name="token" type="string" required="true" />
	<cfargument name="variable" type="string" required="false" default="" />
	<cfscript>
		var loc = {};
		loc.string = "Language string error on " & arguments.token;
		loc.language = $getLanguage();
		
		if (StructKeyExists(loc.language, arguments.token) && loc.language[arguments.token] != "")
			loc.string = loc.language[arguments.token];
		loc.string = Replace(loc.string, "%s", arguments.variable, "one");
	</cfscript>
	<cfreturn loc.string />
</cffunction>

<cffunction name="$loadLanguage" access="private" output="false" returntype="struct">
	<cfargument name="language" type="string" required="true" />
	<cfscript>
		var loc = {};
		loc.defaultLanguage = "en";
		loc.fileLocation = ExpandPath("../../scripts/languages/" & arguments.language & ".js");
		
		try
		{
			loc.fileContents = $file(action="read", file=loc.fileLocation);
		}
		catch (Any e)
		{
			loc.fileLocation = variables.class.root & "/scripts/languages/" & arguments.defaultLanguage & ".js";
			loc.fileContents = $file(action="read", file=loc.fileLocation);
		}
	</cfscript>
	<cfreturn DeserializeJSON(loc.fileContents) />
</cffunction>