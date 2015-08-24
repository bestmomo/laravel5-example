<!-- #include file="filemanager.class.asp" -->
<%
'ASP Connector for simogeo's Filemanager (http://github.com/simogeo/Filemanager/archives/master)
'It's implemented to work with ckeditor only. Must adapt to work with other editors.
'Developed by Matheus Fraguas at Soft Seven Internet (http://www.seven.com.br)
'The filemanager class requires these components:
'Scripting.FileSystemObject used to acess the filesystem
'ADODB.Stream used to serve a file to the browser (download)
'Dundas.Upload.2 used when uploading a file to the server
'GflAx.GflAx used to get an image's dimensions
'Last update 2012-07-10


Dim mode, userPath, FileManager

Response.ContentType = "application/json"
Response.Charset = "ISO-8859-1"

' Uncomment these lines to implement session
' If Len(Session("codSite")) = 0 Then
' 	showErrorMessage("Your session has expired. Please login again.")
' End If

Set FileManager = New cFileManager

mode = Request("mode")
Select Case(lCase(mode))
	Case "getinfo":
		path = getPath("path") 'string
		'getsize = Request("getsize") 'bool
		Response.Write FileManager.GetInfo(path)

	Case "getfolder":
		path = getPath("path") 'string
		'getsizes = Request("getsizes") 'bool
		'type = Request("type") 'string optional
		Response.Write FileManager.GetFolder(path)

	Case "rename":
		oldName = getPath("old") 'string
		newName = Trim(Request("new")) 'string

		If inStr(newName,"/") > 0 Or inStr(newName,"\") > 0 Then
			showErrorMessage("Invalid name.")
		End If

		Response.Write FileManager.Rename(oldName, newName)

	Case "delete":
		path = getPath("path") 'string
		Response.Write FileManager.Delete(path)

	Case "addfolder":
		path = getPath("path") 'string
		name = Trim(Request("name")) 'string

		If inStr(name,"/") > 0 Or inStr(name,"\") > 0 Then
			showErrorMessage("Invalid name.")
		End If

		Response.Write FileManager.AddFolder(path, name)

	Case "download":
		path = getPath("path") 'string
		FileManager.Download(path)
		Response.End

	Case Else:

		'Uploading?
		If Left(lCase(Request.ServerVariables("HTTP_CONTENT_TYPE")),20) = "multipart/form-data;" Then
			Response.ContentType = "text/html"
			Response.Write "<textarea>"
			Response.Write FileManager.add()
			Response.Write "</textarea>"
		End If

End Select
Set FileManager = Nothing

Function getPath(name)
	Dim path
	path = Request(name)
	path = Trim(path)
	path = Replace(path,"\","/")
	If inStr(path,"../") > 0 Or Left(path,Len(userPath)) <> userPath Then
		showErrorMessage("Invalid path.")
	End If
	getPath = path
End Function

Sub showErrorMessage(text)
		Response.Clear
		%>
		{
			"Error": "<%=text%>",
			"Code": -1
		}
		<%
		Response.End
End Sub
%>
