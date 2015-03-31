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

    ofstream output_file(argv[3]);
    if (!output_file.is_open()) {
    	cout << "Unable to open csv file";
    	return 0-1;
    }

  	sqlite3 *db;
	//char *zErrMsg = 0;
	int rc = 0;
	sqlite3_stmt *stmt;
	const char *query;
	char first[100];
	strcat(first, "SELECT * FROM ");
	strcat(first, argv[2]);
	strcat(first, ";");
	int row = 0;
	query = first;


	/* Open database */
	rc = sqlite3_open(argv[1], &db);
	if (rc) {
	  fprintf(stderr, "Can't open database: %s\n", sqlite3_errmsg(db));
	  exit(0);
	}
	else {
	  fprintf(stderr, "Opened database successfully\n");
	}

	sqlite3_prepare_v2(db, query, strlen(query)+1, &stmt,NULL);

	while (1) {
        int t = sqlite3_column_count(stmt);
        int s = sqlite3_step (stmt);
        if (s == SQLITE_ROW) {
            for(int x=0; x<t; x++) {
            	if (sqlite3_column_type(stmt, x) == SQLITE_TEXT) {
            		const unsigned char * text;
            		text  = sqlite3_column_text (stmt, x);
            		printf ("%s ", text);
            		output_file << "'" << text << "'";
            	}
            	if (sqlite3_column_type(stmt, x) == SQLITE_INTEGER) {
            		int int_val;
            		int_val = sqlite3_column_int(stmt, x);
            		printf("%d ", int_val);
            		output_file << int_val;
            	}
            	else if (sqlite3_column_type(stmt,x) != SQLITE_INTEGER && sqlite3_column_type(stmt,x) != SQLITE_TEXT) {
            		cout << "Not correct data type";
            		return 0-1;
            	}
            	if(x == t-1) {
            		output_file << endl;
            	}
            	else {
            		output_file << ",";
            	}


            }
            printf("\n");
            row++;
        }
        else if (s == SQLITE_DONE) {
            break;
        }
        else {
            fprintf (stderr, "Failed.\n");
            exit (1);
        }
    }

    sqlite3_finalize(stmt);

  	sqlite3_close(db);
 	output_file.close();
  return 0;
}
