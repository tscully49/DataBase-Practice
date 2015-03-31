#include <iostream>
#include <sqlite3.h>

int main(int argc, char** argv)
{
  if(argc != 4)
    {
      std::cerr << "USAGE: " << argv[0] << " <database file> <table name> <CSV file>" << std::endl;
      return 1;
    }

  std::cout << "Implement me!" << std::endl;

  return 0;
}
