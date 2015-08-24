<!-- #include file="filemanager.config.asp" -->
<%
Class cFileManager

	Private userPath
	Private fs
	Private upl
	Private objStream
	Private objImage

	Private Sub Class_Initialize()
		Set fs = Server.CreateObject("Scripting.FileSystemObject")
		Set objStream = Server.CreateObject("ADODB.Stream")
		Set upl = Server.CreateObject("Dundas.Upload.2")
		userPath = ""
		If enableImageHandle Then
			Set objImage = Server.CreateObject("GflAx.GflAx")
		End If
	End Sub

	Private Sub Class_Terminate()
		Set fs = Nothing
		Set objStream = Nothing
		Set upl = Nothing
		If enableImageHandle Then
			Set objImage = Nothing
		End If
	End Sub


	Private Function isFolder(path)
		isFolder = (Right(path,1) = "/")
	End Function

	Private Function returnError(message)
		ReturnError = "		{" & vbCrLf & _
					"			Error: ""Error: " & message & """," & vbCrLf & _
					"			Code: -1" & vbCrLf & _
					"		}" & vbCrLf
	End Function

	Private Function getImageProp(path, byRef width, byRef height)
		getImageProp = False
		If enableImageHandle Then
			objImage.LoadBitmap Server.MapPath(path)
			width = objImage.Width
			height = objImage.Height
			getImageProp = True
		End If
	End Function

	Private Function isImageExt(ext)
		For x = 0 To uBound(imgExtensions)
			If ext = imgExtensions(x) Then
				isImageExt = True
				Exit Function
			End If
		Next
		isImageExt = False
	End Function

	Private Function getFileInfo(path, file)
		Dim strFileProp, fileExt, preview, width, height

		'get file type
		fileExt = lCase(Split(file,".")(uBound(Split(file,"."))))

		strFileProp = strFileProp & "		""Path"": """ & path & """," & vbCrLf
		strFileProp = strFileProp & "		""Filename"": """ & file.Name & """," & vbCrLf
		strFileProp = strFileProp & "		""File Type"": ""jpg""," & vbCrLf

		If isImageExt(fileExt) Then
			preview = userPath & path
		Else
			Select Case fileExt
				Case "txt", "rtf": preview = "txt"
				Case "zip": preview = "zip"
				Case "doc", "docx": preview = "doc"
				Case "xls", "xlsx", "docx": preview = "xls"
				Case "pdf": preview = "pdf"
				Case "swf": preview = "swf"
				Case "htm", "html": preview = "htm"
				Case "wav", "mp3", "wma", "mid": preview = "other_music"
				Case "avi", "mpg", "mpeg", "wmv", "mp4", "mov", "swf": preview = "other_movie"
				Case Else: preview = "default"
			End Select
			preview = ckeditorPath & "images/fileicons/" & preview & ".png"
		End If

		strFileProp = strFileProp & "		""Preview"": """ & preview & """," & vbCrLf
		strFileProp = strFileProp & "		""Properties"": {" & vbCrLf
		strFileProp = strFileProp & "			""Date Created"": null, " & vbCrLf
		strFileProp = strFileProp & "			""Date Modified"": """ & file.DateLastModified & """, " & vbCrLf
		If isImageExt(fileExt) And enableImageHandle Then
			If getImageProp(path, width, height) Then
				strFileProp = strFileProp & "			""Height"": " & height & "," & vbCrLf
				strFileProp = strFileProp & "			""Width"": " & width & "," & vbCrLf
			End If
		End If
		strFileProp = strFileProp & "			""Size"": " & file.size & " " & vbCrLf
		strFileProp = strFileProp & "		}," & vbCrLf
		strFileProp = strFileProp & "		""Error"": """"," & vbCrLf
		strFileProp = strFileProp & "		""Code"": 0" & vbCrLf
		getFileInfo = strFileProp
	End Function

	Private Function getFolderInfo(path, folder)
		Dim strFileProp
		strFileProp = strFileProp & "		""Path"": """ & path & """," & vbCrLf
		strFileProp = strFileProp & "		""Filename"": """ & folder.Name & """," & vbCrLf
		strFileProp = strFileProp & "		""File Type"": ""dir""," & vbCrLf
		strFileProp = strFileProp & "		""Preview"": """ & ckeditorPath & "images/fileicons/_Close.png""," & vbCrLf
		strFileProp = strFileProp & "		""Properties"": {" & vbCrLf
		strFileProp = strFileProp & "			""Date Created"": null, " & vbCrLf
		strFileProp = strFileProp & "			""Date Modified"": """ & folder.DateLastModified & """, " & vbCrLf
		strFileProp = strFileProp & "			""Size"": " & folder.size & " " & vbCrLf
		strFileProp = strFileProp & "		}," & vbCrLf
		strFileProp = strFileProp & "		""Error"": """"," & vbCrLf
		strFileProp = strFileProp & "		""Code"": 0" & vbCrLf
		getFolderInfo = strFileProp
	End Function



	Public Function GetInfo(path)
		Dim file, strFileProp

		strFileProp = strFileProp & "{" & vbCrLf
		On Error Resume Next
		If isFolder(path) Then
			Set file = fs.GetFolder(Server.MapPath(userPath + path))
			strFileProp = strFileProp & getFolderInfo(path, file)
		Else
			Set file = fs.GetFile(Server.MapPath(userPath + path))
			strFileProp = strFileProp & getFileInfo(path, file)
		End If
		If Err.Number <> 0 Then
			GetInfo = returnError ("Can't open folder or path")
			Exit Function
		End If
		On Error Goto 0

		strFileProp = strFileProp & "}" & vbCrLf
		Set file = Nothing

		GetInfo = strFileProp
	End Function


	Public Function GetFolder(path)
		Dim folder
		Dim arrFileProp()
		Dim count

'		On Error Resume Next

		Set folder = fs.GetFolder(Server.MapPath(userPath + path))

		ReDim arrFileProp(folder.subfolders.Count + folder.files.Count - 1)

		count = 0

		'loop folders
		For Each item in folder.subfolders
			arrFileProp(count) = "	""" & path & item.Name & "/"": {" & vbCrLf & _
								getFolderInfo(path & item.Name & "/", item) & _
								"}" & vbCrLf
			count = count + 1
		Next

		'loop files
		For Each item in folder.files
			arrFileProp(count) = "	""" & path & item.Name & """: {" & vbCrLf  & _
								getFileInfo(path & item.name, item) & _
								"}" & vbCrLf
			count = count + 1
		Next

		If Err.Number <> 0 Then
			GetFolder = returnError ("Can't open folder")
			Exit Function
		End If

		On Error Goto 0

		GetFolder = "{" & vbCrLf & Join(arrFileProp,",") & vbCrLf & "}"
	End Function


	Public Function AddFolder(path, name)
		Dim newPath

		newPath = Server.MapPath(userPath & path & name)

		If fs.FolderExists(newPath) Then
			AddFolder = returnError ("Folder already exists")
		Else
			On Error Resume Next
			fs.CreateFolder(newPath)

			If Err.Number <> 0 Then
				AddFolder = returnError ("Can't create folder")
				Exit Function
			End If

			On Error Goto 0

			AddFolder = "		{" & vbCrLf &_
					"			""Parent"": """ & path & """," & vbCrLf &_
					"			""Name"": """ & name & """," & vbCrLf &_
					"			""Error"": ""No error""," & vbCrLf &_
					"			""Code"": 0" & vbCrLf &_
					"		}" & vbCrLf
		End If

	End Function

	Public Function Rename(oldName, newName)
		Dim item, arrPath, originalName, strReturn

		arrPath = Split(oldName,"/")
		On Error Resume Next
		If isFolder(oldName) Then
			Set item = fs.GetFolder(Server.MapPath(userPath + oldName))
			ReDim Preserve arrPath(uBound(arrPath)-2)
		Else
			Set item = fs.GetFile(Server.MapPath(userPath + oldName))
			ReDim Preserve arrPath(uBound(arrPath)-1)
		End If

		originalName = item.name
		item.Move(item.ParentFolder.Path & "\" & newName)

		If Err.Number <> 0 Then
			Rename = returnError ("Can't rename folder or file")
			Exit Function
		End If
		On Error Goto 0

        Rename = "		{" & vbCrLf &_
				"			""Error"": ""No error""," & vbCrLf &_
				"			""Code"": 0," & vbCrLf &_
				"			""Old Path"": """ & oldName & """," & vbCrLf &_
				"			""Old Name"": """ & originalName & """," & vbCrLf &_
				"			""New Path"": """ & Join(arrPath,"/") & "/" & newName & "/""," & vbCrLf &_
				"			""New Name"": """ & newName & """" & vbCrLf &_
				"		}" & vbCrLf
	End Function


	Public Function Add()
		Dim path, name

		upl.SaveToMemory

		If lCase(upl.Form("mode")) <> "add" Then
			Exit Function
		End If

		Set oFile = upl.Files(0)
		fileName = oFile.OriginalPath

		path = upl.Form("currentpath") 'string
		'name = upl.Form("name") 'string

		On Error Resume Next
		oFile.SaveAs Server.MapPath(userPath & path & fileName)

		If Err.Number <> 0 Then
			Add = returnError ("Can't save file")
			Exit Function
		End If
		On Error Goto 0

        Add = "		{" & vbCrLf &_
				"			""Path"": """ & path & """," & vbCrLf &_
				"			""Name"": """ & fileName & """," & vbCrLf &_
				"			""Error"": ""No error""," & vbCrLf &_
				"			""Code"": 0" & vbCrLf &_
				"		}" & vbCrLf
	End Function


	Public Function Delete(path)
		Dim item

		On Error Resume Next
		If isFolder(path) Then
			Set item = fs.GetFolder(Server.MapPath(userPath + path))
		Else
			Set item = fs.GetFile(Server.MapPath(userPath + path))
		End If
		item.Delete(True)

		If Err.Number <> 0 Then
			Delete = returnError ("Can't remove folder or file")
			Exit Function
		End If

		On Error Goto 0

        Delete = "		{" & vbCrLf &_
				"			""Error"": ""No error""," & vbCrLf &_
				"			""Code"": 0," & vbCrLf &_
				"			""Path"": """ & path & """" & vbCrLf &_
				"		}" & vbCrLf
	End Function

	Public Sub Download(path)
		Dim item

    	Server.ScriptTimeout = 30000

		Response.Clear
		Response.ContentType = "application/x-download"
		Set item = fs.GetFile(Server.MapPath(userPath & path))
		Response.AddHeader "Content-Disposition", "attachment; filename=" & item.Name
'		Response.AddHeader "Content-Length", item.Size
		Set item = Nothing

		Response.Buffer = False

		Set objStream = Server.CreateObject("ADODB.Stream")
		objStream.Open
		objStream.Type = 1
		objStream.LoadFromFile(Server.MapPath(userPath + path))
		Response.BinaryWrite(objStream.Read)
		objStream.Close
		Response.End

	End Sub

End Class
%>