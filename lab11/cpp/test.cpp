#include <iostream>
#include <sqlite3.h>

int main(int argc, char** argv)
{
  	if(argc != 4)
    {
      std::cerr << "USAGE: " << argv[0] << " <database file> <table name> <CSV file>" << std::endl;
      return 1;
    }

	sqlite3 *db;
	char *zErrMsg = 0;
	int rc;
	char *sql;

	/* Open database */
	rc = sqlite3_open("myDatabase.db", &db);
	if ( rc ) {
	  fprintf(stderr, "Can't open database: %s\n", sqlite3_errmsg(db));
	  exit(0);
	}
	else {
	  fprintf(stderr, "Opened database successfully\n");
	}
  

  std::cout << "Implement me!" << std::endl;

  return 0;
}
