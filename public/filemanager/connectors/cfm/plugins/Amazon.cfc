<cfcomponent output="false">

	<cfinclude template="../filemanager/filemanager/cfml.cfm" />
	<cfinclude template="../filemanager/filemanager/utils.cfm" />
	
	<cffunction name="init" access="public" output="false" returntype="any">
		<cfargument name="accessKey" type="string" required="false" default="yourAccessKey" />
		<cfargument name="secretKey" type="string" required="false" default="yourSecretKey" />
		<cfargument name="bucketName" type="string" required="false" default="yourBucketName" />
		<cfscript>
			variables.class = Duplicate(arguments);
			variables.class.acl = [
				  { group = "all", permission = "read" }
			];
		</cfscript>
		<cfreturn this />
	</cffunction>
	
	<cffunction name="$getBucketName" access="private" output="false" returntype="string">
		<cfreturn variables.class.bucketName />
	</cffunction>
	
	<cffunction name="$getAccessKey" access="private" output="false" returntype="any">
		<cfreturn variables.class.accessKey />
	</cffunction>
	
	<cffunction name="$getSecretKey" access="private" output="false" returntype="any">
		<cfreturn variables.class.secretKey />
	</cffunction>
	
	<cffunction name="$getRoot" access="private" output="false" returntype="string">
		<cfreturn "s3://#$getAccessKey()#:#$getSecretKey()#@#$getBucketName()#" />
	</cffunction>
	
	<cffunction name="$getAcl" access="private" output="false" returntype="array">
		<cfreturn variables.class.acl />
	</cffunction>
	
	<cffunction name="$getBaseUrl" access="private" output="false" returntype="string">
		<cfreturn "http://#$getBucketName()#.s3.amazonaws.com" />
	</cffunction>
	
	<cffunction name="beforeDownload" access="public" output="false" returntype="any">
		<cfscript>
			var loc = {};
			
			// make sure we have are arguments
			if (!$argumentsExist("path", arguments))
				return error(lang("INVALID_VAR"), true);
				
			loc.fileLocation = $getRoot() & arguments.path;
			
			// if the file does not exist on amazon, allow the file manager to continue processing to find it on the local file system
			if (FileExists(loc.fileLocation))
			{
				loc.fileUrl = $getBaseUrl() & arguments.path;
				location(url=loc.fileUrl, addtoken=false);
			}
		</cfscript>
	</cffunction>
	
	<cffunction name="beforePreview" access="public" output="false" returntype="any">
		<cfscript>
			var loc = {};
			
			// make sure we have are arguments
			if (!$argumentsExist("path", arguments))
				return error(lang("INVALID_VAR"), true);
				
			loc.fileLocation = $getRoot() & arguments.path;
			
			// if the file does not exist on amazon, allow the file manager to continue processing to find it on the local file system
			if (FileExists(loc.fileLocation))
			{
				loc.fileUrl = $getBaseUrl() & arguments.path;
				location(url=loc.fileUrl, addtoken=false);
			}
		</cfscript>
	</cffunction>
	
	<cffunction name="afterAdd" access="public" output="false" returntype="any">
		<cfscript>
			var loc = {};
			loc.response = DeserializeJSON(ReplaceList(arguments.response, "<textarea>,</textarea>", ""));
			
			// only upload to s3 if everything is ok
			if (!loc.response.code)
			{
				loc.path = loc.response.path & loc.response.name;
				loc.source = ExpandPath(loc.path);
				loc.destination = $getRoot() & loc.path;
				
				if (FileExists(loc.source))
				{
					FileCopy(loc.source, loc.destination);
					StoreAddACL(loc.destination, $getAcl());
				}
			}
		</cfscript>
		<cfreturn />
	</cffunction>
	
	<cffunction name="afterThumbnail" access="public" output="false" returntype="any">
		<cfscript>
			var loc = {};
			loc.response = DeserializeJSON(arguments.response);
			
			if (!loc.response.code)
			{
				loc.path = loc.response.path;
				loc.source = ExpandPath(loc.path);
				loc.destination = $getRoot() & loc.path;
				
				FileCopy(loc.source, loc.destination);
				StoreAddACL(loc.destination, $getAcl());
			}
		</cfscript>
	</cffunction>
	
	<cffunction name="afterRename" access="public" output="false" returntype="any">
		<cfscript>
			var loc = {};
			loc.response = DeserializeJSON(arguments.response);
			
			if (!loc.response.code)
			{
				loc.fileLocation = $getRoot() & loc.response["Old Path"];
				loc.directoryObjects = DirectoryList(loc.fileLocation);
				
				// s3 is a little different from the file system in that folders do not exist as objects
				// so we only need to move the items under the folder to the new location
				if (ArrayLen(loc.directoryObjects))
				{
					for (loc.source in loc.directoryObjects)
					{
						loc.destination = Replace(loc.source, loc.response["Old Name"], loc.response["New Name"], "one");
						
						FileMove(loc.source, loc.destination);
						StoreAddACL(loc.destination, $getAcl());
					}
				}
				else if (FileExists(loc.fileLocation))
				{
					loc.destination = Reverse(ListRest(Reverse(loc.fileLocation), "/")) & "/" & arguments.new & "." & ListLast(loc.fileLocation, ".");
					
					FileMove(loc.fileLocation, loc.destination);
					StoreAddACL(loc.destination, $getAcl());
				}
			}
		</cfscript>
	</cffunction>

	<cffunction name="afterDelete" access="public" output="false" returntype="any">
		<cfscript>
			var loc = {};
			loc.response = DeserializeJSON(arguments.response);
			
			if (!loc.response.code)
			{
				loc.fileLocation = $getRoot() & loc.response.path;
				loc.directoryObjects = DirectoryList(loc.fileLocation);
				
				if (ArrayLen(loc.directoryObjects))
				{
					for (loc.source in loc.directoryObjects)
						FileDelete(loc.source);
				}
				else if (FileExists(loc.fileLocation))
				{
					FileDelete(loc.fileLocation);
				}
			}
		</cfscript>
		<cfreturn />
	</cffunction>
	
</cfcomponent>
