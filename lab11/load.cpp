#include <iostream>
#include <sqlite3.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <fstream>
#include <string>

using namespace std;

int main(int argc, char** argv)
{
  	if(argc != 4)
    {
      std::cerr << "USAGE: " << argv[0] << " <database file> <table name> <CSV file>" << std::endl;
      return 1;
    }

    ifstream input_file(argv[3]);
    if (!input_file.is_open()) {
    	cout << "Unable to open csv file";
    	return 0-1;
    }

  	sqlite3 *db;
	//char *zErrMsg = 0;
	int rc = 0;
	sqlite3_stmt *stmt, *stm;
	const char *delete_all;
	/*char first[100];
	strcat(first, "DELETE * FROM ");
	strcat(first, argv[2]);
	strcat(first, ";");*/
	int row = 0;
	delete_all = "DELETE * FROM mytable;";
	cout << delete_all << endl;

	/* Open database */
	rc = sqlite3_open(argv[1], &db);
	if (rc) {
	  fprintf(stderr, "Can't open database: %s\n", sqlite3_errmsg(db));
	  exit(0);
	}
	else {
	  fprintf(stderr, "Opened database successfully\n");
	}

	sqlite3_prepare_v2(db, delete_all, strlen(delete_all)+1, &stmt,NULL);
	sqlite3_step(stmt);

	string line;

	while(getline(input_file, line)){

        const char *query;
		string first;
		first = first + "INSERT INTO mytable VALUES (";
		first = first + line;
		first = first + ");";
		query = first.c_str();
		sqlite3_prepare_v2(db, query, strlen(query)+1, &stm,NULL);
		sqlite3_step(stm);

		sqlite3_finalize(stm);
		std::cout << line << std::endl;
		cout << first << endl;
            //cout<< value << endl;
    }

  
	sqlite3_finalize(stmt);

  	sqlite3_close(db);
 	input_file.close();
  	return 0;
}
