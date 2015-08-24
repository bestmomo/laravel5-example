// Filemanager ASP.NET MVC connector
// Author: David Hammond <dave@modernsignal.com>
// Based on ASHX connection by Ondřej "Yumi Yoshimido" Brožek | <cholera@hzspraha.cz>

using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.IO;
using System.Linq;
using System.Text;
using System.Text.RegularExpressions;
using System.Web;
using System.Web.Configuration;
using System.Web.Script.Serialization;
using System.Web.Mvc;

namespace MyProject.Areas.FilemanagerArea.Controllers
{
    /// <summary>
    /// Filemanager controller
    /// </summary>
    public class FilemanagerController : Controller
    {
        /// <summary>
        /// Root directory for all file uploads [string]
        /// Set in web.config. E.g. <add key="Filemanager_RootPath" value="/uploads/"/>
        /// </summary>
        private string RootPath = WebConfigurationManager.AppSettings["Filemanager_RootPath"]; // Root directory for all file uploads [string]

        /// <summary>
        /// Directory for icons. [string]
        /// Set in web.config E.g. <add key="Filemanager_IconDirectory" value="/Scripts/filemanager/images/fileicons/"/>
        /// </summary>
        private string IconDirectory = WebConfigurationManager.AppSettings["Filemanager_IconDirectory"]; // Icon directory for filemanager. [string]

        /// <summary>
        /// White list of allowed file extensions
        /// </summary>
        private List<string> allowedExtensions = new List<string> { ".ai", ".asx", ".avi", ".bmp", ".csv", ".dat", ".doc", ".docx", ".epub", ".fla", ".flv", ".gif", ".html", ".ico", ".jpeg", ".jpg", ".m4a", ".mobi", ".mov", ".mp3", ".mp4", ".mpa", ".mpg", ".mpp", ".pdf", ".png", ".pps", ".ppsx", ".ppt", ".pptx", ".ps", ".psd", ".qt", ".ra", ".ram", ".rar", ".rm", ".rtf", ".svg", ".swf", ".tif", ".txt", ".vcf", ".vsd", ".wav", ".wks", ".wma", ".wmv", ".wps", ".xls", ".xlsx", ".xml", ".zip" }; // Only allow these extensions to be uploaded

        /// <summary>
        /// List of image file extensions
        /// </summary>
        private List<string> imgExtensions = new List<string> { ".jpg", ".png", ".jpeg", ".gif", ".bmp" }; // Only allow this image extensions. [string]

        /// <summary>
        /// Serializer for generating json responses
        /// </summary>
        private JavaScriptSerializer json = new JavaScriptSerializer();

        /// <summary>
        /// Process file manager action
        /// </summary>
        /// <param name="mode"></param>
        /// <param name="path"></param>
        /// <returns></returns>
        [Authorize()]
        public ActionResult Index(string mode, string path = null)
        {
            Response.ClearHeaders();
            Response.ClearContent();
            Response.Clear();

            try
            {
                switch (mode)
                {
                    case "getinfo":
                        return Content(GetInfo(path), "application/json", Encoding.UTF8);
                    case "getfolder":
                        return Content(GetFolderInfo(path), "application/json", Encoding.UTF8);
                    case "move":
                        var oldPath = Request.QueryString["old"];
                        var newPath = string.Format("{0}{1}/{2}", Request.QueryString["root"], Request.QueryString["new"], Path.GetFileName(oldPath));
                        return Content(Move(oldPath, newPath), "application/json", Encoding.UTF8);
                    case "rename":
                        return Content(Rename(Request.QueryString["old"], Request.QueryString["new"]), "application/json", Encoding.UTF8);
                    case "replace":
                        return Content(Replace(Request.Form["newfilepath"]), "text/html", Encoding.UTF8);
                    case "delete":
                        return Content(Delete(path), "application/json", Encoding.UTF8);
                    case "addfolder":
                        return Content(AddFolder(path, Request.QueryString["name"]), "application/json", Encoding.UTF8);
                    case "download":
                        if (System.IO.File.Exists(Server.MapPath(path)) && IsInRootPath(path))
                        {
                            FileInfo fi = new FileInfo(Server.MapPath(path));
                            Response.AddHeader("Content-Disposition", "attachment; filename=" + Server.UrlPathEncode(path));
                            Response.AddHeader("Content-Length", fi.Length.ToString());
                            return File(fi.FullName, "application/octet-stream");
                        }
                        else
                        {
                            return new HttpNotFoundResult("File not found");
                        }
                    case "add":
                        return Content(AddFile(Request.Form["currentpath"]), "text/html", Encoding.UTF8);
                    case "preview":
                        var fi2 = new FileInfo(Server.MapPath(Request.QueryString["path"]));
                        return new FilePathResult(fi2.FullName, "image/" + fi2.Extension.TrimStart('.'));
                    default:
                        return Content("");
                }
            }
            catch (HttpException he)
            {
                return Content(Error(he.Message), "application/json", Encoding.UTF8);
            }
        }

        //===================================================================
        //========================== END EDIT ===============================
        //===================================================================       

        /// <summary>
        /// Is the file an image file
        /// </summary>
        /// <param name="fileInfo"></param>
        /// <returns></returns>
        private bool IsImage(FileInfo fileInfo)
        {
            return imgExtensions.Contains(Path.GetExtension(fileInfo.FullName).ToLower());
        }

        /// <summary>
        /// Is the file in the root path?  Don't allow uploads outside the root path.
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        private bool IsInRootPath(string path)
        {
            return path != null && Path.GetFullPath(path).StartsWith(Path.GetFullPath(RootPath));
        }

        /// <summary>
        /// Add a file
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        private string AddFile(string path)
        {
            string response;

            if (Request.Files.Count == 0 || Request.Files[0].ContentLength == 0)
            {
                response = Error("No file provided.");
            }
            else
            {
                if (!IsInRootPath(path))
                {
                    response = Error("Attempt to add file outside root path");
                }
                else
                {
                    System.Web.HttpPostedFileBase file = Request.Files[0];
                    if (!allowedExtensions.Contains(Path.GetExtension(file.FileName).ToLower()))
                    {
                        response = Error("Uploaded file type is not allowed.");
                    }
                    else
                    {
                        //Only allow certain characters in file names
                        var baseFileName = Regex.Replace(Path.GetFileNameWithoutExtension(file.FileName), @"[^\w_-]", "");
                        var filePath = Path.Combine(path, baseFileName + Path.GetExtension(file.FileName));

                        //Make file name unique
                        var i = 0;
                        while (System.IO.File.Exists(Server.MapPath(filePath)))
                        {
                            i = i + 1;
                            baseFileName = Regex.Replace(baseFileName, @"_[\d]+$", "");
                            filePath = Path.Combine(path, baseFileName + "_" + i + Path.GetExtension(file.FileName));
                        }
                        file.SaveAs(Server.MapPath(filePath));

                        response = json.Serialize(new
                        {
                            Path = path,
                            Name = Path.GetFileName(file.FileName),
                            Error = "No error",
                            Code = 0
                        });
                    }
                }
            }
            return "<textarea>" + response + "</textarea>";
        }

        /// <summary>
        /// Add a folder
        /// </summary>
        /// <param name="path"></param>
        /// <param name="newFolder"></param>
        /// <returns></returns>
        private string AddFolder(string path, string newFolder)
        {
            if (!IsInRootPath(path))
            {
                return Error("Attempt to add folder outside root path");
            }

            StringBuilder sb = new StringBuilder();
            Directory.CreateDirectory(Path.Combine(Server.MapPath(path), newFolder));

            sb.AppendLine("{");
            sb.AppendLine("\"Parent\": \"" + path + "\",");
            sb.AppendLine("\"Name\": \"" + newFolder + "\",");
            sb.AppendLine("\"Error\": \"No error\",");
            sb.AppendLine("\"Code\": 0");
            sb.AppendLine("}");

            return sb.ToString();
        }

        /// <summary>
        /// Delete a file
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        private string Delete(string path)
        {
            if (!IsInRootPath(path))
            {
                return Error("Attempt to delete file outside root path");
            }
            if (!System.IO.File.Exists(Server.MapPath(path)) && !Directory.Exists(Server.MapPath(path)))
            {
                return Error("File not found");
            }

            FileAttributes attr = System.IO.File.GetAttributes(Server.MapPath(path));

            StringBuilder sb = new StringBuilder();

            if ((attr & FileAttributes.Directory) == FileAttributes.Directory)
            {
                Directory.Delete(Server.MapPath(path), true);
            }
            else
            {
                System.IO.File.Delete(Server.MapPath(path));
            }

            sb.AppendLine("{");
            sb.AppendLine("\"Error\": \"No error\",");
            sb.AppendLine("\"Code\": 0,");
            sb.AppendLine("\"Path\": \"" + path + "\"");
            sb.AppendLine("}");

            return sb.ToString();
        }

        /// <summary>
        /// Generate json for error message
        /// </summary>
        /// <param name="msg"></param>
        /// <returns></returns>
        private string Error(string msg)
        {
            return json.Serialize(new
            {
                Error = msg,
                Code = -1
            });
        }

        /// <summary>
        /// Get folder information
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        private string GetFolderInfo(string path)
        {
            if (!IsInRootPath(path))
            {
                return Error("Attempt to view files outside root path");
            }
            if (!Directory.Exists(Server.MapPath(path)))
            {
                return Error("Directory not found");
            }

            DirectoryInfo RootDirInfo = new DirectoryInfo(Server.MapPath(path));
            StringBuilder sb = new StringBuilder();

            sb.AppendLine("{");

            int i = 0;

            foreach (DirectoryInfo DirInfo in RootDirInfo.GetDirectories())
            {
                if (i > 0)
                {
                    sb.Append(",");
                    sb.AppendLine();
                }

                sb.AppendLine("\"" + Path.Combine(path, DirInfo.Name) + "\": {");
                sb.AppendLine("\"Path\": \"" + Path.Combine(path, DirInfo.Name) + "/\",");
                sb.AppendLine("\"Filename\": \"" + DirInfo.Name + "\",");
                sb.AppendLine("\"File Type\": \"dir\",");
                sb.AppendLine("\"Preview\": \"" + IconDirectory + "_Open.png\",");
                sb.AppendLine("\"Properties\": {");
                sb.AppendLine("\"Date Created\": \"" + DirInfo.CreationTime.ToString() + "\", ");
                sb.AppendLine("\"Date Modified\": \"" + DirInfo.LastWriteTime.ToString() + "\", ");
                sb.AppendLine("\"Height\": 0,");
                sb.AppendLine("\"Width\": 0,");
                sb.AppendLine("\"Size\": 0 ");
                sb.AppendLine("},");
                sb.AppendLine("\"Error\": \"\",");
                sb.AppendLine("\"Code\": 0	");
                sb.Append("}");

                i++;
            }

            foreach (FileInfo fileInfo in RootDirInfo.GetFiles())
            {
                if (i > 0)
                {
                    sb.Append(",");
                    sb.AppendLine();
                }

                sb.AppendLine("\"" + Path.Combine(path, fileInfo.Name) + "\": {");
                sb.AppendLine("\"Path\": \"" + Path.Combine(path, fileInfo.Name) + "\",");
                sb.AppendLine("\"Filename\": \"" + fileInfo.Name + "\",");
                sb.AppendLine("\"File Type\": \"" + fileInfo.Extension.Replace(".", "") + "\",");

                if (IsImage(fileInfo))
                {
                    sb.AppendLine("\"Preview\": \"" + Path.Combine(path, fileInfo.Name) + "?" + fileInfo.LastWriteTime.Ticks.ToString() + "\",");
                }
                else
                {
                    var icon = String.Format("{0}{1}.png", IconDirectory, fileInfo.Extension.Replace(".", ""));
                    if (!System.IO.File.Exists(Server.MapPath(icon)))
                    {
                        icon = String.Format("{0}default.png", IconDirectory);
                    }
                    sb.AppendLine("\"Preview\": \"" + icon + "\",");
                }

                sb.AppendLine("\"Properties\": {");
                sb.AppendLine("\"Date Created\": \"" + fileInfo.CreationTime.ToString() + "\", ");
                sb.AppendLine("\"Date Modified\": \"" + fileInfo.LastWriteTime.ToString() + "\", ");

                if (IsImage(fileInfo))
                {
                    using (System.Drawing.Image img = System.Drawing.Image.FromFile(fileInfo.FullName))
                    {
                        sb.AppendLine("\"Height\": " + img.Height.ToString() + ",");
                        sb.AppendLine("\"Width\": " + img.Width.ToString() + ",");
                    }
                }

                sb.AppendLine("\"Size\": " + fileInfo.Length.ToString() + " ");
                sb.AppendLine("},");
                sb.AppendLine("\"Error\": \"\",");
                sb.AppendLine("\"Code\": 0	");
                sb.Append("}");

                i++;
            }

            sb.AppendLine();
            sb.AppendLine("}");

            return sb.ToString();
        }

        /// <summary>
        /// Get file information
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        private string GetInfo(string path)
        {
            if (!IsInRootPath(path))
            {
                return Error("Attempt to view file outside root path");
            }
            if (!System.IO.File.Exists(Server.MapPath(path)) && !Directory.Exists(Server.MapPath(path)))
            {
                return Error("File not found");
            }

            StringBuilder sb = new StringBuilder();

            FileAttributes attr = System.IO.File.GetAttributes(Server.MapPath(path));

            if ((attr & FileAttributes.Directory) == FileAttributes.Directory)
            {
                DirectoryInfo DirInfo = new DirectoryInfo(Server.MapPath(path));

                sb.AppendLine("{");
                sb.AppendLine("\"Path\": \"" + path + "\",");
                sb.AppendLine("\"Filename\": \"" + DirInfo.Name + "\",");
                sb.AppendLine("\"File Type\": \"dir\",");
                sb.AppendLine("\"Preview\": \"" + IconDirectory + "_Open.png\",");
                sb.AppendLine("\"Properties\": {");
                sb.AppendLine("\"Date Created\": \"" + DirInfo.CreationTime.ToString() + "\", ");
                sb.AppendLine("\"Date Modified\": \"" + DirInfo.LastWriteTime.ToString() + "\", ");
                sb.AppendLine("\"Height\": 0,");
                sb.AppendLine("\"Width\": 0,");
                sb.AppendLine("\"Size\": 0 ");
                sb.AppendLine("},");
                sb.AppendLine("\"Error\": \"\",");
                sb.AppendLine("\"Code\": 0	");
                sb.AppendLine("}");
            }
            else
            {
                FileInfo fileInfo = new FileInfo(Server.MapPath(path));

                sb.AppendLine("{");
                sb.AppendLine("\"Path\": \"" + path + "\",");
                sb.AppendLine("\"Filename\": \"" + fileInfo.Name + "\",");
                sb.AppendLine("\"File Type\": \"" + fileInfo.Extension.Replace(".", "") + "\",");

                if (IsImage(fileInfo))
                {
                    sb.AppendLine("\"Preview\": \"" + path + "?" + fileInfo.LastWriteTime.Ticks.ToString() + "\",");
                }
                else
                {
                    sb.AppendLine("\"Preview\": \"" + String.Format("{0}{1}.png", IconDirectory, fileInfo.Extension.Replace(".", "")) + "\",");
                }

                sb.AppendLine("\"Properties\": {");
                sb.AppendLine("\"Date Created\": \"" + fileInfo.CreationTime.ToString() + "\", ");
                sb.AppendLine("\"Date Modified\": \"" + fileInfo.LastWriteTime.ToString() + "\", ");

                if (IsImage(fileInfo))
                {
                    using (System.Drawing.Image img = System.Drawing.Image.FromFile(Server.MapPath(path)))
                    {
                        sb.AppendLine("\"Height\": " + img.Height.ToString() + ",");
                        sb.AppendLine("\"Width\": " + img.Width.ToString() + ",");
                    }
                }

                sb.AppendLine("\"Size\": " + fileInfo.Length.ToString() + " ");
                sb.AppendLine("},");
                sb.AppendLine("\"Error\": \"\",");
                sb.AppendLine("\"Code\": 0	");
                sb.AppendLine("}");
            }

            return sb.ToString();

        }


        private string Move(string oldPath, string newPath)
        {
            if (!IsInRootPath(oldPath))
            {
                return Error("Attempt to modify file outside root path");
            }
            else if (!IsInRootPath(newPath))
            {
                return Error("Attempt to move a file outside root path");
            }
            else if (!System.IO.File.Exists(Server.MapPath(oldPath)) && !Directory.Exists(Server.MapPath(oldPath)))
            {
                return Error("File not found");
            }

            FileAttributes attr = System.IO.File.GetAttributes(Server.MapPath(oldPath));

            StringBuilder sb = new StringBuilder();

            if ((attr & FileAttributes.Directory) == FileAttributes.Directory)
            {
                DirectoryInfo oldDir = new DirectoryInfo(Server.MapPath(oldPath));
                newPath = Path.Combine(newPath, oldDir.Name);
                Directory.Move(Server.MapPath(oldPath), Server.MapPath(newPath));
                DirectoryInfo newDir = new DirectoryInfo(Server.MapPath(newPath));

                sb.AppendLine("{");
                sb.AppendLine("\"Error\": \"No error\",");
                sb.AppendLine("\"Code\": 0,");
                sb.AppendLine("\"Old Path\": \"" + oldPath + "\",");
                sb.AppendLine("\"Old Name\": \"" + oldDir.Name + "\",");
                sb.AppendLine("\"New Path\": \"" + newDir.FullName.Replace(HttpRuntime.AppDomainAppPath, "/").Replace(Path.DirectorySeparatorChar, '/') + "\",");
                sb.AppendLine("\"New Name\": \"" + newDir.Name + "\"");
                sb.AppendLine("}");
            }
            else
            {
                FileInfo oldFile = new FileInfo(Server.MapPath(oldPath));
                FileInfo newFile = new FileInfo(Server.MapPath(newPath));
                if (newFile.Extension != oldFile.Extension)
                {
                    //Don't allow extension to be changed
                    newFile = new FileInfo(Path.ChangeExtension(newFile.FullName, oldFile.Extension));
                }
                System.IO.File.Move(oldFile.FullName, newFile.FullName);

                sb.AppendLine("{");
                sb.AppendLine("\"Error\": \"No error\",");
                sb.AppendLine("\"Code\": 0,");
                sb.AppendLine("\"Old Path\": \"" + oldPath.Replace(oldFile.Name, "") + "\",");
                sb.AppendLine("\"Old Name\": \"" + oldFile.Name + "\",");
                sb.AppendLine("\"New Path\": \"" + newFile.FullName.Replace(HttpRuntime.AppDomainAppPath, "/").Replace(Path.DirectorySeparatorChar, '/') + "\",").Replace(newFile.Name, "");
                sb.AppendLine("\"New Name\": \"" + newFile.Name + "\"");
                sb.AppendLine("}");
            }

            return sb.ToString();
        }

        /// <summary>
        /// Rename a file or directory
        /// </summary>
        /// <param name="path"></param>
        /// <param name="newName"></param>
        /// <returns></returns>
        private string Rename(string path, string newName)
        {
            if (!IsInRootPath(path))
            {
                return Error("Attempt to modify file outside root path");
            }
            if (!System.IO.File.Exists(Server.MapPath(path)) && !Directory.Exists(Server.MapPath(path)))
            {
                return Error("File not found");
            }

            FileAttributes attr = System.IO.File.GetAttributes(Server.MapPath(path));

            StringBuilder sb = new StringBuilder();

            if ((attr & FileAttributes.Directory) == FileAttributes.Directory)
            {
                DirectoryInfo oldDir = new DirectoryInfo(Server.MapPath(path));
                Directory.Move(Server.MapPath(path), Path.Combine(oldDir.Parent.FullName, newName));
                DirectoryInfo newDir = new DirectoryInfo(Path.Combine(oldDir.Parent.FullName, newName));

                sb.AppendLine("{");
                sb.AppendLine("\"Error\": \"No error\",");
                sb.AppendLine("\"Code\": 0,");
                sb.AppendLine("\"Old Path\": \"" + path + "\",");
                sb.AppendLine("\"Old Name\": \"" + oldDir.Name + "\",");
                sb.AppendLine("\"New Path\": \"" + newDir.FullName.Replace(HttpRuntime.AppDomainAppPath, "/").Replace(Path.DirectorySeparatorChar, '/') + "\",");
                sb.AppendLine("\"New Name\": \"" + newDir.Name + "\"");
                sb.AppendLine("}");
            }
            else
            {
                FileInfo oldFile = new FileInfo(Server.MapPath(path));
                //Don't allow extension to be changed
                newName = Path.GetFileNameWithoutExtension(newName) + oldFile.Extension;
                FileInfo newFile = new FileInfo(Path.Combine(oldFile.Directory.FullName, newName));
                System.IO.File.Move(oldFile.FullName, newFile.FullName);

                sb.AppendLine("{");
                sb.AppendLine("\"Error\": \"No error\",");
                sb.AppendLine("\"Code\": 0,");
                sb.AppendLine("\"Old Path\": \"" + path + "\",");
                sb.AppendLine("\"Old Name\": \"" + oldFile.Name + "\",");
                sb.AppendLine("\"New Path\": \"" + newFile.FullName.Replace(HttpRuntime.AppDomainAppPath, "/").Replace(Path.DirectorySeparatorChar, '/') + "\",");
                sb.AppendLine("\"New Name\": \"" + newFile.Name + "\"");
                sb.AppendLine("}");
            }

            return sb.ToString();
        }

        /// <summary>
        /// Replace a file
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        private string Replace(string path)
        {
            if (Request.Files.Count == 0 || Request.Files[0].ContentLength == 0)
            {
                return Error("No file provided.");
            }
            else if (!IsInRootPath(path))
            {
                return Error("Attempt to replace file outside root path");
            }
            else
            {
                var fi = new FileInfo(Server.MapPath(path));
                HttpPostedFileBase file = Request.Files[0];
                if (!allowedExtensions.Contains(Path.GetExtension(file.FileName).ToLower()))
                {
                    return Error("Uploaded file type is not allowed.");
                }
                else if (!Path.GetExtension(file.FileName).Equals(fi.Extension))
                {
                    return Error("Replacement file must have the same extension as the file being replaced.");
                }
                else if (!fi.Exists)
                {
                    return Error("File to replace not found.");
                }
                else
                {
                    file.SaveAs(fi.FullName);

                    return "<textarea>" + json.Serialize(new
                    {
                        Path = path.Replace("/" + fi.Name, ""),
                        Name = fi.Name,
                        Error = "No error",
                        Code = 0
                    }) + "</textarea>";
                }
            }
        }
    }
}