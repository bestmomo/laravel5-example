<!---

	Filemanager Coldfusion connector
	
	filemanager.cfm
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cfsilent>
	<cfsetting showdebugoutput="false">
	<!--- include our configuration file --->
	<cfinclude template="filemanager.config.cfm" />
	
	<!--- create the file manager object to use for the request --->
	<cfset fileManager = CreateObject("component", "filemanager.FileManager").init(config) />
	
	<!--- setup our response --->
	<cfset response = "" />
	
	<!--- validate that the request is authorized to access the file system --->
	<cfif not authorize()>
		<cfset response = fileManager.error(fileManager.lang("AUTHORIZATION_REQUIRED")) />
	<cfelse>
	
		<!--- validate we have a proper mode variable --->
	 	<cfif (not StructKeyExists(url, "mode") and cgi.request_method eq "GET") or (not StructKeyExists(form, "mode") and cgi.request_method eq "POST")>
			<cfset response = fileManager.error(fileManager.lang("INVALID_ACTION")) />
		</cfif>
		
		<!--- only run our get/post code if we do not have an response --->
		<cfif not Len(response)>
			<cfif cgi.request_method eq "GET">
				<cfset response = fileManager.execute(argumentCollection=url) />	
			<cfelseif cgi.request_method eq "POST" and cgi.content_type contains "multipart/form-data">
				<cfset response = fileManager.execute(argumentCollection=form) />
			</cfif>
		</cfif>
		
	</cfif>
</cfsilent>
<cfoutput>#response#</cfoutput>
