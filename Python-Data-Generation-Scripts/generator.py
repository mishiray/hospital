import mysql.connector
import json
import re

LOG = "[LOG] {}"

# Load connection.json
with open('Python-Data-Generation-Scripts/connection.json') as json_data:
    CONN = json.load(json_data)

# Load sql-query.json
with open('Python-Data-Generation-Scripts/sql-query.json') as json_data:
    SQL_Q = json.load(json_data)

# COMMANDS
CREATE_TABLE = r"((C|c)reate|CREATE) ((T|t)able|TABLE) \w+ in ((S|s)chema|SCHEMA) \w+"
CREATE_SCHEMA = r"((C|c)reate|CREATE) ((S|s)chema|SCHEMA) \w+"


def initialize_DB(mycursor):
    '''
    Creates all necessary schema and tables if they do not already exist
    '''

    # Fetch all databases
    mycursor.execute("SHOW DATABASES")
    schemas = [x[0] for x in mycursor.fetchall()]

    if SQL_Q['schema'] not in schemas:
        mycursor.execute(SQL_Q['create-schema'])
        print(LOG.format(f"Created schema '{SQL_Q['schema']}'"))
    else:
        print(LOG.format(f"Schema '{SQL_Q['schema']}' exists."))

    # Fetch all tables in hospital
    mycursor.execute("SHOW TABLES")
    tables = [x[0] for x in mycursor.fetchall()]

    for table in SQL_Q['tables']:
        if table not in tables:
            mycursor.execute(SQL_Q['create-table'][table])
            print(LOG.format(f"Created table '{table}'"))
        else:
            print(LOG.format(f"Table '{table}' exists."))


if __name__ == "__main__":

    # Initialize connection to database
    mydb = mysql.connector.connect(**CONN)
    mycursor = mydb.cursor()

    initialize_DB(mycursor)

    while True:

        command = input('\n>> ')
