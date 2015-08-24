/*
 *	Filemanager.java utility class for for filemanager.jsp
 *
 *	@license	MIT License
 *	@author		Dick Toussaint <d.tricky@gmail.com>
 *	@copyright	Authors
 */
package com.nartex;

import java.awt.Dimension;
import java.awt.Image;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Properties;

import javax.servlet.ServletContext;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.swing.ImageIcon;

import org.apache.commons.fileupload.FileItem;
import org.apache.commons.fileupload.FileItemFactory;
import org.apache.commons.fileupload.disk.DiskFileItemFactory;
import org.apache.commons.fileupload.servlet.ServletFileUpload;
import org.json.JSONException;
import org.json.JSONObject;

public class FileManager {

	protected static Properties config = null;
	protected static JSONObject language = null;
	protected Map<String, String>  get =  new HashMap<String, String>();
	protected Map<String, String>  properties =  new HashMap<String, String>();
	protected Map item =  new HashMap();
	protected Map<String, String>  params =  new HashMap<String, String>();
	protected String documentRoot = "";
	protected String fileManagerRoot = "";
	protected String referer = "";
	
	protected JSONObject error = null; 

	SimpleDateFormat dateFormat; 
	List files = null;
	
	public FileManager(ServletContext servletContext, HttpServletRequest request){
		// get document root like in php
		String contextPath = request.getContextPath();
		String documentRoot = servletContext.getRealPath("/").replaceAll("\\\\", "/");
		documentRoot = documentRoot.substring(0, documentRoot.indexOf(contextPath));
	    
		this.referer = request.getHeader("referer");
		this.fileManagerRoot = documentRoot + referer.substring(referer.indexOf(contextPath), referer.indexOf("index.html"));

	    // get uploaded file list
		FileItemFactory factory = new DiskFileItemFactory();
	    ServletFileUpload upload = new ServletFileUpload(factory);
	    if (ServletFileUpload.isMultipartContent(request))
	    	try {
		    	files = upload.parseRequest(request);
			} catch (Exception e) { // no error handling}
		}

		this.properties.put("Date Created", null);
		this.properties.put("Date Modified", null);
		this.properties.put("Height", null);
		this.properties.put("Width", null);
		this.properties.put("Size", null);
		
		// load config file
		loadConfig();
		
	    if(config.getProperty("doc_root") != null)
	    	this.documentRoot = config.getProperty("doc_root");
	    else
	    	this.documentRoot = documentRoot;
	    
	    dateFormat = new SimpleDateFormat(config.getProperty("date"));

	    this.setParams();
		
		loadLanguageFile();
	}

	public JSONObject error(String msg) {
		JSONObject errorInfo = new JSONObject();
		try {
			errorInfo.put("Error", msg);
			errorInfo.put("Code", "-1");
			errorInfo.put("Properties", this.properties);			
		} catch (JSONException e) {
			this.error("JSONObject error");
		}
		this.error = errorInfo;
		return error;
	}
	
	public JSONObject getError(){
		return error;
	}
	public String lang(String key) {
		String text = "";
		try {
			text = language.getString(key);			
		} catch (Exception e) {}
		if (text == null || text == "")
			text = "Language string error on " + key;
		return text;
	}
	
	public boolean setGetVar(String var, String value) {
		boolean retval = false;
		if(value == null || value == "") {
			this.error(sprintf(lang("INVALID_VAR"), var));
		} else {
			this.get.put(var, sanitize(value));
			retval = true;
		}
		return retval;
	}

	public JSONObject getInfo() {
		this.item = new HashMap();
		this.item.put("properties", this.properties);
		this.getFileInfo("");
		JSONObject array = new JSONObject();
		
		try {
			array.put("Path", this.get.get("path"));
			array.put("Filename", this.item.get("filename"));
			array.put("File Type", this.item.get("filetype"));
			array.put("Preview", this.item.get("preview"));
			array.put("Properties", this.item.get("properties"));
			array.put("Error", "");
			array.put("Code", 0);
		} catch (JSONException e) {
			this.error("JSONObject error");
		}
		return array;
	}
	
	public JSONObject getFolder() {
		JSONObject array = null;
		File dir = new File(documentRoot + this.get.get("path"));
		
		File file = null;
		if(!dir.isDirectory()) {
			this.error(sprintf(lang("DIRECTORY_NOT_EXIST"), this.get.get("path")));
		}
		else {
			if (!dir.canRead()){
				this.error(sprintf(lang("UNABLE_TO_OPEN_DIRECTORY"), this.get.get("path")));
			} else {
				array = new JSONObject();
				String[] files = dir.list();
				JSONObject data = null;
				JSONObject props = null;
				for (int i = 0; i < files.length; i++) {
					data = new JSONObject();
					props = new JSONObject();
					file = new File(documentRoot + this.get.get("path") + files[i]);
					if (file.isDirectory() && 
							!contains(config.getProperty("unallowed_dirs"), files[i])){
						try{
							props.put("Date Created", (String)null);
							props.put("Date Modified", (String)null);
							props.put("Height", (String)null);
							props.put("Width", (String)null);
							props.put("Size", (String)null);
							data.put("Path", this.get.get("path") + files[i] + "/");
							data.put("Filename", files[i]);
							data.put("File Type", "dir");
							data.put("Preview", config.getProperty("icons-path") + config.getProperty("icons-directory"));
							data.put("Error", "");
							data.put("Code", 0);
							data.put("Properties", props);
						
							array.put(this.get.get("path") + files[i] + "/", data);
						} catch (JSONException e) {
							this.error("JSONObject error");
						}
						
					} else if (!contains(config.getProperty("unallowed_files"), files[i])){
						this.item = new HashMap();
						this.item.put("properties", this.properties);
						this.getFileInfo(this.get.get("path") + files[i]);
						
						if (this.params.get("type") == null || (this.params.get("type") != null && (!this.params.get("type").equals("Image") || this.params.get("type").equals("Image") && 
								contains(config.getProperty("images"), (String)this.item.get("filetype"))))) {
							try{
								data.put("Path", this.get.get("path") + files[i]);
								data.put("Filename", this.item.get("filename"));
								data.put("File Type", this.item.get("filetype"));;
								data.put("Preview", this.item.get("preview"));
								data.put("Properties", this.item.get("properties"));
								data.put("Error", "");
								data.put("Code", 0);
		
								array.put(this.get.get("path") + files[i], data);
							} catch (JSONException e) {
								this.error("JSONObject error");
							}
						}
					}
				}
			}
		}
		return array;
	}
	
	public JSONObject rename() {
		if((this.get.get("old")).endsWith("/")) {
			this.get.put("old", (this.get.get("old")).substring(0,((this.get.get("old")).length() - 1)));
		}
		boolean error = false;
		JSONObject array = null;
		String tmp[] = (this.get.get("old")).split("/");
		String filename = tmp[tmp.length - 1];
		int pos = this.get.get("old").lastIndexOf("/");
		String path = (this.get.get("old")).substring(0, pos + 1);
		File fileFrom = null;
		File fileTo = null;
		try {
			fileFrom = new File(this.documentRoot + this.get.get("old"));
			fileTo = new File(this.documentRoot + path + this.get.get("new"));
		    if(fileTo.exists()) {
		    	if(fileTo.isDirectory()) {
		    		this.error(sprintf(lang("DIRECTORY_ALREADY_EXISTS"),this.get.get("new")));
					error = true;
		        }
		        else { // fileTo.isFile
		        	this.error(sprintf(lang("FILE_ALREADY_EXISTS"),this.get.get("new")));
					error = true;
		        }
		    }
		    else if (!fileFrom.renameTo(fileTo)){
				this.error(sprintf(lang("ERROR_RENAMING_DIRECTORY"), filename + "#" + this.get.get("new")));
				error = true;
			}
		} catch (Exception e) {
			if(fileFrom.isDirectory()) {
				this.error(sprintf(lang("ERROR_RENAMING_DIRECTORY"), filename + "#" + this.get.get("new")));
			} else {
				this.error(sprintf(lang("ERROR_RENAMING_FILE"), filename + "#" + this.get.get("new")));
			}
			error = true;
		}
		if (!error){
			array = new JSONObject();
			try{
				array.put("Error", "");
				array.put("Code", 0);
				array.put("Old Path", this.get.get("old"));
				array.put("Old Name", filename);
				array.put("New Path", path + this.get.get("new"));
				array.put("New Name", this.get.get("new"));
			} catch (JSONException e) {
				this.error("JSONObject error");
			}
		}
		return array;
	}
	
	public JSONObject delete() {
		JSONObject array = null;
		File file = new File(this.documentRoot + this.get.get("path"));
		if(file.isDirectory()) {
			array = new JSONObject();
			this.unlinkRecursive(this.documentRoot + this.get.get("path"), true);
			try {
				array.put("Error", "");
				array.put("Code", 0);
				array.put("Path", this.get.get("path"));
			} catch (Exception e) {
				this.error("JSONObject error");
			}
		} else if(file.exists()) {
			array = new JSONObject();
			if (file.delete()){
				try {
					array.put("Error", "");
					array.put("Code", 0);
					array.put("Path", this.get.get("path"));
				} catch (JSONException e) {
					this.error("JSONObject error");
				}
			}
			else
				this.error(sprintf(lang("ERROR_DELETING FILE"), this.get.get("path")));
			return array;
		} else {
			this.error(lang("INVALID_DIRECTORY_OR_FILE"));
		}
		return array;
	}
	
	public JSONObject add() {
		JSONObject fileInfo = null;
		Iterator it = this.files.iterator();
		String mode = "";
		String currentPath = "";
		if (!it.hasNext()){
			this.error(lang("INVALID_FILE_UPLOAD"));
		}
		else {
			String allowed[] = {".","-"};
		    FileItem item = null;
		    String fileName = "";
		    try {				
				while (it.hasNext()) {
				    item = (FileItem) it.next();
				    if (item.isFormField()){
				    	if (item.getFieldName().equals("mode")){
				    		mode = item.getString();
				    		if (!mode.equals("add")){
								this.error(lang("INVALID_FILE_UPLOAD"));
				    		}
				    	} else if (item.getFieldName().equals("currentpath")){
				    		currentPath = item.getString();
				    	}
				    }
				    else if (item.getFieldName().equals("newfile")){
			    		fileName = item.getName();
			    		// strip possible directory (IE)
			    		int pos = fileName.lastIndexOf(File.separator);
			    		if (pos > 0)
			    			fileName = fileName.substring(pos + 1);
			    		boolean error = false;
				    	long maxSize = 0;
						if(config.getProperty("upload-size") != null){
							maxSize = Integer.parseInt(config.getProperty("upload-size"));
							if (maxSize != 0 && item.getSize() > (maxSize * 1024 * 1024)){
								this.error(sprintf(lang("UPLOAD_FILES_SMALLER_THAN"), maxSize + "Mb"));
								error = true;
							}
						}
						if(!error){
							if (!isImage(fileName) && (config.getProperty("upload-imagesonly") != null && config.getProperty("upload-imagesonly").equals("true")
									|| this.params.get("type") != null && this.params.get("type").equals("Image"))) {
								this.error(lang("UPLOAD_IMAGES_ONLY"));
							} else {
								fileInfo = new JSONObject();
								LinkedHashMap<String, String> strList = new LinkedHashMap<String, String>();
								strList.put("fileName", fileName);
								fileName = (String)cleanString(strList, allowed).get("fileName");
								
								if(config.getProperty("upload-overwrite").equals("false")) {
									fileName = this.checkFilename(this.documentRoot + currentPath, fileName, 0);
								}
								
								File saveTo = new File(this.documentRoot + currentPath + fileName);
								item.write(saveTo);
						
								fileInfo.put("Path", currentPath);
								fileInfo.put("Name", fileName);
								fileInfo.put("Error", "");
								fileInfo.put("Code", 0);
							}
						}
			    	}
				}
			} catch (Exception e) {
				this.error(lang("INVALID_FILE_UPLOAD"));
			}
		}
		return fileInfo;
	
	}
	
	public JSONObject addFolder() {
		JSONObject array = null;
		String allowed[] = {"-"," "};
		LinkedHashMap<String, String> strList = new LinkedHashMap<String, String>();
		strList.put("fileName", this.get.get("name"));
		String filename = (String)cleanString(strList, allowed).get("fileName");
		if (filename.length() == 0) // the name existed of only special characters
			this.error(sprintf(lang("UNABLE_TO_CREATE_DIRECTORY"), this.get.get("name")));
		else {
			File file = new File(this.documentRoot + this.get.get("path") + filename);
			if(file.isDirectory()) {
				this.error(sprintf(lang("DIRECTORY_ALREADY_EXISTS"), filename));			
			}
			else if (!file.mkdir()){
				this.error(sprintf(lang("UNABLE_TO_CREATE_DIRECTORY"), filename));
			}
			else {
				try {
					array = new JSONObject();
					array.put("Parent", this.get.get("path"));
					array.put("Name", filename);
					array.put("Error", "");
					array.put("Code", 0);
				} catch (JSONException e) {
					this.error("JSONObject error");
				}
			}
		}
		return array;
	}
	
	public void download(HttpServletResponse resp) {
		File file = new File(this.documentRoot + this.get.get("path"));
		if(this.get.get("path") != null && file.exists()) {
			resp.setHeader("Content-type", "application/force-download");
			resp.setHeader("Content-Disposition", "inline;filename=\"" + documentRoot + this.get.get("path") + "\"");
			resp.setHeader("Content-Transfer-Encoding", "Binary");
			resp.setHeader("Content-length", "" + file.length());
			resp.setHeader("Content-Type", "application/octet-stream");
			resp.setHeader("Content-Disposition", "attachment; filename=\"" + file.getName() + "\"");
			readFile(resp, file);
		} else {
			this.error(sprintf(lang("FILE_DOES_NOT_EXIST"), this.get.get("path")));
		}
	}
	
	private void readFile(HttpServletResponse resp, File file){
		OutputStream os = null;
		FileInputStream fis = null;
		try {
			os = resp.getOutputStream();
			fis = new FileInputStream(file);
		    byte fileContent[] = new byte[(int)file.length()];
		    fis.read(fileContent);
		    os.write(fileContent);
		} catch (Exception e) {
			this.error(sprintf(lang("INVALID_DIRECTORY_OR_FILE"), file.getName()));
		}
		finally {
			try {
				if (os != null)
					os.close();
			} catch (Exception e2) {}
			try {
				if (fis != null)
					fis.close();
			} catch (Exception e2) {}
		}
	}

	public void preview(HttpServletResponse resp) {
		File file = new File(this.documentRoot + this.get.get("path"));
		if(this.get.get("path") != null && file.exists()) {
			resp.setHeader("Content-type", "image/" + getFileExtension(file.getName()));
			resp.setHeader("Content-Transfer-Encoding", "Binary");
			resp.setHeader("Content-length", "" + file.length());
			resp.setHeader("Content-Disposition", "inline; filename=\"" + getFileBaseName(file.getName()) + "\"");
		    readFile(resp, file);
		} else {
			error(sprintf(lang("FILE_DOES_NOT_EXIST"),this.get.get("path")));
		}
	}
	  
	  private String getFileBaseName(String filename){
		  String retval = filename;
		  int pos = filename.lastIndexOf(".");
		  if (pos > 0)
			  retval = filename.substring(0, pos);
		  return retval;
	  }

	  private String getFileExtension(String filename){
		  String retval = filename;
		  int pos = filename.lastIndexOf(".");
		  if (pos > 0)
			  retval = filename.substring(pos + 1);
		  return retval;
	  }

	  private void setParams() {
		String[] tmp = this.referer.split("\\?");
		String[] params_tmp = null;
		LinkedHashMap<String, String> params = new LinkedHashMap<String, String>();
		if(tmp.length > 1 && tmp[1] != "") {
			params_tmp = tmp[1].split("&");
			for (int i = 0; i < params_tmp.length; i++) {
				tmp = params_tmp[i].split("=");
				if(tmp.length > 1 && tmp[1] != "") {
					params.put(tmp[0], tmp[1]);
				}
			}
		}
		this.params = params;
	}
	
	public String getConfigString(String key){
		return config.getProperty(key);
	}
	
	public String getDocumentRoot(){
		return this.documentRoot;
	}

	private void getFileInfo(String path) {
		String pathTmp = path;
		if(pathTmp == "") {
			pathTmp = this.get.get("path");
		}
		String[] tmp = pathTmp.split("/");
		File file = new File(this.documentRoot + pathTmp);
		this.item = new HashMap();
		String fileName = tmp[tmp.length - 1];
		this.item.put("filename", fileName);
		if (file.isFile())
			this.item.put("filetype", fileName.substring(fileName.lastIndexOf(".") + 1));
		else
			this.item.put("filetype", "dir");
		this.item.put("filemtime", "" + file.lastModified());
		this.item.put("filectime", "" + file.lastModified());
		
		this.item.put("preview", config.getProperty("icons-path") + "/" + config.getProperty("icons-default")); // @simo
		
		HashMap<String, String> props = new HashMap();
		if(file.isDirectory()) {
			
			this.item.put("preview", config.getProperty("icons-path") + config.getProperty("icons-directory"));
			
		} 
		else if (isImage(pathTmp)) 
		{
			this.item.put("preview", "connectors/jsp/filemanager.jsp?mode=preview&path=" + pathTmp);
			Dimension imgData = getImageSize(documentRoot + pathTmp);
			props.put("Height", "" + imgData.height);
			props.put("Width", "" + imgData.width);
			props.put("Size", "" + file.length());
		} else {
			File icon = new File(fileManagerRoot + config.getProperty("icons-path") + ((String)this.item.get("filetype")).toLowerCase() + ".png");
			if(icon.exists()) {			
				this.item.put("preview", config.getProperty("icons-path") + ((String)this.item.get("filetype")).toLowerCase() + ".png");
				props.put("Size", "" + file.length());
			}
		}
		
		props.put("Date Modified", dateFormat.format(new Date(new Long((String)this.item.get("filemtime")))));
		this.item.put("properties", props);
	}
	
	private boolean isImage(String fileName){
		boolean isImage = false;
		String ext = "";
		int pos = fileName.lastIndexOf(".");
		if (pos > 1 && pos != fileName.length()){
			ext = fileName.substring(pos + 1);
			isImage = contains(config.getProperty("images"), ext);
		}
		return isImage;
	}
	
	public boolean contains(String where, String what){
		boolean retval = false;

		String[] tmp = where.split(",");
		for (int i = 0; i < tmp.length; i++) {
			if (what.equalsIgnoreCase(tmp[i])){
				retval = true;
				break;
			}
		}
		return retval;
	}

	private Dimension getImageSize(String path){
		Dimension imgData = new Dimension(); 
		Image img = new ImageIcon(path).getImage();
		imgData.height = img.getHeight(null);
		imgData.width = img.getWidth(null);
		return imgData;
	}
	
	private void unlinkRecursive(String dir, boolean deleteRootToo) {
		File dh = new File(dir);
		File fileOrDir = null;
		
		if(dh.exists()) {
			String[] objects = dh.list();
			for (int i = 0; i < objects.length; i++) {
				fileOrDir = new File(dir + "/" + objects[i]);
				if (fileOrDir.isDirectory()){
					if (!objects[i].equals(".") && !objects[i].equals("..")){
						unlinkRecursive(dir + "/" + objects[i], true);
					}
				}
				fileOrDir.delete();
				
					
			}
			if (deleteRootToo) {
				dh.delete();
			}
		}
	}
	
	private HashMap<String, String> cleanString(HashMap<String, String> strList, String[] allowed) {
        String allow = "";
        HashMap<String, String> cleaned = null;
        Iterator<String> it = null;
        String cleanStr = null;
        String key = null;
        for (int i = 0; i < allowed.length; i++) {
            allow += "\\" + allowed[i];
		}

        if (strList != null) {
            cleaned = new HashMap<String, String>();
        	it = strList.keySet().iterator();
        	while (it.hasNext()) {
        		key = it.next();
        		cleanStr = strList.get(key).replaceAll("[^{" + allow + "}_a-zA-Z0-9]", "");
        		cleaned.put(key, cleanStr);
			}
        }
        return cleaned;
    }
	
	private String sanitize(String var) {
		String sanitized = var.replaceAll("\\<.*?>","");	
		sanitized = sanitized.replaceAll("http://", "");
		sanitized = sanitized.replaceAll("https://", "");
		sanitized = sanitized.replaceAll("\\.\\./", "");
		return sanitized;
	}
	
	private String checkFilename(String path, String filename, int i) {
		File file = new File(path + filename);
		String i2 = "";
		String[] tmp = null;
		if(!file.exists()) {
			return filename;
		} else {
			if (i != 0)
				i2 = "" + i; 
			tmp = filename.split(i2 + "\\.");
			i++;
			filename = filename.replace(i2 + "." + tmp[tmp.length - 1],i + "." + tmp[tmp.length-1]);
			return this.checkFilename(path, filename, i);
		}
	}	
	
	private void loadConfig(){
		InputStream is;
		if (config == null){
			try {
				is = new FileInputStream(
						this.fileManagerRoot + "connectors/jsp/config.properties");
				config = new Properties();
				config.load(is);
			} catch (Exception e) {
				error("Error loading config file");
			}
		}
	}

	private void loadLanguageFile() {

		// we load langCode var passed into URL if present
		// else, we use default configuration var
		if (language == null){
			String lang = "";
			if (params.get("langCode") != null)
				lang = this.params.get("langCode");
			else 
				lang = config.getProperty("culture");
			BufferedReader br = null;
			InputStreamReader isr = null;
			String text;
			StringBuffer contents = new StringBuffer();
			try {
				isr = new InputStreamReader(new FileInputStream (this.fileManagerRoot + "/scripts/languages/" + lang + ".js"), "UTF-8");
				br = new BufferedReader (isr);
				while ((text = br.readLine()) != null)
					contents.append(text);
				language = new JSONObject(contents.toString());
			} catch (Exception e) {
				  this.error("Fatal error: Language file not found.");				
			}
			finally {
				try {
					if (br != null)
						br.close();
				} catch (Exception e2) {}
				try {
					if (isr != null)
						isr.close();
				} catch (Exception e2) {}
			}
		}
	}
	
	public String sprintf(String text, String params){
		String retText = text;
		String[] repl = params.split("#");
		for (int i = 0; i < repl.length; i++) {
			retText = retText.replaceFirst("%s", repl[i]);
		}
		return retText;
	}
	
	public void log(String filename, String msg)
	{
		try {
			BufferedWriter out = new BufferedWriter(new FileWriter(filename, true));
			out.append(msg + "\r\n");
			out.close();
		} 
		catch (IOException e) 
		{ 
			e.printStackTrace();
		}
	}
}
