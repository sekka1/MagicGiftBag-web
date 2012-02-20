<%@page import="java.sql.Connection"%>
<%@page import="java.sql.DriverManager"%>
<%@ page import="com.infosoftglobal.fusioncharts.Constants"%>

<%@ page import="javax.naming.Context"%>
<%@ page import="javax.naming.InitialContext"%>
<%@ page import="javax.naming.NamingException"%>
<%@ page import="javax.sql.DataSource"%>
<% 
	     /*In this page, we open the connection to the Database
	     */
	    Connection oConn = null;
		// The Database Name is got from the web.xml context param
		String dbName=getServletContext().getInitParameter("dbName");
		
		//Checking whether the database is Access or MySQL so that the appropriate jsp can be included 
		//to get the connection to that database.Ideally, the connection should be got from a Java class which has different 
		//methods to get the connection to different databases. For demo,we have used an Include.
		
		if(dbName.equals(Constants.ACCESSDB)) {
		     /*
		     Our Access database is contained in ../DB/FactoryDB.mdb
		     It's a very simple database with just 2 tables (for the sake of demo).
		     Ideally the Connection to the database should be made in a Java class or using Connection Pooling component
		     Here, the complete path to the db is taken from web.xml 
	        */
		    Class.forName("sun.jdbc.odbc.JdbcOdbcDriver");
	
		    String absolutePath = getServletContext().getInitParameter("AccessDBPath");
		    if (null == oConn) {
			/* add connection mode here if required*/
				String connString = "jdbc:odbc:Driver={Microsoft Access Driver (*.mdb)};DBQ="+ absolutePath;
		
				oConn = DriverManager.getConnection(connString, "", "");
		    }
		}
		else if(dbName.equals(Constants.MYSQLDB)) {
		    
		    /*In this page, we open the connection to the Database
		     Our MySQL database.
		     It's a very simple database with just 2 tables (for the sake of demo).
		     Ideally the Connection to the database should be made in a Java class or using Connection Pooling component
		     Here the Data Source name comes from the web.xml.
	        */
	        oConn = null;

			try {
				   Context initContext = new InitialContext();
				   Context envContext  = (Context)initContext.lookup("java:/comp/env");
			       DataSource ds = (DataSource)envContext.lookup("jdbc/FactoryDB");
			       oConn = ds.getConnection();
			} catch (java.sql.SQLException e) {
			    // TODO Auto-generated catch block
			    e.printStackTrace();
			} catch (NamingException e) {
			    // TODO Auto-generated catch block
			    e.printStackTrace();
			}   
		}
%>
