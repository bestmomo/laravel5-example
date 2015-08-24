<!---

	Filemanager Coldfusion connector
	
	accessors.cfm
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cffunction name="$getLanguage" access="private" output="false" returntype="struct">
	<cfreturn variables.class.language />
</cffunction>

<cffunction name="$getConfig" access="private" output="false" returntype="struct">
	<cfreturn variables.class.config />
</cffunction>

<cffunction name="$getProperties" access="private" output="false" returntype="struct">
	<cfreturn Duplicate(variables.class.properties) />
</cffunction>

<cffunction name="$getPlugins" access="private" output="false" returntype="struct">
	<cfreturn Duplicate(variables.class.plugins) />
</cffunction>

<cffunction name="$getRoot" access="private" output="false" returntype="string">
	<cfargument name="baseTemplatePath" type="string" required="false" default="#GetDirectoryFromPath(GetBaseTemplatePath())#" />
	<cfargument name="cgiTemplatePath" type="string" required="false" default="#GetDirectoryFromPath(cgi.path_info)#" />
	<cfreturn Replace(arguments.baseTemplatePath, Replace(arguments.cgiTemplatePath, "/", "\", "all"), "", "all") />
</cffunction>
