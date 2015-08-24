<!---

	Filemanager Coldfusion connector
	
	core.cfm
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cffunction name="execute" access="public" output="false" returntype="string">
	<cfargument name="mode" type="string" required="true" />
	<cfscript>
		var loc = {};
		
		// check to see if the method exists
		if (!StructKeyExists(variables, arguments.mode))
			return error(lang("MODE_ERROR"));

		// filter path to prevent tranversing outside of webroot
		if (StructKeyExists(arguments, "path"))
		{
			arguments.path = $securePath(arguments.path);
		}

		if (StructKeyExists(arguments, "currentpath"))
		{
			arguments.currentpath = $securePath(arguments.currentpath);
		}
		
		
		// execute any before callback actions
		loc.callBackResponse = run(type="before", argumentCollection=arguments);
		if (StructKeyExists(loc, "callbackResponse"))
			return loc.callbackResponse;
		
		// execute action
		loc.response = $invoke(componentReference=this, method=arguments.mode, argumentCollection=arguments);
		
		// execute any after callback actions
		loc.callBackResponse = run(type="after", argumentCollection=arguments, response=loc.response);
		if (StructKeyExists(loc, "callbackResponse"))
			return loc.callBackResponse;
	</cfscript>
	<cfreturn loc.response />
</cffunction>

<cffunction name="run" access="public" output="false" returntype="string">
	<cfargument name="type" type="string" required="true" />
	<cfargument name="mode" type="string" required="true" />
	<cfargument name="response" type="string" required="false" default="" />
	<cfscript>
		var loc = {};
		loc.plugins = $getPlugins();
		
		loc.callBackMethod = arguments.type & arguments.mode;
		
		for (loc.plugin in loc.plugins)
		{
			if (StructKeyExists(loc.plugin, loc.callBackMethod) && IsCustomFunction(loc.plugin[loc.callbackMethod]))
			{
				loc.response = $invoke(componentReference=loc.plugin, method=loc.callbackMethod, argumentCollection=arguments);
				// stop all plugin processing if we get a response
				if (StructKeyExists(loc, "response") && IsJSON(loc.response))
					return loc.response;
			}
		}
	</cfscript>
	<cfreturn />
</cffunction>

<cffunction name="download" access="public" output="false" returntype="any">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("path", arguments))
			return error(lang("INVALID_VAR"), true);
			
		loc.fileLocation = $getRoot() & arguments.path;
		
		if (!FileExists(loc.fileLocation))
			return error(lang("FILE_DOES_NOT_EXIST", arguments.path));
		
		// prompt the user to download the file
		$header(name="content-disposition", value="attachment; filename=""#ListLast(arguments.path, '/')#""");
		$content(type="application/x-download", file=$getRoot() & arguments.path, deleteFile=false);
		$abort();
	</cfscript>
</cffunction>

<cffunction name="preview" access="public" output="false" returntype="any">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("path", arguments))
			return error(lang("INVALID_VAR"), true);
			
		loc.fileLocation = $getRoot() & arguments.path;
		
		if (!FileExists(loc.fileLocation))
			return error(lang("FILE_DOES_NOT_EXIST", arguments.path));
		
		// prompt the user to download the file
		$header(name="content-disposition", value="inline; filename=""#ListLast(arguments.path, '/')#""");
		$content(type="image/#ListLast(arguments.path, '.')#", file=$getRoot() & arguments.path, deleteFile=false);
		$abort();
	</cfscript>
</cffunction>

<cffunction name="rename" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("old,new", arguments))
			return error(lang("INVALID_VAR"), true);
		
		loc.newPath = Reverse(ListRest(Reverse(arguments.old), "/")) & "/" & arguments.new;
		loc.fileLocation = $getRoot() & arguments.old;
		
	
		
		if (DirectoryExists(loc.fileLocation))
		{
			loc.newLocation = $getRoot() & loc.newPath & "/";
			
			if (DirectoryExists(loc.newLocation))
				return error(lang("DIRECTORY_ALREADY_EXISTS"));
				
			execute(mode="rename", old=$thumbLocation(arguments.old), new=arguments.new);
			
			try
			{
				$directory(action="rename", directory=loc.fileLocation, newDirectory=loc.newLocation);
			}
			catch (Any e)
			{
				return error(lang("ERROR_RENAMING_DIRECTORY"));	
			}
		}
		else if (FileExists(loc.fileLocation))
		{
			loc.newLocation = Reverse(ListRest(Reverse(loc.fileLocation), "/")) & "/" & arguments.new & "." & ListLast(loc.fileLocation, ".");
			
			if (FileExists(loc.newLocation))
				return error(lang("FILE_ALREADY_EXISTS"));
				
			// delete the thumb if it exists, if it doesn't we just ignore
			if (IsImageFile(loc.fileLocation))
				execute(mode="rename", old=$thumbLocation(arguments.old), new=arguments.new);
			
			try
			{
				$file(action="rename", source=loc.fileLocation, destination=loc.newLocation, mode=755);
			}
			catch (Any e)
			{
				return error(lang("ERROR_RENAMING_FILE"));	
			}
		}
		else
		{
			return error(lang("INVALID_DIRECTORY_OR_FILE"));
		}
		
		loc.response = {};
		loc.response["Old Path"] = Replace(loc.fileLocation, $getRoot(), "", "all");
		loc.response["Old Name"] = ListLast(loc.fileLocation, "/");
		loc.response["New Path"] = Replace(loc.newLocation, $getRoot(), "", "all");
		loc.response["New Name"] = ListLast(loc.newLocation, "/");
		loc.response["Error"] = "";
		loc.response["Code"] = 0;
	</cfscript>
	<cfreturn  SerializeJSON(loc.response) />
</cffunction>

<cffunction name="delete" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("path", arguments))
			return error(lang("INVALID_VAR"), true);
			
		loc.fileLocation = $getRoot() & arguments.path;
		
		if (DirectoryExists(loc.fileLocation))
		{
			// delete the thumb directory
			execute(mode="delete", path=$thumbLocation(arguments.path));
			$directory(action="delete", directory=loc.fileLocation, recurse=true);
		}
		else if (FileExists(loc.fileLocation))
		{
			// delete the thumb if it exists, if it doesn't we just ignore
			if (IsImageFile(loc.fileLocation))
				execute(mode="delete", path=$thumbLocation(arguments.path));
		
			$file(action="delete", file=loc.fileLocation);
		}
		else
		{
			return error(lang("INVALID_DIRECTORY_OR_FILE"));
		}

		loc.response = {};
		loc.response["Path"] = arguments.path;
		loc.response["Error"] = "";
		loc.response["Code"] = 0;
	</cfscript>
	<cfreturn  SerializeJSON(loc.response) />
</cffunction>

<cffunction name="add" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("currentPath,upload", arguments))
			return error(lang("INVALID_VAR"), true);
			
		if (!$argumentsExist("newFile", arguments))
			return error(lang("INVALID_FILE_UPLOAD"), true);
		
		// get our config for uploads
		loc.config = $getConfig();
			
		// move the file to a new location in the temp folder so we can see it's real attributes
		loc.fileInfo = $file(action="upload", destination=GetTempDirectory(), fileField="newFile", mode=755, nameConflict=loc.config.upload.nameConflict);
		loc.fileInfo.serverPath = loc.fileInfo.serverDirectory & "/" & loc.fileInfo.serverFile;
		
		if (ListFindNoCase(loc.config.upload.exclude, loc.fileInfo.serverFileExt))
		{
			// we have a bad file so delete it and return the error
			$file(action="delete", file=loc.fileInfo.serverPath);
			return error(lang("INVALID_FILE_UPLOAD"), true);
		}
		
		// check the file size if one has been set in the config
		if (loc.config.upload.size != false && IsNumeric(loc.config.upload.size) && loc.fileInfo.fileSize gt loc.config.upload.size)
		{
			loc.uploadSizePretty = loc.config.upload.size / 1024 / 1024;
			$file(action="delete", file=loc.fileInfo.serverPath);
			return error(lang("UPLOAD_FILES_SMALLER_THAN", loc.uploadSizePretty & " MB"), true);
		}
		
		// if we are only allowing image uploads, make sure it's an image
		if (loc.config.upload.imagesOnly || (StructKeyExists(arguments, "type") && arguments.type == "images"))
		{
			if (!IsImageFile(loc.fileInfo.serverDirectory & "/" & loc.fileInfo.serverFile))
			{
				$file(action="delete", file=loc.fileInfo.serverPath);
				return error(lang("UPLOAD_IMAGES_ONLY"), true);
			}
			
			if (!ListFindNoCase(loc.config.images.extensions, loc.fileInfo.serverFileExt))
			{
				$file(action="delete", file=loc.fileInfo.serverPath);
				return error(lang("UPLOAD_IMAGES_TYPE_JPEG_GIF_PNG"), true);
			}
		}
		
		// if we are here, everything is fine so move the file to it's final destination
		loc.finalFileName = $cleanString(loc.fileInfo.serverFile, ".,-");
		loc.fileDestination = $getRoot() & arguments.currentPath & loc.finalFileName;
		
		$file(action="move", source=loc.fileInfo.serverPath, destination=loc.fileDestination, mode=755);
		
		if (loc.config.images.createThumbnail && IsImageFile(loc.fileDestination))
			execute(mode="thumbnail", path=arguments.currentPath & loc.finalFileName);
		
		loc.response = {};
		loc.response["Path"] = arguments.currentPath;
		loc.response["Name"] = loc.finalFileName;
		loc.response["Error"] = "";
		loc.response["Code"] = 0;
		
		loc.response = "<textarea>" & SerializeJSON(loc.response) & "</textarea>";
	</cfscript>
	<cfreturn loc.response />
</cffunction>

<cffunction name="thumbnail" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};

		// make sure we have are arguments
		if (!$argumentsExist("path", arguments))
			return error(lang("INVALID_VAR"));
			
		loc.config = $getConfig();
		loc.fileLocation = $getRoot() & arguments.path;
		loc.thumbPath = $thumbLocation(arguments.path);
		
		if (loc.config.images.createThumbnail && IsImageFile(loc.fileLocation))
		{
			loc.newLocation = $getRoot() & loc.thumbPath;
			loc.directory = Reverse(ListRest(Reverse(loc.newLocation), "/"));

			if (!DirectoryExists(loc.directory))
				$directory(action="create", directory=loc.directory, mode=755);
			
			loc.image = ImageRead(loc.fileLocation);
			
			// thumbs need to fit in 128x128
			ImageScaleToFit(loc.image, 128, 128, "highQuality");
			
			// write our image
			ImageWrite(loc.image, loc.newLocation);
		}

		loc.response = {};
		loc.response["Path"] = loc.thumbPath;
		loc.response["Name"] = ListLast(loc.newLocation, "/");
		loc.response["Error"] = "";
		loc.response["Code"] = 0;
	</cfscript>
	<cfreturn  SerializeJSON(loc.response) />
</cffunction>

<cffunction name="addFolder" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("path,name", arguments))
			return error(lang("INVALID_VAR"));
		
		loc.directory = $getRoot() & arguments.path & arguments.name;

		if (DirectoryExists(loc.directory))
		{
			return error(lang("DIRECTORY_ALREADY_EXISTS", arguments.name));
		}	
		try
		{
			$directory(action="create", directory=loc.directory, mode=755);
		}
		catch (Any e)
		{
			return error(lang("UNABLE_TO_CREATE_DIRECTORY"));
		}
		
		loc.response = {};
		loc.response["Parent"] = arguments.path;
		loc.response["Name"] = UrlEncodedFormat(arguments.name, "utf-8");
		loc.response["Error"] = "";
		loc.response["Code"] = 0;
	</cfscript>
	<cfreturn  SerializeJSON(loc.response) />
</cffunction>

<cffunction name="getInfo" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("path", arguments))
			return error(lang("INVALID_VAR"));
			
			
		loc.absolutePath = $getRoot() & arguments.path;
		
		if (!DirectoryExists(loc.absolutePath) && !FileExists(loc.absolutePath))
		{
			loc.errorType =  "FILE_DOES_NOT_EXIST";
			if (Right(arguments.path, 1) == "/"){
				loc.errorType = "DIRECTORY_NOT_EXIST";
			}
			return error(lang(loc.errorType, arguments.path));
		}
			
		loc.response = {};
		loc.response["Path"] = arguments.path;
		loc.response["Filename"] = ListLast(Reverse(ListRest(Reverse(arguments.path), ".")), "/");
		loc.response["File"] = ListLast(arguments.path, "/");
		loc.response["File Type"] = ListLast(arguments.path, ".");
		loc.response["Preview"] = $getFilePreview(path=arguments.path);
		loc.response["Properties"] = $getFileProperties(path=loc.absolutePath);
		loc.response["Error"] = "";
		loc.response["Code"] = 0;
	</cfscript>
	<cfreturn  SerializeJSON(loc.response) />
</cffunction>

<cffunction name="getFolder" access="public" output="false" returntype="string">
	<cfscript>
		var loc = {};
		
		// make sure we have are arguments
		if (!$argumentsExist("path,showThumbs", arguments))
			return error(lang("INVALID_VAR"));
			
		loc.response = [];
		loc.config = $getConfig();
		loc.currentPath = $getRoot() & arguments.path;
	
		if (!DirectoryExists(loc.currentPath))
			return error(lang("DIRECTORY_NOT_EXIST"));

		try
		{
			loc.contents = $directory(action="list", directory=loc.currentPath, recurse=false, type="all", sort="type ASC, name ASC");
		}
		catch (Any e)
		{
			return error(lang("UNABLE_TO_OPEN_DIRECTORY"));
		}

		for (loc.i = 1; loc.i lte loc.contents.Recordcount; loc.i++)
		{
			if (loc.contents.type[loc.i] == "dir" && !ListFindNoCase(loc.config.tree.exclude, loc.contents.name[loc.i]))
			{
				loc.relativePath = $relativePathFromRoot(loc.contents.directory[loc.i] & "/" & loc.contents.name[loc.i]) & "/";
				
				loc.temp = {};
				loc.temp["Path"] = loc.relativePath;
				loc.temp["Filename"] = ListLast(loc.relativePath, "/");
				loc.temp["File"] = ListLast(loc.relativePath, "/");
				loc.temp["File Type"] = "dir";
				loc.temp["Preview"] = loc.config.icons.path & loc.config.icons.directory;
				loc.temp["Properties"] = $getProperties();
				loc.temp["Error"] = "";
				loc.temp["Code"] = 0;
				ArrayAppend(loc.response, loc.temp);
			}
			else if (loc.contents.type[loc.i] == "file" && !ListFindNoCase(loc.config.tree.exclude, loc.contents.name[loc.i]))
			{
				loc.absolutePath = loc.contents.directory[loc.i] & "/" & loc.contents.name[loc.i];
				loc.relativePath = $relativePathFromRoot(loc.absolutePath);
				
				loc.temp = {};
				loc.temp["Path"] = loc.relativePath;
				loc.temp["Filename"] = ListLast(loc.relativePath, "/");
				loc.temp["File Type"] = ListLast(loc.relativePath, ".");
				loc.temp["Preview"] = $getFilePreview(path=loc.relativePath, thumbs=arguments.showThumbs);
				loc.temp["Properties"] = $getFileProperties(path=loc.absolutePath);
				loc.temp["Error"] = "";
				loc.temp["Code"] = 0;
				ArrayAppend(loc.response, loc.temp);
			}
		}

	</cfscript>
	<cfreturn  SerializeJSON(loc.response) />
</cffunction>

<cffunction name="error" access="public" output="false" returntype="string">
	<cfargument name="string" type="string" required="true" />
	<cfargument name="textarea" type="boolean" required="false" default="false" />
	<cfscript>
		var response = StructNew();
		response["Error"] = arguments.string;
		response["Code"] = -1;
		response["Properties"] = Duplicate(variables.class.properties);
		
		response = SerializeJSON(response);
		if (arguments.textArea)
			response = "<textarea>" & response & "</textarea>";	
	</cfscript>
	<cfreturn response />
</cffunction>