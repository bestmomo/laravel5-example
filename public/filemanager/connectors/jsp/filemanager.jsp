<%@ page language="java" import="java.util.*"%>
<%@ page import="com.nartex.*"%>
<%@ page import="org.json.JSONObject"%>
<%@ page import="java.io.*"%>
<%@ page import="org.apache.commons.fileupload.*"%>
<%@ page import="org.apache.commons.fileupload.disk.*"%>
<%@ page import="org.apache.commons.fileupload.servlet.*"%>
<%@include file="auth.jsp"%>
<%
/*
 *	connector filemanager.jsp
 *
 *	@license	MIT License
 *	@author		Dick Toussaint <d.tricky@gmail.com>
 *	@copyright	Authors
 */
  
	FileManager fm = new FileManager(getServletContext(), request);

	JSONObject responseData = null;

	String mode = "";
    boolean putTextarea = false;
	if(!auth()) {
		fm.error(fm.lang("AUTHORIZATION_REQUIRED"));
	}
	else { 
		if(request.getMethod().equals("GET")) {
			if(request.getParameter("mode") != null && request.getParameter("mode") != "") {
				mode = request.getParameter("mode");
				if (mode.equals("getinfo")){
					if(fm.setGetVar("path", request.getParameter("path"))) {
						responseData = fm.getInfo();
					}
				}
				else if (mode.equals("getfolder")){
					if(fm.setGetVar("path", request.getParameter("path"))) {
						responseData = fm.getFolder();
					}
				}
				else if (mode.equals("rename")){
					if(fm.setGetVar("old", request.getParameter("old")) && 
							fm.setGetVar("new", request.getParameter("new"))) {
						responseData = fm.rename();
					}
				}
				else if (mode.equals("delete")){
					if(fm.setGetVar("path", request.getParameter("path"))) {
						responseData = fm.delete();
					}
				}
				else if (mode.equals("addfolder")){
					if(fm.setGetVar("path", request.getParameter("path")) && 
							fm.setGetVar("name", request.getParameter("name"))) {
						responseData = fm.addFolder();
					}
				}
				else if (mode.equals("download")){
					if(fm.setGetVar("path", request.getParameter("path"))) {
						fm.download(response);
					}
				}
				else if (mode.equals("preview")){
					if(fm.setGetVar("path", request.getParameter("path"))) {
						fm.preview(response);
					}
				}
				else {
					fm.error(fm.lang("MODE_ERROR"));
				}
			}
		}
		else if(request.getMethod().equals("POST")){
			mode = "upload";
			responseData = fm.add();
			putTextarea = true;
		}
	}
	if (responseData == null){
		responseData = fm.getError();
	}
	if (responseData != null){
	  	PrintWriter pw = response.getWriter();
	  	String responseStr = responseData.toString();
	  	if (putTextarea)
	  		responseStr = "<textarea>" + responseStr + "</textarea>";
	  	//fm.log("c:\\logfilej.txt", "mode:" + mode + ",response:" + responseStr);
	  	pw.print(responseStr);
	  	pw.close();
	}
  %>	
