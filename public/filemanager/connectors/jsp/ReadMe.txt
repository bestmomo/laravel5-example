The libraries directory contains the java source and classes used by the java connector file 'filemanager.jsp'

You should copy the libraries/java/bin directory content for instance to the WEB-INF\classes directory (in case you are using a Tomcat application server) to make everything work

After copying the libraries/java/bin directory content, you can delete the whole libraries directory. It is not needed for a production version of the file manager. 