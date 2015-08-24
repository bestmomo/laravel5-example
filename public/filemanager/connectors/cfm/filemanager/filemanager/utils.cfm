<!---

	Filemanager Coldfusion connector
	
	utils.cfm
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cffunction name="$argumentsExist" access="private" output="false" returntype="boolean">
	<cfargument name="keys" type="string" required="true" />
	<cfargument name="scope" type="struct" required="true" />
	<cfscript>
		var loc = {};
		
		for (loc.i = 1; loc.i lte ListLen(arguments.keys); loc.i++)
		{
			loc.key = ListGetAt(arguments.keys, loc.i);
			if (!StructKeyExists(arguments.scope, loc.key) || !Len(arguments.scope[loc.key]))
				return false;
		}
	</cfscript>
	<cfreturn true />
</cffunction>

<cffunction name="$relativePathFromRoot" access="private" output="false" returntype="string">
	<cfargument name="path" type="string" required="true" />
	<cfreturn ReplaceList(Replace(arguments.path, $getRoot(), "", "all"), "\", "/") />
</cffunction>

<cffunction name="$getFileProperties" access="private" output="false" returntype="struct">
	<cfargument name="path" type="string" required="true" />
	<cfscript>
		var loc = {};
		loc.properties = {};
		loc.properties["Date Created"] = "";
		
		loc.info = GetFileInfo(arguments.path);
		
		loc.properties["Date Modified"] = loc.info.Lastmodified;
		loc.properties["Size"] = loc.info.size;
		
		if (IsImageFile(arguments.path))
		{
			loc.image = ImageRead(arguments.path);
			loc.info = ImageInfo(loc.image);
			loc.properties["Height"] = loc.info.height;
			loc.properties["Width"] = loc.info.width;
		}
	</cfscript>
	<cfreturn loc.properties />
</cffunction>

<cffunction name="$getFilePreview" access="private" output="false" returntype="string">
	<cfargument name="path" type="string" required="true" />
	<cfargument name="thumbs" type="boolean" required="false" default="false" />
	<cfscript>
		var loc = {};
		loc.config = $getConfig();
		loc.defaultImages = "aac,avi,bmp,chm,css,dll,doc,fla,gif,htm,html,ini,jar,jpeg,jpg,js,lasso,mdb,mov,mp3,mpg,pdf,php,png,ppt,py,rb,real,reg,rtf,sql,swf,txt,vbs,wav,wma,xls,xml,xsl,zip";
		
		loc.thumbPath = $thumbLocation(arguments.path);
		loc.fileLocation = $getRoot() & arguments.path;
		loc.thumbLocation = $getRoot() & loc.thumbPath;
		
		loc.extension = ListLast(loc.fileLocation, ".");
		
		if (!IsImageFile(loc.fileLocation))
		{
			if (ListFindNoCase(loc.defaultImages, loc.extension))
				return loc.config.icons.path & loc.extension & ".png";
		}

		if (FileExists(loc.fileLocation) && !arguments.thumbs)
			return "connectors/cfm/filemanager.cfm?mode=preview&path=" & arguments.path;
		
		if (FileExists(loc.thumbLocation) && arguments.thumbs)
			return "connectors/cfm/filemanager.cfm?mode=preview&path=" & loc.thumbPath;
			
		if (IsImageFile(loc.fileLocation))
			return loc.config.icons.path & loc.extension & ".png";
	</cfscript>
	<cfreturn loc.config.icons.path & loc.config.icons.default />
</cffunction>

<cffunction name="$thumbLocation" access="private" output="false" returntype="string">
	<cfargument name="path" type="string" required="true" />
	<cfset var config = $getConfig() />
	<cfreturn Replace(arguments.path, config.base, config.base & "/" & config.images.thumbnailFolder, "one") />
</cffunction>

<cffunction name="$cleanString" access="private" output="false" returntype="string">
	<cfargument name="string" type="string" required="true" />
	<cfargument name="allowed" type="string" required="false" default="" />
	<cfscript>
		var loc = {};
		
		loc.allow = "";
		
		for (loc.i = 1; loc.i lte ListLen(arguments.allowed); loc.i++)
			loc.allow &= "\#ListGetAt(arguments.allowed, loc.i)#";
		
		arguments.string = REREplace(arguments.string, "[^{#loc.allow#}_a-zA-Z0-9]", "", "all");
		
	</cfscript>
	<cfreturn arguments.string />
</cffunction>

<cffunction name="$convertPath" access="private" output="false" returntype="string">
	<cfargument name="path" type="string" required="true">
	<cfreturn Replace(arguments.path, "\", "/", "all")>
</cffunction>

<cffunction name="$securePath" access="private" output="false" returntype="string">
	<cfargument name="path" type="string" required="true">
	<cfset var loc = {}>
	<cfset loc.config = $getConfig() />
	<cfset loc.webPath = "/#loc.config.base#">
	<cfset arguments.path = $convertPath(arguments.path)>
	<cfset arguments.path = ReReplaceNoCase(arguments.path, "\.+/", "", "all")>
	
	<cfset loc.webPathFull = $convertPath($getRoot() & loc.webPath)>
	<cfset loc.pathFull = $convertPath($getRoot() & arguments.path)>
	
	<cfif left(loc.pathFull, len(loc.webPathFull)) neq loc.webPathFull>
		<cfset arguments.path = loc.webPath>
	</cfif>
	<cfreturn arguments.path>
</cffunction>