<!---

	Filemanager Coldfusion connector
	
	cfml.cfm
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cffunction name="$dump" access="private" output="true" returntype="void">
	<cfargument name="variable" type="any" required="true" />
	<cfargument name="abort" type="boolean" required="false" default="true" />
	<cfdump var="#arguments.variable#" />
	<cfif arguments.abort>
		<cfabort />
	</cfif>
</cffunction>

<cffunction name="$getMemento" access="public" output="false" returntype="struct">
	<cfreturn variables.class />
</cffunction>	

<cffunction name="$setting" returntype="void" access="public" output="false">
	<cfsetting attributeCollection="#arguments#">
</cffunction>

<cffunction name="$image" returntype="struct" access="public" output="false">
	<cfset var returnValue = {}>
	<cfset arguments.structName = "returnValue">
	<cfimage attributeCollection="#arguments#">
	<cfreturn returnValue>
</cffunction>

<cffunction name="$content" returntype="any" access="public" output="false">
	<cfcontent attributeCollection="#arguments#">
</cffunction>

<cffunction name="$header" returntype="void" access="public" output="false">
	<cfheader attributeCollection="#arguments#">
</cffunction>

<cffunction name="$abort" returntype="void" access="public" output="false">
	<cfabort attributeCollection="#arguments#">
</cffunction>

<cffunction name="$directory" returntype="any" access="public" output="false">
	<cfset var returnValue = "">
	<cfset arguments.name = "returnValue">
	<cfdirectory attributeCollection="#arguments#">
	<cfreturn returnValue>
</cffunction>

<cffunction name="$file" returntype="any" access="public" output="false">
	<cfargument name="action" type="string" required="true" />
	<cfset var returnValue = "">
	<cfif arguments.action eq "upload">
		<cfset arguments.result = "returnValue">
	<cfelse>
		<cfset arguments.variable = "returnValue">
	</cfif>
	<cffile attributeCollection="#arguments#">
	<cfreturn returnValue>
</cffunction>

<cffunction name="$throw" returntype="void" access="public" output="false">
	<cfthrow attributeCollection="#arguments#">
</cffunction>

<cffunction name="$invoke" returntype="any" access="public" output="false">
	<cfset var loc = {}>
	<cfset arguments.returnVariable = "loc.returnValue">
	<cfif StructKeyExists(arguments, "componentReference")>
		<cfset arguments.component = arguments.componentReference>
		<cfset StructDelete(arguments, "componentReference")>
	<cfelseif NOT StructKeyExists(variables, arguments.method)>
		<!--- this is done so that we can call dynamic methods via "onMissingMethod" on the object (we need to pass in the object for this so it can call methods on the "this" scope instead) --->
		<cfset arguments.component = this>
	</cfif>
	<cfif StructKeyExists(arguments, "invokeArgs")>
		<cfset arguments.argumentCollection = arguments.invokeArgs>
		<cfset StructDelete(arguments, "invokeArgs")>
	</cfif>
	<cfinvoke attributeCollection="#arguments#">
	<cfif StructKeyExists(loc, "returnValue")>
		<cfreturn loc.returnValue />
	</cfif>
</cffunction>

<cffunction name="$createObjectFromRoot" returntype="any" access="public" output="false">
	<cfargument name="path" type="string" required="true">
	<cfargument name="fileName" type="string" required="true">
	<cfargument name="method" type="string" required="true">
	<cfscript>
		var returnValue = "";
		arguments.returnVariable = "returnValue";
		arguments.component = ListChangeDelims(arguments.path, ".", "/") & "." & ListChangeDelims(arguments.fileName, ".", "/");
		arguments.argumentCollection = Duplicate(arguments);
		StructDelete(arguments, "path");
		StructDelete(arguments, "fileName");
	</cfscript>
	<cfinclude template="../../root.cfm">
	<cfreturn returnValue>
</cffunction>