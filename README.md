# DBMS-project
### Description
A semester project for Database Management Systems (CSC-3032-1) The database is based on storing information pertaining to ski clubs. The process of designing the database itself included designing Entity-Relationship (ER) models and reducing those ER models into relational schemas, specifying the domain, referential integrity, and unique constraints. The database was carefully designed for optimal performance and thus indexing was carefully thought out. Then, normalization into Boyce–Codd normal form (BCNF) was done (ensuring lossless decomposition) before implementation to ensure a good design. The schema was implemented using PostgreSQL as the database with a variety of tools, including IntelliJ IDEA, DBbeaver, PGadmin, Ubuntu (Debian-based Linux operating system to execute psql commands). In addition to creating the database, a web interface was built for the purposes of simulating the deployment of the database, demonstrating how the database could be utilized. PHP was used to allow the web page to interact with the database and create dynamic web pages. Since PHP is a server-side scripting language, a web server was necessary for testing. I used an Apache HTTP server deployed locally via XAMMP (while I also copied the database over to our department server for testing). The web pages use the typically HTML, CSS, and JS.
### Implementation
#### To view a tour of the database in effect go to this link: 
https://github.com/Lanshenberger/DBMS-project/blob/master/demonstration/club_database_walk-through.pdf \
#### To run the database on your system, follow these steps: 
1. Install these tools/programs necessary for operation
	* Install PostgreSQL: https://www.postgresql.org/download/
	* Install an Apache web server. XAMPP is a simple option: https://www.apachefriends.org/download.html
	* An optional tool for assisting in managing the database is pgAdmin, available here: https://www.pgadmin.org/download/
2. Create the database:
	*  Download the database backup file available here: https://github.com/Lanshenberger/DBMS-project/blob/master/Club%20database/club_database_backup.sql. Either download the entire project and navigate to this file or view it in raw format and "save as...". Please note the backup file contains sample data and will add sample data on restore.  
	*  Run the backup script on a newly created database:
		* Using pgAdmin:
			1. Create the database by right-clicking on "databases"->Create->Database...
			2. Right-click on the newly created database and select Restore...
		* Using command line interface:
			1. Establish a command line connection by execting this command (remember to set a password for user postgres): 
			```
			psql -U postgres
			```
			2. Create a new database after establishing the connection then quit:
			```
			create database <dbname>;
			\q
			```
			3. Run the backup script (use psql not pg_restore because the script is in plain format):
			```
			psql -U <username> -d <dbname> -1 -f <filename>.sql
			```
			4. An example of this
			
