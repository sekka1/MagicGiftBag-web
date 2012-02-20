This version works with the Access mdb database which is bundled with the web application or with MySQL whose database-creation scripts are provided along with the web application.
We have used JSP 2.0 and j2sdk1.4.2_02 for the development of this demo.

Prerequisites:
1. Java version - j2sdk1.4.2_02

2. Apache Tomcat version - jakarta-tomcat-5.0.28

3. MySQL 4.X or higher OR MS Access database

Installation Instructions:
1. Java version - j2sdk1.4.2_02 which can be downloaded from the following link:
http://java.sun.com/j2se/1.4.2/download.html

2. After downloading and installing java,JAVA_HOME environment variable has to be set to determine the base path of the JDK.
To set this variable on Windows do the following:
Go to MyComputer, right click to view properties -> advanced tab ->environment variables 
Set a new Variable name: JAVA_HOME with Value as the installation directory. For eg: 
C:\j2sdk1.4.2_02

3. Apache Tomcat version - jakarta-tomcat-5.0.28 which can be downloaded from the following link:
http://tomcat.apache.org/download-55.cgi

Click on the core zip file. Download it.Extract it to some folder.

4.After downloading and installing tomcat,CATALINA_HOME environment variable has to be set to determine the base path of the tomcat.
To set this variable on Windows do the following:
Go to MyComputer, right click to view properties -> advanced tab ->environment variables 
Set a new Variable name: CATALINA_HOME with Value as the installation directory. For eg: 
C:\jakarta-tomcat-5.0.25

5. Copy the fcgeneral.war to "CATALINA_HOME"/webapps folder
6. Creation of the database required by FusionCharts:
   If you are using MSAccess, then  copy the FactoryDB.mdb present in the Database/MSAccess       folder to some location on your hard-drive.
   If you are using MySQL, then run the FactoryDBCreation.sql (present in the Database/MySQL) in your MySQL.
This creates the FactoryDB database on your machine.
7. In order to configure the MySQL database:
   Open the file "CATALINA_HOME"/conf/server.xml	
   Add the following xml code just before </Host>
   <Context path="/fcgeneral" docBase="fcgeneral"
        debug="5" reloadable="true" crossContext="true">

  <Logger className="org.apache.catalina.logger.FileLogger"
             prefix="localhost_DBTest_log." suffix=".txt"
             timestamp="true"/>

  <Resource name="jdbc/FactoryDB"
               auth="Container"
               type="javax.sql.DataSource"/>

  <ResourceParams name="jdbc/FactoryDB">
    <parameter>
      <name>factory</name>
      <value>org.apache.commons.dbcp.BasicDataSourceFactory</value>
    </parameter>

    <!-- Maximum number of dB connections in pool. Make sure you
         configure your mysqld max_connections large enough to handle
         all of your db connections. Set to 0 for no limit.
         -->
    <parameter>
      <name>maxActive</name>
      <value>100</value>
    </parameter>

    <!-- Maximum number of idle dB connections to retain in pool.
         Set to 0 for no limit.
         -->
    <parameter>
      <name>maxIdle</name>
      <value>30</value>
    </parameter>

    <!-- Maximum time to wait for a dB connection to become available
         in ms, in this example 10 seconds. An Exception is thrown if
         this timeout is exceeded.  Set to -1 to wait indefinitely.
         -->
    <parameter>
      <name>maxWait</name>
      <value>10000</value>
    </parameter>

    <!-- MySQL dB username and password for dB connections  -->
    <parameter>
     <name>username</name>
     <value>fcuser</value>
    </parameter>
    <parameter>
     <name>password</name>
     <value>fcuser</value>
    </parameter>

    <!-- Class name for the old mm.mysql JDBC driver - uncomment this entry and comment next
         if you want to use this driver - we recommend using Connector/J though
    <parameter>
       <name>driverClassName</name>
       <value>org.gjt.mm.mysql.Driver</value>
    </parameter>
     -->
    
    <!-- Class name for the official MySQL Connector/J driver -->
    <parameter>
       <name>driverClassName</name>
       <value>com.mysql.jdbc.Driver</value>
    </parameter>
    
    <!-- The JDBC connection url for connecting to your MySQL dB.
         The autoReconnect=true argument to the url makes sure that the
         mm.mysql JDBC Driver will automatically reconnect if mysqld closed the
         connection.  mysqld by default closes idle connections after 8 hours.
         -->
    <parameter>
      <name>url</name>
      <value>jdbc:mysql://localhost:3306/factorydb?autoReconnect=true</value>
    </parameter>
  </ResourceParams>
</Context>  

In the above xml,please change the username,password,url according to your database.Optionally, you can change the resource name,resourceparams name which are used to lookup for this database.Whatever values are given here same values should be given in the web.xml of your webapp.

8. Changes to web.xml present in "CATALINA_HOME"/webapps/fcgeneral/WEB-INF/ folder:
In order to change the database from mysql to access or vice-versa, change the following entry in web.xml.
<context-param>
      <param-name>dbName</param-name> 
      <param-value>MySQL</param-value>
      <description>Database Name</description>
 </context-param>

Currently, you can set the param-value to MySQL or MSAccess.(case-sensitive)
In the case of Access DB, you have to set the path to the database in the following parameter in web.xml.
 <context-param>
      <param-name>AccessDBPath</param-name>
      <param-value>C:/tomcat/fcgeneral/GeneralPages/JSP/DB/FactoryDB.mdb</param-value>
      <description> Absolute path to the Access DB </description>
 </context-param>
In the case of MySQL DB,you have to set the Data Source Name in the following parameter in the web.xml
<!-- DataSourceName as given in the resource-ref tag and in server.xml -->  
 <context-param>
       <param-name>dataSourceName</param-name> 
       <param-value>jdbc/FactoryDB</param-value>
       <description>DataSource Name</description>
 </context-param>

9.If you are using MySQL as the database, please start your MySQL instance.
10.To start the tomcat server,go to the tomcat installation directory ("CATALINA_HOME") and run startup.bat

11.Access the FusionCharts website by opening the browser window with the following address:
http://localhost:8080/fcgeneral/GeneralPages/JSP/


Note: "CATALINA_HOME" refers to the installation directory of Tomcat