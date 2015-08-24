<!---

	Filemanager Coldfusion connection configuration
	
	filemanager.config.cfm
	config for the filemanager.cfm connector
	
	@license MIT License
	@author James Gibson <james.gibson (at) liquifusion (dot) com>
	@copyright Author

--->
<cfsilent>

	<cfset config = {
		  language = "en"
	} />
	
	
	<cffunction name="authorize" access="public" output="false" returntype="boolean">
		<cfscript>
			// You can insert you own code here to check if the user is authorized.
			// If you return true by default, everyone on the web will have access
			// to your servers file system.
			var returnValue = false;
		</cfscript>
		<cfreturn returnValue />
	</cffunction>
	
	<!--- icon settings --->
	<cfset config.icons = {
		  path = "images/fileicons/"
		, directory = "_Open.png"
		, default = "default.png"
	} />
	
	<!--- upload settings --->
	<cfset config.upload = {
		  nameConflict = "overwrite"
		, size = false 
		, imagesOnly = false
		, exclude = "cfm,cfml,cfc,dbm,jsp,asp,aspx,exe,php,cgi,shtml,rb,msi"
	} />
	
	<!--- allowed image file types --->
	<cfset config.images = {
		  createThumbnail = true
		, thumbnailFolder = "_thumbs"
		, extensions = "jpg,jpeg,gif,png"
	} />
	
	<!--- files and folders to exclude from the tree view --->
	<cfset config.tree = {
		  exclude = ".htaccess,_thumbs"
	} />
	
	<!--- root folder to use, do not include an ending slash --->
	<cfset config.base = "userfiles" />
	
	<!--- plugins to execute when the coldfusion file manager is run --->
	<cfset config.plugins = [] />
	
</cfsilent>
